<x-app-layout>

	@php
		$breadCrumbs = [
			['label' => 'Private', 'url' => 'backend_lesson_create', 'icon' => 'plus'],
			['label' => 'Groups', 'url' => 'backend_group_lesson_create', 'icon' => 'plus'],
			['label' => 'Lesson List', 'url' => 'backend_lesson_index', 'icon' => 'music'],
			['label' => 'Grouped Lesson List', 'url' => 'backend_group_lesson_index', 'icon' => 'music'],
			['label' => 'Single Group Lesson'],
			['label' => 'Lesson Histories', 'url' => 'backend_lesson_histories_index', 'icon' => 'clock'],
		];
	 @endphp

	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />

	<!-- Start Main Widgets -->
	<x-indicators.page_header_widget pageName="Modify Group lesson" />
	<!-- End Main Widgets -->


	<!-- Alert Component -->
	<x-indicators.alert-component message="Form Submission" />
	<!-- Alert Component -->
	<!-- ---------------- APP AREA Starts ---------------- -->

	<!-- -------------- MAIN FORM -------------- -->
	<section class="p-4 bg-gray-50 rounded-lg dark:bg-neutral-800">
		<form method="POST" action="{{ route('backend_group_lesson_store') }}" class="rounded-lg p-6 dark:bg-neutral-700">
			@csrf

			<input type="hidden" name="flag" value="update">
			<input type="hidden" name="id" value="{{ $lessonGroup?->id }}">

			<!-- -------------- BASIC INFO -------------- -->
			<div class="mb-12 p-4 grid grid-cols-12 gap-6 border border-gray-400 rounded-lg">

				<div class="col-span-6 flex_between_center gap-2">
					<label class="inputLabelMedium min-w-24 m-0">DB ID:</label>
					<input type="text" class="normalInput" value="{{ $lessonGroup?->id }}" readonly disabled>
				</div>

				<div class="col-span-6 flex_between_center gap-2">
					<label class="inputLabelMedium min-w-24 m-0">Group UID:</label>
					<input type="text" class="normalInput" value="{{ $lessonGroup?->group_uid }}" readonly disabled>
				</div>

				<div class="col-span-4 flex_between_center gap-2">
					<label class="inputLabelMedium m-0">Creator:</label>
					<input type="text" class="normalInput" value="{{ $lessonGroup?->groupRelatingBackTo_Creator?->name }}"
						readonly disabled>
				</div>

				<div class="col-span-4 flex_between_center gap-2">
					<label class="inputLabelMedium m-0">Created:</label>
					<input type="text" class="normalInput" value="@datetime(optional($lessonGroup)->created_at)" readonly
						disabled>
				</div>

				<div class="col-span-4 flex_between_center gap-2">
					<label class="inputLabelMedium m-0">Updated:</label>
					<input type="text" class="normalInput" value="@datetime(optional($lessonGroup)->updated_at)" readonly
						disabled>
				</div>

			</div>

			<!-- -------------- TEACHER -------------- -->
			<div class="p-4 border border-gray-400 rounded-lg">

				<div class="grid grid-cols-2 gap-4">

					<div class="mb-5 mt-2">
						<label class="inputLabelMedium">Teacher <span class="redLabel">*</span></label>
						{{-- <select name="teacher_id" class="dropdownSelectInputs"> --}}
							<select name="teacher_id" class="singleSelectWithSearch dropdownSelectInputs student-id"
								data-dropdown-position="absolute">
								@foreach ($teachers as $teacher)
									<option value="{{ $teacher->id }}" {{ $lessonGroup?->teacher_id === $teacher->id ? 'selected' : '' }}>
										[{{ $teacher->id }}] {{ $teacher->name }} ||
										{{ $teacher->userRelatingBackTo_Instrument?->name }}
									</option>
								@endforeach
							</select>
					</div>

					<div class="mb-5 mt-2">
						<label class="inputLabelMedium">Subject <span class="redLabel">*</span></label>
						<select name="subject_id" class="singleSelectWithSearch dropdownSelectInputs"
							data-dropdown-position="absolute">
							<option value="">--Select Subject--</option>
							@foreach ($instrumentSubjects as $subject)
								<option value="{{ $subject->id }}" {{ $lessonGroup?->subject_id === $subject->id ? 'selected' : '' }}>
									[{{ $subject->id }}] {{ $subject->name }}
								</option>
							@endforeach
						</select>
					</div>

				</div>

				<div class="grid grid-cols-1 gap-4">

					<div class="mb-5 mt-2">
						<label class="inputLabelMedium">Group Name/Title <span class="redLabel">*</span></label>
						<input type="text" name="title" class="normalInput" value="{{ $lessonGroup?->title }}">
					</div>

				</div>

				<!-- -------------- SCHEDULE SECTION (MATCH CREATE) -------------- -->

				<div class="grid grid-cols-3 gap-4">

					<div class="mb-5 mt-2">
						<label class="inputLabelMedium">Schedule day(s) <span class="redLabel">*</span></label>

						<select name="schedule_day[]" class="dropdownSelectInputs w-full" multiple multiselect-search="true"
							multiselect-select-all="true" data-dropdown-position="absolute">

							@foreach (\App\Enums\WeekdaysEnums::cases() as $day)
								<option value="{{ $day->value }}" @selected(in_array($day->value, $lessonGroup?->schedule_day ?? []))>
									{{ $day->value }}
								</option>
							@endforeach

						</select>
					</div>

					<div class="mb-5 mt-2">
						<label class="inputLabelMedium">Starting Date <span class="redLabel">*</span></label>
						<input type="date" name="starting_date" class="normalInput"
							value="{{ $lessonGroup?->starting_date?->format('Y-m-d') }}">
					</div>

					<div class="mb-5 mt-2">
						<label class="inputLabelMedium">Difficulty</label>
						<select name="difficulty" class="dropdownSelectInputs">
							<option value="">--Select Difficulty--</option>
							@foreach ($difficulties as $difficulty)
								<option value="{{ $difficulty['value'] }}" {{ $lessonGroup?->difficulty === $difficulty['value'] ? 'selected' : '' }}>
									{{ $difficulty['label'] }}
								</option>
							@endforeach
						</select>
					</div>

				</div>

				<!-- -------------- TIME -------------- -->
				<div class="grid grid-cols-12 gap-4">

					<div class="col-span-5 mb-5 mt-2">
						<label class="inputLabelMedium">Start Time <span class="redLabel">*</span></label>
						<input type="time" name="start_time" class="normalInput"
							value="{{ $lessonGroup?->start_time ? \Carbon\Carbon::parse($lessonGroup->start_time)->format('H:i') : '' }}">
					</div>

					<div class="col-span-5 mb-5 mt-2">
						<label class="inputLabelMedium">End Time <span class="redLabel">*</span></label>
						<input type="time" name="end_time" class="normalInput"
							value="{{ $lessonGroup?->end_time ? \Carbon\Carbon::parse($lessonGroup->end_time)->format('H:i') : '' }}">
					</div>

					<div class="col-span-2 mb-5 mt-2">
						<label class="inputLabelMedium">Lesson Time</label>
						<input id="lessonDuration" type="text" class="normalInput" readonly disabled>
					</div>

				</div>

				<!-- -------------- REMARKS -------------- -->
				<div class="mb-5 mt-2">
					<label class="inputLabelMedium">Remarks</label>
					<textarea class="summernote" name="remarks">{!! $lessonGroup?->remarks !!}</textarea>
				</div>

			</div>
			<!-- -------------- TEACHER -------------- -->

			<!-- -------------- STUDENTS -------------- -->
			<div class="mt-12 p-4 border border-gray-400 rounded-lg">

				<h2 class="text-center">All Students</h2>

				<template id="studentTemplate">
					<div class="student-block grid grid-cols-5 gap-4 p-4 border border-gray-400 rounded-lg mb-4">

						<div class="col-span-4">
							<label class="inputLabelMedium">Pick a student</label>
							<select class="singleSelectWithSearch dropdownSelectInputs student-id">
								<option value="">Select student</option>
								@foreach ($availableStudents as $s)
									<option value="{{ $s->id }}">
										[{{ $s->id }}] | {{ $s->name }} --- {{ $s?->address }}
									</option>
								@endforeach
							</select>
						</div>

						<div class="col-span-1">
							<label class="inputLabelMedium">Fee(s)</label>
							<div class="flex gap-2">
								<input type="number" class="w-full normalInput student-fees" step="any">
								<button type="button" class="removeStudentBtn px-3 hover:bg-red-200 rounded-lg">
									{!! \App\Helpers\IconPack::trash(['class' => 'iconPackItem w-5 h-5 text-red-500']) !!}
								</button>
							</div>
						</div>

					</div>
				</template>

				<div id="studentBlocks" class="mb-5 mt-2">

					@php $index = 0; @endphp

					@foreach($lessonGroup->groupRelationWith_Students ?? [] as $member)
						<div class="student-block grid grid-cols-5 gap-4 p-4 border border-gray-400 rounded-lg mb-4">
							@php
								$originalUser = $member?->studentRelatingBackTo_User;
								$userAvatar = \App\Helpers\Ui\GetUserAvatar::alignAvatar($originalUser, true);
							@endphp

							<div class="col-span-2">
								<label class="inputLabelMedium">Student Details</label>
								<div class="flex_start_center gap-4">
									<a href="{{ route('backend_single_user', $originalUser->id) }}">
										<img src="{{ $userAvatar }}" alt="" class="w-20 h-20 rounded-lg hover:scale-105">
									</a>
									<div>
										<p class="text-sm dark:text-gray-200"><b>ID:</b> <span>{{ $originalUser?->id }}</span></p>
										<p class="text-sm dark:text-gray-200"><b>Name:</b> <span>{{ $originalUser?->name }}</span>
										</p>
										<p class="dark:text-gray-400"><b>Member UID:</b> <span
												class="text-xs">{{ $member?->member_uid }}</span></p>
										<p class="dark:text-gray-300"><b>User Email:</b> <span
												class="text-sm">{{ $originalUser?->email ?? "N/A" }}</span>
										</p>
										<p class="dark:text-gray-300"><b>Guardian:</b> <span
												class="text-xs">{{ $originalUser?->guardian ?? "N/A" }}</span></p>
										<p class="text-sm text-gray-500 dark:text-gray-300"><b>Class Joining date:</b> <span
												class="text-xs">@datetime(optional($member)->created_at)</span></p>
									</div>
								</div>
							</div>

							<div class="col-span-2">
								<label class="inputLabelMedium">Pick a student</label>
								<select name="students[{{ $index }}][id]"
									class="singleSelectWithSearch dropdownSelectInputs student-id"
									data-dropdown-position="absolute">
									@foreach ($students as $student)
										<option value="{{ $student->id }}" {{ $member->student_id == $student->id ? 'selected' : '' }}>
											[{{ $student->id }}] &nbsp;|&nbsp; {{ $student->name }} &nbsp;|&nbsp; {{ $student->email}}
										</option>
									@endforeach
								</select>
							</div>

							<div class="col-span-1">
								<label class="inputLabelMedium">Fee(s)</label>
								<div class="flex gap-2">
									<input type="number" name="students[{{ $index }}][fees]" value="{{ $member->fees }}"
										class="normalInput student-fees" step="any">
									<button type="button" class="removeStudentBtn px-3 hover:bg-red-200 rounded-lg">
										{!! \App\Helpers\IconPack::trash(['class' => 'iconPackItem w-5 h-5 text-red-500']) !!}
									</button>
								</div>
							</div>

						</div>
						@php $index++; @endphp
					@endforeach


				</div>

				<div class="flex justify-end">
					<button type="button" id="addStudentBtn" class="px-4 py-2 text-xs bg-violet-600 text-white rounded">+ Add
						Student</button>
				</div>


			</div>
			<!-- -------------- STUDENTS -------------- -->

			<button type="submit" class="my-6 saveButton">Update Lesson</button>

		</form>
	</section>
	<!-- -------------- MAIN FORM -------------- -->

	<!------------------ HISTORY ------------------>
	<section class="p-4 mt-8 bg-gray-50 rounded-lg dark:bg-neutral-800">
		<div class="mb-4">
			<h4 class="inline-block">Lesson History/Schedule</h4>
			<a href="{{ route('backend_lesson_histories_lesson_index', $lessonGroup) }}" class="float-right darkButton">
				Class Histories
			</a>
			<div class="clear-both"></div>
		</div>


		<form method="POST" action="{{ route('backend_lesson_histories_store') }}">
			@csrf

			<input type="hidden" name="flag" value="create">
			<input type="hidden" name="type" value="group">
			<input type="hidden" name="group_id" value="{{ $lessonGroup->id }}">

			<div class="mb-12 p-4 grid grid-cols-12 gap-6 border border-gray-400 rounded-lg shadow-lg">
				<h4 class="col-span-12">ENTER THIS LESSON'S CLASS HISTORY DATA</h4>
				<label class="col-span-12 grayLabel">(Final/Class Date means when the class is taken. For Scheduled lessons,
					use original date as 'from' and class date as 'planned date')</label>
				<div id="phaseDropdown" class="col-span-12 md:col-span-6 lg:col-span-5">
					<label class="inputLabelMedium">Phase Status<span class="redLabel">*</span></label>
					<select id="phaseSelect" name="phase" class="normalInput">
						@foreach(\App\Domains\Lessons\Enums\LessonPhaseEnums::cases() as $phase)
							<option value="{{ $phase->value }}">
								{{ $phase->label() }}
							</option>
						@endforeach
					</select>
				</div>

				<div id="originalDateWrapper" class="col-span-12 md:col-span-6 lg:col-span-2 hidden">
					<label class="inputLabelMedium">Original Date <span class="redLabel">*</span></label>
					@php
						$today = now();
						$isScheduledDay = in_array($today->format('l'), $lessonGroup?->schedule_day ?? []);
						$originalDateValue = $isScheduledDay ? $today->format('Y-m-d') : '';
					@endphp
					<input type="date" name="original_date" class="normalInput" value="{{ $originalDateValue }}">
				</div>

				<div class="col-span-12 md:col-span-6 lg:col-span-3">
					<label class="inputLabelMedium">Final/Class Date <span class="redLabel">*</span></label>
					<input type="date" name="final_date" class="normalInput" required>
				</div>

				<div class="col-span-12 md:col-span-6 lg:col-span-2">
					<label class="inputLabelMedium">Class Start Time<span class="redLabel">*</span></label>
					<input type="time" name="final_start_time" class="normalInput"
						value="{{ $lessonGroup->start_time ? \Carbon\Carbon::parse($lessonGroup->start_time)->format('H:i') : '' }}">
				</div>

				<div class="col-span-12 md:col-span-6 lg:col-span-2">
					<label class="inputLabelMedium">Class End Time<span class="redLabel">*</span></label>
					<input type="time" name="final_end_time" class="normalInput"
						value="{{ $lessonGroup->end_time ? \Carbon\Carbon::parse($lessonGroup->end_time)->format('H:i') : '' }}">
				</div>

				<div class="col-span-12 flex_end_center">
					<button type="submit" class="inline-block saveButton my-4">Create History</button>
				</div>
			</div>

		</form>
	</section>
	<!------------------ HISTORY ------------------>

	<!------------------ PAYABLE DUES ------------------>
	<section class="p-4 mt-8 bg-gray-50 rounded-lg dark:bg-neutral-800">
		<div class="mb-4">
			<h4 class="inline-block">Payable Dues</h4>
			<a href="{{ route('backend_group_lesson_dues', $lessonGroup) }}" class="float-right darkButton">Dues</a>
			<div class="clear-both"></div>
		</div>

		<div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-neutral-700">
			<table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700 payableSummaryTable">
				<thead class="bg-gray-100 dark:bg-neutral-900">
					<tr>
						<th>Total Records</th>
						<th>Total Payable Amounts</th>
						<th>Total Paid Amounts</th>
						<th>Total Dues</th>
						<th>Last Paid Amount</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-gray-200 bg-white dark:divide-neutral-700 dark:bg-neutral-800">
					<tr>
						<td>{{ $payables['rows'] ?? 0 }}</td>
						<td>{{ number_format((float)($payables['total'] ?? 0), 2) }}</td>
						<td>{{ number_format((float)($payables['paid'] ?? 0), 2) }}</td>
						<td>{{ number_format((float)($payables['dues'] ?? 0), 2) }}</td>
						<td>
							{{ $payables['last_payment']?->amount !== null ? number_format((float)$payables['last_payment']->amount, 2) : 'N/A' }}
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</section>
	<!------------------ PAYABLE DUES ------------------>

	<!-- ---------------- APP AREA Starts ---------------- -->
