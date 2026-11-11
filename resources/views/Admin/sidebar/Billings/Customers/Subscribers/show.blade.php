<x-app-layout>

   @php
   $breadCrumbs = [
   ['label' => 'Customers', 'url' => 'backend_billings_customers_index', 'icon' => 'userTrippleLine'],
   ['label' => 'Subscribers', 'url' => 'backend_billings_customers_subscribers', 'icon' => 'userTripple'],
   ['label' => 'Subscriber', 'icon' => 'userSingle']
   ];
   @endphp

   <x-indicators.breadcrumb :crumbs="$breadCrumbs" />

   <!-- Start Main Widgets -->
   <x-indicators.page_header_widget pageName="Subscriber: {{ $subscriber?->subscriberWebRelatingBackTo_User?->name ?? '' }}" />
   <!-- End Main Widgets -->

   <!-- Alert Component -->
   <x-indicators.alert-component message="Modification" />
   <!-- Alert Component -->
   @php
   $userAvatar = null;
   $userAvatar = $subscriber?->subscriberWebRelatingBackTo_User?->avatar ? Storage::url($subscriber?->subscriberWebRelatingBackTo_User?->avatar) : asset('site_assets/dummies/dummy_man.webp');
   $zones = \App\Helpers\Ui\ConvertDynamicTimezone::handle();
   @endphp

   @php
   $ACCORDION_1_VISIBILITY = ''; // hidden or ''
   @endphp
   <!-- ---------------- APP AREA Starts ---------------- -->
   <section class="p-4 bg-gray-300 dark:bg-neutral-600 rounded-lg">
      <div class="flex_between_center">
         <h4>Personal Details</h4>
         <a href="{{ route('backend_single_user', $subscriber?->subscriberWebRelatingBackTo_User?->id) }}">
            {!! \App\Helpers\IconPack::eye(['class' => 'iconPackItem w-5 h-5 text-gray-500']) !!}
         </a>
      </div>
      <div class="flex items-start gap-8">
         <img src="{{ $userAvatar }}" alt="avatar" class="h-48 rounded-lg">
         <div>
            <p><span class="text-base text-gray-500">DB ID: </span> {{ $subscriber?->subscriberWebRelatingBackTo_User?->id ?? "N/A" }}</p>
            <h3><span class="text-base text-gray-500">Name: </span> {{ $subscriber?->subscriberWebRelatingBackTo_User?->name ?? "N/A" }}</h3>
            <h4>
               <span class="text-base text-gray-500">Email: </span>
               {{ $subscriber?->subscriberWebRelatingBackTo_User?->email ?? "N/A" }}</h4>
            <h4>
               <span class="text-base text-gray-500">Phone: </span>
               {{ $subscriber?->subscriberWebRelatingBackTo_User?->phone ?? "N/A" }}</h4>
            <h4>
               <span class="text-base text-gray-500">Role: </span>
               {{ $subscriber?->subscriberWebRelatingBackTo_User?->user_role ?? "N/A" }}</h4>
         </div>
      </div>
   </section>

   <section class="p-4 bg-gray-300 dark:bg-neutral-600 rounded-lg">
      <h4>Payment Summary</h4>
      <div class="wrapperGroup">
         <p class="title">Amount</p>
         <b class="dark:text-gray-100">:</b>
         <p class="value">{{ $subscriber?->amount ?? 0.00 }}</p>
      </div>
      <div class="wrapperGroup">
         <p class="title">Payment Status</p>
         <b class="dark:text-gray-100">:</b>
         <p class="value">{{ ucfirst($subscriber?->payment_status) ?? "N/A" }}</p>
      </div>
      <div class="wrapperGroup">
         <p class="title">First Subscribed</p>
         <b class="dark:text-gray-100">:</b>
         <p class="value">{{ $subscriber?->created_at ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s',
            $subscriber->getRawOriginal('created_at'), 'UTC' )->timezone($zones['zone'])->format('d M Y, H:i') . ' (UTC' . $zones['offset'] . ')' : 'N/A' }}</p>
      </div>
   </section>

   <section class="p-4 bg-gray-300 dark:bg-neutral-600 rounded-lg">
      <div class="flex_between_center">
         <h4>Tier/Package Summary</h4>
         <a href="{{ route('backend_subscription_tier_show', $subscriber?->subscriberWebRelatingBackTo_Tier?->id) }}">
            {!! \App\Helpers\IconPack::eye(['class' => 'iconPackItem w-5 h-5 text-gray-500']) !!}
         </a>
      </div>
      <div class="wrapperGroup">
         <p class="title">Tier Database ID</p>
         <b class="dark:text-gray-100">:</b>
         <p class="value">{{ ucfirst($subscriber?->subscriberWebRelatingBackTo_Tier?->id) ?? "N/A" }}</p>
      </div>
      <div class="wrapperGroup">
         <p class="title">Platform</p>
         <b class="dark:text-gray-100">:</b>
         <p class="value">{{ ucfirst($subscriber?->subscriberWebRelatingBackTo_Tier?->platform) ?? "N/A" }}</p>
      </div>
      <div class="wrapperGroup">
         <p class="title">Tier Name</p>
         <b class="dark:text-gray-100">:</b>
         <p class="value">{{ ucfirst($subscriber?->subscriberWebRelatingBackTo_Tier?->name) ?? "N/A" }}</p>
      </div>
      <div class="wrapperGroup">
         <p class="title">Duration</p>
         <b class="dark:text-gray-100">:</b>
         <p class="value">{{ $subscriber?->subscriberWebRelatingBackTo_Tier?->duration ?? "N/A" }} day(s)</p>
      </div>
      <div class="wrapperGroup">
         <p class="title">Starting Price</p>
         <b class="dark:text-gray-100">:</b>
         <p class="value">${{ ucfirst($subscriber?->subscriberWebRelatingBackTo_Tier?->starting_price) ?? "N/A" }}</p>
      </div>
      <div class="wrapperGroup">
         <p class="title">Final Price</p>
         <b class="dark:text-gray-100">:</b>
         <p class="value">${{ $subscriber?->subscriberWebRelatingBackTo_Tier?->final_price ?? "$0.00" }}</p>
      </div>
      <div class="wrapperGroup">
         <p class="title">Benefits</p>
         <b class="dark:text-gray-100">:</b>
         <span class="value">{!! $subscriber?->subscriberWebRelatingBackTo_Tier?->benefits ?? "N/A" !!}</span>
      </div>
      <div class="wrapperGroup">
         <p class="title">Stripe Product ID</p>
         <b class="dark:text-gray-100">:</b>
         <p class="value">${{ $subscriber?->subscriberWebRelatingBackTo_Tier?->stripe_product_id ?? "N/A" }}</p>
      </div>
      <div class="wrapperGroup">
         <p class="title">Stripe Price ID</p>
         <b class="dark:text-gray-100">:</b>
         <p class="value">${{ $subscriber?->subscriberWebRelatingBackTo_Tier?->stripe_price_id ?? "N/A" }}</p>
      </div>
   </section>

   <section class="p-4 bg-gray-300 dark:bg-neutral-600 rounded-lg">
      <div class="flex_between_center">
         <h4>Service Summary</h4>
         <a href="{{ route('backend_project_show', $subscriber?->subscriberWebRelationWith_Project?->id) }}">
            {!! \App\Helpers\IconPack::eye(['class' => 'iconPackItem w-5 h-5 text-gray-500']) !!}
         </a>
      </div>
      <div class="wrapperGroup">
         <p class="title">Service/project ID</p>
         <b class="dark:text-gray-100">:</b>
         <p class="value">{{ $subscriber?->service_id ?? 'N/A' }}</p>
      </div>
      <div class="wrapperGroup">
         <p class="title">Project Title</p>
         <b class="dark:text-gray-100">:</b>
         <p class="value">{{ ucfirst($subscriber?->subscriberWebRelationWith_Project?->title ?? 'N/A') }}</p>
      </div>
      <div class="wrapperGroup">
         <p class="title">Project Image</p>
         <b class="dark:text-gray-100">:</b>
         <img src="{{ $subscriber?->subscriberWebRelationWith_Project?->image ? Storage::url($subscriber?->subscriberWebRelationWith_Project?->image) : '' }}" alt="No-Image" class="w-96">
      </div>
      <div class="wrapperGroup">
         <p class="title">Location</p>
         <b class="dark:text-gray-100">:</b>
         <p class="value">{{ ucfirst($subscriber?->subscriberWebRelationWith_Project?->location ?? 'N/A') }}</p>
      </div>
   </section>

   <section class="p-4 bg-gray-300 dark:bg-neutral-600 rounded-lg">
      <h4>Subscription Details</h4>
      <div class="wrapperGroup">
         <p class="title">Payment Status</p>
         <b class="dark:text-gray-100">:</b>
         <p class="value">{{ ucfirst($subscriber?->payment_status ?? 'N/A') }}</p>
      </div>
      <div class="wrapperGroup">
         <p class="title">Subscription Status</p>
         <b class="dark:text-gray-100">:</b>
         <p class="value">{{ ucfirst($subscriber?->sub_status ?? 'N/A') }}</p>
      </div>
      <div class="wrapperGroup">
         <p class="title">Amount</p>
         <b class="dark:text-gray-100">:</b>
         <p class="value">${{ number_format($subscriber?->amount ?? 0, 2) }}</p>
      </div>
      <div class="wrapperGroup">
         <p class="title">Duration</p>
         <b class="dark:text-gray-100">:</b>
         <p class="value">{{ $subscriber?->duration ?? 'N/A' }} day(s)</p>
      </div>
      <div class="wrapperGroup">
         <p class="title">Note</p>
         <b class="dark:text-gray-100">:</b>
         <p class="value">{{ $subscriber?->note ?? 'N/A' }}</p>
      </div>
      <div class="wrapperGroup">
         <p class="title">Status</p>
         <b class="dark:text-gray-100">:</b>
         <p class="value">{{ $subscriber?->status ? 'Active' : 'Inactive' }}</p>
      </div>
      <div class="wrapperGroup">
         <p class="title">Invoice ID</p>
         <b class="dark:text-gray-100">:</b>
         <p class="value">{{ $subscriber?->invoice_id ?? 'N/A' }}</p>
      </div>
      <div class="wrapperGroup">
         <p class="title">Invoice Number</p>
         <b class="dark:text-gray-100">:</b>
         <p class="value">{{ $subscriber?->invoice_num ?? 'N/A' }}</p>
      </div>
      <div class="wrapperGroup">
         <p class="title">Next Renewal</p>
         <b class="dark:text-gray-100">:</b>
         <p class="value">{{ $subscriber?->next_renewal_at ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s',
            $subscriber->getRawOriginal('next_renewal_at'), 'UTC' )->timezone($zones['zone'])->format('d M Y, H:i') . ' (UTC' . $zones['offset'] . ')' : 'N/A' }}</p>
      </div>
      <div class="wrapperGroup">
         <p class="title">Renewed At</p>
         <b class="dark:text-gray-100">:</b>
         <p class="value">{{ $subscriber?->renewed_at ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s',
            $subscriber->getRawOriginal('renewed_at'), 'UTC' )->timezone($zones['zone'])->format('d M Y, H:i') . ' (UTC' . $zones['offset'] . ')' : 'N/A' }}</p>
      </div>
      <div class="wrapperGroup">
         <p class="title">Cancelled At</p>
         <b class="dark:text-gray-100">:</b>
         <p class="value">{{ $subscriber?->cancelled_at ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s',
            $subscriber->getRawOriginal('cancelled_at'), 'UTC' )->timezone($zones['zone'])->format('d M Y, H:i') . ' (UTC' . $zones['offset'] . ')' : 'N/A' }}</p>
      </div>
      <div class="wrapperGroup">
         <p class="title">Card Payer/Owner</p>
         <b class="dark:text-gray-100">:</b>
         <p class="value">{{ $subscriber?->payer ?? 'N/A' }}</p>
      </div>
      <div class="wrapperGroup">
         <p class="title">Card</p>
         <b class="dark:text-gray-100">:</b>
         <p class="value">{{ strtoupper($subscriber?->brand ?? 'N/A') }} •••• {{ $subscriber?->last4 ?? 'N/A' }}</p>
      </div>
      <div class="wrapperGroup">
         <p class="title">Card Expiry</p>
         <b class="dark:text-gray-100">:</b>
         <p class="value">{{ $subscriber?->expiry ?? 'N/A' }}</p>
      </div>
      <div class="wrapperGroup">
         <p class="title">Stripe Session</p>
         <b class="dark:text-gray-100">:</b>
         <p class="value">{{ $subscriber?->stripe_checkout_session_id ?? 'N/A' }}</p>
      </div>
      <div class="wrapperGroup">
         <p class="title">Stripe Subscription</p>
         <b class="dark:text-gray-100">:</b>
         <p class="value">{{ $subscriber?->stripe_subscription_id ?? 'N/A' }}</p>
      </div>
      <div class="wrapperGroup">
         <p class="title">Stripe Customer</p>
         <b class="dark:text-gray-100">:</b>
         <p class="value">{{ $subscriber?->stripe_customer_id ?? 'N/A' }}</p>
      </div>

      <div class="wrapperGroup">
         <p class="title">1st Invoice</p>
         <b class="dark:text-gray-100">:</b>
         @if($subscriber?->pdf_1st)
            <a href="{{ $subscriber?->pdf_1st }}" class="value">
               {!! \App\Helpers\IconPack::pdf(['class' => 'iconPackItem w-8 h-8 text-red-500']) !!}
            </a>
         @else
            <p class="value">N/A</p>
         @endif
      </div>
      <div class="wrapperGroup">
         <p class="title">Latest Invoice</p>
         <b class="dark:text-gray-100">:</b>
         @if($subscriber?->pdf_latest)
            <a href="{{ $subscriber?->pdf_latest }}" class="value">
               {!! \App\Helpers\IconPack::pdf(['class' => 'iconPackItem w-8 h-8 text-red-500']) !!}
            </a>
         @else
            <p class="value">N/A</p>
         @endif
      </div>

   </section>
   <!-- ---------------- APP AREA Starts ---------------- -->

</x-app-layout>

<style scoped>
   .title {
      min-width: 144px;
   }

   .dark .title {
      color: #f3f4f6;
   }

   .value {
      width: 100%;
      margin: 4px 0px;
      padding: 0.5rem;
      border-radius: 0.5rem;
      background-color: #f3f4f6;
   }

   .dark .value {
      background-color: #a3a3a3;
   }

   .wrapperGroup {
      display: flex;
      align-items: center;
      gap: 8px;
   }

</style>
