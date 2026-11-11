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
	<x-indicators.page_header_widget pageName="Settings: Email Management" />
	<!-- End Main Widgets -->


	<!-- Alert Component -->
	<x-indicators.alert-component message="Form Submission" />
	<!-- Alert Component -->

	@php
		$ORDER_IN_PAGE = 1;
		$ACCORDION_1_VISIBILITY = 'hidden'; // hidden or ''
		$ACCORDION_2_VISIBILITY = 'hidden';
	@endphp

	@php
		$BOOK_FLAG = [
			'created', 'accepted', 'revoked', 'rejected', 'completed', 'cancelled'
		]
	@endphp

	<!---------------------------------------------------------------------------------------------- accordion -->
	<p class="text-gray-400 text-center">The emails will be sent to both providers & users. Make sure to use neutral tone.</p>
	<div class="hs-accordion-group">

		<x-ui_items.accordion.accordion-destroy />

		@foreach ($BOOK_FLAG as $index=>$item)
		
		<div id="hs-destroy-heading-{{$index}}"
			class="hs-accordion-to-destroy hs-accordion hs-accordion-active:border-gray-200 PrelineAccordionItemParent">

			<x-ui_items.accordion.accordion_toggle_button toggleLabel="Modify '{{$item}}' Email Template" />

			<div id="hs-destroy-collapse-{{$index}}"
				class="hs-accordion-content {{ $ACCORDION_1_VISIBILITY }} w-full overflow-hidden transition-[height] duration-300"
				role="region" aria-labelledby="hs-destroy-heading-{{$index}}">
				<div class="p-3">
					<!-- --------------------------------- -->
					<section
						class="displayArea mx-auto my-5 rounded-lg border-none bg-white p-4 shadow-md shadow-gray-400 dark:bg-neutral-700 dark:shadow-none">
						<div class="my-5">
							<form method="POST" action="{{ route('backend_settings_mail_store') }}"
								class="rounded-lg p-2 dark:bg-neutral-700" enctype="multipart/form-data">
								@csrf

								<!-- IDENTIFIER INPUTS -->
								<div class="mb-5 mt-2">
									<label for="flag" class="inputLabelMedium">Template Flag<span
											class="text-red-500">*</span></label>
									<select id="flag" name="flag" class="dropdownSelectInputs">
										<option value="{{$item}}" selected>{{$item}} Bookings</option>
									</select>
								</div>
								<!-- IDENTIFIER INPUTS -->

								<div>

									<div class="mb-5 mt-2">
										<label for="subject" class="inputLabelMedium">Email Subject
											<span class="redLabel">*</span>
										</label>
										<input type="text" name="subject" class="mediumInput" value="{{ $emailTemp[$index]?->subject ?? null }}">
									</div>

									<!-- -->

									<div class="mb-5 mt-2">
										<label for="greeting" class="inputLabelMedium">Greetings Text</label>
										<textarea class="summernote" name="greeting" id="greeting">{!! $emailTemp[$index]?->greeting ?? null !!}</textarea>
									</div>

									<!--  -->
									<hr class="border-0.5 border-gray-400">
									<p class="py-8 bg-gray-100 dark:bg-neutral-600 text-gray-500 dark:text-gray-300 text-center">"Here, default booking status will be printed."</p>
									<hr class="border-0.5 border-gray-400">
									<!--  -->

									<div class="mb-5 mt-2">
										<label for="body_message" class="inputLabelMedium">Body Message</label>
										<input class="mediumInput" name="body_message" id="body_message" value="{{ $emailTemp[$index]?->body_message ?? null }}"></input>
									</div>

									<!--  -->

									<div class="mb-5 mt-2">
										<label for="end_message" class="inputLabelMedium">End Message</label>
										<input class="mediumInput" name="end_message" value="{{ $emailTemp[$index]?->end_message ?? null }}"></input>
									</div>

									<!--  -->
									<hr class="border-0.5 border-gray-400">
									<p class="py-8 bg-gray-100 dark:bg-neutral-600 text-gray-500 dark:text-gray-300 text-center">"Here, both party(s) contact(s) will be printed."</p>
									<hr class="border-0.5 border-gray-400">
									<!--  -->

									<div class="mb-5 mt-2">
										<label for="support_message" class="inputLabelMedium">Support Message</label>
										<input class="mediumInput" name="support_message" value="{{ $emailTemp[$index]?->support_message ?? null }}"></input>
									</div>

									<!--  -->

									<div class="mb-5 mt-2">
										<label for="support_details" class="inputLabelMedium">Support Details</label>
										<input class="mediumInput" name="support_details" value="{{ $emailTemp[$index]?->support_details ?? null }}"></input>
									</div>

									<!--  -->
								</div>

								<button type="submit" class="saveButton my-4">Save</button>
							</form>
						</div>
					</section>
					<!-- --------------------------------- -->
				</div>
			</div>
		</div>

		@endforeach
		<!--  -->

	</div>


	<!------------------------------------------------------------------------------------------------>
	<!-- ---------------- APP AREA Starts ---------------- -->



</x-app-layout>