<x-app-layout>

	@php
		$breadCrumbs = [
			['label' => 'Customers', 'url' => 'backend_billings_customers_index', 'icon' => 'userTrippleLine'],
			['label' => 'Subscribers', 'icon' => 'userTripple']
		];
	 @endphp

	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />

	<!-- Start Main Widgets -->
	<x-indicators.page_header_widget pageName="Customers: All Subscribers" />
	<!-- End Main Widgets -->

	<!-- Alert Component -->
	<x-indicators.alert-component message="Form Submission" />
	<!-- Alert Component -->

	@php
		$ORDER_IN_PAGE = 1;
		$ACCORDION_1_VISIBILITY = 'hidden'; // hidden or ''
		$ACCORDION_2_VISIBILITY = '';
		$ACCORDION_3_VISIBILITY = '';
	@endphp

	<!-- -------------------------------- APP AREA Starts -------------------------------- -->
	<div class="px-6 py-2">

		<div class="">
			<form action="{{ route('backend_billings_subscriber_bulk_delete') }}" method="POST">
				@csrf
				<x-ui_items.datatable.datatable-btn-group exportRoute="backend_projects_spreadsheet_export" importRoute="backend_projects_excel_import" spreadsheetButton="false" importButton="false" pdfButton="false" deleteButton="true" />
				<!------->
				<table id="dataTableDisplay"
					class="dataTableDisplay min-w-full text-sm border border-gray-300 bg-white rounded-xl shadow-lg">
					<thead class="text-xs text-gray-700 uppercase bg-gray-100 rounded-t-xl">
						<tr>
							<th class="no-row-click dt-body-center">
								<input id="select_all" type="checkbox"
									class="w-4 h-4 rounded border-gray-400 cursor-pointer checked:bg-blue-600 checked:border-blue-600">
							</th>
							<th>SN</th>
							<th>DB ID</th>
							<th class="min-w-44">User</th>
							<th class="min-w-32">Tier</th>
							<th class="min-w-32 text-center">Service</th>
							<th>Payment Status</th>
							<th>Sus Status</th>
							<th>Amount</th>
							<th>Duration (days)</th>
							<th class="min-w-32">Next Renewal</th>
							<th class="min-w-32">Card</th>
							<th>Status</th>
							<th class="min-w-24">Created At</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</form>
		</div>
	</div>
	<!-- --------------------------------- -->

	<!-- -------------------------------- APP AREA Starts -------------------------------- -->

	<!------------ SCRIPT FOR DATA-TABLE ------------>
	<script>
		$(function () {
			window.dataTableDisplay = $('#dataTableDisplay').DataTable({
				processing: true,
				serverSide: true,
				responsive: true,
				ordering: true,
				scrollX: true,
				ajax: {
					url: '{{ route('backend_billings_subscribers_datatable') }}',
					type: 'POST',
					headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
				},
				columns: [
					{ data: 'checkbox', orderable: false, searchable: false },
					{ data: 'SN' },
					{ data: 'id' },
					{ data: 'user' },
					{ data: 'tier' },
					{ data: 'service' },
					{ data: 'payment_status' },
					{ data: 'sub_status' },
					{ data: 'amount_display' },
					{ data: 'duration_data' },
					{ data: 'next_renewal_at', className: 'text-xs', searchable: true },
					{ data: 'card', orderable: false, searchable: false },
					{ data: 'status_label', orderable: true },
					{ data: 'created_at_formatted' },
					{ data: 'actions', orderable: false, searchable: false }
				],
				pageLength: 25,
				createdRow: function (row, data) {
					dtApplyRowEvents(row, data);
				}
			});
		})
	</script>
	<!------------ SCRIPT FOR DATA-TABLE ------------>


</x-app-layout>