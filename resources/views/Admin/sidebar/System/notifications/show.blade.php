<x-app-layout>

	@php
		$breadCrumbs = [
			['label' => 'Workout', 'url' => 'backend_project_create', 'icon' => 'cubic'],
			['label' => 'All Categories', 'url' => 'dashboard', 'icon' => 'layers'],
			['label' => 'Create Plans', 'url' => 'backend_project_create', 'icon' => 'handsHolding'],
			['label' => 'All Plans', 'url' => 'backend_project_index', 'icon' => 'documentLined'],
			['label' => 'Create Exercise', 'url' => 'backend_project_exercise_create', 'icon' => 'dumbbell'],
			['label' => 'All Exercise', 'url' => 'backend_project_exercise_index', 'icon' => 'dumbbell'],
			['label' => 'Exercise'],
		];
	 @endphp

	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />
	<!-- ---------------- APP AREA Starts ---------------- -->

	<!-- Start Main Widgets -->
	<x-indicators.page_header_widget pageName="Plans: Modify An Exercise" />
	<!-- End Main Widgets -->


	<!-- Alert Component -->
	<x-indicators.alert-component message="Category Modification" />
	<!-- Alert Component -->

	@php
		$ACCORDION_1_VISIBILITY = ''; // hidden or ''
		$ACCORDION_2_VISIBILITY = '';
	 @endphp

	<!---------------------------------------------------------------------------------------------- accordion -->
	<div class="hs-accordion-group">

		<x-ui_items.accordion.accordion-destroy />

		<div id="hs-destroy-heading-one"
			class="hs-accordion-to-destroy hs-accordion hs-accordion-active:border-gray-200 PrelineAccordionItemParent">

			<x-ui_items.accordion.accordion_toggle_button toggleLabel="Modify Exercise/Class" />

			<div id="hs-destroy-collapse-one"
				class="hs-accordion-content {{ $ACCORDION_1_VISIBILITY }} w-full overflow-hidden transition-[height] duration-300"
				role="region" aria-labelledby="hs-destroy-heading-one">
				<div class="p-3">
					<!-- --------------------- -->
					<section
						class="displayArea mx-auto my-5 rounded-lg border-none bg-white p-4 shadow-md shadow-gray-400 dark:bg-neutral-700 dark:shadow-none">
						<div class="my-5">
							<form method="POST" action="{{ route('backend_project_exercise_store') }}"
								class="rounded-lg p-2 dark:bg-neutral-700" enctype="multipart/form-data">
								@csrf

								<!--- IDENTIFIER HIDDEN INPUTS -->
								<input type="hidden" name="id" class="smallInput" value="{{ $exercise?->id }}" readonly>
								<!--- IDENTIFIER HIDDEN INPUTS -->

								<div>

									<div class="mb-5 mt-2">
										<label for="title" class="inputLabelMedium">Select a plan
											<span class="redLabel">*</span>
										</label>
										<select id="plan" name="plan_id"
											class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
											<option value="" selected>Choose a Plan</option>
											@foreach ($workoutPlans as $plan)
												<option value="{{ $plan->id }}" {{ $plan?->id == $exercise->plan_id ? 'selected' : '' }}>
													{{ $plan->title }}
												</option>
											@endforeach
										</select>
									</div>
									<!-- -->
									<div class="mb-5 mt-2">
										<label for="title" class="inputLabelMedium">Exercise/Class Title
											<span class="redLabel">*</span>
										</label>
										<input type="text" name="title" class="mediumInput"
											value="{{ $exercise?->title ?? null }}">
									</div>
									<!--  -->
									<div class="mb-5 mt-2">
										<label for="description" class="inputLabelMedium">Description
											<span class="text-sm text-gray-400 font-light"> (Max 2000 characters. Use
												Ctrl+Shift+V for safe pasting)</span>
										</label>
										<textarea class="summernote" name="description" id="description">
											{{ $exercise?->description ?? null }}
										</textarea>
									</div>
									<!--  -->
									<div class="mb-5 mt-2">
										<label for="instruction" class="inputLabelMedium">Instruction
											<span class="text-sm text-gray-400 font-light"> (Max 2000 characters. Use
												Ctrl+Shift+V for safe pasting)</span>
										</label>
										<textarea class="summernote" name="instruction" id="instruction">
											{!! $exercise?->instruction ?? null !!}
										</textarea>
									</div>

									<!--  -->

									<div class="mb-5 mt-2">
										<label for="calorie_burn" class="inputLabelMedium">How much calorie this
											exercise may burn
											<span class="text-gray-400 font-light"> (example: 320 kcal, etc.)</span>
										</label>
										<input type="text" name="calorie_burn" class="normalInput"
											value="{!! $exercise?->calorie_burn ?? null !!}">
									</div>

									<!-- -->

									<div class="mb-5 mt-2">
										<label for="duration" class="inputLabelMedium">How long this exercise may take
											<span class="text-gray-400 font-light"> (example: 25 minutes, 30 seconds,
												etc.)</span>
										</label>
										<input type="text" name="duration" class="normalInput"
											value="{{ $exercise?->duration ?? null }}">
									</div>

									<!-- -->

									<div class="mb-5 mt-2">
										<label for="reps" class="inputLabelMedium">Number of reps
											<span class="text-gray-400 font-light"> (example: 3, 4, etc. Supports any
												strings.)</span>
										</label>
										<input type="text" name="reps" class="normalInput" value="{{ $exercise?->reps ?? null }}">
									</div>

									<!-- -->

									<div class="mb-5 mt-2">
										<label for="sets" class="inputLabelMedium">Number of sets
											<span class="text-gray-400 font-light"> (example: 3, 4, etc. Supports any
												strings.)</span>
										</label>
										<input type="text" name="sets" class="normalInput" value="{{ $exercise?->sets ?? null }}">
									</div>

									<!-- --------- -->

									<div class="my-5">
										<label for="video" class="inputLabelMedium">Video
											<span class="text-sm text-gray-400 font-light"> (Max 10 MB)</span>
										</label>

										<input type="file" id="videoDropify" name="video" class="dropify" data-height="150" @if ($exercise?->video) data-default-file="{{ Storage::url($exercise->video) }}" @endif />

										<input type="hidden" name="remove_video" id="remove_video" value="0">
									</div>

									<!-- -->

									<div class="my-5">
										<label for="thumbnail" class="inputLabelMedium">Thumbnail
											<span class="text-sm text-gray-400 font-light"> (Max 10 MB)</span>
										</label>

										<input type="file" id="imageDropify" name="thumbnail" class="dropify" data-height="150" @if ($exercise?->thumbnail) data-default-file="{{ Storage::url($exercise->thumbnail) }}"
										@endif />

										<input type="hidden" name="remove_image" id="remove_image" value="0">
									</div>

									<!--  -->

									<div class="mb-5 mt-2">
										<label for="quiz_eligible" class="inputLabelMedium">Active Status</label>
										<select id="courses" name="status" class="dropdownSelectInputs">
											<option value="1" {{ $exercise?->status == 1 ? 'selected' : '' }}>
												Active</option>
											<option value="0" {{ $exercise?->status == 0 ? 'selected' : '' }}>
												Inactive</option>
										</select>
									</div>

								</div>

								<button type="submit" class="showButton my-4">Update</button>
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

			<x-ui_items.accordion.accordion_toggle_button toggleLabel="Display video" />

			<div id="hs-destroy-collapse-two"
				class="hs-accordion-content {{ $ACCORDION_2_VISIBILITY }} w-full overflow-hidden transition-[height] duration-300"
				role="region" aria-labelledby="hs-destroy-heading-two">
				<div class="p-3">
					<!-- --------------------- -->
					<section
						class="displayArea mx-auto my-5 rounded-lg border-none bg-white p-4 shadow-md shadow-gray-400 dark:bg-neutral-700 dark:shadow-none">
						<div class="w-full h-auto my-5">

							@if (isset($exercise) && $exercise?->video)
								@php
									try {
										// Check if file exists in storage (works for local and cloud)
										$videoExists = Storage::disk(config('filesystems.default'))->exists($exercise->video);
									} catch (\Exception $e) {
										$videoExists = false;
									}
								@endphp

								@if ($videoExists)
									<div class="w-full rounded-lg border border-gray-300 shadow-sm">
										<video class="w-full h-auto object-cover" controls preload="metadata">
											<source src="{{ Storage::url($exercise->video) }}" type="video/mp4">
											Your browser does not support the video tag.
										</video>
									</div>
								@else
									<div class="p-2 border text-center">
										<span class="text-xs text-red-500">File corrupted or failed to access the storage! Try
											reuploading, check format or try with a different file.</span>
									</div>
								@endif
							@else
								<div class="p-2 border text-center">
									<span class="text-xs text-orange-400">No playable video file available!</span>
								</div>
							@endif

						</div>

					</section>
					<!-- --------------------------------- -->
				</div>
			</div>
		</div>
		<!--  -->

	</div>

	<!---------------------------------------------------------------------------------------------- accordion -->


	{{-- Script for tweaking remove_image = 1 --}}
	<script>
		$(document).ready(function () {
			const drEvent = $('#imageDropify').dropify();

			drEvent.on('dropify.afterClear', function (event, element) {
				$('#remove_image').val(1);
			});

			drEvent.on('change', function () {
				$('#remove_image').val(0);
			});
		});
		$(document).ready(function () {
			const drEvent = $('#videoDropify').dropify();

			drEvent.on('dropify.afterClear', function (event, element) {
				$('#remove_video').val(1);
			});

			drEvent.on('change', function () {
				$('#remove_video').val(0);
			});
		});
	</script>
	{{-- Script for tweaking remove_image = 1 --}}

	<!-- ---------------- APP AREA Starts ---------------- -->

</x-app-layout>