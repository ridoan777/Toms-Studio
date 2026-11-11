<x-app-layout>

	@php
		$breadCrumbs = [
			['label' => 'Private', 'url' => 'backend_lesson_create', 'icon' => 'plus'],
			['label' => 'Groups', 'url' => 'backend_group_lesson_create', 'icon' => 'plus'],
			['label' => 'Lesson List'],
			['label' => 'Grouped Lesson List', 'url' => 'backend_group_lesson_index', 'icon' => 'music'],
			['label' => 'Lesson Histories', 'url' => 'backend_lesson_histories_index', 'icon' => 'clock'],
		];
	 @endphp

	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />

	<!-- Start Main Widgets -->
	<x-indicators.page_header_widget pageName="All Lessons" />
	<!-- End Main Widgets -->

	<x-ui_items.banners.announcement_banner rolesToShow="admin,super_admin" message="After adding a student to a lesson, wait a few minutes, until the payable/due & class-history records are auto-generated." redirect="backend_billings_payables_index" redirectText="Check dues here" bgColor="bg-yellow-300" textColor="text-gray-800" />

	<!-- Alert Component -->
	<x-indicators.alert-component message="Form Submission" />
	<!-- Alert Component -->

	@php
		$ORDER_IN_PAGE = 1;
		$ACCORDION_1_VISIBILITY = ''; // hidden or ''
	@endphp

	<!-- -------------------------------- APP AREA Starts -------------------------------- -->
	<div class="px-6 py-2">
		<div>
			<a href="{{ route('backend_lesson_create') }}" class="darkButton">+ Create</a>
		</div>
		<!--------->

		<div class="">
			<form action="{{ route('backend_lesson_bulk_delete') }}" method="POST">
				@csrf
				<x-ui_items.datatable.datatable-btn-group exportRoute="backend_projects_spreadsheet_export"
					importRoute="backend_projects_excel_import" spreadsheetButton="false" importButton="false"
					pdfButton="false" deleteButton="true" />
				<!------->
				<table id="dataTableDisplay"
					class="dataTableDisplay min-w-full text-sm border border-gray-300 bg-white rounded-xl shadow-lg">
					<thead class="text-xs text-gray-400 uppercase bg-gray-100 rounded-t-xl">
						<tr>
							<th class="no-row-click dt-body-center">
								<input id="select_all" type="checkbox"
									class="w-4 h-4 rounded border-gray-400 cursor-pointer checked:bg-blue-600 checked:border-blue-600">
							</th>
							<th>SN</th>
							<th>DB ID</th>
							<th class="min-w-20">UID</th>
							<th class="min-w-32">Title</th>
							<th class="min-w-26 text-center">Subject</th>
							<th class="min-w-26 text-center">Type</th>
							<th class="min-w-26">Fees | Last Pay</th>
							<th class="min-w-40">Teacher</th>
							<th class="min-w-40">Student</th>
							<th>Schedule</th>
							<th>Start|Running</th>
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
					url: "{{ route('backend_lesson_datatable') }}",
					type: 'POST',
					headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
				},
				columns: [
					{ data: 'checkbox', className: 'no-row-click dt-body-center', orderable: false, searchable: false },
					{ data: 'SN', className: 'px-3 py-2 text-center' },
					{ data: 'id', className: 'px-3 py-2 text-center' },
					{ data: 'lesson_uid', className: 'px-3 py-2 text-center' },
					{ data: 'title', className: 'px-3 py-2 text-center' },
					{ data: 'phase_subject', className: 'px-3 py-2 text-center', orderable: false, searchable: false },
					{ data: 'type', className: 'px-3 py-2 text-center', orderable: false, searchable: false },
					{ data: 'fees_last_class', className: 'px-3 py-2 text-center', orderable: false, searchable: false },
					{ data: 'teacher', className: 'px-3 py-2 text-center' },
					{ data: 'student', className: 'px-3 py-2 text-center' },
					{ data: 'schedule', className: 'px-3 py-2 text-center text-xs' },
					{ data: 'dates', className: 'px-3 py-2 text-center text-xs' },
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

<style>
	table th {
		text-align: center !important;
	}
</style>