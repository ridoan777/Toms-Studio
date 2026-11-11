<?php

namespace App\Http\Controllers\Billings\Payables;

use App\Helpers\{UidGenerator, ApiJsonReturnHelper, Auth\OwnershipAuthCheck};
use App\Http\Controllers\Controller;
use App\Helpers\Errors\ExceptionHandling;
use App\Models\Billings\Payments\PaymentPayable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\{Middleware, HasMiddleware};
use Spatie\Permission\Middleware\{PermissionMiddleware, RoleMiddleware};
use Throwable;

class ApiPayableController extends Controller implements HasMiddleware
{
   use AuthorizesRequests;
   public static function middleware(): array
   {
      return [
         new Middleware('verify_role_key:admin,student', only: ['statusMakeUp']),
         new Middleware(PermissionMiddleware::using('lesson_index'), only: ['index', 'myLessons']),
         new Middleware(PermissionMiddleware::using('lesson_show'), only: ['showSingle', 'showGroup']),
      ];
   }
   // -----------------------------------------------

   public function index(Request $request)
   {
      try {
         $USER = $request->user();

         $payables = $this->payableBase($USER, 0);

         return ApiJsonReturnHelper::handle(true, 200, "All due payables have been fetched successfully!", $payables);
      } catch (Throwable $e) {
         return ExceptionHandling::handle('fetching payables', $e);
      }
   }

   public function cancelled(Request $request)
   {
      try {
         $USER = $request->user();

         $payables = $this->payableBase($USER, 1);

         return ApiJsonReturnHelper::handle(true, 200, "All cancelled payables have been fetched successfully!", $payables);
      } catch (Throwable $e) {
         return ExceptionHandling::handle('fetching payables', $e);
      }
   }

   private function payableBase($USER, int $cancelFlag)
   {
      $total_due_in_cents = PaymentPayable::studentId($USER->id)->whereNull('paid_at')->isCancelled(0)->sum('amount');

      $payables = PaymentPayable::studentId($USER->id)
         ->with([
            'payableRelatingBackTo_Student:id,user_uid,username,name,user_role,guardian,avatar,status',
            'payableRelatingBackTo_GroupMember:id,member_uid,group_id,student_id,fees,status',
            'payableRelatingBackTo_GroupMember.studentRelatingBackTo_User:id,user_uid,username,name,user_role,avatar,status',
            'payable',
         ])->select(['id', 'payable_uid', 'student_id', 'payable_type', 'payable_id', 'group_member_id', 'week_start', 'amount', 'paid_at', 'created_by', 'is_cancelled', 'cancel_req_at', 'cancel_reason', 'cancel_resolve', 'cancelled_at'])
         ->latest('id')->isCancelled($cancelFlag)->paginate(20);

      $payables->transform(function ($item) use ($USER) {
         $item->unsetRelation('payableRelatingBackTo_Student');
         $item->unsetRelation('payableRelatingBackTo_GroupMember');

         $item->payment_for_lesson = $item->payable;
         $item->student_user_id = $USER->id;

         $item->due_date = collect([$item->week_start, $item->created_at])->filter()->max();

         if ($item->payment_for_lesson) {
            $item->payment_for_lesson->makeHidden(['difficulty', 'start_time', 'end_time', 'remarks', 'creator', 'last_modifier', 'updated_at', 'lesson_duration_in_min']);
         }

         $item->unsetRelation('payable');
         return $item;
      });

      return [
         'payable_widget' => [
            'total_due' => round($total_due_in_cents / 100, 2),
            'currency' => config('services.stripe.stripe_currency'),
         ],
         'payable_dues' => $payables
      ];
   }
   // -----------------------------------------------

   public function cancelRequest(Request $request)
   {
      try {
         $validated = $request->validate([
            'payable_id' => 'required|integer|exists:payment_payables,id',
            'is_cancelled' => 'required|boolean|in:1,0',
            'cancel_reason' => 'nullable|string|max:255',
         ]);

         $isCancel = $validated['is_cancelled'] ?? null;
         $isCancelled = $isCancel && (bool)$isCancel === true;

         $USER = $request->user();

         $payable = PaymentPayable::findOrFail((int)$validated['payable_id']);

         OwnershipAuthCheck::ownerVsOwner($payable->student_id, $USER->id);

         if ($payable->paid_at) {
            ExceptionHandling::bailout(409, "This payable due record is already paid!");
         }

         if ($payable->is_cancelled) {
            ExceptionHandling::bailout(409, "Aborting! This payable due record is already cancelled & approaved.");
         }

         $class = $payable->payable;

         $payable->update([
            'cancel_req_at' => $isCancelled ? now() : null,
            'cancel_reason' => $isCancelled ? $validated['cancel_reason'] : null,
            'cancel_resolve' => null
         ]);

         return ApiJsonReturnHelper::handle(true, 200, "Due cancellation request for '{$class->title}' has been placed sucessfully!", $payable);
      } catch (Throwable $e) {
         return ExceptionHandling::handle('requesting due cancellation', $e);
      }
   }
}
