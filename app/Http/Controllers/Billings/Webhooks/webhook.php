<?php

namespace App\Http\Controllers;

use App\Domains\Billings\Enums\{TrxPhaseEnums, FallbackTrxPaymentStatusEnums};
use App\Helpers\Errors\ExceptionHandling;
use App\Helpers\PaymentHelpers\CentsConversion;
use App\Models\Billings\Payments\{PaymentTrx, PaymentItem};
use App\Models\Billings\Subscriptions\SubscribedUserWeb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Cache, Log, DB};
use Laravel\Cashier\Http\Controllers\WebhookController as CashierWebhookController;
use Carbon\Carbon;
use Throwable;

class webhook extends CashierWebhookController
{
   public function handleWebhook(Request $request)
   {
      $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
      $webhookSecret = config('services.stripe.webhook_secret');
      $payload = $request->getContent();
      $sig_header = request()->header('Stripe-Signature');
      $event = null;

      try {
         $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $webhookSecret);
         $event = $event->toArray();
      } catch (Throwable $e) {
         Log::error('Stripe webhook event error', [$e]);
         return response()->json(['error' => 'Invalid payload in Stripe webhook event'], 400);
      }

      $eventId = $event['id'];
      $lock = Cache::lock("stripe_evt_{$eventId}", 300);
      if (!$lock->get()) {
         return response()->json(['status' => 'success', 'message' => 'Duplicate event locked'], 200);
      }

      Log::info("==================================");
      Log::info("==================================");
      Log::info($event['type']);

