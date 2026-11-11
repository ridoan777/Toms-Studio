<x-app-layout>

	@php
		$breadCrumbs = [
			['label' => 'Private', 'url' => 'backend_lesson_create', 'icon' => 'plus'],
			['label' => 'Groups', 'url' => 'backend_group_lesson_create', 'icon' => 'plus'],
			['label' => 'Lesson List', 'url' => 'backend_lesson_index', 'icon' => 'music'],
			['label' => 'Single Lesson'],
			['label' => 'Grouped Lesson List', 'url' => 'backend_group_lesson_index', 'icon' => 'music'],
			['label' => 'Lesson Histories', 'url' => 'backend_lesson_histories_index', 'icon' => 'clock'],
		];
	 @endphp

	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />

	<x-indicators.page_header_widget pageName="Modify A Lesson" />

	<x-indicators.alert-component message="Form Submission" />

	<!--------------------------- APP AREA --------------------------->

	<!------------------ LESSON FORM ------------------>
	<section class="p-4 bg-gray-50 rounded-lg dark:bg-neutral-800">
		<form method="POST" action="{{ route('backend_lesson_store') }}" class="rounded-lg p-6 dark:bg-neutral-700">
			@csrf

			<input type="hidden" name="flag" value="update">
			<input type="hidden" name="id" value="{{ $singleLesson?->id }}">

			<div>
				<div class="p-4 mb-12 grid grid-cols-2 gap-8 border border-gray-400 rounded">
	
					<div class="col-span-1 mb-5 mt-2 flex_between_center">
						<label class="inputLabelMedium min-w-24 m-0">DB ID:</label>
						<input type="text" class="normalInput" value="{{ $singleLesson?->id ?? null }}" readonly disabled>
					</div>
	
					<div class="col-span-1 mb-5 mt-2 flex_between_center">
						<label class="inputLabelMedium min-w-28 m-0">Lesson UID:</label>
						<input type="text" class="normalInput w-full" value="{{ $singleLesson?->lesson_uid ?? null }}"
							readonly disabled>
					</div>
	
					<div class="col-span-1 mb-5 mt-2 flex_between_center">
						<label class="inputLabelMedium min-w-24 m-0">Creator:</label>
						<input type="text" class="normalInput w-full mx-2"
							value="{{ $singleLesson?->lessonRelatingBackTo_Creator?->name ?? null }}" readonly disabled>
						<a href="{{ route('backend_single_user', $singleLesson?->creator) }}">
							{!! \App\Helpers\IconPack::telegramArrow(['class' => 'iconPackItem w-5 h-5 text-violet-500']) !!}
						</a>
					</div>
	
					<div class="col-span-1 mb-5 flex_between_center">
						<label class="inputLabelMedium min-w-28 m-0">Created at:</label>
						<input type="text" class="normalInput w-full" value="@datetime(optional($singleLesson)->created_at))"
							readonly disabled>
					</div>
	
				</div>

				<div class="grid grid-cols-2 gap-4">

					<div class="mb-5 mt-2">
						<label class="inputLabelMedium">Teacher <span class="redLabel">*</span></label>
						<select name="teacher_id" class="singleSelectWithSearch dropdownSelectInputs" data-dropdown-position="absolute">
							<option>--Select Teacher--</option>
							@foreach ($teachers as $teacher)
							<option value="{{ $teacher->id }}" {{ $singleLesson?->teacher_id === $teacher->id ? 'selected' : ''
								}}>
								[{{ $teacher?->id }}] {{ $teacher?->name ?? "No Name" }} ||
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
							<option value="{{ $student->id }}" {{ $singleLesson?->student_id === $student->id ? 'selected' : ''
								}}>
								[{{ $student?->id }}] {{ $student?->name ?? "No Name" }} ||
								{{ $student?->address ?? null }}
							</option>
							@endforeach
						</select>
					</div>

				</div>

				<div class="grid grid-cols-2 gap-4">

					<div class="mb-5 mt-2">
						<label class="inputLabelMedium">Title <span class="redLabel">*</span></label>
						<input type="text" name="title" class="normalInput" value="{{ $singleLesson?->title ?? null }}">
					</div>

					<div class="mb-5 mt-2 grid grid-cols-3 gap-4 justify-start">
						<div>
							<label class="inputLabelMedium">Lesson Category <span class="redLabel">*</span></label>
							<div class="flex items-center space-x-6 mt-2">
								@foreach (\App\Enums\LessonCategoryEnums::cases() as $type)
								<label class="flex items-center cursor-pointer">
									<input type="radio" name="lesson_category" value="{{ $type->value }}" {{
										$singleLesson?->lesson_category?->value === $type->value ? 'checked' : '' }}>
									<span class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
										{{ ucfirst($type->value) }}
									</span>
								</label>
								@endforeach
							</div>
						</div>
						<div class="mt-4">
							@if($singleLesson?->lesson_category !== \App\Enums\LessonCategoryEnums::MAKEUP)
								<span class="px-3 py-1 text-xs font-medium rounded-full bg-violet-100 text-violet-800 dark:bg-violet-900 dark:text-violet-300">Auto-Accepted</span>
							@elseif($singleLesson?->lesson_category === \App\Enums\LessonCategoryEnums::MAKEUP && $singleLesson?->is_accepted)
								<span class="px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">Accepted</span>
							@else
								<span class="px-3 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-300">Pending</span>
							@endif
						</div>
					</div>

				</div>

				<div class="grid grid-cols-2 gap-4">

					<div class="mb-5 mt-2">
						<label class="inputLabelMedium">Fees</label>
						<input type="number" step="0.01" name="fees" class="normalInput"
							value="{{ $singleLesson?->fees ?? null }}">
					</div>

					<div class="mb-5 mt-2">
						<label class="inputLabelMedium">Difficulty</label>
						<select name="difficulty" class="dropdownSelectInputs">
							<option value="">--Select Difficulty--</option>
							@foreach ($difficulties as $difficulty)
							<option value="{{ $difficulty['value'] }}" {{ $singleLesson?->difficulty === $difficulty['value'] ?
								'selected' : '' }}>
								{{ $difficulty['label'] }}
							</option>
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
							<option value="{{ $subject->id }}" {{ $singleLesson?->subject_id === $subject->id ? 'selected' : ''
								}}>
								[{{ $subject->id }}] {{ $subject->name }}
							</option>
							@endforeach
						</select>
					</div>

					<div class="mb-5 mt-2">
						<label class="inputLabelMedium">Schedule day(s)</label>
						<select name="schedule_day[]" class="dropdownSelectInputs w-full" multiple data-dropdown-position="absolute">
							@foreach (\App\Enums\WeekdaysEnums::cases() as $day)
							<option value="{{ $day->value }}" @selected(in_array($day->value, $singleLesson?->schedule_day ??
								[]))>
								{{ $day->value }}
							</option>
							@endforeach
						</select>
					</div>

					<div class="mb-5 mt-2">
						<label class="inputLabelMedium">Starting Date</label>
						<input type="date" name="starting_date" class="normalInput"
							value="{{ $singleLesson?->starting_date ? \Carbon\Carbon::parse($singleLesson->starting_date)->format('Y-m-d') : null }}">
					</div>

				</div>

				<div class="grid grid-cols-12 gap-4">

					<div class="col-span-5 mb-5 mt-2">
						<label class="inputLabelMedium">Start Time <span class="redLabel">*</span></label>
						<input type="time" name="start_time" class="normalInput"
							value="{{ $singleLesson?->start_time ?? null }}">
					</div>

					<div class="col-span-5 mb-5 mt-2">
						<label class="inputLabelMedium">End Time <span class="redLabel">*</span></label>
						<input type="time" name="end_time" class="normalInput" value="{{ $singleLesson?->end_time ?? null }}">
					</div>

					<div class="col-span-2 mb-5 mt-2">
						<label class="inputLabelMedium">Lesson Time</label>
						<input id="lessonDuration" type="text" class="normalInput" readonly disabled>
					</div>

				</div>

				<div class="mb-5 mt-2">
					<label for="remarks" class="inputLabelMedium">Remarks</label>
					<textarea class="summernote" name="remarks">{!! $singleLesson?->remarks ?? null !!}</textarea>
				</div>

			</div>

			<button type="submit" class="showButton my-4">Update Lesson</button>
		</form>
	</section>
	<!------------------ LESSON FORM ------------------>

	<!------------------ HISTORY ------------------>
	<section class="p-4 mt-8 bg-gray-50 rounded-lg dark:bg-neutral-800">
		<div class="mb-4">
			<h4 class="inline-block">Lesson History/Schedule</h4>
			<a href="{{ route('backend_lesson_histories_lesson_index', $singleLesson) }}" class="float-right darkButton">
				Class Histories
			</a>
			<div class="clear-both"></div>
		</div>
		
		<form method="POST" action="{{ route('backend_lesson_histories_store') }}">
			@csrf

			<input type="hidden" name="flag" value="create">
			<input type="hidden" name="type" value="lesson">
			<input type="hidden" name="lesson_id" value="{{ $singleLesson->id }}">

			<div class="mb-12 p-4 grid grid-cols-12 gap-6 border border-gray-400 rounded-lg shadow-lg">
				<h4 class="col-span-12">ENTER THIS LESSON'S CLASS HISTORY DATA</h4>
				<label class="col-span-12 grayLabel">(Final/Class Date means when the class is taken. For Scheduled lessons, use original date as 'from' and class date as 'planned date')</label>
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

				<div id="originalDateWrapper"
					class="col-span-12 md:col-span-6 lg:col-span-2 hidden">
					<label class="inputLabelMedium">Original Date <span class="redLabel">*</span></label>
					@php
						$today = now();
						$isScheduledDay = in_array($today->format('l'), $singleLesson?->schedule_day ?? []);
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
						value="{{ $singleLesson->start_time ? \Carbon\Carbon::parse($singleLesson->start_time)->format('H:i') : '' }}">
				</div>

				<div class="col-span-12 md:col-span-6 lg:col-span-2">
					<label class="inputLabelMedium">Class End Time<span class="redLabel">*</span></label>
					<input type="time" name="final_end_time" class="normalInput"
						value="{{ $singleLesson->end_time ? \Carbon\Carbon::parse($singleLesson->end_time)->format('H:i') : '' }}">
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
			<a href="{{ route('backend_lesson_dues', $singleLesson) }}" class="float-right darkButton">Dues</a>
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
						<td>{{ number_format((float) ($payables['total'] ?? 0), 2) }}</td>
						<td>{{ number_format((float) ($payables['paid'] ?? 0), 2) }}</td>
						<td>{{ number_format((float) ($payables['dues'] ?? 0), 2) }}</td>
						<td>{{ $payables['last_payment']?->amount !== null ? number_format((float) $payables['last_payment']->amount, 2) : 'N/A' }}</td>
					</tr>
				</tbody>
			</table>
		</div>
	</section>
	<!------------------ PAYABLE DUES ------------------>

	<!--------------------------- APP AREA --------------------------->
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

<!--------------------------- CALCULATE LESSON DURATION --------------------------->
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

			const [sh, sm] = start.split(':').map(Number);
			const [eh, em] = end.split(':').map(Number);

			let startMinutes = (sh * 60) + sm;
			let endMinutes = (eh * 60) + em;

			if (endMinutes < startMinutes) endMinutes += 1440;

			durationField.value = (endMinutes - startMinutes) + ' Minutes';
		}

		startInput.addEventListener('change', updateDuration);
		endInput.addEventListener('change', updateDuration);

		updateDuration();

	});
</script>
<!--------------------------- CALCULATE LESSON DURATION --------------------------->

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
