<x-app-layout>

	@php
		$breadCrumbs = [
			['label' => 'Private', 'url' => 'backend_lesson_create', 'icon' => 'plus'],
			['label' => 'Groups', 'url' => 'backend_group_lesson_create', 'icon' => 'plus'],
			['label' => 'Lesson List', 'url' => 'backend_lesson_index', 'icon' => 'music'],
			['label' => 'Grouped Lesson List', 'url' => 'backend_group_lesson_index', 'icon' => 'music'],
			['label' => $lessonGroup->group_uid, 'url' => 'backend_group_lesson_show', 'params' => [$lessonGroup->id]],
			['label' => 'Dues'],
			['label' => 'Lesson Histories', 'url' => 'backend_lesson_histories_index', 'icon' => 'clock'],
		];
	@endphp

	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />

	<x-indicators.page_header_widget pageName="Lesson Payable Dues" />

	<x-indicators.alert-component message="Form Submission" />

	<section class="p-4 bg-gray-50 rounded-lg dark:bg-neutral-800">
		<div class="mb-4 flex items-center justify-between gap-4">
			<div>
				<h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $lessonGroup?->title ?? 'Lesson' }}
				</h4>
				<p class="text-sm text-gray-600 dark:text-gray-300">Only dues for this lesson are shown below.</p>
			</div>
			<a href="{{ route('backend_lesson_show', $lessonGroup) }}" class="darkButton">Back To Lesson</a>
		</div>

		<div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-neutral-700">
			<table class="min-w-full border-collapse">
				<thead class="bg-gray-100 dark:bg-neutral-900">
					<tr>
						<th class="duesTableHead">#</th>
						<th class="duesTableHead">DB ID</th>
						<th class="duesTableHead">Payable UID</th>
						<th class="duesTableHead">Due/Class Date</th>
						<th class="duesTableHead">Amount</th>
						<th class="duesTableHead">Paid At</th>
						<th class="duesTableHead">Cancelled</th>
						<th class="duesTableHead">Created At</th>
					</tr>
				</thead>
				<tbody class="bg-white dark:bg-neutral-800">
					@forelse ($groupPayables as $index => $payable)
						<tr class="border-t border-gray-200 dark:border-neutral-700 duesTableRow"
							onclick="window.location='{{ route('backend_billings_payable_show', $payable) }}'">
							<td class="duesTableCell">{{ $groupPayables->firstItem() + $index }}</td>
							<td class="duesTableCell">{{ $payable->id }}</td>
							<td class="duesTableCell break-all">{{ $payable->payable_uid }}</td>
							<td class="duesTableCell">
								{{ $payable->week_start ? \Carbon\Carbon::parse($payable->week_start)->format('d M Y') : 'N/A' }}
							</td>
							<td class="duesTableCell">${{ number_format((float)($payable->amount ?? 0), 2) }}</td>
							<td class="duesTableCell">
								{{ $payable->paid_at ? \Carbon\Carbon::parse($payable->paid_at)->format('d M Y, h:i A') : 'N/A' }}
							</td>
							<td class="duesTableCell">{{ $payable->is_cancelled ? 'Yes' : 'No' }}</td>
							<td class="duesTableCell">
								{{ $payable->created_at ? \Carbon\Carbon::parse($payable->created_at)->format('d M Y, h:i A') : 'N/A' }}
							</td>
						</tr>
					@empty
						<tr>
							<td colspan="8" class="duesTableEmpty">No lesson payable dues found.</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>

		@if ($groupPayables->hasPages())
			<div class="mt-4">
				{{ $groupPayables->onEachSide(1)->links() }}
			</div>
		@endif
	</section>

</x-app-layout>

<style>
	.duesTableHead {
		padding: 0.75rem 1rem;
		text-align: left;
		font-size: 0.75rem;
		font-weight: 600;
		text-transform: uppercase;
		letter-spacing: 0.05em;
		color: rgb(55 65 81);
	}

	.duesTableCell {
		text-align: center;
		padding: 0.875rem 1rem;
		font-size: 0.875rem;
		color: rgb(55 65 81);
		vertical-align: top;
	}

	.duesTableRow {
		cursor: pointer;
	}

	.duesTableRow:hover {
		background-color: rgb(243 244 246);
	}

	.duesTableEmpty {
		padding: 1rem;
		text-align: center;
		font-size: 0.875rem;
		color: rgb(107 114 128);
	}

	.dark .duesTableHead,
	.dark .duesTableCell {
		color: rgb(229 231 235);
	}

	.dark .duesTableRow:hover {
		background-color: rgb(38 38 38);
	}

	.dark .duesTableEmpty {
		color: rgb(209 213 219);
	}
</style>