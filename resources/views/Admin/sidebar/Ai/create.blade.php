<x-app-layout>

	@php
		$breadCrumbs = [
			['label' => 'Create Project', 'icon' => 'layers'],
			['label' => 'Project List', 'url' => 'backend_project_index'],
		];
	 @endphp

	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />
	<!-- ---------------- APP AREA Starts ---------------- -->

	<!-- Start Main Widgets -->
	<x-indicators.page_header_widget pageName="Project: Create" />
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

		<div id="hs-destroy-heading-one" {{-- Module --}}
			class="hs-accordion-to-destroy hs-accordion hs-accordion-active:border-gray-200 PrelineAccordionItemParent">

			@php
				$ORDER_IN_PAGE = 2;
			@endphp

			<x-ui_items.accordion.accordion_toggle_button toggleLabel="2. Create New Project" />

			<div id="hs-destroy-collapse-one"
				class="hs-accordion-content {{ $ACCORDION_1_VISIBILITY }} w-full overflow-hidden transition-[height] duration-300"
				role="region" aria-labelledby="hs-destroy-heading-one">
				<div class="p-3">
					<!-- Form::About-Promo -->
					<section
						class="displayArea mx-auto my-5 rounded-lg border-none bg-white p-4 shadow-md shadow-gray-400 dark:bg-neutral-700 dark:shadow-none">
						<div class="my-5">
							
							<form method="POST" action="{{ route('backend_project_store') }}"
								class="rounded-lg p-2 dark:bg-neutral-700" enctype="multipart/form-data">
								@csrf

								<!--- IDENTIFIER HIDDEN INPUTS -->
								<input type="hidden" name="flag" class="smallInput" value="create">
								<!--- IDENTIFIER HIDDEN INPUTS -->

								<div>

									<div class="mb-5 mt-2">
										<select id="category" name="project_manager_id"
											class="dropdownSelectInputs">
											<option value="" selected>Choose a User</option>
											@foreach ($users as $user)
												<option value="{{ $user->id }}">{{ $user->name }}</option>
											@endforeach
										</select>
									</div>
									<!-- -->

									<div class="mb-5 mt-2">
										<label for="title" class="inputLabelMedium">Project Title
											<span class="redLabel">*</span>
											<span class="text-sm text-gray-400 font-light"> (Be careful of making projects⚠️. Each
												project will create a seprate directory based on the title if you add images)</span>
										</label>
										<input type="text" name="title" class="mediumInput" placeholder="max 255-characters">
									</div>

									<!-- -->

									<div class="mb-5 mt-2">
										<label for="location" class="inputLabelMedium">Location
											<span class="redLabel">*</span>
										</label>
										<input type="text" name="location" class="mediumInput" placeholder="max 255-characters">
									</div>

									<!-- -->

									<div class="grid grid-cols-2 gap-4">
										<div class="mb-5 mt-2">
											<label for="start_date" class="inputLabelMedium">Start Date
												<span class="redLabel">*</span>
											</label>
											<input type="date" name="start_date" class="mediumInput" placeholder="max 255-characters">
										</div>
										<!---->
										<div class="mb-5 mt-2">
											<label for="target_date" class="inputLabelMedium">Target Date
												<span class="redLabel">*</span>
											</label>
											<input type="date" name="target_date" class="mediumInput" placeholder="max 255-characters">
										</div>
									</div>

									<!-- -->

									<div class="mb-5 mt-2">
										<label for="description" class="inputLabelMedium">Project Description
											<span class="text-sm text-gray-400 font-light"> (OPTIONAL! Max 2000 characters. Use Ctrl+Shift+V for safe pasting)</span>
										</label>
										<textarea class="summernote" name="description" id="description"></textarea>
									</div>

									<!--  -->
									<div class="my-5">
										<label for="image" class="inputLabelMedium">Feature Image
											<span class="text-sm text-gray-400 font-light"> (Max 10 MB)</span>
										</label>

										<input type="file" id="image" name="image" class="dropify" data-height="150" />

										<input type="hidden" name="remove_image" id="remove_image" value="0">
									</div>

								</div>

								<button type="submit" class="saveButton my-4">Create Project</button>
							</form>
						</div>
					</section>
					<!-- Form::About-Promo -->
				</div>
			</div>
		</div>

	</div>

	<!---------------------------------------------------------------------------------------------- accordion -->



	<!-- ---------------- APP AREA Starts ---------------- -->
	<script>
		$(document).ready(function () {
			const drLogo = $('.dropify').dropify();

			drLogo.on('dropify.afterClear', function () {
				$('#remove_image').val(1);
			});

			drLogo.on('change', function () {
				$('#remove_image').val(0);
			});
		});
	</script>
</x-app-layout>