</x-app-layout>

<!--------------------------- PAYABLE DUES STYLE --------------------------->
<style>
	.payableSummaryTable td {
		padding: 0.75rem 1rem;
		text-align: center;
		font-size: 0.875rem;
		color: rgb(55 65 81);
	}

	.dark .payableSummaryTable th,
	.dark .payableSummaryTable td {
		color: rgb(229 231 235);
	}
</style>
<!--------------------------- PAYABLE DUES STYLE --------------------------->

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
		updateDuration();

	});
</script>
<!---------- CALCULATE LESSON DURATION ------------>

<!---------- ADD STUDENT-ADD BLOCK ------------>
<script>
	document.addEventListener('DOMContentLoaded', function () {

		const container = document.getElementById('studentBlocks');
		const addBtn = document.getElementById('addStudentBtn');
		const template = document.getElementById('studentTemplate');

		function reindex() {
			const blocks = container.querySelectorAll('.student-block');

			blocks.forEach((block, i) => {
				block.querySelector('.student-id').name = `students[${i}][id]`;
				block.querySelector('.student-fees').name = `students[${i}][fees]`;
			});
		}

		function bindRemove(btn) {
			btn.addEventListener('click', function () {
				btn.closest('.student-block').remove();
				reindex();
			});
		}

		// bind existing buttons (from DB)
		document.querySelectorAll('.removeStudentBtn').forEach(bindRemove);

		addBtn.addEventListener('click', function () {

			const clone = template.content.cloneNode(true);
			const block = clone.querySelector('.student-block');

			// removing plugin leftovers
			block.querySelectorAll('.multiselect-dropdown').forEach(e => e.remove());

			// resetting values
			block.querySelector('.student-id').selectedIndex = 0;
			block.querySelector('.student-fees').value = '';

			// bind remove
			bindRemove(block.querySelector('.removeStudentBtn'));

			container.appendChild(block);

			reindex();

			// re-init dropdown plugin
			if (typeof SingleSelectDropdown === 'function') {
				SingleSelectDropdown();
			}
		});

		reindex();

	});
</script>
<!---------- ADD STUDENT-ADD BLOCK ------------>


<!-- ---------------- Hide/Show Original Date  ---------------- -->
<script>
	document.addEventListener('DOMContentLoaded', function () {

		const phaseSelect = document.getElementById('phaseSelect');
		const originalDateWrapper = document.getElementById('originalDateWrapper');
		const phaseDropdown = document.getElementById('phaseDropdown');

		function toggleOriginalDate() {

			if (phaseSelect.value === 'scheduled') {
				originalDateWrapper.classList.remove('hidden');
				phaseDropdown.classList.remove('lg:col-span-5');
			}
			else {
				originalDateWrapper.classList.add('hidden');
				phaseDropdown.classList.add('lg:col-span-3');
			}
		}

		phaseSelect.addEventListener('change', toggleOriginalDate);

		toggleOriginalDate();

	});
</script>
<!-- ---------------- Hide/Show Original Date  ---------------- -->