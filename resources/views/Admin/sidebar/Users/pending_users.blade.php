<x-app-layout>
	@php
		$breadCrumbs = [
			['label' => 'Users', 'url' => 'backend_all_user', 'icon' => 'userTrippleLine'],
			['label' => 'Pending Users'],
		];
	 @endphp

	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />


	<!-- Start Main Widgets -->
	<x-indicators.page_header_widget pageName="Pending Users" />
	<!-- End Main Widgets -->


	<!-- Alert Component -->
	<x-indicators.alert-component message="User List" />
	<!-- Alert Component -->

	<!-- ---------------- APP AREA Starts ---------------- -->
	<h4 class="mx-8 text-gray-500 text-sm text-bold">If not verified, pending users will be auto deleted within the spare
		time of 6 hours when this page is visited.</h4>
	<div class="p-6">

		<div class="">
			<form action="{{ route('backend_user_bulk_delete', "backend_user_bulk_delete") }}" method="POST">
				@csrf

				<x-ui_items.datatable.datatable-btn-group exportRoute="backend_projects_spreadsheet_export"
					importRoute="backend_projects_excel_import" spreadsheetButton="false" importButton="false"
					deleteButton="true" />
				<!----->
				<table id="dataTableDisplay" class="min-w-full text-sm border border-gray-100 bg-white shadow-lg">
					<thead class="text-xs text-gray-700 uppercase bg-gray-300 dark:bg-gray-400 rounded-t-xl">
						<tr>
							<th class="no-row-click dt-body-center">
								<input id="select_all" type="checkbox"
									class="w-4 h-4 rounded border-gray-400 cursor-pointer checked:bg-blue-600 checked:border-blue-600">
							</th>
							<th>SN</th>
							<th>DB ID</th>
							<th class="min-w-32 max-w-48">UID | Username</th>
							<th>Avatar</th>
							<th class="min-w-32 max-w-40 text-left!">Name</th>
							<th>Role:Name|Key</th>
							<th>Email | Phone</th>
							<th>Joined</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</form>
		</div>
	</div>

	<!-------------------- DATATABLE -------------------->
	<script>
		$(function () {
			const table = $('#dataTableDisplay').DataTable({
				processing: true,
				serverSide: true,
				responsive: true,
				ajax: {
					url: "{{ route('backend_dataTable_pending_users') }}",
					type: 'POST',
					headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
				},
				columns: [
					{ data: 'checkbox', className: 'no-row-click dt-body-center', orderable: false, searchable: false },
					{ data: 'SN', className: 'text-center', orderable: false, searchable: false },
					{ data: 'id', className: 'text-center', orderable: false, searchable: false },
					{ data: 'unique_ids', orderable: false, searchable: false },
					{ data: 'avatar_img', orderable: false, searchable: false },
					{ data: 'name', orderable: false, searchable: false },
					{ data: 'role', orderable: false, searchable: false },
					{ data: 'email', orderable: false, searchable: false },
					{ data: 'created_at_formatted', orderable: false, searchable: false },
					{ data: 'actions', className: 'no-row-click', orderable: false, searchable: false }
				],
				pageLength: 25,
				createdRow: function (row, data) {
					dtApplyRowEvents(row, data);	// Clickable rows
				}
			});
		});
	</script>
	<!-------------------- DATATABLE -------------------->



</x-app-layout>