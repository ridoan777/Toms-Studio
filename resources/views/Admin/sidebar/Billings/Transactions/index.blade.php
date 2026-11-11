<x-app-layout>

	@php
		$breadCrumbs = [
			['label' => 'Payables', 'url' => 'backend_billings_payables_index', 'icon' => 'dollarSack'],
			['label' => 'Transactions'],
		];
	@endphp

	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />

	<x-indicators.page_header_widget pageName="All Transactions" />

	<x-indicators.alert-component message="Form Submission" />

	<div class="px-6 py-2">
		<form action="{{ route('backend_transactions_bulk_delete') }}" method="POST">
			@csrf
			<x-ui_items.datatable.datatable-btn-group exportRoute="backend_projects_spreadsheet_export"
				importRoute="backend_projects_excel_import" spreadsheetButton="false" importButton="false" pdfButton="false"
				deleteButton="true" />

			<table id="dataTableDisplay"
				class="dataTableDisplay min-w-full text-sm border border-gray-300 bg-white rounded-xl shadow-lg dark:border-neutral-700 dark:bg-neutral-800">
				<thead
					class="text-xs text-gray-700 uppercase bg-gray-100 rounded-t-xl dark:bg-neutral-700 dark:text-gray-100">
					<tr>
						<th class="no-row-click dt-body-center">
							<input id="select_all" type="checkbox"
								class="w-4 h-4 rounded border-gray-400 cursor-pointer checked:bg-blue-600 checked:border-blue-600">
						</th>
						<th>SN</th>
						<th>DB ID</th>
						<th>TRX UID</th>
						<th class="min-w-44">Payer</th>
						<th>Payment</th>
						<th>Phase</th>
						<th>Paid</th>
						<th>Stripe Fee</th>
						<th>Takeaway</th>
						<th class="min-w-52">Created At</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</form>
	</div>

	<script>
		$(function () {
			window.dataTableDisplay = $('#dataTableDisplay').DataTable({
				processing: true,
				serverSide: true,
				responsive: true,
				ordering: true,
				scrollX: true,
				order: [[11, 'desc']],
				ajax: {
					url: "{{ route('backend_transactions_datatable') }}",
					type: 'POST',
					headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
				},
				columns: [
					{ data: 'checkbox', className: 'no-row-click dt-body-center', orderable: false, searchable: false },
					{ data: 'SN', className: 'px-3 py-2 text-center' },
					{ data: 'id', className: 'px-3 py-2 text-center' },
					{ data: 'trx_uid', className: 'min-w-24 px-3 py-2 text-center text-xs' },
					{ data: 'payer', className: 'px-3 py-2 text-center', searchable: true },
					{ data: 'payment_status_label', className: 'px-3 py-2 text-center', searchable: true },
					{ data: 'trx_phase_label', className: 'px-3 py-2 text-center', searchable: true },
					{ data: 'paid_amount_display', className: 'px-3 py-2 text-center', searchable: true },
					{ data: 'stripe_fee_display', className: 'px-3 py-2 text-center', searchable: true },
					{ data: 'takeaway_amount_display', className: 'px-3 py-2 text-center', searchable: true },
					{ data: 'created_at_formatted', className: 'px-3 py-2 text-center text-xs', searchable: true },
					{ data: 'actions', className: 'px-3 py-2 text-center', orderable: false, searchable: false }
				],
				pageLength: 25,
				createdRow: function (row, data) {
					dtApplyRowEvents(row, data);
				}
			});
		});
	</script>

</x-app-layout>