<x-app-layout>

	@php
		$breadCrumbs = [
			['label' => 'Settings', 'url' => 'backend_settings_index', 'icon' => 'gearLine'],
			['label' => 'Notification'],
		];
	 @endphp

	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />
	<!-- ---------------- APP AREA Starts ---------------- -->

	<!-- Start Main Widgets -->
	<x-indicators.page_header_widget pageName="Settings: Notification Management" />
	<!-- End Main Widgets -->


	<!-- Alert Component -->
	<x-indicators.alert-component message="Form Submission" />
	<!-- Alert Component -->

	@php
		$ACCORDION_1_VISIBILITY = ''; // hidden or ''
	@endphp

	<!---------------------------------------------------------------------------------------------- accordion -->
	<div class="hs-accordion-group">

		<x-ui_items.accordion.accordion-destroy />


		<div id="hs-destroy-heading-one"
			class="hs-accordion-to-destroy hs-accordion hs-accordion-active:border-gray-200 PrelineAccordionItemParent">

			<x-ui_items.accordion.accordion_toggle_button toggleLabel="" />

			<div id="hs-destroy-collapse-one"
				class="hs-accordion-content {{ $ACCORDION_1_VISIBILITY }} w-full overflow-hidden transition-[height] duration-300"
				role="region" aria-labelledby="hs-destroy-heading-one">
				<div class="p-3">
					<!-- --------------------------------- -->
					<section
						class="displayArea mx-auto my-5 rounded-lg border-none bg-white p-4 shadow-md shadow-gray-400 dark:bg-neutral-700 dark:shadow-none">
						<div class="my-5">
							<form method="POST" action="{{ route('backend_settings_notification_store') }}"
								class="rounded-lg p-2 dark:bg-neutral-700" enctype="multipart/form-data">
								@csrf

								<!-- IDENTIFIER INPUTS -->
								<div class="mb-5 mt-2">
								</div>
								<!-- IDENTIFIER INPUTS -->

								<div>

									<div class="mb-5 mt-2">
										@php
											$notifications = [
												[
													"label" => "1. In-App Notifications",
													"name" => "in_app_notification",
													"options" => [
														"No" => 0,
														"Yes" => 1,
													],
												],
												[
													"label" => "2. Email Notifications",
													"name" => "email_notification",
													"options" => [
														"Yes" => 1,
														"No" => 0,
													],
												],
												[
													"label" => "3.1 Push Notifications",
													"name" => "push_notification",
													"options" => [
														"Yes" => 1,
														"No" => 0,
													],
												],
												[
													"label" => "3.2 Push Notification Access",
													"name" => "multi_push_notification",
													"options" => [
														"Multi Device ON (Might Act Slower)" => 1,
														"Multi Device OFF" => 0,
													],
												],
												[
													"label" => "4.1 Logging (Admin)",
													"name" => "admin_log",
													"options" => [
														"Yes" => 1,
														"No" => 0,
													],
												],
												[
													"label" => "4.2 Logging (User)",
													"name" => "user_log",
													"options" => [
														"Yes" => 1,
														"No" => 0,
													],
												],
											];
										@endphp

										@foreach ($notifications as $item)
											<label for="{{ $item['name'] }}" class="mt-5 inputLabelMedium">
												{{ $item['label'] }}
											</label>

											<select name="{{ $item['name'] }}" class="dropdownSelectInputs">
												@foreach ($item['options'] as $text => $val)
													<option value="{{ $val }}" {{ (old($item['name'], $notificationSetup[$item['name']] ?? null) == $val) ? 'selected' : '' }}>
														{{ $text }}
													</option>
												@endforeach
											</select>

											<x-ui_items.forms.input-error class="mt-2" :messages="$errors->get($item['name'])" />
										@endforeach

									</div>
									<!-- -->

								</div>

								<button type="submit" class="saveButton my-4">Save</button>
							</form>
						</div>
					</section>
					<!-- --------------------------------- -->
				</div>
			</div>
		</div>

		<!--  -->

	</div>


	<!------------------------------------------------------------------------------------------------>
	<!-- ---------------- APP AREA Starts ---------------- -->



</x-app-layout>