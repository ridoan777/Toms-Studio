<x-app-layout>

	@php
		$breadCrumbs = [
			['label' => 'Private', 'url' => 'backend_lesson_create', 'icon' => 'plus'],
			['label' => 'Create Grouped Lesson'],
			['label' => 'Lesson List', 'url' => 'backend_lesson_index', 'icon' => 'music'],
			['label' => 'Grouped Lesson List', 'url' => 'backend_group_lesson_index', 'icon' => 'music'],
		];
	 @endphp

	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />

	<!-- Start Main Widgets -->
	<x-indicators.page_header_widget pageName="Create Grouped Lesson" />
	<!-- End Main Widgets -->


	<!-- Alert Component -->
	<x-indicators.alert-component message="Form Submission" />
	<!-- Alert Component -->
	<!-- ---------------- APP AREA Starts ---------------- -->

	<section class="mb-12">
		<form method="POST" action="{{ route('backend_group_lesson_store') }}" class="rounded-lg p-6 dark:bg-neutral-700">
			@csrf

			<input type="hidden" name="flag" value="create">

			<div>
				<div class="grid grid-cols-2 gap-4">

					<div class="mb-5 mt-2">
						<label class="inputLabelMedium">Teacher <span class="redLabel">*</span></label>
						<select name="teacher_id" class="dropdownSelectInputs">
							<option>--Select Teacher--</option>
							@foreach ($teachers as $teacher)
								<option value="{{ $teacher->id }}">[{{ $teacher?->id }}] {{ $teacher?->name ?? "No Name" }} ||
									{{ $teacher->userRelatingBackTo_Instrument?->name ?? 'No Instrument' }}
								</option>
							@endforeach
						</select>
					</div>

					<div class="mb-5 mt-2">
						<label class="inputLabelMedium">Subject <span class="redLabel">*</span></label>
						<select name="subject_id" class="dropdownSelectInputs">
							<option>--Select Subject--</option>
							@foreach ($instrumentSubjects as $subject)
								<option value="{{ $subject->id }}">[{{ $subject->id }}] {{ $subject->name }}</option>
							@endforeach
						</select>
					</div>

				</div>

				<div class="grid grid-cols-2 gap-4">

					<div class="mb-5 mt-2">
						<label class="inputLabelMedium">Group Name/Title <span class="redLabel">*</span></label>
						<input type="text" name="title" class="normalInput" placeholder="Lesson title">
					</div>

					<div class="mb-5 mt-2">
						<label class="inputLabelMedium">Phase <span class="redLabel">*</span></label>
						<select name="phase" class="dropdownSelectInputs">
							<option>--Select Phase--</option>
							@foreach ($phases as $phase)
								<option value="{{ $phase['value'] }}">{{ $phase['label'] }}</option>
							@endforeach
						</select>
					</div>

				</div>

				<div class="grid grid-cols-2 gap-4">

					<div class="mb-5 mt-2">
						<label class="inputLabelMedium">Schedule Day <span class="redLabel">*</span></label>
						<select name="schedule_day" class="dropdownSelectInputs">
							<option value="">--Select Day--</option>
							@foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
								<option value="{{ $day }}">{{ $day }}</option>
							@endforeach
						</select>
					</div>

					<div class="mb-5 mt-2">
						<label class="inputLabelMedium">Difficulty</label>
						<select name="difficulty" class="dropdownSelectInputs">
							<option value="">--Select Difficulty--</option>
							@foreach ($difficulties as $difficulty)
								<option value="{{ $difficulty['value'] }}">{{ $difficulty['label'] }}</option>
							@endforeach
						</select>
					</div>

				</div>

				<div class="grid grid-cols-12 gap-4">

					<div class="col-span-5 mb-5 mt-2">
						<label class="inputLabelMedium">Start Time <span class="redLabel">*</span></label>
						<input type="time" name="start_time" class="normalInput">
					</div>

					<div class="col-span-5 mb-5 mt-2">
						<label class="inputLabelMedium">End Time <span class="redLabel">*</span></label>
						<input type="time" name="end_time" class="normalInput">
					</div>

					<div class="col-span-2 mb-5 mt-2">
						<label class="inputLabelMedium">Lesson Time <span class="grayLabel">(counted)</span></label>
						<input id="lessonDuration" type="text" class="normalInput" value="" readonly disabled>
					</div>

				</div>

				<div class="mb-5 mt-2">
					<label for="remarks" class="inputLabelMedium">Remarks
						<span class="text-sm text-gray-400 font-light"> (OPTIONAL! Max 2000 characters. Use
							Ctrl+Shift+V for safe pasting)</span>
					</label>
					<textarea class="summernote" name="remarks" id="remarks"></textarea>
				</div>

				<h2>Add Students</h2>
				<div id="studentBlocks" class="mb-5 mt-2">

					<div
						class="student-block grid grid-cols-2 gap-4 p-4 border rounded-lg shadow-lg shadow-gray-400 dark:shadow-neutral-500 mb-4">

						<div class="mb-5 mt-2">
							<label class="inputLabelMedium">Pick a student</label>
							<select name="students[0][id]" class="singleSelectWithSearch dropdownSelectInputs" data-dropdown-position="static">
								<option value="">Select student</option>
								@foreach ($students as $student)
									<option value="{{ $student->id }}">
										[{{ $student->id }}] | {{ $student->name }} | {{ $student->email }}
									</option>
								@endforeach
							</select>
						</div>

						<div class="mb-5 mt-2 flex items-end gap-2">
							<div class="w-full">
								<label class="inputLabelMedium">Fee(s)</label>
								<input type="number" name="students[0][fees]" class="normalInput">
							</div>

							{{-- <button type="button"
								class="removeStudentBtn hidden px-3 py-2 bg-red-500 text-white rounded">−</button> --}}
							<button type="button" class="removeStudentBtn hidden p-3 hover:bg-red-200 rounded-lg">
								{!! \App\Helpers\IconPack::trash(['class' => 'iconPackItem w-5 h-5 text-red-500']) !!}
							</button>
						</div>

					</div>

				</div>

				<div class="mb-5 mt-2 flex justify-end">
					<button type="button" id="addStudentBtn" class="px-4 py-2 text-xs bg-violet-600 text-white rounded">+ Add
						Student</button>
				</div>

			</div>

			<button type="submit" class="my-12 saveButton">Create Lesson</button>
		</form>
	</section>
	<!-- ---------------- APP AREA Starts ---------------- -->

