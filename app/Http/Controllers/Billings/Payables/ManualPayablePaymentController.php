<?php

namespace App\Http\Controllers\Billings\Payables;

use App\Domains\Billings\Enums\{CurrencyEnums, FallbackTrxPaymentStatusEnums, ItemPhaseEnums, TrxPhaseEnums};
use App\Helpers\UidGenerator;
use App\Http\Controllers\Controller;
use App\Models\Billings\Payments\{PaymentItem, PaymentPayable, PaymentTrx};
use App\Models\Workflows\Lessons\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class ManualPayablePaymentController extends Controller
{
   public function store(Request $request, PaymentPayable $payable)
   {
      # Due to proprietary secrecy, code that contained business rules were removed
   }
}
