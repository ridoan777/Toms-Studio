<x-app-layout>

	@php
		$breadCrumbs = [
			['label' => 'Lesson List', 'url' => 'backend_lesson_index', 'icon' => 'music'],
			['label' => 'Grouped Lesson List', 'url' => 'backend_group_lesson_index', 'icon' => 'music'],
			['label' => 'Create Note'],
			['label' => 'Note List', 'url' => 'backend_lesson_note_index', 'icon' => 'documentLined'],
		];
	 @endphp

	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />

	<!-- Start Main Widgets -->
	<x-indicators.page_header_widget pageName="Create Lesson Note" />
	<!-- End Main Widgets -->


	<!-- Alert Component -->
	<x-indicators.alert-component message="Form Submission" />
	<!-- Alert Component -->
	<!-- ---------------- APP AREA Starts ---------------- -->

	<section class="mb-12 p-6">
		<form method="POST" action="{{ route('backend_lesson_note_store') }}" enctype="multipart/form-data"
			class="rounded-lg p-6 bg-gray-200 shadow-md shadow-gray-400 dark:bg-neutral-700 dark:shadow-none">
			@csrf

			<div>

				<div class="grid grid-cols-2 gap-4">
					<div class="mb-5 mt-2">
						<label class="inputLabelMedium">Lesson</label>
						<select name="lesson_id" id="lesson_id" class="singleSelectWithSearch dropdownSelectInputs"
							data-dropdown-position="absolute">
							<option value="">--Select Lesson--</option>
							@foreach ($lessons as $lesson)
								<option value="{{ $lesson->id }}">
									[{{ $lesson->id }}] {{ $lesson->title }} |
									{{ $lesson?->lessonRelatingBackTo_Intrument?->name ?? "No Instrument" }}
								</option>
							@endforeach
						</select>
					</div>

					<div class="mb-5 mt-2">
						<label class="inputLabelMedium">Group Lesson</label>
						<select name="group_id" id="group_id" class="singleSelectWithSearch dropdownSelectInputs"
							data-dropdown-position="absolute">
							<option value="">--Select Group Lesson--</option>
							@foreach ($lessonGroups as $group)
								<option value="{{ $group->id }}">
									[{{ $group->id }}] {{ $group->title }} |
									{{ $group?->groupRelatingBackTo_Intrument?->name ?? "No Instrument" }}
								</option>
								</option>
							@endforeach
						</select>
					</div>
				</div>

				<div class="grid grid-cols-2 gap-4">

					<div class="mb-5 mt-2 privateLesson hidden">
						<label class="inputLabelMedium">Student <span class="redLabel">*</span></label>
						<select name="student_id[]" class="singleSelectWithSearch dropdownSelectInputs"
							data-dropdown-position="absolute" disabled></select>
					</div>
					<div class="mb-5 mt-2 groupLesson hidden">
						<label class="inputLabelMedium">Student <span class="redLabel">*</span></label>
						<select name="student_id[]" class="dropdownSelectInputs" data-dropdown-position="absolute" multiple
							multiselect-search="true" multiselect-select-all="true" disabled></select>
					</div>

					<div class="mb-5 mt-2">
						<label class="inputLabelMedium">Status</label>
						<select name="status" class="dropdownSelectInputs">
							<option value="1">Active</option>
							<option value="0">Inactive</option>
						</select>
					</div>

				</div>

				<div class="mb-5 mt-2">
					<label class="inputLabelMedium">Title <span class="redLabel">*</span></label>
					<input type="text" name="title" class="normalInput" placeholder="Note title">
				</div>

				<div class="mb-5 mt-2">
					<label for="note" class="inputLabelMedium">Note
						<span class="text-sm text-gray-400 font-light"> (OPTIONAL! Max 2000 characters. Use
							Ctrl+Shift+V for safe pasting)</span>
					</label>
					<textarea class="summernote" name="note" id="note"></textarea>
				</div>

				<div class="mt-12 p-4 border border-gray-400 rounded-lg">
					<h2 class="mb-4">Video Gallery</h2>

					<div id="videoBlocks" class="mb-5 mt-2"></div>

					<template id="videoTemplate">
						<div class="gallery-block grid grid-cols-12 gap-4 p-4 border border-gray-400 rounded-lg mb-4">
							<div class="col-span-11">
								<label class="inputLabelMedium">Video File</label>
								<input type="file" class="normalInput gallery-file" accept=".mp4,.mov,.mkv,.ogg,.qt">
								<input type="hidden" class="gallery-remove" value="0">
							</div>

							<div class="col-span-1 flex items-end">
								<button type="button" class="removeGalleryBtn p-3 hover:bg-red-200 rounded-lg">
									{!! \App\Helpers\IconPack::trash(['class' => 'iconPackItem w-5 h-5 text-red-500']) !!}
								</button>
							</div>
						</div>
					</template>

					<div class="flex justify-end">
						<button type="button" id="addVideoBtn" class="px-4 py-2 text-xs bg-violet-600 text-white rounded">+
							Add Video</button>
					</div>
				</div>

				<div class="mt-12 p-4 border border-gray-400 rounded-lg">
					<h2 class="mb-4">Audio Gallery</h2>

					<div id="audioBlocks" class="mb-5 mt-2"></div>

					<template id="audioTemplate">
						<div class="gallery-block grid grid-cols-12 gap-4 p-4 border border-gray-400 rounded-lg mb-4">
							<div class="col-span-11">
								<label class="inputLabelMedium">Audio File</label>
								<input type="file" class="normalInput gallery-file" accept=".mp3,.wav,.aac,.m4a,.ogg">
								<input type="hidden" class="gallery-remove" value="0">
							</div>

							<div class="col-span-1 flex items-end">
								<button type="button" class="removeGalleryBtn p-3 hover:bg-red-200 rounded-lg">
									{!! \App\Helpers\IconPack::trash(['class' => 'iconPackItem w-5 h-5 text-red-500']) !!}
								</button>
							</div>
						</div>
					</template>

					<div class="flex justify-end">
						<button type="button" id="addAudioBtn" class="px-4 py-2 text-xs bg-violet-600 text-white rounded">+
							Add Audio</button>
					</div>
				</div>

				<div class="mt-12 p-4 border border-gray-400 rounded-lg">
					<h2 class="mb-4">Document Gallery</h2>

					<div id="docBlocks" class="mb-5 mt-2"></div>

					<template id="docTemplate">
						<div class="gallery-block grid grid-cols-12 gap-4 p-4 border border-gray-400 rounded-lg mb-4">
							<div class="col-span-11">
								<label class="inputLabelMedium">Document File</label>
								<input type="file" class="normalInput gallery-file" accept=".pdf,.doc,.docx,.txt,.xls,.xlsx">
								<input type="hidden" class="gallery-remove" value="0">
							</div>

							<div class="col-span-1 flex items-end">
								<button type="button" class="removeGalleryBtn p-3 hover:bg-red-200 rounded-lg">
									{!! \App\Helpers\IconPack::trash(['class' => 'iconPackItem w-5 h-5 text-red-500']) !!}
								</button>
							</div>
						</div>
					</template>

					<div class="flex justify-end">
						<button type="button" id="addDocBtn" class="px-4 py-2 text-xs bg-violet-600 text-white rounded">+ Add
							Document</button>
					</div>
				</div>

			</div>

			<button type="submit" class="my-12 saveButton">Create Note</button>
		</form>
	</section>
	<!-- ---------------- APP AREA Starts ---------------- -->

