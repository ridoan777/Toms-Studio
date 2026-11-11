<x-app-layout>

	@php
		$breadCrumbs = [
			['label' => 'Settings', 'url' => 'backend_settings_index', 'icon' => 'gearLine'],
			['label' => 'Static Pages'],
		];
	 @endphp

	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />
	<!-- ---------------- APP AREA Starts ---------------- -->

	<!-- Start Main Widgets -->
	<x-indicators.page_header_widget pageName="Settings: Static Pages" />
	<!-- End Main Widgets -->


	<!-- Alert Component -->
	<x-indicators.alert-component message="Form Submission" />
	<!-- Alert Component -->

	@php
		$ACCORDION_1_VISIBILITY = 'hidden'; // hidden or ''
		$ACCORDION_2_VISIBILITY = 'hidden';
		$ACCORDION_3_VISIBILITY = 'hidden';
		$ACCORDION_4_VISIBILITY = 'hidden';
	 @endphp

	@php
		$CARDS = [
			'about' => [
				'title' => "About Us",
				'icon' => 'userTripple',
				'link' => 'public_url_about',
			],
			'terms' => [
				'title' => "Terms & Condition",
				'icon' => 'list',
				'link' => 'public_url_terms',
			],
			'privacy' => [
				'title' => "Privacy Policy",
				'icon' => 'shield',
				'link' => 'public_url_privacy',
			],
			'help' => [
				'title' => "Help & Support",
				'icon' => 'handsHolding',
				'link' => 'public_url_help_support',
			],
		]
	@endphp

	<!---------------------------------------------------------------------------------------------- accordion -->
	<div class="allStatics grid sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
		@foreach ($CARDS as $key => $card)
			<!-- Card -->
			<a class="group flex flex-col bg-white border border-gray-200 shadow-2xs rounded-xl hover:shadow-md focus:outline-hidden focus:shadow-md transition dark:bg-neutral-900 dark:border-neutral-800"
				href="{{ route($card['link']) }}">
				<div class="p-4 md:p-5">
					<div class="flex gap-x-5">
						{!! \App\Helpers\IconPack::{$card['icon']}(['class' => 'iconPackItem mt-1 shrink-0 w-5 h-5 text-gray-500 dark:text-neutral-200']) !!}

						<div class="grow">
							<h3
								class="group-hover:text-blue-600 font-semibold text-gray-800 text-base 2xl:text-lg dark:group-hover:text-neutral-400 dark:text-neutral-200">
								{{ $card['title'] }}
							</h3>
						</div>
					</div>
				</div>
			</a>
			<!-- End Card -->
		@endforeach

	</div>

	<div class="hs-accordion-group">

		<x-ui_items.accordion.accordion-destroy />

		<div id="hs-destroy-heading-one"
			class="hs-accordion-to-destroy hs-accordion hs-accordion-active:border-gray-200 PrelineAccordionItemParent">

			<x-ui_items.accordion.accordion_toggle_button toggleLabel="About Us" />

			<div id="hs-destroy-collapse-one"
				class="hs-accordion-content {{ $ACCORDION_1_VISIBILITY }} w-full overflow-hidden transition-[height] duration-300"
				role="region" aria-labelledby="hs-destroy-heading-one">
				<div class="p-3">
					<!-- --------------------- -->
					<section
						class="displayArea mx-auto my-5 rounded-lg border-none bg-white p-4 shadow-md shadow-gray-400 dark:bg-neutral-700 dark:shadow-none">
						<div class="my-5">

							<form method="POST" action="{{ route('backend_static_page_store') }}"
								class="rounded-lg p-2 dark:bg-neutral-700" enctype="multipart/form-data">
								@csrf

								<!--  -->
								<div class="mb-5 mt-2">
									<input type="hidden" name="id" class="mediumInput" value="{{ $aboutUs?->id ?? null }}"
										readonly>
									<input type="hidden" name="type" class="mediumInput" value="about_us" readonly>
								</div>

								<!--  -->
								<div class="mb-5 mt-2">
									<label for="title" class="inputLabelMedium">Title<span class="redLabel">*</span></label>
									<input type="text" name="title" class="mediumInput" placeholder="60 characters"
										value="{{ $aboutUs?->title ?? null }}">
								</div>

								<!--  -->

								<div class="mb-5 mt-2">
									<label for="body" class="inputLabelMedium">Body<span class="redLabel">*</span></label>
									<textarea class="summernote" name="body">{!! $aboutUs?->body ?? null !!}</textarea>
								</div>

								<!--  -->

								<div class="mb-5 mt-2">
									<label for="quiz_eligible" class="inputLabelMedium">Active Status</label>
									<select id="courses" name="status" class="dropdownSelectInputs">
										<option value="1" {{ $aboutUs?->status == 1 ? 'selected' : '' }}>Active</option>
										<option value="0" {{ $aboutUs?->status == 0 ? 'selected' : '' }}>Inactive</option>
									</select>
								</div>

								<button type="submit" class="saveButton my-4">Submit/Update</button>
							</form>
						</div>
					</section>
					<!-- --------------------------------- -->
				</div>
			</div>
		</div>
		<!--  -->
		<div id="hs-destroy-heading-two"
			class="hs-accordion-to-destroy hs-accordion hs-accordion-active:border-gray-200 PrelineAccordionItemParent">

			<x-ui_items.accordion.accordion_toggle_button toggleLabel="Terms and Conditions" />

			<div id="hs-destroy-collapse-two"
				class="hs-accordion-content {{ $ACCORDION_2_VISIBILITY }} w-full overflow-hidden transition-[height] duration-300"
				role="region" aria-labelledby="hs-destroy-heading-two">
				<div class="p-3">
					<!-- --------------------- -->
					<section
						class="displayArea mx-auto my-5 rounded-lg border-none bg-white p-4 shadow-md shadow-gray-400 dark:bg-neutral-700 dark:shadow-none">
						<div class="my-5">

							<form method="POST" action="{{ route('backend_static_page_store') }}"
								class="rounded-lg p-2 dark:bg-neutral-700" enctype="multipart/form-data">
								@csrf

								<!--  -->
								<div class="mb-5 mt-2">
									<input type="hidden" name="id" class="mediumInput" value="{{ $terms?->id ?? null }}"
										readonly>
									<input type="hidden" name="type" class="mediumInput" value="term_n_conditions" readonly>
								</div>

								<!--  -->
								<div class="mb-5 mt-2">
									<label for="title" class="inputLabelMedium">Title<span class="redLabel">*</span></label>
									<input type="text" name="title" class="mediumInput" placeholder="60 characters"
										value="{{ $terms?->title ?? null }}">
								</div>

								<!--  -->

								<div class="mb-5 mt-2">
									<label for="body" class="inputLabelMedium">Body<span class="redLabel">*</span></label>
									<textarea class="summernote" name="body">{!! $terms?->body ?? null !!}</textarea>
								</div>

								<!--  -->

								<div class="mb-5 mt-2">
									<label for="quiz_eligible" class="inputLabelMedium">Active Status</label>
									<select id="courses" name="status" class="dropdownSelectInputs">
										<option value="1" {{ $terms?->status == 1 ? 'selected' : '' }}>Active</option>
										<option value="0" {{ $terms?->status == 0 ? 'selected' : '' }}>Inactive</option>
									</select>
								</div>

								<button type="submit" class="saveButton my-4">Submit/Update</button>
							</form>
						</div>
					</section>
					<!-- --------------------------------- -->
				</div>
			</div>
		</div>
		<!--  -->
		<div id="hs-destroy-heading-three"
			class="hs-accordion-to-destroy hs-accordion hs-accordion-active:border-gray-200 PrelineAccordionItemParent">

			<x-ui_items.accordion.accordion_toggle_button toggleLabel="Privacy Policy" />

			<div id="hs-destroy-collapse-three"
				class="hs-accordion-content {{ $ACCORDION_3_VISIBILITY }} w-full overflow-hidden transition-[height] duration-300"
				role="region" aria-labelledby="hs-destroy-heading-three">
				<div class="p-3">
					<!-- --------------------- -->
					<section
						class="displayArea mx-auto my-5 rounded-lg border-none bg-white p-4 shadow-md shadow-gray-400 dark:bg-neutral-700 dark:shadow-none">
						<div class="my-5">

							<form method="POST" action="{{ route('backend_static_page_store') }}"
								class="rounded-lg p-2 dark:bg-neutral-700" enctype="multipart/form-data">
								@csrf

								<!--  -->
								<div class="mb-5 mt-2">
									<input type="hidden" name="id" class="mediumInput" value="{{ $privacyPolicy?->id ?? null }}"
										readonly>
									<input type="hidden" name="type" class="mediumInput" value="privacy_policy" readonly>
								</div>

								<!--  -->
								<div class="mb-5 mt-2">
									<label for="title" class="inputLabelMedium">Title<span class="redLabel">*</span></label>
									<input type="text" name="title" class="mediumInput" placeholder="60 characters"
										value="{{ $privacyPolicy?->title ?? null }}">
								</div>

								<!--  -->

								<div class="mb-5 mt-2">
									<label for="body" class="inputLabelMedium">Body<span class="redLabel">*</span></label>
									<textarea class="summernote" name="body">{!! $privacyPolicy?->body ?? null !!}</textarea>
								</div>

								<!--  -->

								<div class="mb-5 mt-2">
									<label for="quiz_eligible" class="inputLabelMedium">Active Status</label>
									<select id="courses" name="status" class="dropdownSelectInputs">
										<option value="1" {{ $privacyPolicy?->status == 1 ? 'selected' : '' }}>Active</option>
										<option value="0" {{ $privacyPolicy?->status == 0 ? 'selected' : '' }}>Inactive</option>
									</select>
								</div>

								<button type="submit" class="saveButton my-4">Submit/Update</button>
							</form>
						</div>
					</section>
					<!-- --------------------------------- -->
				</div>
			</div>
		</div>
		<!--  -->
		<div id="hs-destroy-heading-four"
			class="hs-accordion-to-destroy hs-accordion hs-accordion-active:border-gray-200 PrelineAccordionItemParent">

			<x-ui_items.accordion.accordion_toggle_button toggleLabel="Help & Support" />

			<div id="hs-destroy-collapse-four"
				class="hs-accordion-content {{ $ACCORDION_4_VISIBILITY }} w-full overflow-hidden transition-[height] duration-300"
				role="region" aria-labelledby="hs-destroy-heading-four">
				<div class="p-3">
					<!-- --------------------- -->
					<section
						class="displayArea mx-auto my-5 rounded-lg border-none bg-white p-4 shadow-md shadow-gray-400 dark:bg-neutral-700 dark:shadow-none">
						<div class="my-5">

							<form method="POST" action="{{ route('backend_static_page_store') }}"
								class="rounded-lg p-2 dark:bg-neutral-700" enctype="multipart/form-data">
								@csrf

								<!--  -->
								<div class="mb-5 mt-2">
									<input type="hidden" name="id" class="mediumInput" value="{{ $helpSupport?->id ?? null }}"
										readonly>
									<input type="hidden" name="type" class="mediumInput" value="help_n_support" readonly>
								</div>

								<!--  -->
								<div class="mb-5 mt-2">
									<label for="title" class="inputLabelMedium">Title<span class="redLabel">*</span></label>
									<input type="text" name="title" class="mediumInput" placeholder="60 characters"
										value="{{ $helpSupport?->title ?? null }}">
								</div>

								<!--  -->

								<div class="mb-5 mt-2">
									<label for="body" class="inputLabelMedium">Body<span class="redLabel">*</span></label>
									<textarea class="summernote" name="body">{!! $helpSupport?->body ?? null !!}</textarea>
								</div>

								<!--  -->

								<div class="mb-5 mt-2">
									<label for="quiz_eligible" class="inputLabelMedium">Active Status</label>
									<select id="courses" name="status" class="dropdownSelectInputs">
										<option value="1" {{ $helpSupport?->status == 1 ? 'selected' : '' }}>Active</option>
										<option value="0" {{ $helpSupport?->status == 0 ? 'selected' : '' }}>Inactive</option>
									</select>
								</div>

								<button type="submit" class="saveButton my-4">Submit/Update</button>
							</form>
						</div>
					</section>
					<!-- --------------------------------- -->
				</div>
			</div>
		</div>
		<!--  -->

	</div>

	<!---------------------------------------------------------------------------------------------- accordion -->

	<!-- ---------------- APP AREA Starts ---------------- -->

</x-app-layout>