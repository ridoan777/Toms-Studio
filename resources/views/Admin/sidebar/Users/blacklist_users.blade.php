<x-app-layout>
	@php
		$breadCrumbs = [
			['label' => 'Users', 'url' => 'backend_all_user', 'icon' => 'userTrippleLine'],
			['label' => 'Blacklisted'],
		];
	 @endphp

	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />

	<!----------- Start Main Widgets ----------->
	<x-indicators.page_header_widget pageName="Blacklisted Users" />
	<!----------- End Main Widgets ----------->

	<!----------- Announcement Banner ----------->
	<x-ui_items.banners.announcement_banner rolesToShow="super_admin"
		message="⚠️Be Careful! You are logged-in as Super Admin. Any changes you do, will have direct-immediate effect without verifications! Better log-in as an Admin." bgColor="bg-yellow-300" />
	<!----------- End Announcement Banner ----------->

	<!----------- Alert Component ----------->
	<x-indicators.alert-component message="User List" />
	<!----------- Alert Component ----------->


	<!-- ---------------- APP AREA Starts ---------------- -->
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
							<th>UID | Username</th>
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
			window.dataTableDisplay = $('#dataTableDisplay').DataTable({
				processing: true,
				serverSide: true,
				responsive: true,
				ordering: true,
				scrollX: true,
				ajax: {
					url: '{{ route('backend_dataTable_blacklisted_users') }}',
					type: 'POST',
					headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
				},
				columns: [
					{ data: 'checkbox', className: 'no-row-click dt-body-center', orderable: false, searchable: false },
					{ data: 'SN', className: 'text-center', orderable: true, searchable: true },
					{ data: 'id', className: 'text-center', orderable: true, searchable: true },
					{ data: 'unique_ids', orderable: true, searchable: true },
					{ data: 'avatar_img', orderable: true, searchable: true },
					{ data: 'name', orderable: true, searchable: true },
					{ data: 'role', orderable: true, searchable: true },
					{ data: 'email', orderable: true, searchable: true },
					{ data: 'created_at_formatted', orderable: true, searchable: true },
					{ data: 'actions', className: 'no-row-click', orderable: true, searchable: true }
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