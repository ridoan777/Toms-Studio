<?php

namespace App\Http\Controllers\Billings\Checkouts;

use App\Domains\Billings\Actions\{PaymentTrxCreateAction, LineItemCreateAction, SessionCreateAction};
use App\Domains\Billings\Enums\{CurrencyEnums, FallbackTrxPaymentStatusEnums, ItemPhaseEnums, TrxPhaseEnums};
use App\Helpers\Errors\LoggerAccess;
use App\Http\Controllers\Controller;
use App\Helpers\{ApiJsonReturnHelper, Errors\ExceptionHandling};
use App\Http\Requests\Billings\CheckoutValidationRequest;
use App\Models\Billings\Payments\{PaymentTrx, PaymentPayable, PaymentItem};
use App\Models\Workflows\Lessons\{Lesson};
use Illuminate\Support\Facades\{DB, Log};
use Illuminate\{Http\Request};
use App\Helpers\Settings\SettingsGatekeeping;
use Stripe\Stripe;
use Throwable;

// this is the new one

class StripeOneTimePayCheckoutController extends Controller
{
   public function checkout(CheckoutValidationRequest $request, PaymentTrxCreateAction $trxAction, LineItemCreateAction $lineItemAction, SessionCreateAction $SessionCreateAction)
   {
      # Due to proprietary secrecy, code that contained business rules were removed
   }
   // ------------------------------------------------


   public function success(Request $request)
   {
      # Due to proprietary secrecy, code that contained business rules were removed
   }

   public function cancel(Request $request)
   {
      # Due to proprietary secrecy, code that contained business rules were removed
   }

   // ------------------------------------------------
}
