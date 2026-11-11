<x-app-layout>

	@php
		$breadCrumbs = [
			['label' => 'Private', 'url' => 'backend_lesson_create', 'icon' => 'plus'],
			['label' => 'Groups', 'url' => 'backend_group_lesson_create', 'icon' => 'plus'],
			['label' => 'Lesson List', 'url' => 'backend_lesson_index', 'icon' => 'music'],
			['label' => 'Grouped Lesson List', 'url' => 'backend_group_lesson_index', 'icon' => 'music'],
			['label' => 'Schedule Histories'],
		];
	@endphp

	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />

	<!-- Start Main Widgets -->
	<x-indicators.page_header_widget pageName="All Lesson Schedules or Class Histories" />
	<!-- End Main Widgets -->


	<!-- Alert Component -->
	<x-indicators.alert-component message="Form Submission" />
	<!-- Alert Component -->

	@php
		$ORDER_IN_PAGE = 1;
		$ACCORDION_1_VISIBILITY = ''; // hidden or ''
		$phaseColors = ['upcoming' => 'bg-cyan-400 opacity-75', 'in_progress' => 'bg-green-400 opacity-75', 'scheduled' => 'bg-orange-400 opacity-75', 'completed' => 'bg-blue-400 opacity-75', 'no_show' => 'bg-fuchsia-400 opacity-75', 'cancelled' => 'bg-red-400 opacity-75'];
		$typeBgColors = [\App\Enums\LessonTypeEnums::LESSON->value => 'bg-indigo-500/20 hover:bg-indigo-500/30', \App\Enums\LessonTypeEnums::GROUP->value => 'bg-emerald-500/20 hover:bg-emerald-500/30'];
		$typeTextColors = [\App\Enums\LessonTypeEnums::LESSON->value => 'text-indigo-500/75 dark:text-indigo-200', \App\Enums\LessonTypeEnums::GROUP->value => 'text-emerald-500/75 dark:text-emerald-200'];
		$pageWindowStart = max(1, $historiesPage->currentPage() - 2);
		$pageWindowEnd = min($historiesPage->lastPage(), $historiesPage->currentPage() + 2);
	@endphp

	<!-- -------------------------------- APP AREA Starts -------------------------------- -->

	<div class="p-6 bg-gray-50 dark:bg-neutral-700 rounded-lg">
		<!--------------- HEADER --------------->
		<div class="mb-6 flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
			<div>
				<p class="text-lg font-semibold text-gray-800 dark:text-white">Lesson & Group Lesson Histories</p>
				<p class="text-sm text-gray-500 dark:text-gray-300">
					Showing {{ $allHistories->count() }} matched histories across {{ $historiesPage->count() }} date group(s) on this page
				</p>
			</div>

			<form action="{{ route('backend_lesson_histories_index') }}" method="GET" class="w-full xl:max-w-3xl">
				<div class="flex flex-col gap-3 md:flex-row">
					<input type="text" name="search" value="{{ $search }}"
						placeholder="Search by history id, lesson id/name/uid/teacher, group id/name/uid/teacher"
						class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-700 focus:border-blue-500 focus:ring-blue-500 dark:border-neutral-500 dark:bg-neutral-800 dark:text-gray-100">

					<div class="flex gap-2">
						<button type="submit"
							class="px-4 py-3 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700">
							Search
						</button>
						<a href="{{ route('backend_lesson_histories_index') }}"
							class="px-4 py-3 text-sm font-semibold text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 dark:bg-neutral-600 dark:text-white dark:hover:bg-neutral-500">
							Reset
						</a>
					</div>
				</div>
			</form>
		</div>

		@if ($search)
			<p class="mb-4 text-sm text-gray-500 dark:text-gray-300">
				Search result for: <span class="font-semibold text-gray-700 dark:text-white">{{ $search }}</span>
			</p>
		@endif
		<!--------------- HEADER --------------->
		

		<!--------------- HISTORY BLOCK --------------->
		@if ($historiesPage->isEmpty())
			<div
				class="rounded-lg border border-dashed border-gray-300 p-10 text-center text-sm text-gray-500 dark:border-neutral-500 dark:text-gray-300">
				No lesson histories found for the current search.
			</div>
		@else
			<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 2xl:grid-cols-5 gap-4">

				@foreach ($historiesPage as $date => $histories)
					<div class="min-h-64 p-4 border border-gray-300 rounded-lg bg-gray-50 dark:bg-neutral-700 shadow-sm">
						<div class="mb-4 pb-3 border-b border-gray-300 dark:border-neutral-500">
							<p class="font-bold text-sm dark:text-yellow-500">{{ \Carbon\Carbon::parse($date)->format('D, d M Y') }}
							</p>
							<p class="text-2xs text-gray-500 dark:text-gray-300">{{ $histories->count() }} Lesson(s)</p>
						</div>

						<div class="space-y-4">
							@foreach ($histories as $index => $history)
								@php
									$lesson = $history->scheduleRelatingBackTo_Lesson;
									$group = $history->scheduleRelatingBackTo_LessonGroup;
									$type = $lesson ? \App\Enums\LessonTypeEnums::LESSON->value : \App\Enums\LessonTypeEnums::GROUP->value;

									$studentName = $lesson
										? ($lesson->lessonRelatingBackTo_Student?->name ?? 'N/A')
										: ($group ? $group->groupRelationWith_Students->count() . ' student(s)' : 'Group Lesson');

									$teacherName = $lesson ? ($lesson->lessonRelatingBackTo_Teacher?->name ?? 'N/A') : ($group?->groupRelatingBackTo_Teacher?->name ?? 'N/A');

									$instrumentName = $lesson ? ($lesson->lessonRelatingBackTo_Intrument?->name ?? 'N/A') : ($group?->groupRelatingBackTo_Intrument?->name ?? 'N/A');
								@endphp
								<div
									class="p-3 hover:scale-105 border border-gray-200 dark:border-neutral-500 rounded-lg {{ $typeBgColors[$type] ?? 'bg-white' }}">
									<a href="{{ route('backend_lesson_histories_show', $history->id) }}">
										<div class="flex_between_center text-sm">
											<p class="pillThin text-black dark:text-white {{ $typeBgColors[$type] ?? 'bg-gray-500' }}">
												{{ $lesson ? 'Lesson' : 'Group' }}
											</p>

											<p class="text-2xs text-gray-500 dark:text-gray-200">
												{{ $history?->final_start_time?->format('h:i A') ?? 'N/A' }} -
												{{ $history?->final_end_time?->format('h:i A') ?? 'N/A' }}
											</p>

											<span>
												<form action="{{ route('backend_lesson_histories_delete', $history->id) }}" method="POST"
													onsubmit="return confirm('Are you sure you want to delete this history?');">
													@csrf
													@method('DELETE')
													<button type="submit" class="deleteButton scale-75">
														{!! \App\Helpers\IconPack::trash(['class' => 'iconPackItem w-3 h-3 text-gray-100']) !!}
													</button>
												</form>
											</span>
										</div>

										<p
											class="mt-3 font-medium text-sm {{ $typeTextColors[$type] ?? 'text-red-500' }} opacity-100 text-center">
											{{ $lesson ? $lesson?->title : $group?->title }}
										</p>
										<p class="my-4 font-semibold text-sm dark:text-white text-center">{{ $studentName }}</p>

										<div class="mt-2 text-center">
											<span
												class="inline-block px-2 py-0.5 text-xs font-bold text-white rounded {{ $phaseColors[$history->phase] ?? 'bg-gray-400' }}">
												{{ strtoupper(str_replace('_', ' ', $history?->phase ?? 'NO PHASE')) }}
											</span>
											@if($history->phase == 'scheduled')
												<div class="mt-1 text-3xs text-gray-500 dark:text-gray-400">
													Original: {{ $history->original_date?->format('d M') }} |
													{{ $history->original_start_time?->format('h:i A') }}
												</div>
											@endif
										</div>

										<div class="mt-2 flex flex-wrap gap-1">
											<span
												class="inline-block px-2 py-0.5 text-xs border dark:border-gray-400 rounded text-green-900 dark:text-white">{{ $instrumentName }}</span>
											<span
												class="inline-block px-2 py-0.5 text-xs border bg-gray-200 dark:border-gray-400 rounded text-gray-500">By:
												{{ $teacherName }}</span>
										</div>

										@if(app()->environment('local', 'staging'))
											<span class="text-xs text-gray-400">[{{ $history?->id }}]</span>
										@endif
										<span class="text-3xs text-center text-gray-400">{{ $history?->lesson_history_uid }}</span>
									</a>
									<p class="text-3xs text-center text-gray-500">Creator: {{ $history?->scheduleRelatingBackTo_Creator?->name }}</p>
								</div>
							@endforeach
						</div>
					</div>
				@endforeach

			</div>
		@endif
		<!--------------- HISTORY BLOCK --------------->

		<!--------------- PAGINATION --------------->
		<div class="mt-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
			<p class="text-sm text-gray-500 dark:text-gray-300">
				Page {{ $historiesPage->currentPage() }} of {{ $historiesPage->lastPage() }} |
				{{ $historiesPage->total() }} date group(s) matched
			</p>
			<div class="flex flex-col gap-3 md:flex-row md:items-center">
				<div class="flex flex-wrap items-center gap-2">
					@if ($historiesPage->onFirstPage())
						<span class="px-3 py-2 text-sm rounded-lg bg-gray-200 text-gray-400 dark:bg-neutral-600 dark:text-gray-500">Prev</span>
					@else
						<a href="{{ $historiesPage->previousPageUrl() }}"
							class="px-3 py-2 text-sm rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 dark:bg-neutral-600 dark:text-white dark:hover:bg-neutral-500">Prev</a>
					@endif

					@if ($pageWindowStart > 1)
						<a href="{{ $historiesPage->url(1) }}"
							class="px-3 py-2 text-sm rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 dark:bg-neutral-600 dark:text-white dark:hover:bg-neutral-500">1</a>
						@if ($pageWindowStart > 2)
							<span class="px-1 text-sm text-gray-500 dark:text-gray-300">...</span>
						@endif
					@endif

					@for ($pageNumber = $pageWindowStart; $pageNumber <= $pageWindowEnd; $pageNumber++)
						@if ($pageNumber == $historiesPage->currentPage())
							<span class="px-3 py-2 text-sm rounded-lg bg-blue-600 text-white">{{ $pageNumber }}</span>
						@else
							<a href="{{ $historiesPage->url($pageNumber) }}"
								class="px-3 py-2 text-sm rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 dark:bg-neutral-600 dark:text-white dark:hover:bg-neutral-500">{{ $pageNumber }}</a>
						@endif
					@endfor

					@if ($pageWindowEnd < $historiesPage->lastPage())
						@if ($pageWindowEnd < $historiesPage->lastPage() - 1)
							<span class="px-1 text-sm text-gray-500 dark:text-gray-300">...</span>
						@endif
						<a href="{{ $historiesPage->url($historiesPage->lastPage()) }}"
							class="px-3 py-2 text-sm rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 dark:bg-neutral-600 dark:text-white dark:hover:bg-neutral-500">{{ $historiesPage->lastPage() }}</a>
					@endif

					@if ($historiesPage->hasMorePages())
						<a href="{{ $historiesPage->nextPageUrl() }}"
							class="px-3 py-2 text-sm rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 dark:bg-neutral-600 dark:text-white dark:hover:bg-neutral-500">Next</a>
					@else
						<span class="px-3 py-2 text-sm rounded-lg bg-gray-200 text-gray-400 dark:bg-neutral-600 dark:text-gray-500">Next</span>
					@endif
				</div>

				<form action="{{ route('backend_lesson_histories_index') }}" method="GET" class="flex items-center gap-2">
					@if ($search)
						<input type="hidden" name="search" value="{{ $search }}">
					@endif
					<input type="number" name="page" min="1" max="{{ $historiesPage->lastPage() }}"
						value="{{ $historiesPage->currentPage() }}"
						class="w-24 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:ring-blue-500 dark:border-neutral-500 dark:bg-neutral-800 dark:text-gray-100">
					<button type="submit"
						class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700">
						Go
					</button>
				</form>
			</div>
		</div>
		<!--------------- PAGINATION --------------->
	</div>

	<!-- -------------------------------- APP AREA Starts -------------------------------- -->

</x-app-layout>