</x-app-layout>

<!---------- CALCULATE LESSON DURATION ------------>
<script>
	document.addEventListener('DOMContentLoaded', function () {

		const startInput = document.querySelector('input[name="start_time"]');
		const endInput = document.querySelector('input[name="end_time"]');
		const durationField = document.getElementById('lessonDuration');

		function updateDuration() {
			const start = startInput.value;
			const end = endInput.value;

			if (!start || !end) {
				durationField.value = '';
				return;
			}

			const [startingHour, startingMinutes] = start.split(':').map(Number);
			const [endingHour, endingMinutes] = end.split(':').map(Number);

			let startMinutes = startingHour * 60 + startingMinutes;
			let endMinutes = endingHour * 60 + endingMinutes;

			// handle cross-midnight
			if (endMinutes < startMinutes) {
				endMinutes += 24 * 60;
			}

			const diff = endMinutes - startMinutes;

			durationField.value = diff + ' Minutes';
		}

		startInput.addEventListener('change', updateDuration);
		endInput.addEventListener('change', updateDuration);

	});
</script>
<!---------- CALCULATE LESSON DURATION ------------>

<!---------- ADD STUDENT-ADD BLOCK ------------>
<script>
	document.addEventListener('DOMContentLoaded', function () {

		const container = document.getElementById('studentBlocks');
		const addBtn = document.getElementById('addStudentBtn');

		let index = 1;

		addBtn.addEventListener('click', function () {

			const firstBlock = container.querySelector('.student-block');
			const clone = firstBlock.cloneNode(true);

			// to remove old dropdown UI from clone
			clone.querySelectorAll('.multiselect-dropdown').forEach(e => e.remove());

			// to show original select again
			clone.querySelectorAll('select').forEach(e => e.style.display = 'block');

			// to update names
			clone.querySelectorAll('select, input').forEach(el => {
				if (el.name.includes('[id]')) {
					el.name = `students[${index}][id]`;
					el.value = '';
				}
				if (el.name.includes('[fees]')) {
					el.name = `students[${index}][fees]`;
					el.value = '';
				}
			});

			// to show remove button
			const removeBtn = clone.querySelector('.removeStudentBtn');
			removeBtn.classList.remove('hidden');

			removeBtn.addEventListener('click', function () {
				clone.remove();
			});

			container.appendChild(clone);

			// re-init single select script (IMPORTANT)
			SingleSelectDropdown();

			index++;
		});

	});
</script>
<!---------- ADD STUDENT-ADD BLOCK ------------>