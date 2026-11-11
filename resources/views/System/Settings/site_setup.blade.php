<x-app-layout>

	@php
		$breadCrumbs = [
			['label' => 'Settings', 'url' => 'backend_settings_index', 'icon' => 'gearLine'],
			['label' => 'This site'],
		];
	 @endphp

	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />
	<!-- ---------------- APP AREA Starts ---------------- -->

	<!-- Start Main Widgets -->
	<x-indicators.page_header_widget pageName="Settings: Basic Site Management" />
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

			<x-ui_items.accordion.accordion_toggle_button toggleLabel="Site Settings" />

			<div id="hs-destroy-collapse-one"
				class="hs-accordion-content {{ $ACCORDION_1_VISIBILITY }} w-full overflow-hidden transition-[height] duration-300"
				role="region" aria-labelledby="hs-destroy-heading-one">
				<div class="p-3">
					<!-- --------------------------------- -->
					<section
						class="displayArea mx-auto my-5 rounded-lg border-none bg-white p-4 shadow-md shadow-gray-400 dark:bg-neutral-700 dark:shadow-none">
						<div class="my-5">

							<form method="POST" action="{{ route('backend_settings_site_setup_store') }}"
								class="rounded-lg p-2 dark:bg-neutral-700" enctype="multipart/form-data">
								@csrf

								<!-- Site Title -->
								<div class="mb-5 mt-2">
									<label for="site_title" class="inputLabelMedium">
										Site Title <span class="redLabel">*</span>
										<span class="text-sm text-gray-400">
											(changing this rapidly can impact site's visibility to users)
										</span>
									</label>
									<input type="text" name="site_title" id="site_title" class="normalInput"
										value="{{ $siteSetupTitle['site_title'] ?? null }}">
									<x-ui_items.forms.input-error class="mt-2" :messages="$errors->get('site_title')" />
								</div>
								<!-- -->

								<div class="my-5">
									<label for="site_logo" class="inputLabelMedium">Site Logo
										<span class="text-sm text-gray-400">
											(Keep logo under 2MB for smoother site loading. Best case is under 200KB)
											(This logo is for admin dashboard+internal use only. This does not change the app
											icon/logo)
										</span>
									</label>
									<input type="file" id="site_logo" name="site_logo" class="dropify dropifySiteLogo"
										data-height="150" @if (!empty($siteSetupTitle['site_logo']))
										data-default-file="{{ asset($siteSetupTitle['site_logo']) }}" @endif />

									<x-ui_items.forms.input-error class="mt-2" :messages="$errors->get('site_logo')" />
									<input type="hidden" name="remove_image" id="remove_image" value="0">
								</div>
								<!-- -->
								<div class="mb-5 mt-2">
									@php
										$size = [
											"scale-105" => "120%",
											"w-full" => "Auto/Full",
											"w-3/4" => "75%",
											"w-2/3" => "66%",
											"w-1/2" => "50%",
											"w-1/3" => "33%",
											"w-1/4" => "25%",
										]
									@endphp
									<label for="logo_size" class="inputLabelMedium">Logo Width
										<span class="text-sm text-gray-400">
											(This only affects the admin dashboard, not frontend app)
										</span>
									</label>
									<select id="logo_size" name="logo_size" class="dropdownSelectInputs">
										@foreach ($size as $key => $value)
											<option value="{{ $key }}" {{ ($siteSetupTitle['logo_size'] ?? null) == $key ? 'selected' : '' }}>
												{{ $value }}
											</option>
										@endforeach
									</select>
									<x-ui_items.forms.input-error class="mt-2" :messages="$errors->get('site_title')" />
								</div>
								<!-- -->

								<!-- Site Favicon -->
								<div class="my-5">
									<label for="favicon" class="inputLabelMedium">Site Icon (Favicon)
										<span class="text-sm text-gray-400"> (Max 128Kb)
											(Keep favicon under 20KB and use trusted sites/tools for conversion.)
										</span>
									</label>

									<input type="file" id="favicon" name="favicon" class="dropify" data-height="150" @if (!empty($siteSetupTitle['favicon']))
									data-default-file="{{ asset($siteSetupTitle['favicon']) }}" @endif />

									<x-ui_items.forms.input-error class="mt-2" :messages="$errors->get('favicon')" />
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
	{{-- Script for tweaking remove_image = 1 --}}
	<script>
		$(document).ready(function () {
			// Initialize site logo dropify with backend remove tracking
			const drLogo = $('.dropifySiteLogo').dropify();

			drLogo.on('dropify.afterClear', function () {
				$('#remove_image').val(1);
			});

			drLogo.on('change', function () {
				$('#remove_image').val(0);
			});

			// Initialize favicon dropify (frontend only)
			$('#favicon').dropify();
		});
	</script>



	<!-- ---------------- APP AREA Starts ---------------- -->



</x-app-layout>