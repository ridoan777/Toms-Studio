<x-app-layout>

	@php
		$breadCrumbs = [
			['label' => 'Private', 'url' => 'backend_lesson_create', 'icon' => 'plus'],
			['label' => 'Groups', 'url' => 'backend_group_lesson_create', 'icon' => 'plus'],
			['label' => 'Lesson List', 'url' => 'backend_lesson_index', 'icon' => 'music'],
			['label' => 'Grouped Lesson List', 'url' => 'backend_group_lesson_index', 'icon' => 'music'],
			['label' => 'Lesson Histories', 'url' => 'backend_lesson_histories_index', 'icon' => 'clock'],
		];
	 @endphp

	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />

	<!-- Start Main Widgets -->
	<x-indicators.page_header_widget pageName="{{ $lesson?->title ?? 'A Lesson Schedules' }}" />
	<!-- End Main Widgets -->


	<!-- Alert Component -->
	<x-indicators.alert-component message="Form Submission" />
	<!-- Alert Component -->

	@php
		$ORDER_IN_PAGE = 1;
		$ACCORDION_1_VISIBILITY = ''; // hidden or ''

		$phaseColors = [
			'upcoming' => 'bg-cyan-400 opacity-75',
			'in_progress' => 'bg-green-400 opacity-75',
			'scheduled' => 'bg-orange-400 opacity-75',
			'completed' => 'bg-blue-400 opacity-75',
			'no_show' => 'bg-fuchsia-400 opacity-75',
			'cancelled' => 'bg-red-400 opacity-75',
		];
	@endphp

	<!-- -------------------------------- APP AREA Starts -------------------------------- -->
	<div class="p-6 bg-gray-50 dark:bg-neutral-700 rounded-lg">
		<div class="grid grid-cols-4 2xl:grid-cols-7 gap-4">
			@foreach ($histories as $index => $history)
				<div class="min-h-64 p-4 border border-gray-300 rounded-lg bg-white dark:bg-neutral-600 shadow-sm">
					<div class="mb-4 flex_between_center text-sm">
						<p class="text-semibold">{{ $index+1 }}</p>
						<p class="text-sm text-gray-500 dark:text-gray-200 grid text-end">
							<span>{{ $history?->final_date->format('D, d-M-Y') ?? "no date" }}</span>
							<span>{{ $history?->final_start_time ?? "no start" }} - {{ $history?->final_end_time ?? "no end" }}</span>
						</p>
					</div>
					<p class="text-gray-400 text-3xs">{{ $history?->lesson_history_uid }}</p>
					<p class="font-semibold text-sm">{{ $student?->name }}</p>

					<div class="mt-2">
						<span class="inline-block px-2 py-0.5 text-xs font-bold text-white rounded {{ $phaseColors[$history->phase] ?? 'bg-gray-400' }}">
							{{ strtoupper(str_replace('_', ' ', $history?->phase ?? "no phase")) }}
						</span>
					</div>

					<div class="mt-2">
						<span class="inline-block px-2 py-0.5 text-xs border border-gray-400 rounded">
							{{ $lesson?->lessonRelatingBackTo_Intrument?->name ?? "no instrument" }}
						</span>
						<span class="my-2 inline-block px-2 py-0.5 text-xs border border-gray-400 rounded">
							By: {{ $teacher?->name ?? "no instrument" }}
						</span>
					</div>
				</div>
			@endforeach
		</div>
	</div>

	<!-- -------------------------------- APP AREA Starts -------------------------------- -->

</x-app-layout>
