<x-app-layout>

	@php
		$breadCrumbs = [
			['label' => 'Private', 'url' => 'backend_lesson_create', 'icon' => 'plus'],
			['label' => 'Groups', 'url' => 'backend_group_lesson_create', 'icon' => 'plus'],
			['label' => 'Lesson List', 'url' => 'backend_lesson_index', 'icon' => 'music'],
			['label' => 'Grouped Lesson List', 'url' => 'backend_group_lesson_index', 'icon' => 'music'],
			['label' => 'Lesson Histories', 'url' => 'backend_lesson_histories_index', 'icon' => 'clock'],
			['label' => 'Single History'],
		];

		$zones = \App\Helpers\Ui\ConvertDynamicTimezone::handle();
	 @endphp

	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />


	<!-- Start Main Widgets -->
	<x-indicators.page_header_widget pageName="Modify history" />
	<!-- End Main Widgets -->


	<!-- Alert Component -->
	<x-indicators.alert-component message="Form Submission" />
	<!-- Alert Component -->
	<!-- ---------------- APP AREA Starts ---------------- -->



	<div class=" p-6 bg-gray-50 rounded-lg dark:bg-neutral-700">
		@php
			$lesson = $history->scheduleRelatingBackTo_Lesson;
			$group = $history->scheduleRelatingBackTo_LessonGroup;
			$title = $lesson ? $lesson->title : ($group ? $group->title : 'N/A');

			$teacher = $lesson ? ($lesson->lessonRelatingBackTo_Teacher?->name ?? 'N/A') : ($group?->groupRelatingBackTo_Teacher?->name ?? 'N/A');
			$student = $lesson ? ($lesson->lessonRelatingBackTo_Student?->name ?? 'N/A') : ($group ? $group->groupRelationWith_Students->count() . ' student(s)' : 'N/A');
			$instrument = $lesson ? ($lesson->lessonRelatingBackTo_Intrument?->name ?? 'N/A') : ($group?->groupRelatingBackTo_Intrument?->name ?? 'N/A');
		@endphp

		<!-------------------- HISTORY INFORMATION --------------------->
		<div class="mb-12 p-4 grid grid-cols-2 lg:grid-cols-12 gap-6 border border-gray-400 rounded-lg shadow-lg">
			<h4 class="col-span-12">HISTORY INFORMATION</h4>
			<div class="col-span-2 lg:col-span-4 my-2">
				<label class="inputLabelMedium min-w-24 m-0">DB ID:</label>
				<input type="text" class="normalInput" value="{{ $history?->id ?? null }}" readonly disabled>
			</div>

			<div class="col-span-2 lg:col-span-4 my-2">
				<label class="inputLabelMedium min-w-24 m-0">UID:</label>
				<input type="text" class="normalInput" value="{{ $history?->lesson_history_uid ?? null }}" readonly
					disabled>
			</div>

			<div class="col-span-2 lg:col-span-4">
				<label class="inputLabelMedium">Lesson Type</label>
				<div
					class="flex items-center h-10 px-3 rounded-lg border border-gray-300 dark:border-neutral-600 bg-gray-100 dark:bg-neutral-800">
					@if($lesson)
					<span
						class="px-2 py-0.5 text-xs font-bold rounded bg-indigo-500/20 text-indigo-600 dark:text-indigo-300 border border-indigo-500/30">SINGLE
						LESSON</span>
					@else
					<span
						class="px-2 py-0.5 text-xs font-bold rounded bg-emerald-500/20 text-emerald-600 dark:text-emerald-300 border border-emerald-500/30">GROUP
						LESSON</span>
					@endif
				</div>
			</div>

			<div class="col-span-6 grid grid-cols-7 gap-4">
				@php
				$creator = $history?->scheduleRelatingBackTo_Creator;
				@endphp
				<h6 class="col-span-12">CREATOR</h6>
				<a href="{{ route('backend_single_user', $creator->id) }}" class="col-span-2" target="_blank">
					<img class="w-full rounded-lg object-cover hover:scale-105"
						src="{{ $creator?->avatar ? Storage::url($creator?->avatar) : asset('site_assets/dummies/dummy_man.webp') }}"
						alt="{{ $creator?->name }}">
				</a>
				<div class="col-span-5 grid">
					<div class="flex_start_center">
						<label class="inputLabelMedium min-w-32 m-0">Name:</label>
						<input type="text" class="normalInput" value="{{ $creator?->name ?? null }}" readonly disabled>
					</div>
					<div class="flex_start_center">
						<label class="inputLabelMedium min-w-32 m-0">Role:</label>
						<input type="text" class="normalInput" value="{{ ucfirst($creator?->user_role) ?? null }}" readonly
							disabled>
					</div>
					<div class="flex_start_center">
						<label class="inputLabelMedium min-w-32 m-0">Created At:</label>
						<input type="text" class="normalInput"
							value="{{ $history?->created_at ? $history->created_at->timezone($zones['zone'])->format('d-M-Y h:i:s A') . ' (UTC' . $zones['offset'] . ')' : null }}"
							readonly disabled>
					</div>
				</div>

			</div>

			<div class="col-span-6 grid grid-cols-7 gap-4">
				@php
				$lastModifier = $history?->scheduleRelatingBackTo_Modifier;
				@endphp
				@if ($lastModifier)
				<h6 class="col-span-12">LAST MODIFIER</h6>
				<a href="{{ route('backend_single_user', $lastModifier?->id) }}" class="col-span-2" target="_blank">
					<img class="w-full rounded-lg object-cover hover:scale-105"
						src="{{ $lastModifier?->avatar ? Storage::url($lastModifier?->avatar) : asset('site_assets/dummies/dummy_man.webp') }}"
						alt="{{ $lastModifier?->name }}">
				</a>
				<div class="col-span-5 grid">
					<div class="flex_start_center">
						<label class="inputLabelMedium min-w-32 m-0">Name:</label>
						<input type="text" class="normalInput" value="{{ $lastModifier?->name ?? null }}" readonly disabled>
					</div>
					<div class="flex_start_center">
						<label class="inputLabelMedium min-w-32 m-0">Role:</label>
						<input type="text" class="normalInput" value="{{ ucfirst($lastModifier?->user_role) ?? null }}"
							readonly disabled>
					</div>
					<div class="flex_start_center">
						<label class="inputLabelMedium min-w-32 m-0">Updated At:</label>
						<input type="text" class="normalInput"
							value="{{ $history?->updated_at ? $history?->updated_at->timezone($zones['zone'])->format('d-M-Y h:i:s A')  . ' (UTC' . $zones['offset'] . ')' : null }}"
							readonly disabled>
					</div>
				</div>
				@else
				<h4 class="col-span-6 text-gray-400 m-auto">No Modifier Yet</h4>
				@endif

			</div>
		</div>
		<!-------------------- HISTORY INFORMATION --------------------->

		<!-------------------- EDITABLE FORM --------------------->

		<form method="POST" action="{{ route('backend_lesson_histories_store') }}">
			@csrf

			<input type="hidden" name="flag" value="update">
			<input type="hidden" name="id" value="{{ $history->id }}">
			<input type="hidden" name="type" value="{{ $lesson ? " lesson" : "group" }}">
			<input type="hidden" name="lesson_id" value="{{ $lesson ? $lesson->id : null }}">
			<input type="hidden" name="group_id" value="{{ $group ? $group->id : null }}">

			<div class="mb-12 p-4 grid grid-cols-12 gap-6 border border-gray-400 rounded-lg shadow-lg">
				<h4 class="col-span-12">UPDATE HISTORY/SCHEDULE DATA</h4>
				<div id="phaseDropdown" class="col-span-12 md:col-span-6 lg:col-span-5">
					<label class="inputLabelMedium">Phase Status</label>
					<select id="phaseSelect" name="phase" class="normalInput">
						@foreach(\App\Domains\Lessons\Enums\LessonPhaseEnums::cases() as $phase)
						<option value="{{ $phase->value }}" @selected($history->phase === $phase->value)>
							{{ $phase->label() }}
						</option>
						@endforeach
					</select>
				</div>

				<div id="originalDateWrapper"
					class="col-span-12 md:col-span-6 lg:col-span-2 {{ $history->phase !== 'scheduled' ? 'hidden' : '' }}">
					<label class="inputLabelMedium">Original Date <span class="redLabel">*</span></label>
					<input type="date" name="original_date" class="normalInput"
						value="{{ $history->original_date ? $history->original_date->format('Y-m-d') : '' }}">
				</div>

				<div class="col-span-12 md:col-span-6 lg:col-span-3">
					<label class="inputLabelMedium">Final Date</label>
					<input type="date" name="final_date" class="normalInput"
						value="{{ $history->final_date ? $history->final_date->format('Y-m-d') : '' }}">
				</div>

				<div class="col-span-12 md:col-span-6 lg:col-span-2">
					<label class="inputLabelMedium">Start Time</label>
					<input type="time" name="final_start_time" class="normalInput"
						value="{{ $history->final_start_time ? $history->final_start_time->format('H:i') : '' }}">
				</div>

				<div class="col-span-12 md:col-span-6 lg:col-span-2">
					<label class="inputLabelMedium">End Time</label>
					<input type="time" name="final_end_time" class="normalInput"
						value="{{ $history->final_end_time ? $history->final_end_time->format('H:i') : '' }}">
				</div>

				<div class="col-span-12 flex_end_center">
					<button type="submit" class="inline-block showButton my-4">Update</button>
				</div>
			</div>

		</form>
		<!-------------------- EDITABLE FORM --------------------->

		<!-------------------- LESSON/GROUP DETAILS BLOCK --------------------->
		<div
			class="mb-12 p-6 flex flex-col md:flex-row gap-8 border border-amber-400/50 bg-amber-50/30 dark:bg-neutral-800/30 rounded-lg shadow-lg">
			<div class="grow grid grid-cols-12 gap-4">
				<div class="col-span-12">
					<h4 class="col-span-12 flex justify-between items-center">
						LESSON/GROUP INFORMATION
						<a href="{{ $lesson ? route('backend_lesson_show', $lesson->id) : route('backend_group_lesson_show', $group->id) }}"
							class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 underline">View Source
							Details</a>
					</h4>
				</div>
				<div class="col-span-12 md:col-span-6 lg:col-span-4">
					<label class="inputLabelMedium">Source ID</label>
					<input type="text" class="normalInput" value="#{{ $lesson ? $lesson->id : $group?->id }}" readonly
						disabled>
				</div>
				<div class="col-span-12 md:col-span-6 lg:col-span-4">
					<label class="inputLabelMedium">Source UID</label>
					<input type="text" class="normalInput" value="{{ $lesson ? $lesson->lesson_uid : $group?->group_uid }}"
						readonly disabled>
				</div>
				<div class="col-span-12 md:col-span-6 lg:col-span-4">
					<label class="inputLabelMedium">Instrument</label>
					<input type="text" class="normalInput" value="{{ $instrument }}" readonly disabled>
				</div>
				<div class="col-span-12 md:col-span-6 lg:col-span-4">
					<label class="inputLabelMedium">Title</label>
					<input type="text" class="normalInput" value="{{ $title }}" readonly disabled>
				</div>
				<div class="col-span-12 md:col-span-6 lg:col-span-4">
					<label class="inputLabelMedium">Starting Date</label>
					<input type="text" class="normalInput"
						value="{{ $lesson ? $lesson?->starting_date->format('d-M-Y') : ($group ? $group?->starting_date->format('d-M-Y') : 'N/A') }}"
						readonly disabled>
				</div>
				<div class="col-span-12 md:col-span-6 lg:col-span-4">
					<label class="inputLabelMedium">Price/Rate</label>
					<input type="text" class="normalInput" value="${{ $lesson ? $lesson->fees : $group?->price }}" readonly
						disabled>
				</div>
			</div>
		</div>
		<!-------------------- LESSON/GROUP DETAILS BLOCK --------------------->

		<!-------------------- TEACHER DETAILS BLOCK --------------------->

		@php
		$teacherUser = $lesson ? $lesson->lessonRelatingBackTo_Teacher : $group?->groupRelatingBackTo_Teacher;
		@endphp
		<div
			class="mb-12 p-6 flex flex-col md:flex-row gap-8 border border-emerald-400/50 bg-emerald-50/30 dark:bg-neutral-800/30 rounded-lg shadow-lg">
			<div class="shrink-0 flex flex-col items-center justify-center">
				<a href="{{ route('backend_single_user', $teacherUser?->id) }}" target="_blank">
					<img class="h-24 w-24 rounded-lg object-cover hover:scale-105"
						src="{{ $teacherUser?->avatar ? Storage::url($teacherUser?->avatar) : asset('site_assets/dummies/dummy_man.webp') }}"
						alt="{{ $teacherUser?->name }}">
				</a>
				<span class="mt-2 text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Teacher
					Profile</span>
			</div>

			<div class="grow grid grid-cols-12 gap-4">
				<div class="col-span-12 md:col-span-6 lg:col-span-3">
					<label class="inputLabelMedium">Teacher ID</label>
					<input type="text" class="normalInput" value="#{{ $teacherUser?->id ?? 'N/A' }}" readonly disabled>
				</div>
				<div class="col-span-12 md:col-span-6 lg:col-span-3">
					<label class="inputLabelMedium">User UID</label>
					<input type="text" class="normalInput" value="{{ $teacherUser?->user_uid ?? 'N/A' }}" readonly disabled>
				</div>
				<div class="col-span-12 md:col-span-6 lg:col-span-6">
					<label class="inputLabelMedium">Full Name</label>
					<input type="text" class="normalInput" value="{{ $teacherUser?->name ?? 'N/A' }}" readonly disabled>
				</div>
				<div class="col-span-12 md:col-span-6 lg:col-span-6">
					<label class="inputLabelMedium">Email Address</label>
					<input type="text" class="normalInput" value="{{ $teacherUser?->email ?? 'N/A' }}" readonly disabled>
				</div>
				<div class="col-span-12 md:col-span-6 lg:col-span-6">
					<label class="inputLabelMedium">Phone Number</label>
					<input type="text" class="normalInput" value="{{ $teacherUser?->phone ?? 'N/A' }}" readonly disabled>
				</div>
			</div>
		</div>
		<!-------------------- TEACHER DETAILS BLOCK --------------------->

		<!-------------------- STUDENT(S) DETAILS BLOCK --------------------->
		@php
		$students = $lesson ? collect([$lesson->lessonRelatingBackTo_Student]) : ($group ?
		$group->groupRelationWith_Students : collect());
		@endphp

		<div class="mb-12 p-6 border border-blue-400/50 bg-blue-50/30 dark:bg-neutral-800/30 rounded-lg shadow-lg">

			<div class="space-y-6">
				@if($lesson)
				@forelse ($students as $studentUser)
				<div
					class="flex flex-col md:flex-row gap-8 pb-6 {{ !$loop->last ? 'border-b border-blue-200 dark:border-neutral-600' : '' }}">
					<div class="shrink-0 flex flex-col items-center justify-center">
						<a href="{{ route('backend_single_user', $studentUser?->id) }}" target="_blank">
							<img class="h-24 w-24 rounded-lg object-cover hover:scale-105"
								src="{{ $studentUser?->avatar ? Storage::url($studentUser?->avatar) : asset('site_assets/dummies/dummy_man.webp') }}"
								alt="{{ $studentUser?->name }}">
						</a>
						<span class="mt-2 text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider">{{
							$lesson ? 'Student Details' : 'Enrolled Students (' . $students->count() . ')' }}</span>
					</div>

					<div class="grow grid grid-cols-12 gap-4">
						<div class="col-span-12 md:col-span-6 lg:col-span-3">
							<label class="inputLabelMedium">Student ID</label>
							<input type="text" class="normalInput" value="#{{ $studentUser?->id ?? 'N/A' }}" readonly disabled>
						</div>
						<div class="col-span-12 md:col-span-6 lg:col-span-3">
							<label class="inputLabelMedium">User UID</label>
							<input type="text" class="normalInput" value="{{ $studentUser?->user_uid ?? 'N/A' }}" readonly
								disabled>
						</div>
						<div class="col-span-12 md:col-span-6 lg:col-span-6">
							<label class="inputLabelMedium">Full Name</label>
							<input type="text" class="normalInput" value="{{ $studentUser?->name ?? 'N/A' }}" readonly
								disabled>
						</div>
						<div class="col-span-12 md:col-span-6 lg:col-span-6">
							<label class="inputLabelMedium">Email Address</label>
							<input type="text" class="normalInput" value="{{ $studentUser?->email ?? 'N/A' }}" readonly
								disabled>
						</div>
						<div class="col-span-12 md:col-span-6 lg:col-span-6">
							<label class="inputLabelMedium">Phone Number</label>
							<input type="text" class="normalInput" value="{{ $studentUser?->phone ?? 'N/A' }}" readonly
								disabled>
						</div>
					</div>
				</div>
				@empty
				<div class="text-center py-4 text-gray-500">No students found.</div>
				@endforelse
				@else
				<div class="grow grid grid-cols-12 gap-4">
					<div class="col-span-12 md:col-span-6 lg:col-span-3">
						<label class="inputLabelMedium">Total Students</label>
						<input type="text" class="normalInput" value="{{ $students?->count() ?? 'N/A' }}" readonly disabled>
					</div>
				</div>
				@endif
			</div>
		</div>
		<!-------------------- STUDENT(S) DETAILS BLOCK --------------------->

	</div>

	<!-- ---------------- APP AREA ENDS ---------------- -->

</x-app-layout>

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