<x-app-layout>

	@php
		$breadCrumbs = [
			['label' => 'Chat Blocklists', 'url' => 'backend_chat_blocklist_index', 'icon' => 'noMessage'],
			['label' => 'All Reports', 'url' => 'backend_disputes_report_index', 'icon' => 'noMessage'],
			['label' => 'Chat Reports'],
		];
	 @endphp

	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />
	<!-- ---------------- APP AREA Starts ---------------- -->

	<!-- Start Main Widgets -->
	<x-indicators.page_header_widget pageName="Reports: In-App Chats" />
	<!-- End Main Widgets -->


	<!-- Alert Component -->
	<x-indicators.alert-component message="Form Submission" />
	<!-- Alert Component -->

	@php
		$ORDER_IN_PAGE = 1;
		$ACCORDION_1_VISIBILITY = ''; // hidden or ''
	 @endphp

	<!---------------------------------------------------------------------------------------------- accordion -->
	<div class="hs-accordion-group">

		<x-ui_items.accordion.accordion-destroy />

		<div id="hs-destroy-heading-two"
			class="hs-accordion-to-destroy hs-accordion hs-accordion-active:border-gray-200 PrelineAccordionItemParent">

			<x-ui_items.accordion.accordion_toggle_button toggleLabel="Chat Reports History" />

			<div id="hs-destroy-collapse-two"
				class="hs-accordion-content {{ $ACCORDION_1_VISIBILITY }} w-full overflow-hidden transition-[height] duration-300"
				role="region" aria-labelledby="hs-destroy-heading-two">
				<div class="p-3">
					<!-- --------------------------------- -->
					<section
						class="displayArea mx-auto my-5 rounded-lg border-none bg-white p-4 shadow-md shadow-gray-400 dark:bg-neutral-700 dark:shadow-none">
						<div class="my-5">
							<div class="overflow-x-auto">
								<table id="dataTableDisplay"
									class="dataTableDisplay min-w-full text-sm border border-gray-300 bg-white rounded-xl shadow-lg">
									<thead class="text-xs text-gray-700 uppercase bg-gray-100 rounded-t-xl">
										<tr>
											<th>SN</th>
											<th>DB ID</th>
											<th>Victim/Blocker</th>
											<th>Accused/Blocked</th>
											<th>Section</th>
											<th>Content</th>
											<th>Phase</th>
											<th>Label</th>
											<th>Description</th>
											<th>Status</th>
											<th>Created At</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody></tbody>
								</table>
							</div>
						</div>
					</section>
					<!-- --------------------------------- -->
				</div>
			</div>
		</div>
		<!--  -->

	</div>
	<!---------------------------------------------------------------------------------------------- accordion -->


	<!------------ SCRIPT FOR DATA-TABLE ------------>
	<script>
		$(function () {
			window.dataTableDisplay = $('#dataTableDisplay').DataTable({
				processing: true,
				serverSide: true,
				responsive: true,
				ordering: true,
				scrollX: true,
				autoWidth: false,
				ajax: {
					url: '{{ route('backend_chat_reports_datatable') }}',
					type: 'POST',
					headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
				},
				columns: [
					{ data: 'SN', className: 'px-3 py-2 text-center' },
					{ data: 'id', className: 'px-3 py-2 text-center' },
					{ data: 'victim', className: 'px-3 py-2 text-left', orderable: false },
					{ data: 'accused', className: 'px-3 py-2 text-left', orderable: false },
					{ data: 'section', className: 'px-3 py-2 text-center' },
					{ data: 'content', className: 'px-3 py-2 text-center' },
					{ data: 'phase', className: 'px-3 py-2 text-center', orderable: false },
					{ data: 'issue_label', className: 'px-3 py-2 text-left' },
					{ data: 'description', className: 'px-3 py-2 text-left' },
					{ data: 'status_label', className: 'px-3 py-2 text-center' },
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

	<!------------ SCRIPT FOR DATA-TABLE ------------>



	<!-- ---------------- APP AREA Starts ---------------- -->

</x-app-layout>