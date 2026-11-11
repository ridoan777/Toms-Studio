<x-app-layout>

	@php
		$breadCrumbs = [
			['label' => 'All Users', 'url' => 'backend_all_user', 'icon' => 'userTrippleLine'],
			['label' => 'Create User(s)'],
		];
	 @endphp

	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />
	<!-- ---------------- APP AREA Starts ---------------- -->

	<!-- Start Main Widgets -->
	<x-indicators.page_header_widget pageName="User: Create New" />
	<!-- End Main Widgets -->


	<!-- Alert Component -->
	<x-indicators.alert-component message="Form Submission" />
	<!-- Alert Component -->

	@php
		$ACCORDION_1_VISIBILITY = ''; // hidden or ''
	 @endphp

	<!---------------------------------------------------------------------------------------------- accordion -->
	<div class="hs-accordion-group">

		<x-ui_items.accordion.accordion-destroy />

		<div id="hs-destroy-heading-one" {{-- Module --}}
			class="hs-accordion-to-destroy hs-accordion hs-accordion-active:border-gray-200 PrelineAccordionItemParent">

			@php
				$ORDER_IN_PAGE = 2;
			@endphp

			<x-ui_items.accordion.accordion_toggle_button toggleLabel="Create new users" />

			<div id="hs-destroy-collapse-one"
				class="hs-accordion-content {{ $ACCORDION_1_VISIBILITY }} w-full overflow-hidden transition-[height] duration-300"
				role="region" aria-labelledby="hs-destroy-heading-one">
				<div class="p-3">
					<!-- Form::About-Promo -->
					<section
						class="displayArea mx-auto my-5 rounded-lg border-none bg-white p-4 shadow-md shadow-gray-400 dark:bg-neutral-700 dark:shadow-none">
						<div class="my-5">
							<form method="POST" action="{{ route('backend_create_users_new_store') }}"
								class="rounded-lg p-2 dark:bg-neutral-700" enctype="multipart/form-data">
								@csrf

								<!--- IDENTIFIER HIDDEN INPUTS -->
								<input type="hidden" name="flag" class="smallInput" value="create">
								<!--- IDENTIFIER HIDDEN INPUTS -->

								<div>
									<div class="grid grid-cols-2 gap-4">

										<div class="mb-5 mt-2">
											<label for="name" class="inputLabelMedium">User's Name
												<span class="redLabel">*</span>
											</label>
											<input type="text" name="name" class="normalInput" placeholder="max 255-characters">
										</div>

										<div class="mb-5 mt-2">
											<label for="user_role" class="inputLabelMedium">Select Role
												<span class="redLabel">*</span>
											</label>
											<select name="user_role" class="dropdownSelectInputs">
												<option>--Select Role--</option>
												@foreach ($roles as $role)
													<option value="{{ $role->role_key }}">
														{{ $role->name === 'admin' ? 'Admin (❗Careful. They can remove other admins)' : ucfirst($role->name) }}
													</option>
												@endforeach
											</select>
										</div>
									</div>

									<!-- -->

									<div class="grid grid-cols-2 gap-4">
										<div class="mb-5 mt-2">
											<label for="username" class="inputLabelMedium">Username
												<span class="redLabel">*</span>
												<span class="text-sm text-gray-400 font-light">(Must be unique)</span>
											</label>
											<input type="text" name="username" class="normalInput"
												placeholder="min:4 characters, max:20 characters">
										</div>
										<div class="mb-5 mt-2">
											<label for="email" class="inputLabelMedium">Email
												<span class="redLabel">*</span>
												<span class="grayLabel">(Must be unique)</span>
												<span class="grayLabel"> (Admin added users will not go
													through verifications)</span>
											</label>
											<input type="text" name="email" class="normalInput" placeholder="max 255-characters">
										</div>
									</div>
									<!-- -->

									<div class="grid grid-cols-2 gap-4">

										<div class="mb-5 mt-2">
											<label for="phone" class="inputLabelMedium">Phone
												<span class="grayLabel">(Optional)</span>
											</label>
											<input type="text" name="phone" class="normalInput" placeholder="max 255-characters">
										</div>

										<div class="mb-5 mt-2">
											<label for="address" class="inputLabelMedium">Residential Address
												<span class="grayLabel">(Optional)</span>
											</label>
											<input type="text" name="address" class="normalInput" placeholder="max 255-characters">
										</div>
									</div>
									<!-- -->

									<div class="grid grid-cols-2 gap-4">

										<div class="mb-5 mt-2">
											<label for="guardian" class="inputLabelMedium">Parent/Guardian
												<span class="grayLabel">(Optional) (For students only)</span>
											</label>
											<input type="text" name="guardian" class="normalInput"
												placeholder="max 255-characters">
										</div>

										<div class="mb-5 mt-2">
											<label for="position" class="inputLabelMedium">Position/Instrument
												<span class="grayLabel">(Optional) (For teachers only)</span>
											</label>
											</label>
											<select name="position" class="dropdownSelectInputs">
												<option value="">--Select Postion--</option>
												@foreach ($positions as $item)
													<option value="{{ $item->id }}">
														{{ $item?->name }}
													</option>
												@endforeach
											</select>
										</div>
									</div>
									<!-- -->

									<div class="grid grid-cols-2 gap-4">
										<div class="mb-5 mt-2">
											<label for="password" class="inputLabelMedium">Password
												<span class="redLabel">*</span>
												<span class="grayLabel">(At least 8
													characters)</span>
											</label>
											<input type="text" name="password" class="normalInput" placeholder="Min 8-characters">
										</div>

										<div class="mb-5 mt-2">
											<label for="password_confirmation" class="inputLabelMedium">Confirm Password
											</label>
											<input type="text" name="password_confirmation" class="normalInput">
										</div>
									</div>

									<!-- -->

									<div class="mb-5">
										<label for="avatar" class="inputLabelMedium">User Avatar
											<span class="grayLabel"> (Optional)</span>
											<span class="grayLabel"> (Max 10 MB)</span>
										</label>

										<input type="file" id="avatarDropify" name="avatar" class="dropify" data-height="150" />

										<input type="hidden" name="remove_image" id="remove_avatar" value="0">
									</div>

								</div>

								<button type="submit" class="saveButton my-4">Create User</button>
							</form>
						</div>
					</section>
					<!-- Form::About-Promo -->
				</div>
			</div>
		</div>

	</div>
	<!---------------------------------------------------------------------------------------------- accordion -->

	<!-- ---------------- APP AREA ENDS ---------------- -->
</x-app-layout>