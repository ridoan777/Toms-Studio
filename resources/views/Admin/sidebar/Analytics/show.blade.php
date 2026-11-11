<x-app-layout>

	@php
		$breadCrumbs = [
			['label' => 'Project List', 'url' => 'backend_project_index', 'icon' => 'layers'],
			['label' => 'Inventory', 'url' => 'backend_tools_tool_index', 'icon' => 'repair'],
			['label' => 'Tool'],
		];
	@endphp

	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />
	<!-- ---------------- APP AREA Starts ---------------- -->

	<!-- Start Main Widgets -->
	<x-indicators.page_header_widget pageName="Inventory Record" />
	<!-- End Main Widgets -->


	<!-- Alert Component -->
	<x-indicators.alert-component message="Project Modification" />
	<!-- Alert Component -->

	@php
		$ACCORDION_1_VISIBILITY = ''; // hidden or ''
	@endphp

	<!---------------------------------------------------------------------------------------------- accordion -->
	<div class="hs-accordion-group">

		<x-ui_items.accordion.accordion-destroy />

		<div id="hs-destroy-heading-one"
			class="hs-accordion-to-destroy hs-accordion hs-accordion-active:border-gray-200 PrelineAccordionItemParent">

			<x-ui_items.accordion.accordion_toggle_button toggleLabel="View an Inventory Tool" />

			<div id="hs-destroy-collapse-one"
				class="hs-accordion-content {{ $ACCORDION_1_VISIBILITY }} w-full overflow-hidden transition-[height] duration-300"
				role="region" aria-labelledby="hs-destroy-heading-one">
				<div class="p-3">
					<!-- --------------------- -->
					<section
						class="displayArea mx-auto my-5 rounded-lg border-none bg-white p-4 shadow-md shadow-gray-400 dark:bg-neutral-700 dark:shadow-none">
						<div class="my-5">
							<form method="POST" action="{{----}}"
								class="rounded-lg p-2 dark:bg-neutral-700" enctype="multipart/form-data">

								@csrf

								<!-- IDENTIFIER INPUTS -->
								<input type="hidden" name="flag" value="update">
								<input type="hidden" name="id" value="{{ $singleTool?->id }}">
								<!-- IDENTIFIER INPUTS -->

								<div>

									<!-- TOOL UID -->
									<div class="mb-5 mt-2">
										<label class="inputLabelMedium">Tool UID</label>
										<input type="text" class="normalInput" value="{{ $singleTool?->tool_uid }}" readonly>
									</div>

									<!-- PHASE SELECT (hidden for now, same pattern as deliveries) -->
									<div class="hidden mb-5 mt-2">
										<label class="inputLabelMedium">Change Tool Phase/Status</label>
										<select name="phase" class="dropdownSelectInputs">
											<option value="available" {{ $singleTool->phase == 'available' ? 'selected' : '' }}>
												Available</option>
											<option value="in_use" {{ $singleTool->phase == 'in_use' ? 'selected' : '' }}>In Use
											</option>
											<option value="maintenance" {{ $singleTool->phase == 'maintenance' ? 'selected' : '' }}>
												Maintenance</option>
										</select>
									</div>


									<!-- TOOL DETAILS BOX -->
									<div
										class="mb-5 mt-2 p-3 rounded-lg bg-gray-100 dark:bg-neutral-800 dark:border dark:border-neutral-600">

										<h2 class="text-lg font-semibold mb-3 text-gray-700 dark:text-gray-200">
											Tool Details
										</h2>

										<ul class="text-sm space-y-1 text-gray-700 dark:text-gray-300">

											<li><b class="dark:text-gray-200">Project:</b>
												{{ $singleTool['project_details']['title'] ?? '—' }}
											</li>

											<li><b class="dark:text-gray-200">Created By:</b>
												{{ $singleTool['tool_creator']['name'] ?? '—' }}
												({{ $singleTool['tool_creator']['user_role'] ?? '—' }})
											</li>

											<li><b class="dark:text-gray-200">Email:</b>
												{{ $singleTool['tool_creator']['email'] ?? '—' }}
											</li>

											<li><b class="dark:text-gray-200">Model:</b>
												{{ $singleTool->model ?? '—' }}
											</li>

											<li><b class="dark:text-gray-200">Additional Note:</b>
												{{ $singleTool->additional_note ?? '—' }}
											</li>

											<!-- CURRENT PHASE BADGE -->
											@php
												$phaseColors = [
													'available' => 'bg-green-300 bg-opacity-25',
													'in_use' => 'bg-orange-300 bg-opacity-25',
													'maintenance' => 'bg-fuchsia-300 bg-opacity-25',
												];
												$currentPhase = $singleTool->phase ?? 'unknown';
												$colorClass = $phaseColors[$currentPhase] ?? 'bg-gray-300 bg-opacity-25';
											  @endphp

											<li class="flex items-center gap-2">
												<b class="dark:text-gray-200">Current Phase:</b>
												<span
													class="px-3 py-1 rounded-lg {{ $colorClass }} text-gray-800 text-sm capitalize">
													{{ str_replace('_', ' ', ucfirst($currentPhase)) }}
												</span>
											</li>

											<!-- IMAGE -->
											<li><b class="dark:text-gray-200">Image:</b></li>

											<div class="my-2">
												@if($singleTool->tool_image_url)
													<a href="{{ $singleTool->tool_image_url }}" target="_blank">
														<img src="{{ $singleTool->tool_image_url }}"
															class="w-40 rounded shadow dark:shadow-none dark:border dark:border-neutral-500">
													</a>
												@else
													<span class="text-xs text-gray-500 dark:text-gray-400">No image</span>
												@endif
											</div>

										</ul>
									</div>


									<!-- TOOL TRACKING HISTORY -->
									<div
										class="mb-5 mt-2 p-3 rounded-lg bg-gray-100 dark:bg-neutral-800 dark:border dark:border-neutral-600">

										<h2 class="text-lg font-semibold mb-3 text-gray-700 dark:text-gray-200">
											Usage / Maintenance History
										</h2>

										@if(!empty($singleTool['usage_tracking']))

											<div class="space-y-3">

												@foreach ($singleTool['usage_tracking'] as $track)

													@php
														$isOpen = $track['checked_in_at'] === null;
														$trackColor = $track['use_type'] === 'maintenance'
															? 'bg-fuchsia-300 bg-opacity-25'
															: 'bg-blue-300 bg-opacity-25';
													 @endphp

													<div class="p-3 rounded-lg bg-white dark:bg-neutral-700 shadow">

														<ul class="text-sm text-gray-700 dark:text-gray-300 space-y-1">

															<li>
																<b class="dark:text-gray-200">Use Type:</b>
																<span class="px-2 py-1 rounded {{ $trackColor }}">
																	{{ ucfirst($track['use_type']) }}
																</span>
															</li>

															<li><b class="dark:text-gray-200">Worker:</b>
																{{ $track['worker_name'] ?? '—' }}
															</li>

															<li><b class="dark:text-gray-200">Checked Out At:</b>
																{{ \Carbon\Carbon::parse($track['created_at'])->format('d M Y, h:i A') }}
															</li>

															<li><b class="dark:text-gray-200">Checked In At:</b>
																{{ $track['checked_in_at'] ? \Carbon\Carbon::parse($track['checked_in_at'])->format('d M Y, h:i A') : '—' }}
															</li>

															<li><b class="dark:text-gray-200">Note:</b>
																{{ $track['additional_note'] ?? '—' }}
															</li>

														</ul>

													</div>

												@endforeach

											</div>

										@else
											<p class="text-gray-500 text-sm">No usage history found.</p>
										@endif

									</div>

								</div>

								<button type="submit" class="showButton my-4">Update Tool</button>

							</form>
						</div>
					</section>
					<!-- --------------------------------- -->
				</div>
			</div>
		</div>
		<!--  -->

	</div>

	<!---------------------------------------------------------------------------------------------- accordion -->


	<!-- ---------------- APP AREA Starts ---------------- -->

</x-app-layout>