      try {
         switch ($event['type']) {
            case 'checkout.session.completed':
               $session = $event['data']['object'];
               if (!$session)
                  break;

               $paymentIntent = $session['payment_intent'] ?? null;
               $paymentTrx = PaymentTrx::with(['trxRelationWith_Items'])->checkoutSessionId($session['id'])->latest()->first();
               if (!$paymentTrx)
                  break;

               // ----------- save events preventing dedupes -----------
               $savedEvents = $paymentTrx->event_ids ?? ['processed_ids' => [], 'traces' => []];
               if (in_array($eventId, $savedEvents['processed_ids'])) {
                  return $this->successMethod();
               }
               // ----------- save events preventing dedupes -----------

               $paymentItems = $paymentTrx->trxRelationWith_Items;
               if (!$paymentItems || $paymentItems->isEmpty())
                  break;

               $itemTotalAmount = $paymentItems->sum('ss_amount');

               DB::transaction(function () use ($session, $paymentTrx, $itemTotalAmount, $paymentIntent, $eventId, $event, &$savedEvents) {

                  if ($itemTotalAmount === (int)$session['amount_subtotal']) {
                     $paymentTrx->payment_status = $session['payment_status'] ?? FallbackTrxPaymentStatusEnums::PAID->value;
                  } else {
                     $paymentTrx->payment_status = ($session['payment_status'] ?? null) . "_" . FallbackTrxPaymentStatusEnums::PARTIAL->value;
                     $paymentTrx->note = "Item(s) amount summed = {$itemTotalAmount}, but stripe received = {$session['amount_total']}";
                  }

                  $paymentTrx->trx_phase = TrxPhaseEnums::COMPLETED->value;
                  $paymentTrx->paid_amount = $session['amount_total'];
                  $paymentTrx->payment_intent = $paymentIntent;

                  $customer = $session['customer_details'] ?? [];
                  $paymentTrx->customer_details = [
                     'name' => $customer['name'] ?? null,
                     'email' => $customer['email'] ?? null,
                     'phone' => $customer['phone'] ?? null,
                     'address' => collect([
                        $customer['address']['country'] ?? null,
                        $customer['address']['city'] ?? null,
                        $customer['address']['postal_code'] ?? null,
                        $customer['address']['state'] ?? null
                     ])->filter()->implode(', ')
                  ];

                  // ----------- saving event traces -----------
                  $savedEvents['processed_ids'][] = $eventId;
                  $savedEvents['traces'][$event['type']][] = ['event_id' => $eventId, 'created_at' => !empty($event['created']) ? Carbon::createFromTimestamp($event['created'])->toDateTimeString() : null];
                  $paymentTrx->event_ids = $savedEvents;
                  // ----------- saving event traces -----------

                  $paymentTrx->save();
               });
               break;

            case 'charge.updated':
               $charge = $event['data']['object'];
               if (!$charge)
                  break;

               $paymentIntent = $charge['payment_intent'];
               $transactionId = $charge['balance_transaction'];
               if (!$transactionId || !$paymentIntent)
                  break;

               try {
                  $balanceTransaction = $stripe->balanceTransactions->retrieve($transactionId);
               } catch (Throwable $e) {
                  Log::error('Stripe balance transaction failed', [$e]);
                  break;
               }

               $paymentTrx = PaymentTrx::where('payment_intent', $paymentIntent)->latest()->first();
               if (!$paymentTrx) {
                  try {
                     $stripeIntent = $stripe->paymentIntents->retrieve($paymentIntent, ['expand' => ['checkout_session']]);

                     if (!empty($stripeIntent->checkout_session->id)) {
                        $paymentTrx = PaymentTrx::checkoutSessionId($stripeIntent->checkout_session->id)->latest()->first();

                        if ($paymentTrx) {
                           $paymentTrx->payment_intent = $paymentIntent;
                           $paymentTrx->save();
                        }
                     }
                  } catch (Throwable $e) {
                     Log::error('Stripe fallback intent resolution failed due to payment_intent mismatch or missing', [$e]);
                  }
               }

               if (!$paymentTrx)
                  break;

               // ----------- save events preventing dedupes -----------
               $savedEvents = $paymentTrx->event_ids ?? ['processed_ids' => [], 'traces' => []];
               if (in_array($eventId, $savedEvents['processed_ids'])) {
                  return response()->json(['status' => 'success'], 200);
               }
               // ----------- save events preventing dedupes -----------

               $paymentItems = PaymentItem::paymentTrxId($paymentTrx->id)->get();
               if (!$paymentItems || $paymentItems->isEmpty())
                  break;

               $totalAmount = $balanceTransaction['amount'];
               $stripeFee = 0;

               foreach ($balanceTransaction['fee_details'] as $fee_detail) {
                  if ($fee_detail['type'] === 'stripe_fee') {
                     $stripeFee = $fee_detail['amount'];
                     $paymentTrx->stripe_fee = $stripeFee;
                  }
               }

               DB::transaction(function () use ($paymentTrx, $totalAmount, $stripeFee, $paymentItems, $eventId, $event, &$savedEvents) {

                  if ($totalAmount !== $paymentTrx->paid_amount) {
                     $prefixNote = "Saved amount = {$paymentTrx->paid_amount}, but Stripe sent balanceTotal['amount'] = {$totalAmount}";
                     $paymentTrx->note = $paymentTrx->note ? ($paymentTrx->note . " | " . $prefixNote) : $prefixNote;
                  }
                  $paymentTrx->takeaway_amount = $totalAmount - $stripeFee;

                  // ----------- saving event traces -----------
                  $savedEvents['processed_ids'][] = $eventId;
                  $savedEvents['traces'][$event['type']][] = ['event_id' => $eventId, 'created_at' => !empty($event['created']) ? Carbon::createFromTimestamp($event['created'])->toDateTimeString() : null];
                  $paymentTrx->event_ids = $savedEvents;
                  // ----------- saving event traces -----------

                  $paymentTrx->save();

                  $totalAllocated = 0;
                  foreach ($paymentItems as $index => $item) {
                     if ($index === $paymentItems->count() - 1) {
                        $itemFee = $stripeFee - $totalAllocated;
                     } else {
                        $itemFee = ($totalAmount > 0) ? (int)round(($stripeFee * $item->ss_amount) / $totalAmount) : 0;
                        $totalAllocated += $itemFee;
                     }
                     $item->stripe_fee_share = $itemFee;
                     $item->save();
                  }
               });
               break;

            case 'charge.failed':
               $charge = $event['data']['object'];
               if (!$charge)
                  break;

               $paymentTrx = PaymentTrx::paymentIntent($charge['payment_intent'])->latest()->first();
               if ($paymentTrx) {
                  $paymentTrx->payment_status = FallbackTrxPaymentStatusEnums::FAILED->value;
                  $paymentTrx->note = "Payment failed. Reason: " . ($charge['failure_message'] ?? 'Unknown payment error');
                  $paymentTrx->save();
               }
               break;
         }
      } catch (Throwable $e) {
         Log::error('Webhook internal execution error', [$e]);
         return response()->json(['error' => 'Webhook handler failed'], 500);
      } finally {
         optional($lock)->release();
      }

      return response()->json(['status' => 'success'], 200);
   }
}