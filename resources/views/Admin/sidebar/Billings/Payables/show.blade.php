<x-app-layout>

	@php
		$breadCrumbs = [
			['label' => 'Payables', 'url' => 'backend_billings_payables_index', 'icon' => 'dollarSackLine'],
			['label' => $payable?->payable_uid ?? 'Single Payable'],
			['label' => 'Transactions', 'url' => 'backend_transactions_index', 'icon' => 'dollarSack'],
		];

		$zones = \App\Helpers\Ui\ConvertDynamicTimezone::handle();
		$student = $payable?->payableRelatingBackTo_Student;
		$groupMember = $payable?->payableRelatingBackTo_GroupMember;
		$payableObject = $payable?->payable;
		$paymentTrx = $payable?->payment_id ? \App\Models\Billings\Payments\PaymentTrx::find($payable->payment_id) : null;

		$statusText = match ((int)$payable?->status) {
			1 => 'Active',
			default => 'Inactive',
		};
	@endphp

	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />

	<x-indicators.page_header_widget pageName="Payable: {{ $payable?->payable_uid ?? 'N/A' }}" />

	<x-indicators.alert-component message="Modification" />

	<section class="mb-4 p-4 bg-gray-300 dark:bg-neutral-600 rounded-lg">
		<h4 class="mb-4 text-gray-900 dark:text-gray-50">Manual Payment</h4>

		<form action="{{ route('backend_billings_payable_manual_payment', $payable->id) }}" method="POST"
			class="grid grid-cols-12 items-end gap-4">
			@csrf
			<div class="col-span-10">
				<label for="payment_amount" class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-100">Payment
					Amount</label>
				<input type="number" id="payment_amount" name="payment_amount" step="0.01" min="0.01"
					value="{{ old('payment_amount', $payable?->amount) }}"
					class="w-full rounded-lg border border-gray-300 bg-gray-100 p-2 text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-neutral-700 dark:bg-neutral-700 dark:text-gray-50">
			</div>
			<div class="col-span-2">
				<button type="submit" class="showButton"
					onclick="return confirm('Are you sure you want to record this manual payment?');">Record Payment</button>
			</div>
		</form>
	</section>

	<section class="mb-4 p-4 bg-gray-300 dark:bg-neutral-600 rounded-lg">
		<div class="flex_between_center">
			<h4 class="mb-4 text-gray-900 dark:text-gray-50">Payable Summary</h4>
			<div class="flex items-center gap-2">
				<form action="{{ route('backend_billings_payable_toggle', $payable->id) }}" method="POST"
					class="inline-block">
					@csrf
					<button type="submit">
						{!! (int)$payable?->status === 1 ? \App\Helpers\IconPack::toggleOn(['class' => 'iconPackItem w-8 h-8 text-green-500']) : \App\Helpers\IconPack::toggleOff(['class' => 'iconPackItem w-8 h-8 text-red-500']) !!}</button>
				</form>
				<form action="{{ route('backend_billings_payable_delete', $payable->id) }}" method="POST"
					class="inline-block">
					@csrf
					@method('DELETE')
					<button type="submit" class="deleteButton"
						onclick="return confirm('Are you sure you want to delete this payable?');">
						{!! \App\Helpers\IconPack::trash(['class' => 'iconPackItem w-4 h-4 text-gray-100']) !!}
					</button>
				</form>
			</div>
		</div>

		<div class="space-y-2">
			<div class="grid grid-cols-12 items-center gap-2">
				<p class="col-span-2 text-gray-700 dark:text-gray-100">DB ID</p>
				<b class="col-span-1 dark:text-gray-100">:</b>
				<p class="paragraphDisplay">
					{{ $payable?->id ?? 'N/A' }}
				</p>
			</div>
			<div class="grid grid-cols-12 items-center gap-2">
				<p class="col-span-2 text-gray-700 dark:text-gray-100">Payable UID</p>
				<b class="col-span-1 dark:text-gray-100">:</b>
				<p class="paragraphDisplay">
					{{ $payable?->payable_uid ?? 'N/A' }}
				</p>
			</div>
			<div class="grid grid-cols-12 items-center gap-2">
				<p class="col-span-2 text-gray-700 dark:text-gray-100">Type</p>
				<b class="col-span-1 dark:text-gray-100">:</b>
				<p class="paragraphDisplay">
					{{ class_basename($payable?->payable_type ?? 'N/A') }}
				</p>
			</div>
			<div class="grid grid-cols-12 items-center gap-2">
				<p class="col-span-2 text-gray-700 dark:text-gray-100">Amount (Dues)</p>
				<b class="col-span-1 dark:text-gray-100">:</b>
				<p class="paragraphDisplay">
					{{ $payable?->amount !== null ? '$' . number_format((float)$payable->amount, 2) : 'N/A' }}
				</p>
			</div>
			<div class="grid grid-cols-12 items-center gap-2">
				<p class="col-span-2 text-gray-700 dark:text-gray-100">Paid Amount</p>
				<b class="col-span-1 dark:text-gray-100">:</b>
				<p class="paragraphDisplay">
					{{ $paymentTrx?->paid_amount !== null ? '$' . number_format((float)$paymentTrx->paid_amount / 100, 2) : 'N/A' }}
				</p>
			</div>
			<div class="grid grid-cols-12 items-center gap-2">
				<p class="col-span-2 text-gray-700 dark:text-gray-100">Status</p>
				<b class="col-span-1 dark:text-gray-100">:</b>
				<p class="paragraphDisplay">
					{{ $statusText }}
				</p>
			</div>
			<div class="grid grid-cols-12 items-center gap-2">
				<p class="col-span-2 text-gray-700 dark:text-gray-100">Payment Week Start</p>
				<b class="col-span-1 dark:text-gray-100">:</b>
				<p class="paragraphDisplay">
					{{ $payable?->week_start ? \Carbon\Carbon::parse($payable->week_start)->format('d M Y') : 'N/A' }}
				</p>
			</div>
			<div class="grid grid-cols-12 items-center gap-2">
				<p class="col-span-2 text-gray-700 dark:text-gray-100">Paid At</p>
				<b class="col-span-1 dark:text-gray-100">:</b>
				<p class="paragraphDisplay">
					{{ $payable?->paid_at ? \Carbon\Carbon::parse($payable->paid_at)->timezone($zones['zone'])->format('d M Y, H:i') . ' (UTC' . $zones['offset'] . ')' : 'N/A' }}
				</p>
			</div>
			<div class="grid grid-cols-12 items-center gap-2">
				<p class="col-span-2 text-gray-700 dark:text-gray-100">Created At</p>
				<b class="col-span-1 dark:text-gray-100">:</b>
				<p class="paragraphDisplay">
					{{ $payable?->created_at ? \Carbon\Carbon::parse($payable->created_at)->timezone($zones['zone'])->format('d M Y, H:i') . ' (UTC' . $zones['offset'] . ')' : 'N/A' }}
				</p>
			</div>
		</div>
	</section>

	<section class="mb-4 p-4 bg-gray-300 dark:bg-neutral-600 rounded-lg space-y-2">

		<h4 class="mb-4 text-gray-900 dark:text-gray-50">Cancel Request</h4>

		<div class="grid grid-cols-12 items-center gap-2">
			<p class="col-span-2 text-gray-700 dark:text-gray-100">Is Cancelled?</p>
			<b class="col-span-1 dark:text-gray-100">:</b>
			<p
				class="paragraphDisplay {{ $payable->is_cancelled ? 'text-red-600! dark:text-red-400!' : 'text-green-600! dark:text-green-400!' }}">
				{{ $payable->is_cancelled ? "Yes" : "No" }}
			</p>
		</div>
		<div class="grid grid-cols-12 items-center gap-2">
			<p class="col-span-2 text-gray-700 dark:text-gray-100">Cancel Request At</p>
			<b class="col-span-1 dark:text-gray-100">:</b>
			<p class="paragraphDisplay">
				{{ $payable?->cancel_req_at ? \Carbon\Carbon::parse($payable->cancel_req_at)->timezone($zones['zone'])->format('d M Y, H:i') . ' (UTC' . $zones['offset'] . ')' : 'N/A' }}
			</p>
		</div>
		<div class="grid grid-cols-12 items-center gap-2">
			<p class="col-span-2 text-gray-700 dark:text-gray-100">Reason</p>
			<b class="col-span-1 dark:text-gray-100">:</b>
			<p class="paragraphDisplay">{{ $payable->cancel_reason }}</p>
		</div>
		@if($payable->cancel_req_at !== null && !$payable->is_cancelled && $payable->cancel_resolve !== 'reject')
			<div class="grid grid-cols-12 items-center gap-2">
				<p class="col-span-2 text-gray-700 dark:text-gray-100">Resolve</p>
				<b class="col-span-1 dark:text-gray-100">:</b>
				<div class="paragraphDisplay">
					<a href="{{ route('backend_billings_payable_resolve_cancellation', ['payable' => $payable->id, 'flag' => 'accept']) }}"
						class="saveButton">Accept</a>
					<a href="{{ route('backend_billings_payable_resolve_cancellation', ['payable' => $payable->id, 'flag' => 'reject']) }}"
						class="deleteButton">Reject</a>
				</div>
			</div>
		@else
			<div class="grid grid-cols-12 items-center gap-2">
				<p class="col-span-2 text-gray-700 dark:text-gray-100">Cancelled At:</p>
				<b class="col-span-1 dark:text-gray-100">:</b>
				<p class="paragraphDisplay">{{ $payable?->cancelled_at ? \Carbon\Carbon::parse($payable->cancelled_at)->timezone($zones['zone'])->format('d M Y, H:i') . ' (UTC' . $zones['offset'] . ')' : 'N/A' }}</p>
			</div>
		@endif

	</section>

	<section class="mb-4 p-4 bg-gray-300 dark:bg-neutral-600 rounded-lg">
		<div class="flex_between_center">
			<h4 class="mb-4 text-gray-900 dark:text-gray-50">Student Details</h4>
			@if($student)
				<a href="{{ route('backend_single_user', $student->id) }}">
					{!! \App\Helpers\IconPack::eye(['class' => 'iconPackItem w-5 h-5 text-gray-500 dark:text-gray-100']) !!}
				</a>
			@endif
		</div>

		<div class="space-y-2">
			<div class="grid grid-cols-12 items-center gap-2">
				<p class="col-span-2 text-gray-700 dark:text-gray-100">Student DB ID</p>
				<b class="col-span-1 dark:text-gray-100">:</b>
				<p class="paragraphDisplay">
					{{ $student?->id ?? 'N/A' }}
				</p>
			</div>
			<div class="grid grid-cols-12 items-center gap-2">
				<p class="col-span-2 text-gray-700 dark:text-gray-100">Student UID</p>
				<b class="col-span-1 dark:text-gray-100">:</b>
				<p class="paragraphDisplay">
					{{ $student?->user_uid ?? 'N/A' }}
				</p>
			</div>
			<div class="grid grid-cols-12 items-center gap-2">
				<p class="col-span-2 text-gray-700 dark:text-gray-100">Username</p>
				<b class="col-span-1 dark:text-gray-100">:</b>
				<p class="paragraphDisplay">
					{{ $student?->username ?? 'N/A' }}
				</p>
			</div>
			<div class="grid grid-cols-12 items-center gap-2">
				<p class="col-span-2 text-gray-700 dark:text-gray-100">Name</p>
				<b class="col-span-1 dark:text-gray-100">:</b>
				<p class="paragraphDisplay">
					{{ $student?->name ?? 'N/A' }}
				</p>
			</div>
			<div class="grid grid-cols-12 items-center gap-2">
				<p class="col-span-2 text-gray-700 dark:text-gray-100">Email</p>
				<b class="col-span-1 dark:text-gray-100">:</b>
				<p class="paragraphDisplay">
					{{ $student?->email ?? 'N/A' }}
				</p>
			</div>
		</div>
	</section>

	<section class="mb-4 p-4 bg-gray-300 dark:bg-neutral-600 rounded-lg">
		<h4 class="mb-4 text-gray-900 dark:text-gray-50">Linked Lesson Details</h4>

		<div class="space-y-2">
			<div class="grid grid-cols-12 items-center gap-2">
				<p class="col-span-2 text-gray-700 dark:text-gray-100">Record DB ID</p>
				<b class="col-span-1 dark:text-gray-100">:</b>
				<p class="paragraphDisplay">
					{{ $payable?->payable_id ?? 'N/A' }}
				</p>
			</div>
			<div class="grid grid-cols-12 items-center gap-2">
				<p class="col-span-2 text-gray-700 dark:text-gray-100">Title</p>
				<b class="col-span-1 dark:text-gray-100">:</b>
				<p class="paragraphDisplay">
					{{ $payableObject?->title ?? 'N/A' }}
				</p>
			</div>
			<div class="grid grid-cols-12 items-center gap-2">
				<p class="col-span-2 text-gray-700 dark:text-gray-100">Record UID</p>
				<b class="col-span-1 dark:text-gray-100">:</b>
				<p class="paragraphDisplay">
					{{ $payableObject?->lesson_uid ?? $payableObject?->group_uid ?? 'N/A' }}
				</p>
			</div>
			<div class="grid grid-cols-12 items-center gap-2">
				<p class="col-span-2 text-gray-700 dark:text-gray-100">Raw Type</p>
				<b class="col-span-1 dark:text-gray-100">:</b>
				<p class="paragraphDisplay">
					{{ $payable?->payable_type ?? 'N/A' }}
				</p>
			</div>
		</div>
	</section>

	@if($groupMember)
		<section class="mb-4 p-4 bg-gray-300 dark:bg-neutral-600 rounded-lg">
			<h4 class="mb-4 text-gray-900 dark:text-gray-50">Group Member Details</h4>

			<div class="space-y-2">
				<div class="grid grid-cols-12 items-center gap-2">
					<p class="col-span-2 text-gray-700 dark:text-gray-100">Member DB ID</p>
					<b class="col-span-1 dark:text-gray-100">:</b>
					<p class="paragraphDisplay">
						{{ $groupMember?->id ?? 'N/A' }}
					</p>
				</div>
				<div class="grid grid-cols-12 items-center gap-2">
					<p class="col-span-2 text-gray-700 dark:text-gray-100">Member UID</p>
					<b class="col-span-1 dark:text-gray-100">:</b>
					<p class="paragraphDisplay">
						{{ $groupMember?->member_uid ?? 'N/A' }}
					</p>
				</div>
				<div class="grid grid-cols-12 items-center gap-2">
					<p class="col-span-2 text-gray-700 dark:text-gray-100">Group ID</p>
					<b class="col-span-1 dark:text-gray-100">:</b>
					<p class="paragraphDisplay">
						{{ $groupMember?->group_id ?? 'N/A' }}
					</p>
				</div>
				<div class="grid grid-cols-12 items-center gap-2">
					<p class="col-span-2 text-gray-700 dark:text-gray-100">Member Fees</p>
					<b class="col-span-1 dark:text-gray-100">:</b>
					<p class="paragraphDisplay">
						{{ $groupMember?->fees !== null ? '$' . number_format((float)$groupMember->fees, 2) : 'N/A' }}
					</p>
				</div>
				<div class="grid grid-cols-12 items-center gap-2">
					<p class="col-span-2 text-gray-700 dark:text-gray-100">Member Student</p>
					<b class="col-span-1 dark:text-gray-100">:</b>
					<p class="paragraphDisplay">
						{{ $groupMember?->studentRelatingBackTo_User?->name ?? $groupMember?->studentRelatingBackTo_User?->username ?? 'N/A' }}
					</p>
				</div>
			</div>
		</section>
	@endif

</x-app-layout>

<style>
	.paragraphDisplay {
		grid-column: span 9 / span 9;
		width: 100%;
		padding: 0.5rem;
		border-radius: 0.5rem;
		background-color: rgb(243 244 246);
		color: rgb(17 24 39);
		overflow-wrap: anywhere;
		word-break: break-word;
	}

	.dark .paragraphDisplay {
		background-color: rgb(64 64 64);
		color: rgb(249 250 251);
	}
</style>