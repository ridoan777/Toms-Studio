<x-app-layout>

	@php
		$breadCrumbs = [
			['label' => 'Billings', 'url' => 'backend_billings_customers_index', 'icon' => 'dollarSack'],
			['label' => 'Transactions', 'url' => 'backend_transactions_index', 'icon' => 'dollarSackLine'],
			['label' => $transaction?->trx_uid ?? 'Single Transaction'],
		];

		$zones = \App\Helpers\Ui\ConvertDynamicTimezone::handle();
		$trxPhase = $transaction?->trx_phase?->value ?? $transaction?->trx_phase ?? 'N/A';
		$statusText = (int) $transaction?->status === 1 ? 'Active' : 'Inactive';
		$formatMoney = fn($value, $currency = null) => $value !== null ? number_format((float) $value / 100, 2) . ($currency ? ' ' . strtoupper($currency) : '') : 'N/A';
		$formatDate = fn($value) => $value ? \Carbon\Carbon::parse($value, 'UTC')->timezone($zones['zone'])->format('d M Y, H:i') . ' (UTC' . $zones['offset'] . ')' : 'N/A';

		$transactionDetails = [
			'DB ID' => $transaction?->id ?? 'N/A',
			'TRX UID' => $transaction?->trx_uid ?? 'N/A',
			'User ID' => $transaction?->user_id ?? 'N/A',
			'Snapshot User ID' => $transaction?->ss_user_id ?? 'N/A',
			'Snapshot Email' => $transaction?->ss_user_email ?? 'N/A',
			'Payment Status' => $transaction?->payment_status ?? 'N/A',
			'Transaction Phase' => $trxPhase,
			'Currency' => strtoupper($transaction?->currency ?? 'N/A'),
			'Paid Amount' => $formatMoney($transaction?->paid_amount, $transaction?->currency),
			'Stripe Fee' => $formatMoney($transaction?->stripe_fee, $transaction?->currency),
			'Exchange Rate' => $transaction?->exchange_rate ?? 'N/A',
			'Takeaway Amount' => $formatMoney($transaction?->takeaway_amount, $transaction?->currency),
			'Payment Intent' => $transaction?->payment_intent ?? 'N/A',
			'Checkout Session ID' => $transaction?->stripe_checkout_session_id ?? 'N/A',
			'Note' => $transaction?->note ?? 'N/A',
			'Status' => $statusText,
			'Created At' => $formatDate($transaction?->created_at),
			'Updated At' => $formatDate($transaction?->updated_at),
		];
	@endphp

	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />

	<x-indicators.page_header_widget pageName="Transaction: {{ $transaction?->trx_uid ?? 'N/A' }}" />

	<x-indicators.alert-component message="Transaction Details" />

	<div class="mb-4 flex items-center justify-end gap-2">
		<form action="{{ route('backend_transactions_toggle', $transaction?->id) }}" method="POST" class="toggleForm inline-flex items-center">
			@csrf
			<button type="submit" class="showButton">Toggle</button>
		</form>
		<form action="{{ route('backend_transactions_delete', $transaction?->id) }}" method="POST" class="deleteForm inline-flex items-center">
			@csrf
			@method('DELETE')
			<button type="submit" class="deleteButton" onclick="return confirm('Are you sure you want to delete this transaction?');">
				{!! \App\Helpers\IconPack::trash(['class' => 'iconPackItem w-4 h-4 text-gray-100']) !!}
			</button>
		</form>
	</div>

	<section class="mb-4 p-4 bg-gray-300 dark:bg-neutral-600 rounded-lg">
		<h4 class="mb-4 text-gray-900 dark:text-gray-50">Transaction Summary</h4>

		<div class="space-y-2">
			@foreach($transactionDetails as $label => $value)
				<div class="grid grid-cols-12 items-center gap-2">
					<p class="col-span-2 text-gray-700 dark:text-gray-100">{{ $label }}</p>
					<b class="col-span-1 dark:text-gray-100">:</b>
					<p class="col-span-9 w-full p-2 rounded-lg bg-gray-100 text-gray-900 break-words dark:bg-neutral-700 dark:text-gray-50">{{ $value }}</p>
				</div>
			@endforeach
		</div>
	</section>

	<section class="mb-4 p-4 bg-gray-300 dark:bg-neutral-600 rounded-lg">
		<h4 class="mb-4 text-gray-900 dark:text-gray-50">Payer Details</h4>

		<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
			<div class="p-3 rounded-lg bg-gray-100 dark:bg-neutral-700">
				<p class="text-xs text-gray-500 dark:text-gray-300">Current User</p>
				<p class="font-medium text-gray-900 dark:text-gray-50">{{ $transaction?->trxRelatingBackTo_User?->name ?? 'N/A' }}</p>
				<p class="text-sm text-gray-600 dark:text-gray-300">{{ $transaction?->trxRelatingBackTo_User?->email ?? 'N/A' }}</p>
			</div>
			<div class="p-3 rounded-lg bg-gray-100 dark:bg-neutral-700">
				<p class="text-xs text-gray-500 dark:text-gray-300">Snapshot User</p>
				<p class="font-medium text-gray-900 dark:text-gray-50">{{ $transaction?->trxRelatingBackTo_SnapshotUser?->name ?? 'N/A' }}</p>
				<p class="text-sm text-gray-600 dark:text-gray-300">{{ $transaction?->ss_user_email ?? 'N/A' }}</p>
			</div>
		</div>
	</section>

	<section class="mb-4 p-4 bg-gray-300 dark:bg-neutral-600 rounded-lg">
		<h4 class="mb-4 text-gray-900 dark:text-gray-50">Stripe / Customer Payloads</h4>

		<div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
			<div>
				<p class="mb-2 text-gray-700 dark:text-gray-100">Card Details</p>
				<pre class="min-h-32 overflow-auto rounded-lg bg-gray-100 p-3 text-xs text-gray-900 dark:bg-neutral-700 dark:text-gray-50">{{ json_encode($transaction?->card_details ?? [], JSON_PRETTY_PRINT) }}</pre>
			</div>
			<div>
				<p class="mb-2 text-gray-700 dark:text-gray-100">Customer Details</p>
				<pre class="min-h-32 overflow-auto rounded-lg bg-gray-100 p-3 text-xs text-gray-900 dark:bg-neutral-700 dark:text-gray-50">{{ json_encode($transaction?->customer_details ?? [], JSON_PRETTY_PRINT) }}</pre>
			</div>
			<div>
				<p class="mb-2 text-gray-700 dark:text-gray-100">Event IDs</p>
				<pre class="min-h-32 overflow-auto rounded-lg bg-gray-100 p-3 text-xs text-gray-900 dark:bg-neutral-700 dark:text-gray-50">{{ json_encode($transaction?->event_ids ?? [], JSON_PRETTY_PRINT) }}</pre>
			</div>
		</div>
	</section>

	<section class="mb-4 p-4 bg-gray-300 dark:bg-neutral-600 rounded-lg">
		<h4 class="mb-4 text-gray-900 dark:text-gray-50">Payment Items</h4>

		<div class="overflow-x-auto">
			<table class="min-w-full text-sm border border-gray-300 bg-white rounded-lg dark:border-neutral-700 dark:bg-neutral-800">
				<thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-neutral-700 dark:text-gray-100">
					<tr>
						<th class="px-3 py-2 text-center">DB ID</th>
						<th class="px-3 py-2 text-center">TRX ID</th>
						<th class="px-3 py-2 text-center">Item UID</th>
						<th class="px-3 py-2 text-center">Lesson ID</th>
						<th class="px-3 py-2 text-center">Group ID</th>
						<th class="px-3 py-2 text-center">Payer</th>
						<th class="px-3 py-2 text-center">Student</th>
						<th class="px-3 py-2 text-center">Phase</th>
						<th class="px-3 py-2 text-center">Currency</th>
						<th class="px-3 py-2 text-center">Amount</th>
						<th class="px-3 py-2 text-center">Fee Share</th>
						<th class="px-3 py-2 text-center">Note</th>
						<th class="px-3 py-2 text-center">Status</th>
						<th class="px-3 py-2 text-center">Created At</th>
						<th class="px-3 py-2 text-center">Updated At</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
					@forelse($transaction?->trxRelationWith_Items ?? [] as $item)
						<tr class="text-gray-800 dark:text-gray-100">
							<td class="px-3 py-2 text-center">{{ $item?->id ?? 'N/A' }}</td>
							<td class="px-3 py-2 text-center">{{ $item?->payment_trx_id ?? 'N/A' }}</td>
							<td class="px-3 py-2 text-center text-xs">{{ $item?->payment_item_uid ?? 'N/A' }}</td>
							<td class="px-3 py-2 text-center">{{ $item?->lesson_id ?? 'N/A' }}</td>
							<td class="px-3 py-2 text-center">{{ $item?->group_id ?? 'N/A' }}</td>
							<td class="px-3 py-2 text-center">
								<div class="text-xs">
									<p>{{ $item?->payer_id ?? 'N/A' }}</p>
									<p>{{ $item?->ss_payer_name ?? 'N/A' }}</p>
									<p>{{ $item?->ss_payer_email ?? 'N/A' }}</p>
								</div>
							</td>
							<td class="px-3 py-2 text-center">
								<div class="text-xs">
									<p>{{ $item?->student_id ?? 'N/A' }}</p>
									<p>{{ $item?->ss_student_name ?? 'N/A' }}</p>
									<p>{{ $item?->ss_student_email ?? 'N/A' }}</p>
								</div>
							</td>
							<td class="px-3 py-2 text-center">{{ $item?->phase?->value ?? $item?->phase ?? 'N/A' }}</td>
							<td class="px-3 py-2 text-center">{{ strtoupper($item?->ss_currency ?? 'N/A') }}</td>
							<td class="px-3 py-2 text-center">{{ $formatMoney($item?->ss_amount, $item?->ss_currency) }}</td>
							<td class="px-3 py-2 text-center">{{ $formatMoney($item?->stripe_fee_share, $item?->ss_currency) }}</td>
							<td class="px-3 py-2 text-center text-xs">{{ $item?->note ?? 'N/A' }}</td>
							<td class="px-3 py-2 text-center">{{ (int) $item?->status === 1 ? 'Active' : 'Inactive' }}</td>
							<td class="px-3 py-2 text-center">{{ $formatDate($item?->created_at) }}</td>
							<td class="px-3 py-2 text-center">{{ $formatDate($item?->updated_at) }}</td>
						</tr>
					@empty
						<tr>
							<td colspan="15" class="px-3 py-6 text-center text-gray-500 dark:text-gray-300">No payment items found.</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>
	</section>

</x-app-layout>
