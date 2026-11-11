<x-app-layout>

	@php
		$breadCrumbs = [
			['label' => 'Lesson List', 'url' => 'backend_lesson_index', 'icon' => 'music'],
			['label' => 'Grouped Lesson List', 'url' => 'backend_group_lesson_index', 'icon' => 'music'],
			['label' => 'Create Note', 'url' => 'backend_lesson_note_create', 'icon' => 'plus'],
			['label' => 'All Notes'],
		];
	 @endphp

	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />

	<!-- Start Main Widgets -->
	<x-indicators.page_header_widget pageName="All Lesson Notes" />
	<!-- End Main Widgets -->


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
			<form action="{{ route('backend_lesson_note_bulk_delete') }}" method="POST">
				@csrf
				<x-ui_items.datatable.datatable-btn-group exportRoute="backend_projects_spreadsheet_export" importRoute="backend_projects_excel_import" spreadsheetButton="false" importButton="false" pdfButton="false" deleteButton="true" />
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
							<th class="">UID</th>
							<th class="min-w-32">Note Title</th>
							<th class="min-w-32">Description</th>
							<th class="min-w-36 text-center">Lesson</th>
							<th class="min-w-36 text-center">Group</th>
							<th class="min-w-20 text-center">Note Resources</th>
							<th class="min-w-40">Teacher</th>
							<th class="min-w-40">Student</th>
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
					url: "{{ route('backend_lesson_note_datatable') }}",
					type: 'POST',
					headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
				},
				columns: [
					{ data: 'checkbox', className: 'no-row-click dt-body-center', orderable: true, searchable: true },
					{ data: 'SN', className: 'px-3 py-2 text-center' },
					{ data: 'id', className: 'px-3 py-2 text-center' },
					{ data: 'note_uid', className: 'px-3 py-2 text-sm text-center' },
					{ data: 'title', className: 'max-w-32 px-3 py-2 text-center' },
					{ data: 'note_short', className: 'px-3 py-2 text-center', orderable: true },
					{ data: 'lesson_details', className: 'px-3 py-2 text-center', orderable: true },
					{ data: 'group_details', className: 'px-3 py-2 text-center', orderable: true },
					{ data: 'resources', className: 'px-3 py-2 text-center', orderable: true, searchable: true },
					{ data: 'teacher', className: 'px-3 py-2 text-center', orderable: true },
					{ data: 'student', className: 'px-3 py-2 text-center', orderable: true },
					{ data: 'status_label', className: 'px-3 py-2 text-center', orderable: true, searchable: true },
					{ data: 'created_at_formatted', className: 'px-3 py-2 text-sm text-center' },
					{ data: 'actions', className: 'px-3 py-2 text-center', orderable: true, searchable: true }
				],
				pageLength: 25,
				createdRow: function (row, data) {
					dtApplyRowEvents(row, data);
				}
			});
		});
	</script>
	<!------------ SCRIPT FOR DATA-TABLE ------------>

</x-app-layout>

<style>
	table th{
		text-align: center !important;
	}
</style>