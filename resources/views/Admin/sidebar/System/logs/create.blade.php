<x-app-layout>

	@php
		$breadCrumbs = [
			['label' => 'Workout', 'url' => 'backend_project_create', 'icon' => 'dumbbell'],
			['label' => 'All Categories', 'url' => 'dashboard', 'icon' => 'layers'],
			['label' => 'Create Plan', 'url' => 'backend_project_create', 'icon' => 'handsHolding'],
			['label' => 'All Plans', 'url' => 'backend_project_index', 'icon' => 'documentLined'],
			['label' => 'Create Exerceise/Class'],
		];
	 @endphp

	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />
	<!-- ---------------- APP AREA Starts ---------------- -->

	<!-- Start Main Widgets -->
	<x-indicators.page_header_widget pageName="Workout: Create Plans" />
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

			<x-ui_items.accordion.accordion_toggle_button toggleLabel="2. Create New Plan" />

			<div id="hs-destroy-collapse-one"
				class="hs-accordion-content {{ $ACCORDION_1_VISIBILITY }} w-full overflow-hidden transition-[height] duration-300"
				role="region" aria-labelledby="hs-destroy-heading-one">
				<div class="p-3">
					<!-- Form::About-Promo -->
					<section
						class="displayArea mx-auto my-5 rounded-lg border-none bg-white p-4 shadow-md shadow-gray-400 dark:bg-neutral-700 dark:shadow-none">
						<div class="my-5">
							<form method="POST" action="{{ route('backend_project_exercise_store') }}"
								class="rounded-lg p-2 dark:bg-neutral-700" enctype="multipart/form-data">
								@csrf

								<!--- IDENTIFIER HIDDEN INPUTS -->
								<input type="hidden" name="flag" class="smallInput" value="create">
								<!--- IDENTIFIER HIDDEN INPUTS -->

								<div>

									<div class="mb-5 mt-2">
										<select id="plan" name="plan_id"
											class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
											<option value="" selected>Choose a Plan</option>
											@foreach ($workoutPlans as $index => $plan)
												<option value="{{ $plan->id }}">{{ $index + 1 }}.
													{{ $plan->title }} |
													Category:{{ $plan->projectRelatingBackTo_Category->name ?? 'No Plan. Create one first' }}
												</option>
											@endforeach
										</select>
									</div>
									<!-- -->
									<div class="mb-5 mt-2">
										<label for="title" class="inputLabelMedium">Exercise/Class Title
											<span class="redLabel">*</span>
										</label>
										<input type="text" name="title" class="mediumInput" placeholder="max 255-characters">
									</div>
									<!--  -->
									<div class="mb-5 mt-2">
										<label for="description" class="inputLabelMedium">Description
											<span class="text-sm text-gray-400 font-light"> (Max 2000 characters. Use
												Ctrl+Shift+V
												for safe pasting)</span>
										</label>
										<textarea class="summernote" name="description" id="description"></textarea>
									</div>
									<!--  -->
									<div class="mb-5 mt-2">
										<label for="instruction" class="inputLabelMedium">Instruction
											<span class="text-sm text-gray-400 font-light"> (Max 2000 characters. Use
												Ctrl+Shift+V
												for safe pasting)</span>
										</label>
										<textarea class="summernote" name="instruction" id="instruction"></textarea>
									</div>

									<!--  -->

									<div class="mb-5 mt-2">
										<label for="calorie_burn" class="inputLabelMedium">How much calorie this
											exercise may burn
											<span class="text-gray-400 font-light"> (example: 320 kcal, etc.)</span>
										</label>
										<input type="text" name="calorie_burn" class="normalInput">
									</div>

									<!-- -->

									<div class="mb-5 mt-2">
										<label for="duration" class="inputLabelMedium">How long this exercise may take
											<span class="text-gray-400 font-light"> (example: 25 minutes, 30 seconds,
												etc.)</span>
										</label>
										<input type="text" name="duration" class="normalInput">
									</div>

									<!-- -->

									<div class="mb-5 mt-2">
										<label for="reps" class="inputLabelMedium">Number of reps
											<span class="text-gray-400 font-light"> (example: 3, 4, etc. Supports any
												strings.)</span>
										</label>
										<input type="text" name="reps" class="normalInput">
									</div>

									<!-- -->

									<div class="mb-5 mt-2">
										<label for="sets" class="inputLabelMedium">Number of sets
											<span class="text-gray-400 font-light"> (example: 3, 4, etc. Supports any
												strings.)</span>
										</label>
										<input type="text" name="sets" class="normalInput">
									</div>

									<!-- --------- -->

									<div class="my-5">
										<label for="video" class="inputLabelMedium">Video
											<span class="text-sm text-gray-400 font-light"> (Max 1GB)</span>
										</label>

										<input type="file" id="video" name="video" class="dropify" data-height="150" />

										<input type="hidden" name="remove_video" id="remove_video" value="0">
									</div>

									<!-- -->

									<div class="my-5">
										<label for="thumbnail" class="inputLabelMedium">Thumbnail Image
											<span class="text-sm text-gray-400 font-light"> (Max 10 MB)</span>
										</label>

										<input type="file" id="thumbnail" name="thumbnail" class="dropify" data-height="150" />

										<input type="hidden" name="remove_image" id="remove_image" value="0">
									</div>

									<!--  -->

									<div class="mb-5 mt-2">
										<label for="status" class="inputLabelMedium">Active Status</label>
										<select name="status" class="dropdownSelectInputs">
											<option value="1">Active</option>
											<option value="0">Inactive</option>
										</select>
									</div>

								</div>

								<button type="submit" class="saveButton my-4">Create Module</button>
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