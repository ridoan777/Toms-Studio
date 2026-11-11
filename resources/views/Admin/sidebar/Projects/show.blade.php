<x-app-layout>

	@php
		$breadCrumbs = [
			['label' => 'Create Project', 'url' => 'backend_project_create', 'icon' => 'layers'],
			['label' => 'Project List', 'url' => 'backend_project_index', 'icon' => 'layers'],
			['label' => 'Single Project'],
		];
	@endphp

	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />
	<!-- ---------------- APP AREA Starts ---------------- -->

	<!-- Start Main Widgets -->
	<x-indicators.page_header_widget pageName="Task/Project: {{ $singleProject?->title ?? null }}" />
	<!-- End Main Widgets -->

	<!-- Alert Component -->
	<x-indicators.alert-component message="Modification" />
	<!-- Alert Component -->

	@php
		$ACCORDION_1_VISIBILITY = ''; // hidden or ''
	@endphp

	<!---------------------------------------------------------------------------------------------- accordion -->
	<div class="hs-accordion-group">

		<x-ui_items.accordion.accordion-destroy />

		<div id="hs-destroy-heading-one"
			class="hs-accordion-to-destroy hs-accordion hs-accordion-active:border-gray-200 PrelineAccordionItemParent">

			<x-ui_items.accordion.accordion_toggle_button toggleLabel="Modify this project" />

			<div id="hs-destroy-collapse-one"
				class="hs-accordion-content {{ $ACCORDION_1_VISIBILITY }} w-full overflow-hidden transition-[height] duration-300"
				role="region" aria-labelledby="hs-destroy-heading-one">
				<div class="p-3">
					<!-- --------------------- -->
					<section
						class="displayArea mx-auto my-5 rounded-lg border-none bg-white p-4 shadow-md shadow-gray-400 dark:bg-neutral-700 dark:shadow-none">
						<div class="my-5">
							<form method="POST" action="{{ route('backend_project_store') }}"
								class="rounded-lg p-2 dark:bg-neutral-700" enctype="multipart/form-data">
								@csrf

								<!--- IDENTIFIER HIDDEN INPUTS -->
								<input type="hidden" name="flag" class="smallInput" value="create">
								<input type="hidden" name="id" class="smallInput" value="{{ $singleProject?->id }}">
								<!--- IDENTIFIER HIDDEN INPUTS -->

								<div>

									<div class="mb-5 mt-2">
										<label for="title" class="inputLabelMedium">Project UID</label>
										<input type="text" class="normalInput" value="{{ $singleProject?->project_uid ?? null }}"
											readonly>
									</div>

									<!--  -->

									<div class="mb-5 mt-2">
										<label for="quiz_eligible" class="inputLabelMedium">Phase
											<span class="redLabel">*</span>
										</label>
										@php
											$phases = ['on-track', 'completed', 'cancelled'];
										@endphp
										<select id="phase" name="phase" class="dropdownSelectInputs">
											<option value="on-track" {{ ($singleProject?->phase === "on-track") ? 'selected' : '' }}>
												On-Track/Running
											</option>
											<option value="completed" {{ ($singleProject?->phase == "completed") ? 'selected' : '' }}>Completed</option>
											<option value="disputed" {{ ($singleProject?->phase == "disputed") ? 'selected' : '' }}>
												Disputed</option>
											<option value="cancelled" {{ ($singleProject?->phase == "cancelled") ? 'selected' : '' }}>Cancelled</option>
										</select>
									</div>

									<!-- -->
									<div class="mb-5 mt-2">
										<label for="quiz_eligible" class="inputLabelMedium">Assign a manager
											<span class="redLabel">*</span>
										</label>
										<select id="category" name="project_manager_id" class="dropdownSelectInputs">
											<option value="" selected>Choose a user</option>
											@foreach ($users as $user)
												<option value="{{ $user->id }}" {{ ($singleProject?->project_manager_id == $user->id) ? 'selected' : '' }}>
													{{ $user->name }}
												</option>
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
										<input type="text" name="title" class="mediumInput"
											value="{{ $singleProject?->title ?? null }}">
									</div>

									<!-- -->

									<div class="mb-5 mt-2">
										<label for="location" class="inputLabelMedium">Location
											<span class="redLabel">*</span>
										</label>
										<input type="text" name="location" class="mediumInput"
											value="{{ $singleProject?->location ?? null }}">
									</div>

									<!-- -->

									<div class="grid grid-cols-2 gap-4">
										<div class="mb-5 mt-2">
											<label for="start_date" class="inputLabelMedium">Start Date
												<span class="redLabel">*</span>
											</label>
											<input type="date" name="start_date" class="mediumInput"
												value="{{ $singleProject?->start_date ?? null }}">
										</div>
										<!---->
										<div class="mb-5 mt-2">
											<label for="target_date" class="inputLabelMedium">Target Date
												<span class="redLabel">*</span>
											</label>
											<input type="date" name="target_date" class="mediumInput"
												value="{{ $singleProject?->target_date ?? null }}">
										</div>
									</div>

									<!-- -->

									<div class="mb-5 mt-2">
										<label for="description" class="inputLabelMedium">Project Description
											<span class="text-sm text-gray-400 font-light"> (OPTIONAL! Max 2000 characters. Use
												Ctrl+Shift+V for safe pasting)</span>
										</label>
										<textarea class="summernote"
											name="description">{!! $singleProject?->description ?? null !!}</textarea>
									</div>

									<!--  -->

									<div class="my-5">
										<label for="image" class="inputLabelMedium">Feature Image
											<span class="text-sm text-gray-400 font-light"> (Max 10 MB)</span>
										</label>

										<input type="file" id="imageDropify" name="image" class="dropify" data-height="150" @if ($singleProject?->image) data-default-file="{{ Storage::url($singleProject->image) }}"
										@endif />

										<input type="hidden" name="remove_image" id="remove_image" value="0">
									</div>

									<!--  -->

									<!-------- Single Video ------->
									<div class="my-5">
										<label for="video" class="inputLabelMedium">Single Video<span class="grayLabel"> (Max 1024
												MB)</span></label>
										@if ($singleProject?->video)
										<video controls class="max-w-full max-h-[420px] mx-auto mt-3">
											<source src="{{ Storage::url($singleProject->video) }}" type="video/mp4">
											Your browser does not support the video tag.
										</video>
										@endif
										<input type="file" name="video" class="filepond" data-height="150" />

										<div class="flex justify-center">
											<label class="inline-flex items-center gap-2 cursor-pointer">
												<span class="grayLabel">Remove This Video</span>

												<input type="hidden" name="remove_video" value="0">
												<input type="checkbox" name="remove_video" value="1" class="hidden peer">

												{!! \App\Helpers\IconPack::trash([
												'class' => 'iconPackItem w-5 h-5 text-gray-400 transition peer-checked:text-red-600'
												]) !!}
											</label>
										</div>
									</div>
									<!-------- Multi-Videos ------->
									<div>
										<label for="video" class="inputLabelMedium">
											Video Gallery<span class="grayLabel"> (Max 1024 MB)</span>
										</label>
										@if (!empty($videoGallery))
										<div class="my-4 grid grid-cols-2 gap-4 items-stretch">
											@foreach ($videoGallery as $index => $item)

											<div class="flex flex-col justify-between h-full">
												<video controls class="border w-full mt-3">
													<source src="{{ Storage::url($item->file) }}" type="video/mp4">
												</video>
												<p class="py-2 appText">[ID:{{ $item?->id }}] [{{ $item?->file ?? null }}]</p>
												<input type="hidden" name="edit_video_gallery[{{ $index }}][id]"
													value="{{ $item->id }}" />
												<input type="file" name="edit_video_gallery[{{ $index }}][replacement]"
													class="filepond" data-height="150" />

												<label class="flex items-center gap-2 mt-2 cursor-pointer">
													<span class="text-sm text-gray-500">Remove</span>

													<input type="hidden" name="edit_video_gallery[{{ $index }}][remove]" value="0">
													<input type="checkbox" name="edit_video_gallery[{{ $index }}][remove]" value="1"
														class="hidden peer">

													{!! \App\Helpers\IconPack::trash(['class' => 'iconPackItem w-5 h-5 text-gray-400
													transition peer-checked:text-red-600 peer-checked:scale-110']) !!}
												</label>
											</div>

											@endforeach
										</div>
										@endif
										<input type="file" name="video_gallery[]" class="filepond" data-height="150" multiple />
										<!--------------->

										<div id="filepond-error"></div>
									</div>

									<!--  -->

									<div class="mb-5 mt-2">
										<label for="quiz_eligible" class="inputLabelMedium">Active Status
											<span class="redLabel">*</span>
										</label>
										<select id="status" name="status" class="dropdownSelectInputs">
											<option value="0" {{ ($singleProject?->status == 0) ? 'selected' : '' }}>Inactive
											</option>
											<option value="1" {{ ($singleProject?->status == 1) ? 'selected' : '' }}>Active</option>
										</select>
									</div>

								</div>

								<button type="submit" id="submitButton" class="showButton my-4">Update Project</button>
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

<script>
	$(document).on('submit', '.toggleForm', function (e) {
		e.preventDefault();
		const form = $(this);
		$.ajax({
			url: form.attr('action'),
			type: 'POST',
			data: form.serialize(),
			success: function () { location.reload(); },
			error: function () { alert('Error toggling status.'); }
		});
	});

	document.addEventListener('DOMContentLoaded', () => {
		document.querySelectorAll('.delivery-card').forEach(card => {
			card.addEventListener('click', () => {
				window.location.href = card.dataset.url;
			});
		});
	});

</script>