</x-app-layout>

<!---------- PREVENT LESSON + GROUP BOTH SELECTED ------------>
<script>
	document.addEventListener('DOMContentLoaded', function () {

		const lessonInput = document.getElementById('lesson_id');
		const groupInput = document.getElementById('group_id');

		lessonInput.addEventListener('change', function () {
			if (lessonInput.value) {
				groupInput.value = '';
			}
		});

		groupInput.addEventListener('change', function () {
			if (groupInput.value) {
				lessonInput.value = '';
			}
		});

	});
</script>
<!---------- PREVENT LESSON + GROUP BOTH SELECTED ------------>

<!---------- DROPDOWN STUDENT ------------>
<script>
	const getStudentsUrl = "{{ route('backend_lesson_notes_get_students') }}";

	function reloadSingleSelect(el)
	{
		const wrapper = el.nextElementSibling;
		if (!wrapper || !wrapper.classList.contains('multiselect-dropdown')) return;

		const listWrap = wrapper.querySelector('.multiselect-dropdown-list-wrapper');
		const list = wrapper.querySelector('.multiselect-dropdown-list');

		function renderOptions()
		{
			list.innerHTML = '';
			Array.from(el.options).forEach(o => {
				const item = document.createElement('div');
				item.innerText = o.text;
				if (o.selected) item.classList.add('checked');
				item.addEventListener('click', () => {
					Array.from(el.options).forEach(opt => opt.selected = false);
					o.selected = true;
					renderOptions();
					refreshDisplay();
					listWrap.style.display = 'none';
					el.dispatchEvent(new Event('change'));
				});
				list.appendChild(item);
			});
		}

		function refreshDisplay()
		{
			wrapper.querySelectorAll('span').forEach(s => s.remove());
			const selected = el.selectedOptions[0];
			if (selected) {
				const tag = document.createElement('span');
				tag.className = 'optext';
				tag.innerText = selected.text;
				wrapper.appendChild(tag);
			} else {
				const placeholder = document.createElement('span');
				placeholder.className = 'placeholder';
				placeholder.innerText = 'Select';
				wrapper.appendChild(placeholder);
			}
		}

		renderOptions();
		refreshDisplay();
	}

	function populateStudents($select, students)
	{
		const el = $select[0];
		el.innerHTML = '';
		students.forEach(s => el.add(new Option(s.text, s.id)));
		el.multiple ? el.loadOptions?.() : reloadSingleSelect(el);
	}

	$(document).ready(function () {
		$('#lesson_id').on('change', function () {
			if ($(this).val()) {
				$('.privateLesson').removeClass('hidden');
				$('.groupLesson').addClass('hidden');
				$('#group_id').val('').trigger('change');
				populateStudents($('.groupLesson select'), []);
				$.get(getStudentsUrl, { lesson_id: $(this).val() }, res => populateStudents($('.privateLesson select'), res));
			}
		});
		$('#group_id').on('change', function () {
			if ($(this).val()) {
				$('.groupLesson').removeClass('hidden');
				$('.privateLesson').addClass('hidden');
				$('#lesson_id').val('').trigger('change');
				populateStudents($('.privateLesson select'), []);
				$.get(getStudentsUrl, { group_id: $(this).val() }, res => populateStudents($('.groupLesson select'), res));
			}
		});
		$('.privateLesson select').prop('disabled', false);
		$('.groupLesson select').prop('disabled', false);
	});
