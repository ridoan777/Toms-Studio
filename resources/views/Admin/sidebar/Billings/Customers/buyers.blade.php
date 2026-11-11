<x-app-layout>

	@php
		$breadCrumbs = [
			['label' => 'Customers', 'url' => 'backend_billings_customers_index', 'icon' => 'userTrippleLine'],
			['label' => 'Buyers', 'icon' => 'userTripple']
		];
	 @endphp

	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />

	<!-- Start Main Widgets -->
	<x-indicators.page_header_widget pageName="Customers: All Payers/Buyers" />
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
			<form action="{{ route('backend_project_bulk_delete') }}" method="POST">
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
							<th>UID</th>
							<th>Title</th>
							<th class="min-w-16">Image</th>
							<th class="min-w-44">Manager</th>
							<th class="min-w-16 text-center">Phase</th>
							<th>Progress</th>
							<th>Location</th>
							<th>Start Date</th>
							<th>Target Date</th>
							<th>Description</th>
							<th>Status</th>
							<th class="min-w-16">Created At</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</form>
		</div>
	</div>
	<!-- --------------------------------- -->
	<x-ui_items.modals.save-file-modal id="hs-confirm-modal" title="Import File"
		message="Upload your Excel/CSV file to process." formAction="#" formMethod="POST" confirmText="Upload"
		cancelText="Cancel" instructionImg="site_assets/report_upload_guide/excel_projects_import.png" />

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
					url: '{{ route('backend_project_datatable') }}',
					type: 'POST',
					headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
				},
				columns: [
					{ data: 'checkbox', className: 'no-row-click dt-body-center', orderable: false, searchable: false },
					{ data: 'SN', className: 'px-3 py-2 text-center' },
					{ data: 'id', className: 'px-3 py-2 text-center' },
					{ data: 'project_uid', className: 'px-3 py-2 text-center' },
					{ data: 'title', className: 'px-3 py-2 text-left' },
					{ data: 'image_preview', className: 'px-3 py-2 text-center', orderable: false, searchable: false },
					{ data: 'manager', className: 'px-3 py-2 text-left' },
					{ data: 'phase', className: 'px-3 py-2 text-center' },
					{ data: 'progress', className: 'px-3 py-2 text-center' },
					{ data: 'location', className: 'px-3 py-2 text-center' },
					{ data: 'start_date', className: 'px-3 py-2 text-center' },
					{ data: 'target_date', className: 'px-3 py-2 text-center' },
					{ data: 'description_short', className: 'px-3 py-2 text-left' },
					{ data: 'status_label', className: 'px-3 py-2 text-center', orderable: false, searchable: false },
					{ data: 'created_at_formatted', className: 'px-3 py-2 text-center' },
					{ data: 'actions', className: 'px-3 py-2 text-center', orderable: false, searchable: false }
				],
				pageLength: 25,
				createdRow: function (row, data) {
					dtApplyRowEvents(row, data);
				}
			});
			// window.dataTableDisplay = table;
		});
	</script>
	<!------------ SCRIPT FOR DATA-TABLE ------------>


</x-app-layout>