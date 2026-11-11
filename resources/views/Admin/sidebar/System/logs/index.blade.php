<x-app-layout>

	@php
		$breadCrumbs = [
			['label' => 'Workout', 'url' => 'backend_project_create', 'icon' => 'cubic'],
			['label' => 'All Categories', 'url' => 'dashboard', 'icon' => 'layers'],
			['label' => 'Create Plans', 'url' => 'backend_project_create', 'icon' => 'handsHolding'],
			['label' => 'All Plans', 'url' => 'backend_project_index', 'icon' => 'documentLined'],
			['label' => 'Create Exercise', 'url' => 'backend_project_exercise_create', 'icon' => 'dumbbell'],
			['label' => 'All Exercises'],
		];
	 @endphp

	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />
	<!-- ---------------- APP AREA Starts ---------------- -->

	<!-- Start Main Widgets -->
	<x-indicators.page_header_widget pageName="Workout: All Exercises" />
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

			<x-ui_items.accordion.accordion_toggle_button toggleLabel="Lists of Available Exercises/Classes" />

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
											<th>Title</th>
											<th>Category</th>
											<th>Plan/Course</th>
											<th>Thumbnail</th>
											<th>Description</th>
											<th>Burn | Duration</th>
											<th>Reps | Sets</th>
											<th>Likes | Comments</th>
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
			const table = $('#dataTableDisplay').DataTable({
				processing: true,
				serverSide: true,
				responsive: true,
				ajax: {
					url: '{{ route('backend_project_exercise_datatable') }}',
					type: 'POST',
					headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
				},
				columns: [
					{ data: 'SN', className: 'px-3 py-2 text-center' },
					{ data: 'id', className: 'px-3 py-2 text-center' },
					{ data: 'title', className: 'px-3 py-2 text-left' },
					{ data: 'category_name', className: 'px-3 py-2 text-center' },
					{ data: 'plan_title', className: 'px-3 py-2 text-center' },
					{ data: 'image_preview', className: 'px-3 py-2 text-center', orderable: false, searchable: false },
					{ data: 'description_short', className: 'px-3 py-2 text-left' },
					{ data: 'burn_duration', className: 'px-3 py-2 text-center' },
					{ data: 'reps_sets', className: 'px-3 py-2 text-center' },
					{ data: 'likes_comments', className: 'px-3 py-2 text-center' },
					{ data: 'status_label', className: 'px-3 py-2 text-center', orderable: false, searchable: false },
					{ data: 'created_at_formatted', className: 'px-3 py-2 text-center' },
					{ data: 'actions', className: 'px-3 py-2 text-center', orderable: false, searchable: false }
				],
				pageLength: 25,
				createdRow: function (row, data, dataIndex) {
					$(row)
						.css('cursor', 'pointer')
						.attr('title', 'Click to edit')
						.on('click', function (e) {
							if (!$(e.target).closest('form, button, a, img').length) {
								window.location.href = data.show_url;
							}
						})
						.on('mouseenter', function () {
							$(this).css({ 'background-color': '#364153', 'color': '#fff' });
						})
						.on('mouseleave', function () {
							$(this).css({ 'background-color': '', 'color': '' });
						});
				}
			});

			$(document).on('submit', '.toggleForm', function (e) {
				e.preventDefault();
				const form = $(this);
				$.ajax({
					url: form.attr('action'),
					type: 'POST',
					data: form.serialize(),
					success: function () { table.ajax.reload(null, false); },
					error: function () { alert('Error toggling status.'); }
				});
			});

			$(document).on('submit', '.deleteForm', function (e) {
				e.preventDefault();
				if (confirm('Are you sure you want to delete this exercise?')) {
					this.submit();
				}
			});
		});
	</script>
	<!------------ SCRIPT FOR DATA-TABLE ------------>


	<style>
		table thead th {
			text-align: center !important;
		}

		.dataTableDisplay tbody tr:nth-child(even) {
			background-color: #e5e7eb;
			/* bg-gray-200 */
		}

		.dataTableDisplay tbody tr:nth-child(odd) {
			background-color: #f3f4f6;
			/* bg-gray-100 */
		}
	</style>



	<!-- ---------------- APP AREA Starts ---------------- -->

</x-app-layout>