</script>
<style>
	.groupLesson .mult-select-tag,
	.groupLesson .multiselect-dropdown {
		width: 100% !important;
	}
</style>
<!---------- DROPDOWN STUDENT ------------>

<!---------- GALLERY BLOCKS ------------>
<script>
	document.addEventListener('DOMContentLoaded', function () {

		function galleryHandler(type, containerId, buttonId, templateId) {
			const container = document.getElementById(containerId);
			const button = document.getElementById(buttonId);
			const template = document.getElementById(templateId);

			function reindex() {
				const blocks = container.querySelectorAll('.gallery-block');

				blocks.forEach((block, index) => {
					block.querySelector('.gallery-file').name = `${type}[${index}][file]`;
					block.querySelector('.gallery-remove').name = `${type}[${index}][remove_file]`;
				});
			}

			function bindRemove(button) {
				button.addEventListener('click', function () {
					button.closest('.gallery-block').remove();
					reindex();
				});
			}

			button.addEventListener('click', function () {
				const clone = template.content.cloneNode(true);
				const block = clone.querySelector('.gallery-block');

				bindRemove(block.querySelector('.removeGalleryBtn'));

				container.appendChild(block);

				reindex();
			});
		}

		galleryHandler('video', 'videoBlocks', 'addVideoBtn', 'videoTemplate');
		galleryHandler('audio', 'audioBlocks', 'addAudioBtn', 'audioTemplate');
		galleryHandler('doc', 'docBlocks', 'addDocBtn', 'docTemplate');

	});
</script>
<!---------- GALLERY BLOCKS ------------>