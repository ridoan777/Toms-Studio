<?php

namespace App\Http\Controllers\Billings\Transactions;

use App\Http\Controllers\Controller;
use App\Helpers\{ApiJsonReturnHelper, Errors\ExceptionHandling};
use App\Helpers\PaymentHelpers\{CentsConversion, SubscriptionAuthCheck};
use App\Models\Billings\Subscriptions\{SubscribedUserWeb, SubscriptionTier};
use Illuminate\Support\Facades\{Auth, DB, Log};
use Illuminate\{Http\Request, Validation\Rule};
use App\Models\Workflows\Projects\Project;
use App\Helpers\Settings\SettingsGatekeeping;
use Stripe\Subscription as StripeSubscription;
use Stripe\Stripe;
use Throwable;

class ApiTransactionsController extends Controller
{
   # Due to proprietary secrecy, code that contained business rules were removed
}
