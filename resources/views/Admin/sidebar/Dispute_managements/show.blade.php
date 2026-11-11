<x-app-layout>

	@php
		$breadCrumbs = [
			['label' => 'All Reports', 'url' => 'backend_disputes_report_index', 'icon' => 'noMessage'],
			['label' => 'Dispute Management'],
		];
	 @endphp

	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />
	<!-- ---------------- APP AREA Starts ---------------- -->

	<!-- Start Main Widgets -->
	<x-indicators.page_header_widget pageName="Report: Manage {{ $report?->issue_section ?? null }} Dispute" />
	<!-- End Main Widgets -->


	<!-- Alert Component -->
	<x-indicators.alert-component message="Category Modification" />
	<!-- Alert Component -->

	@php
		$ACCORDION_1_VISIBILITY = ''; // hidden or ''
		$ACCORDION_2_VISIBILITY = '';
	 @endphp

	<!---------------------------------------------------------------------------------------------- accordion -->
	<div class="hs-accordion-group">

		<x-ui_items.accordion.accordion-destroy />

		<div id="hs-destroy-heading-one"
			class="hs-accordion-to-destroy hs-accordion hs-accordion-active:border-gray-200 PrelineAccordionItemParent">

			<x-ui_items.accordion.accordion_toggle_button toggleLabel="Dispute Reports" />

			<div id="hs-destroy-collapse-one"
				class="hs-accordion-content {{ $ACCORDION_1_VISIBILITY }} w-full overflow-hidden transition-[height] duration-300"
				role="region" aria-labelledby="hs-destroy-heading-one">
				<div class="p-3">
					<!-- --------------------- -->
					<section
						class="displayArea mx-auto my-5 rounded-lg border-none bg-white p-4 shadow-md shadow-gray-400 dark:bg-neutral-700 dark:shadow-none">

						<div class="max-w-6xl mx-auto space-y-6">

							<!----------------- HEADER ----------------->
							<div class="flex justify-between items-center">
								<h1 class="text-2xl font-semibold">Report Ticket # {{ $report->ticket }}</h1>

								<div>
									<label for="quiz_eligible" class="inputLabelMedium inline-block">Current
										Status:</label>
									<span
										class="px-3 py-1 rounded-lg text-sm {{ $report->is_resolved ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
										{{ $report->is_resolved ? 'Resolved' : 'Disputed' }}
									</span>
								</div>
							</div>

							<div class="grid grid-cols-2 gap-4">
								<!----------------- REPORT DETAILS ----------------->
								<div
									class="col-span-1 bg-gray-100 dark:bg-neutral-800 rounded-lg p-5 space-y-4 flex flex-col justify-between gap-8">

									<div>
										<label class="inputLabelMedium inline-block">Issue Label: </label>
										<p class="px-4 py-1 inline-block font-medium bg-amber-400 rounded-lg">
											{{ $report->issue_label }}
										</p>
										<div>
											<label class="inputLabelMedium">Description</label>
											<p
												class="text-sm bg-white dark:bg-neutral-300 text-gray-700 p-4 rounded-lg border border-gray-400">
												{{ $report->description }}
											</p>
										</div>

										<!----------------- USERS ----------------->
										<div class="p-5">
											<h3 class="font-semibold mb-2">Parties</h3>

											<div class="grid gap-4">
												<!----------------- VICTIM ----------------->
												<div class="bg-white dark:bg-neutral-800 border border-gray-500 rounded-lg p-5">
													<h4>
														<span
															class="font-semibold mb-3 px-4 py-1 bg-emerald-200 dark:text-black rounded-lg">Victim</span>
														@if ($report?->reportRelatingBackTo_ChatMessage?->receiver_id ===
														$report?->victimRelationWith_User?->id)
														<span class="text-sm">MESSAGE
															RECEIVER</span>
														@endif
													</h4>

													<div class="flex items-center space-x-4">
														<img src="{{ $report->victimRelationWith_User->avatar_url }}"
															class="w-12 h-12 rounded-full object-cover">

														<div>
															<div class="text-sm text-gray-400">
																DB ID: {{ $report->victimRelationWith_User->id }} |
																User UID:
																{{ $report->victimRelationWith_User->user_uid }}
															</div>
															<div class="font-medium dark:text-gray-400">
																{{ $report->victimRelationWith_User->name }}
															</div>
															<div class="text-sm text-gray-400">
																{{ $report->victimRelationWith_User->email }}
															</div>
															<div class="text-xs text-gray-400">Role:
																{{ $report->victimRelationWith_User->user_role }}
															</div>
														</div>
													</div>
												</div>

												<!----------------- ACCUSED ----------------->
												<div class="bg-white dark:bg-neutral-800 border border-gray-500 rounded-lg p-5">
													<h4 class="font-semibold mb-3">
														<span
															class="font-semibold mb-3 px-4 py-1 bg-amber-200 dark:text-black  rounded-lg">Accused</span>
														@if ($report?->reportRelatingBackTo_ChatMessage?->sender_id ===
														$report?->accusedRelationWith_User?->id)
														<span class="text-sm">MESSAGE SENDER</span>
														@endif
													</h4>

													<div class="flex items-center space-x-4">
														<img src="{{ $report->accusedRelationWith_User->avatar_url }}"
															class="w-12 h-12 rounded-full object-cover">

														<div>
															<div class="text-sm text-gray-400">
																DB ID: {{ $report->accusedRelationWith_User->id }} |
																User UID:
																{{ $report->accusedRelationWith_User->user_uid }}
															</div>
															<div class="font-medium dark:text-gray-400">
																{{ $report->accusedRelationWith_User->name }}
															</div>
															<div class="text-sm text-gray-400">
																{{ $report->accusedRelationWith_User->email }}
															</div>
															<div class="text-xs text-gray-400">Role:
																{{ $report->accusedRelationWith_User->user_role }}
															</div>
														</div>
													</div>
												</div>

											</div>
										</div>

										<!----------------- CHAT/CONTENTS ----------------->
										<div class="bg-white dark:bg-neutral-800 rounded-lg p-5">
											<h2 class="font-semibold mb-2">Reported Content</h2>

											<div class="p-3 bg-gray-100 dark:bg-neutral-400 rounded text-sm inline-block">
												Message ID:
												{{ $report->disputeReport?->id ?? $report?->original_id }}
											</div>
											<div class="mt-2 p-3 bg-gray-100 dark:bg-neutral-400 rounded text-sm">
												{{ $report->disputeReport?->text ?? $report?->original_content }}
											</div>
											{{-- Gallery --}}
											@php
											$gallery = $report->disputeReport?->chatMessageRelationWith_Gallery ?? collect();
											@endphp

											@if ($gallery->count())
											<div class="mt-2 p-3 bg-gray-100 dark:bg-neutral-400 rounded text-sm">
												<h3 class="text-sm font-medium text-gray-500 mb-2">
													Attachments ({{ $gallery->count() }})
												</h3>

												<div class="grid grid-cols-2 gap-3">
													@foreach ($gallery as $file)

													@php
													$url = Storage::url($file->file);
													$mime = data_get($file, 'metadata.meta.mime');
													$name = data_get($file, 'metadata.file_name', 'File');
													@endphp

													{{-- IMAGE --}}
													@if ($mime && Str::startsWith($mime, 'image/'))
													<a href="{{ $url }}" target="_blank"
														class="group relative block rounded overflow-hidden">
														<img src="{{ $url }}" alt="Image"
															class="w-full h-32 object-cover transition-transform duration-200 group-hover:scale-105">
														<div
															class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity">
														</div>
													</a>

													{{-- VIDEO --}}
													@elseif ($mime && Str::startsWith($mime, 'video/'))
													<a href="{{ $url }}" target="_blank"
														class="block rounded overflow-hidden bg-black">
														<video class="w-full h-32 object-cover" muted>
															<source src="{{ $url }}" type="{{ $mime }}">
														</video>
													</a>

													{{-- AUDIO --}}
													@elseif ($mime && Str::startsWith($mime, 'audio/'))
													<div class="p-3 border rounded bg-white">
														<audio controls class="w-full">
															<source src="{{ $url }}" type="{{ $mime }}">
														</audio>
													</div>

													{{-- DOCUMENT / OTHER FILE --}}
													@else
													<a href="{{ $url }}" target="_blank"
														class="flex items-center gap-3 p-3 border rounded bg-white hover:bg-gray-50 transition">
														<span class="text-sm font-medium truncate">{{ $name }}</span>
													</a>
													@endif

													@endforeach
												</div>
											</div>
											@endif

										</div>
									</div>


									<div class="flex gap-4">
										<span class="text-sm text-gray-400 inputLabelMedium">
											Status:
											<strong class="{{ $report->status ? 'text-green-600' : 'text-red-600' }}">
												{{ $report->status ? 'Active' : 'Inactive' }}
											</strong>
										</span>

										<span class="text-sm text-gray-400 inputLabelMedium">
											Reported at: {{ $report->created_at->format('d M Y, H:i') }}
										</span>
									</div>
								</div>
								<!------------------ RESOLUTION FORM ------------------>
								<div class="col-span-1 bg-gray-100 dark:bg-neutral-800 rounded-lg p-5 space-y-4">
									<form method="POST" action="{{ route('backend_disputes_report_handle', $report?->id) }}"
										class="rounded-lg p-2 dark:bg-neutral-700" enctype="multipart/form-data">
										@csrf

										<!------------------>
										<div>
											<input type="hidden" name="issue_section" value="{{ $report?->issue_section ?? '' }}">
										</div>
										<!------------------>
										<div class="mb-5 mt-2">
											<label for="is_resolved" class="inputLabelMedium">1. Resolution Status
												<span class="redLabel">*</span>
											</label>
											<select id="status" name="is_resolved" class="dropdownSelectInputs">
												<option value="1" {{ $report?->status == 1 ? 'selected' : '' }}>
													Resolved
												</option>
												<option value="0" {{ $report?->status == 0 ? 'selected' : '' }}>
													Disputed
												</option>
											</select>
										</div>

										<div class="mb-5 mt-2 flex items-center gap-4">
											<label for="delete_content" class="inputLabelMedium m-0">2. Delete
												Content?</label>
											<input id="delete_content" type="checkbox" class="checkBox p-0"
												id="hs-default-checkbox" name="delete_content">
										</div>

										<div class="mb-5 mt-2 grid gap-4">
											<label for="quiz_eligible" class="inputLabelMedium">3. Take Action
												<span class="redLabel">*</span>
											</label>
											<div class="ml-8 flex items-center gap-4">
												<input type="checkbox" class="block checkBox p-0" id="hs-default-checkbox"
													name="join_ban" {{ (int)$userAccess?->join_ban === 1 ? 'checked' : ''  }} value="1">
												<label for="">Ban from Joinning (chats, groups, etc.)</label>
											</div>
											<div class="ml-8 flex items-center gap-4">
												<input type="checkbox" class="checkBox p-0" id="hs-default-checkbox" name="chat_ban"
													{{ (int)$userAccess?->chat_ban === 1 ? 'checked' : ''  }} value="1">
												<label for="">Ban from Chatting (may receive, but cannot send
													new messages)</label>
											</div>
											<div class="ml-8 flex items-center gap-4">
												<input type="checkbox" class="checkBox p-0" id="hs-default-checkbox"
													name="gallery_ban" {{ (int)$userAccess?->gallery_ban === 1 ? 'checked' : ''  }} value="1">
												<label for="">Ban from Gallery Access (cannot
													add images/files)</label>
											</div>
											<div class="ml-8 flex items-center gap-4">
												<input type="checkbox" class="checkBox p-0" id="hs-default-checkbox"
													name="store_ban" {{ (int)$userAccess?->store_ban === 1 ? 'checked' : ''  }} value="1">
												<label for="">Ban from Storing (cannot post)</label>
											</div>
											<div class="ml-8 flex items-center gap-4">
												<input type="checkbox" class="checkBox p-0" id="hs-default-checkbox" name="view_ban"
													 {{ (int)$userAccess?->view_ban === 1 ? 'checked' : ''  }} value="1">
												<label for="">Ban from View (cannot view post)</label>
											</div>
											<div class="ml-8 flex items-center gap-4">
												<input type="checkbox" class="checkBox p-0" id="hs-default-checkbox"
													name="account_hold" {{ (int)$userAccess?->account_hold === 1 ? 'checked' : ''  }} value="1">
												<label for="">Account hold (cannot view post)</label>
											</div>
										</div>


										<div class="mb-5 mt-2">
											<label for="ban_expiry" class="inputLabelMedium">4. Ban Until
											</label>
											<input type="date" name="ban_expiry" class="normalInput" value="{{ $singleProject?->start_date ?? null }}">
										</div>

										<div class="mb-5 mt-2">
											<label for="message_victim" class="inputLabelMedium">5. Support Message
												(for Victim)
												<span class="redLabel">*</span>
											</label>
											<textarea class="summernote"
												name="message_victim">{!! $singleProject?->message_victim ?? null !!}</textarea>
										</div>

										<div class="mb-5 mt-2">
											<label for="message_accused" class="inputLabelMedium">6. Support Message
												(for Accused)
												<span class="redLabel">*</span>
											</label>
											<textarea class="summernote"
												name="message_accused">{!! $singleProject?->message_accused ?? null !!}</textarea>
										</div>

										<div class="mb-5 mt-2">
											<label for="admin_note" class="inputLabelMedium">7. Admin's Note
												<span class="text-sm text-gray-400">(saved on user access record table)</span>
											</label>
											<textarea class="summernote"
												name="admin_note">{!! $singleProject?->message_accused ?? null !!}</textarea>
										</div>

										<button class="showButton">Send</button>
									</form>
								</div>
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


	{{-- Script for tweaking remove_image = 1 --}}
	<script>
		$(document).ready(function () {
			const drEvent = $('#imageDropify').dropify();

			drEvent.on('dropify.afterClear', function (event, element) {
				$('#remove_image').val(1);
			});

			drEvent.on('change', function () {
				$('#remove_image').val(0);
			});
		});
		$(document).ready(function () {
			const drEvent = $('#videoDropify').dropify();

			drEvent.on('dropify.afterClear', function (event, element) {
				$('#remove_video').val(1);
			});

			drEvent.on('change', function () {
				$('#remove_video').val(0);
			});
		});
	</script>
	{{-- Script for tweaking remove_image = 1 --}}

	<!-- ---------------- APP AREA Starts ---------------- -->

</x-app-layout>