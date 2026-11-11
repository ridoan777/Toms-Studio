<x-app-layout>

	@php
		$breadCrumbs = [
			['label' => 'Lesson', 'icon' => 'plus'],
			['label' => 'Groups', 'url' => 'backend_group_lesson_create', 'icon' => 'plus'],
			['label' => 'Lesson List', 'url' => 'backend_lesson_index', 'icon' => 'music'],
			['label' => 'Grouped Lesson List', 'url' => 'backend_group_lesson_index', 'icon' => 'music'],
			['label' => 'Lesson Histories', 'url' => 'backend_lesson_histories_index', 'icon' => 'clock'],
		];
	 @endphp

	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />

	<!-- Start Main Widgets -->
	<x-indicators.page_header_widget pageName="Create lesson" />
	<!-- End Main Widgets -->


	<!-- Alert Component -->
	<x-indicators.alert-component message="Form Submission" />
	<!-- Alert Component -->
	<!-- ---------------- APP AREA Starts ---------------- -->


	<form method="POST" action="{{ route('backend_lesson_store') }}" class="rounded-lg p-6 dark:bg-neutral-700">
		@csrf

		<input type="hidden" name="flag" value="create">

		<div>
			<div class="grid grid-cols-2 gap-4">

				<div class="mb-5 mt-2">
					<label class="inputLabelMedium">Teacher <span class="redLabel">*</span></label>
					<select name="teacher_id" class="singleSelectWithSearch dropdownSelectInputs" data-dropdown-position="absolute">
						<option>--Select Teacher--</option>
						@foreach ($teachers as $teacher)
							<option value="{{ $teacher->id }}">[{{ $teacher?->id }}] {{ $teacher?->name ?? "No Name" }} ||
								{{ $teacher->userRelatingBackTo_Instrument?->name ?? 'No Instrument' }}
							</option>
						@endforeach
					</select>
				</div>

				<div class="mb-5 mt-2">
					<label class="inputLabelMedium">Student <span class="redLabel">*</span></label>
					<select name="student_id" class="singleSelectWithSearch dropdownSelectInputs" data-dropdown-position="absolute">
						<option>--Select Student--</option>
						@foreach ($students as $student)
							<option value="{{ $student->id }}">[{{ $student?->id }}] {{ $student?->name ?? "No Name" }} ||
								{{ $student?->address ?? null }}
							</option>
						@endforeach
					</select>
				</div>

			</div>

			<div class="grid grid-cols-2 gap-4">

				<div class="mb-5 mt-2">
					<label class="inputLabelMedium">Title <span class="redLabel">*</span></label>
					<input type="text" name="title" class="normalInput" placeholder="Lesson title">
				</div>

				<div class="mb-5 mt-2">
					<label class="inputLabelMedium">Lesson Type <span class="redLabel">*</span></label>
					<div class="flex items-center space-x-6 mt-2">
						@foreach (\App\Enums\LessonCategoryEnums::cases() as $type)
							<label class="flex items-center cursor-pointer">
								<input type="radio" name="lesson_category" value="{{ $type->value }}" {{ $loop->first ? 'checked' : '' }}>
								<span class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">{{ ucfirst($type->value) }}</span>
							</label>
						@endforeach
					</div>
				</div>

			</div>

			<div class="grid grid-cols-2 gap-4">

				<div class="mb-5 mt-2">
					<label class="inputLabelMedium">Fees</label>
					<input type="number" step="0.01" name="fees" class="normalInput" placeholder="0.00">
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

			<div class="grid grid-cols-3 gap-4">

				<div class="mb-5 mt-2">
					<label class="inputLabelMedium">Subject <span class="redLabel">*</span></label>
					<select name="subject_id" class="singleSelectWithSearch dropdownSelectInputs" data-dropdown-position="absolute">
						<option>--Select Subject--</option>
						@foreach ($instrumentSubjects as $subject)
							<option value="{{ $subject->id }}">[{{ $subject->id }}] {{ $subject->name }}</option>
						@endforeach
					</select>
				</div>

				<div class="mb-5 mt-2">
					<label class="inputLabelMedium">Schedule day(s)
						 <span class="redLabel">*</span>
						<span class="grayLabel"> (Weekday(s) this lesson will be conducted)</span>
					</label>
					<select name="schedule_day[]" class="dropdownSelectInputs w-full"
						data-dropdown-position="absolute" multiple multiselect-search="true" multiselect-select-all="true" multiselect-max-items="7">
						@foreach (\App\Enums\WeekdaysEnums::cases() as $day)
							<option value="{{ $day->value }}">{{ $day->value }}</option>
						@endforeach
					</select>
				</div>

				<div class="mb-5 mt-2">
					<label class="inputLabelMedium">Starting Date
						 <span class="redLabel">*</span><span class="grayLabel"> (From when this class will start)</span>
					</label>
					<input type="date" name="starting_date" class="normalInput">
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

		</div>

		<button type="submit" class="saveButton my-4">Create Lesson</button>
	</form>
	<!-- ---------------- APP AREA Starts ---------------- -->

</x-app-layout>

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