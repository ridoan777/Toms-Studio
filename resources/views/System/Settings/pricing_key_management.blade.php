<x-app-layout>

	@php
		$breadCrumbs = [
			['label' => 'Settings', 'url' => 'backend_settings_index', 'icon' => 'gearLine'],
			['label' => 'AI Keys'],
		];
	 @endphp

	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />

	<!-- Start Main Widgets -->
	<x-indicators.page_header_widget pageName="Settings: Index" />
	<!-- End Main Widgets -->

	<!-- Alert Component -->
	<x-indicators.alert-component message="Form Submission" />
	<!-- Alert Component -->

	@php
		$ACCORDION_1_VISIBILITY = ''; // hidden or ''
		$ACCORDION_2_VISIBILITY = 'hidden';
	@endphp

	<!-- -------------------------------- APP AREA Starts -------------------------------- -->
	<div class="hs-accordion-group">

		<x-ui_items.accordion.accordion-destroy />


		<div id="hs-destroy-heading-one" {{-- Stripe Keys --}}
			class="hs-accordion-to-destroy hs-accordion hs-accordion-active:border-gray-200 PrelineAccordionItemParent">

			<x-ui_items.accordion.accordion_toggle_button toggleLabel="1. Add stripe keys" />

			<div id="hs-destroy-collapse-one"
				class="hs-accordion-content {{ $ACCORDION_1_VISIBILITY }} w-full overflow-hidden transition-[height] duration-300"
				role="region" aria-labelledby="hs-destroy-heading-one">
				<div class="p-3">
					<section
						class="displayArea mx-auto my-5 rounded-lg border-none bg-white p-4 shadow-md shadow-gray-400 dark:bg-neutral-700 dark:shadow-none">
						<div class="my-5">

							<form method="POST" action="{{ route('backend_settings_web_pricing_key_store') }}"
								class="rounded-lg p-2 dark:bg-neutral-700" enctype="multipart/form-data">
								@csrf

								<!--- IDENTIFIER HIDDEN INPUTS -->
								<input type="hidden" name="flag" class="smallInput" value="create">
								<!--- IDENTIFIER HIDDEN INPUTS -->

								<div>

									<div class="mb-5 mt-2">
										<label for="stripe_key" class="inputLabelMedium">Publishable Key/Stripe Key
											<span class="redLabel">*</span>
											<span class="text-sm text-gray-400 font-light"> (Found in Stripe Dashboard > Developers
												> API Keys > Overview > Stripe Key/Publishable Key > pk_...Aq54...)</span>
										</label>
										<input type="text" name="stripe_key" class="mediumInput"
											placeholder="{{ !empty($stripeKeys['STRIPE_KEY']) ? substr($stripeKeys['STRIPE_KEY'], 0, 10) . '**************' . substr($stripeKeys['STRIPE_KEY'], -6) : ""}}">
									</div>

									<!--  -->

									<div class="mb-5 mt-2">
										<label for="secret_key" class="inputLabelMedium">Stripe Secret Key
											<span class="redLabel">*</span>
											<span class="text-sm text-gray-400 font-light"> (Found in Stripe Dashboard > Developers
												> API Keys > Overview > Secret Key > sk_...Aq54...)</span>
										</label>
										<input type="text" name="secret_key" class="mediumInput"
											placeholder="{{ !empty($stripeKeys['STRIPE_SECRET']) ? substr($stripeKeys['STRIPE_SECRET'], 0, 10) . '**************' . substr($stripeKeys['STRIPE_SECRET'], -6) : ""}}">
									</div>

									<!--  -->

									<div class="mb-5 mt-2">
										<label for="webhook_secret" class="inputLabelMedium">Webhook Secret Key
											<span class="redLabel">*</span>
											<span class="text-sm text-gray-400 font-light"> (Found in Stripe Dashboard > Developers
												> Webhooks > Add Destination > 'Setup Your events & domain' > Webhook Secret Key >
												whsec_...Aq54...)</span>
										</label>
										<input type="text" name="webhook_secret" class="mediumInput"
											placeholder="{{ !empty($stripeKeys['STRIPE_WEBHOOK_SECRET']) ? substr($stripeKeys['STRIPE_WEBHOOK_SECRET'], 0, 10) . '**************' . substr($stripeKeys['STRIPE_WEBHOOK_SECRET'], -6) : ""}}">
									</div>

									<!--  -->

									<div class="mb-5 mt-2">
										<label for="webhook_secret" class="inputLabelMedium">Currency
											<span class="redLabel">*</span>
											<span class="text-sm text-gray-400 font-light"> (Choose a currency. Leave it blank to
												use USD by default)</span>
										</label>
										<select name="stripe_currency" id="stripe_currency" class="dropdownSelectInputs">
											@foreach($currencies as $currency)
												<option value="{{ $currency->value }}" {{ ($stripeKeys['STRIPE_CURRENCY'] ?? 'USD') === $currency->value ? 'selected' : '' }}>
													{{ $currency->label() }}
												</option>
											@endforeach
										</select>
									</div>

									<!--  -->

									<div class="mb-5 mt-2">
										<label for="clear_keys" class="inputLabelMedium">Clear key values</label>
										<input type="checkbox" name="clear_keys" id="clear_keys" value="1">
										<label for="">
											<span class="text-sm text-gray-400 font-light"> (Once saved, leaving a key field blank
												will not remove the old key. Check this to erase all keys.)</span>
										</label>
									</div>
								</div>

								<button type="submit" class="saveButton my-4">Save</button>
							</form>

						</div>
					</section>
				</div>
			</div>
		</div>
		<!--  -->

		<div id="hs-destroy-heading-two" {{-- RevenueCat Keys --}}
			class="hidden hs-accordion-to-destroy hs-accordion hs-accordion-active:border-gray-200 PrelineAccordionItemParent">

			<x-ui_items.accordion.accordion_toggle_button toggleLabel="2. Add RevenueCat keys" />

			<div id="hs-destroy-collapse-two"
				class="hs-accordion-content {{ $ACCORDION_2_VISIBILITY }} w-full overflow-hidden transition-[height] duration-300"
				role="region" aria-labelledby="hs-destroy-heading-two">
				<div class="p-3">
					<section
						class="displayArea mx-auto my-5 rounded-lg border-none bg-white p-4 shadow-md shadow-gray-400 dark:bg-neutral-700 dark:shadow-none">
						<div class="my-5">

							<form method="POST" action="{{ route('backend_settings_mobile_pricing_key_store') }}"
								class="rounded-lg p-2 dark:bg-neutral-700" enctype="multipart/form-data">
								@csrf

								<!--- IDENTIFIER HIDDEN INPUTS -->
								<input type="hidden" name="flag" class="smallInput" value="create">
								<!--- IDENTIFIER HIDDEN INPUTS -->

								<div>
									<div class="mb-5 mt-2">
										<label for="stripe_key" class="inputLabelMedium">Public API Key
										</label>
										<input type="text" name="rvc_public_key" class="mediumInput" placeholder="RC Public Key"
											value="{{ $revenueCatKeys['REVENUECAT_PUBLIC_API_KEY'] ?? null }}">
									</div>
									<!--  -->
									<div class="mb-5 mt-2">
										<label for="secret_key" class="inputLabelMedium">Stripe Secret Key
										</label>
										<input type="text" name="rvc_secret_key" class="mediumInput" placeholder="RC Secret Key"
											value="{{ $revenueCatKeys['REVENUECAT_SECRET_KEY'] ?? null }}">
									</div>
									<!--  -->
									<div class="mb-5 mt-2">
										<label for="webhook_secret" class="inputLabelMedium">Auth Token
											<span class="redLabel">*</span>
										</label>
										<input type="text" name="rvc_auth_token" class="mediumInput" placeholder="RC Auth Token"
											value="{{ $revenueCatKeys['REVENUECAT_AUTH_TOKEN'] ?? null }}">
									</div>
								</div>

								<button type="submit" class="saveButton my-4">Save</button>
							</form>

						</div>
					</section>
				</div>
			</div>
		</div>
		<!--  -->

	</div>
	<!-- -------------------------------- APP AREA Starts -------------------------------- -->
</x-app-layout>