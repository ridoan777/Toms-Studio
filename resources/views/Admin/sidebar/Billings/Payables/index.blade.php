<x-app-layout>

	@php
		$breadCrumbs = [
			['label' => 'Payables'],
			['label' => 'Cancel Requests', 'url' => 'backend_billings_payable_cancel_req_index'],
			['label' => 'Cancelled', 'url' => 'backend_billings_payable_cancellation_index'],
			['label' => 'Paid', 'url' => 'backend_billings_payable_cancellation_index'],
			['label' => 'Unpaid', 'url' => 'backend_billings_payable_unpaid_index'],
			['label' => 'Transactions', 'url' => 'backend_transactions_index', 'icon' => 'dollarSack'],
		];
	@endphp

	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />

	<x-indicators.page_header_widget pageName="All Payables" />

	<x-indicators.alert-component message="Form Submission" />

	@php
		$ORDER_IN_PAGE = 1;
		$ACCORDION_1_VISIBILITY = '';
	@endphp

	<div class="px-6 py-2">
		<div>
			<a href="{{ route('backend_billings_payables_index') }}" class="saveButton">All</a>
			<a href="{{ route('backend_billings_payable_cancel_req_index') }}" class="darkButton">Cancel Requests</a>
			<a href="{{ route('backend_billings_payable_cancellation_index') }}" class="darkButton">Cancelled</a>
			<a href="{{ route('backend_billings_payable_paid_index') }}" class="darkButton">Paid</a>
			<a href="{{ route('backend_billings_payable_unpaid_index') }}" class="darkButton">Unpaid</a>
		</div>
		<form action="{{ route('backend_billings_payable_bulk_delete') }}" method="POST">
			@csrf
			<x-ui_items.datatable.datatable-btn-group exportRoute="backend_projects_spreadsheet_export" importRoute="backend_projects_excel_import" spreadsheetButton="false" importButton="false" pdfButton="false" deleteButton="true" />

			<table id="dataTableDisplay" class="dataTableDisplay min-w-full text-sm border border-gray-300 bg-white rounded-xl shadow-lg">
				<thead class="text-xs text-gray-700 uppercase bg-gray-100 rounded-t-xl">
					<tr>
						<th class="no-row-click dt-body-center">
							<input id="select_all" type="checkbox" class="w-4 h-4 rounded border-gray-400 cursor-pointer checked:bg-blue-600 checked:border-blue-600">
						</th>
						<th>SN</th>
						<th>DB ID</th>
						<th>UID</th>
						<th class="min-w-44">Student</th>
						<th class="min-w-52">Lesson / Group</th>
						<th>Payments</th>
						<th class="min-w-44">Teacher</th>
						<th>Due date</th>
						<th>Amount</th>
						<th>Status</th>
						<th>Created At</th>
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
				order: [[1, 'desc']],
				ajax: {
					url: "{{ route('backend_billings_payables_datatable') }}",
					type: 'POST',
					headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
				},
				columns: [
					{ data: 'checkbox', className: 'no-row-click dt-body-center', orderable: false, searchable: false },
					{ data: 'SN', className: 'px-3 py-2 text-center' },
					{ data: 'id', className: 'px-3 py-2 text-center' },
					{ data: 'payable_uid', className: 'min-w-20 max-w-24 px-3 py-2 text-center text-xs' },
					{ data: 'student', className: 'px-3 py-2 text-center', orderable: false, searchable: true },
					{ data: 'lesson_group', className: 'px-3 py-2 text-center', orderable: false, searchable: false },
					{ data: 'paid_at_display', className: 'px-3 py-2 text-center' },
					{ data: 'teacher', className: 'px-3 py-2 text-center', orderable: false, searchable: false },
					{ data: 'week_start_display', className: 'px-3 py-2 text-center' },
					{ data: 'amount_display', className: 'px-3 py-2 text-center', orderable: true, searchable: false },
					{ data: 'status_label', className: 'px-3 py-2 text-center', orderable: true, searchable: false },
					{ data: 'created_at_formatted', className: 'px-3 py-2 text-center' },
					{ data: 'actions', className: 'px-3 py-2 text-center', orderable: false, searchable: false }
				],
				pageLength: 25,
				createdRow: function (row, data) {
					dtApplyRowEvents(row, data);
				}
			});
		});
	</script>

	<style>
		table th {
			text-align: center !important;
		}
	</style>

</x-app-layout>
