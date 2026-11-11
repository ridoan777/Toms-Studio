<x-app-layout>
	@php
		$breadCrumbs = [
			['label' => 'Users', 'url' => 'backend_all_user', 'icon' => 'userTrippleLine'],
			['label' => 'Single User'],
		];
	 @endphp

	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />

	<!-- Start Main Widgets -->
	<x-indicators.page_header_widget pageName="Single User Details" />
	<!-- End Main Widgets -->

	<!-- Announcement Banner -->
	<x-ui_items.banners.announcement_banner rolesToShow="super_admin"
		message="⚠️Be Careful! You are logged-in as Super Admin. Any changes you do, will have direct-immediate effect without verifications! Better log-in as an Admin." />
	<!-- End Announcement Banner -->

	<!-- Alert Component -->
	<x-indicators.alert-component message="Single User " />
	<!-- Alert Component -->

	@php
		$userAvatar = \App\Helpers\Ui\GetUserAvatar::alignAvatar($user, false);
		$zones = \App\Helpers\Ui\ConvertDynamicTimezone::handle();
	 @endphp

	<!-- Dynamic Timezone -->
	@php
		$tz = config('settings.display_timezone');
		$timezoneOffset = Carbon\Carbon::now($tz)->format('P');
	 @endphp
	<!-- Dynamic Timezone -->

	<!-- -------------------------------- APP AREA Starts -------------------------------- -->

	<div class="p-6 space-y-6">

		<!------------ HEADER ------------>
		<div class="flex justify-end items-center gap-4 border-b pb-4">
			<a href="{{ route('backend_all_user') }}" class="darkButton">← Back</a>
			<form action="{{ route('backend_user_delete', $user->id) }}" method="POST" class="inline-block p-0 m-0">
				@csrf
				@method('DELETE')
				<button type="submit" class="deleteButton"
					onclick="return confirm('Are you sure you want to delete this user?')">
					{!! \App\Helpers\IconPack::trash(['class' => 'iconPackItem w-4 h-4 text-gray-100']) !!}
				</button>
			</form>
		</div>
		<!------------ HEADER ------------>

		<!------------ BASIC INFO ------------>
		<div class="bg-white dark:bg-neutral-700 shadow rounded-lg p-6 space-y-4">
			<div class="flex items-center space-x-4">
				<div>
					@if ($user->avatar)
						<img src="{{ $userAvatar }}" alt="Avatar" class="w-20 h-20 rounded-full border">
					@else
						<div class="w-20 h-20 bg-gray-300 rounded-full flex items-center justify-center text-gray-500">
							N/A</div>
					@endif
				</div>
				<div>
					<h3 class="text-xl font-semibold">{{ $user->name }}</h3>
					<p class="text-sm text-gray-500 dark:text-gray-300">Role: <span
							class="font-medium">{{ ucfirst($user->user_role) }}</span></p>
					<p class="text-sm text-gray-500 dark:text-gray-300">
						Status:
						@if ($user->status)
							<span class="text-green-600 font-medium">Active</span>
						@else
							<span class="text-red-600 dark:text-red-400 font-medium">Inactive / Blacklisted</span>
						@endif
					</p>
				</div>
			</div>
		</div>
		<!------------ BASIC INFO ------------>

		<!------------ ACCOUNT INFORMATION ------------>
		<div class="bg-white dark:bg-neutral-700 shadow rounded-lg p-6">
			<h4 class="text-lg font-semibold mb-4">Account Information</h4>
			<div class="grid grid-cols-1 md:grid-cols-2 gap-4">

				<p class="dark:text-gray-300"><strong>DB ID:</strong> {{ $user?->id }}</p>
				<p class="dark:text-gray-300"><strong>UID:</strong> {{ $user?->user_uid }}</p>
				<p class="dark:text-gray-300"><strong>Username:</strong> {{ $user?->username ?? null }}</p>
				<p class="dark:text-gray-300"><strong>Gender:</strong> {{ $user?->gender ?? "N/A" }}</p>
				<p class="dark:text-gray-300"><strong>Phone:</strong> {{ $user->phone ?? 'N/A' }}</p>
				<p class="dark:text-gray-300"><strong>Email:</strong> {{ $user->email }}</p>
				<p class="dark:text-gray-300"><strong>Address:</strong> {{ $user->address ?? 'N/A' }}</p>

				<p class="dark:text-gray-300"><strong>Registered At:</strong>
					{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $user->getRawOriginal('created_at'), 'UTC')->timezone($zones['zone'])->format('d M Y, H:i') }}
					(UTC{{ $zones['offset'] }})

				</p>
				<p class="dark:text-gray-300"><strong>Last Updated:</strong>
					{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $user->getRawOriginal('updated_at'), 'UTC')->timezone($zones['zone'])->format('d M Y, H:i') }}
					(UTC{{ $zones['offset'] }})
				</p>

				<p class="flex items-center gap-2 dark:text-gray-300">
					<strong>Email Verified:</strong>

					@if ($user->email_verified_at)
						{!! \App\Helpers\IconPack::singleTick(['class' => 'iconPackItem w-6 h-6 text-green-500']) !!}
						{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $user->getRawOriginal('email_verified_at'), 'UTC')->timezone($zones['zone'])->format('d M Y, H:i') }}
						(UTC{{ $zones['offset'] }})
					@else
						{!! \App\Helpers\IconPack::cross(['class' => 'iconPackItem w-6 h-6 text-red-500']) !!}
						<span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Not Verified</span>

						<!-- Manual Verify Toggle -->
					@endif
					<a href="{{ route('backend_user_manual_verify_store', $user->id) }}"
						class="manual-verify-toggle ml-3 relative inline-block w-11 h-6 cursor-pointer group">

						<!-- Hidden checkbox just for animation -->
						<input type="checkbox" class="sr-only peer" {{ $user->email_verified_at ? 'checked' : '' }}>

						<!-- Toggle background -->
						<span
							class="absolute inset-0 rounded-full transition 
							{{ $user->email_verified_at ? 'bg-green-500 peer-checked:bg-green-500' : 'bg-gray-300 peer-checked:bg-green-500' }}">
						</span>

						<!-- Toggle knob -->
						<span class="absolute top-1/2 left-0.5 -translate-y-1/2 w-5 h-5 bg-white rounded-full shadow-xs transition 
							{{ $user->email_verified_at ? 'translate-x-full' : '' }}">
						</span>
					</a>

				</p>

			</div>
		</div>
		<!------------ ACCOUNT INFORMATION ------------>

		<!------------ UPDATE EMAIL ------------>
		@php
			$userVerifiaction = App\Models\Users\UserVerification::userId($user?->id)->type('email_change')->latest()->first();
			$isExpired = $userVerifiaction?->isExpired();
			$minutes = \Carbon\Carbon::parse(now())->diffInMinutes(\Carbon\Carbon::parse($userVerifiaction?->expires_at));
		@endphp

		@if ($userVerifiaction)
			<div class="p-6 bg-white dark:bg-neutral-700 shadow rounded-lg">
				<div class="flex items-center justify-between">
					<h4 class="text-lg font-semibold mb-4">Email Change Verification</h4>

					<div class="flex_between_center gap-4">
						<form id="resend-verification" method="post" action="{{ route('backend_verify_email_resent_otp') }}">
							@csrf
							<!--- IDENTIFIER HIDDEN INPUTS -->
							<input type="hidden" name="flag" class="smallInput" value="resend_verify_email" readonly>
							<input type="hidden" name="user_id" class="smallInput"
								value="{{ $userVerifiaction?->user_id ?? null }}" readonly>
							<!--- IDENTIFIER HIDDEN INPUTS -->
							<button form="resend-verification" class="darkButton">
								{{ __('Rsend OTP') }}
							</button>
						</form>
						<button type="button" data-modal-target="hs-delete-modal" data-modal-toggle="hs-delete-modal"
							data-delete-url="{{ route('backend_verify_email_delete', $userVerifiaction?->id) }}"
							class="deleteButton">
							{!! \App\Helpers\IconPack::trash(['class' => 'iconPackItem w-4 h-4 text-gray-100']) !!}
							Delete Attempt
						</button>
					</div>
				</div>
				<x-ui_items.modals.delete-modal id="hs-delete-modal" title="Confirm Deletion" confirmText="Yes, delete it"
					cancelText="Cancel" message="Are you sure you want to delete? This action cannot be undone" />

				<form method="POST" action="{{ route('backend_user_update_verify_email') }}" class="rounded-lg p-2 "
					enctype="multipart/form-data">
					@csrf
					<div class="mb-5 mt-2">
						<!--- IDENTIFIER HIDDEN INPUTS -->
						<input type="hidden" name="flag" class="smallInput" value="email_change" readonly>
						<input type="hidden" name="user_id" class="smallInput" value="{{ $userVerifiaction?->user_id ?? null }}"
							readonly>
						<!--- IDENTIFIER HIDDEN INPUTS -->
						<label for="name" class="inputLabelMedium">OTP
							<span class="redLabel">*</span>
							<span class="text-sm text-gray-400 font-light">(Check the old email for OTP. Admins need OTP
								to change other admins' email. Login as Super Admin to avoid verification!)</span>
							<span class="text-sm text-blue-400 font-light">Current OTP expires/expired in:
								{{ round($minutes, 0) }} minutes</span>
						</label>
						<input type="text" name="otp" class="normalInput">

						<button type="submit" class="my-4 showButton">Verify & Change</button>
					</div>
				</form>
			</div>
		@endif

		<!------------ UPDATE EMAIL ------------>

		<!------------ UPDATE USER ------------>
		<div class="p-6 space-y-6 bg-white dark:bg-neutral-700 shadow rounded-lg">
			<h4 class="text-lg font-semibold mb-4">Update User</h4>
			<form method="POST" action="{{ route('backend_create_users_new_store') }}" class="rounded-lg p-2 "
				enctype="multipart/form-data">
				@csrf

				<!--- IDENTIFIER HIDDEN INPUTS -->
				<input type="hidden" name="flag" class="smallInput" value="update" readonly>
				<input type="hidden" name="id" class="smallInput" value="{{ $user?->id ?? null }}" readonly>
				<!--- IDENTIFIER HIDDEN INPUTS -->

				<div>
					<div class="grid grid-cols-2 gap-4">

						<div class="mb-5 mt-2">
							<label for="name" class="inputLabelMedium">User's Name
								<span class="redLabel">*</span>
							</label>
							<input type="text" name="name" class="normalInput" value="{{ $user?->name ?? null }}">
						</div>

						<div>
							<label for="user_role" class="inputLabelMedium">Roles</label>
							<select name="user_role" class="dropdownSelectInputs">
								<option>--Select Role--</option>
								@foreach ($roles as $role)
								@php /** @var \App\Models\Users\User $user */ @endphp
									<option value="{{ $role->role_key }}" {{ $user->hasRoleKey($role?->role_key) ? 'selected' : '' }}>
										id:{{ $role?->id }} | Role name = {{ strtoupper($role?->name) }} | (Key:
										{{ $role?->role_key }})
									</option>
								@endforeach
							</select>
							@error('roles')
								<div>{{ $message }}</div>
							@enderror
						</div>

					</div>

				</div>
				<!-- -->
				<div class="grid grid-cols-2 gap-4">

					<div class="mb-5 mt-2">
						<label for="username" class="inputLabelMedium">Username
							<span class="redLabel">*</span>
							<span class="text-sm text-gray-400 font-light">(Must be unique)</span>
						</label>
						<input type="text" name="username" class="normalInput" value="{{ $user?->username ?? null }}">
					</div>

					<div class="mb-5 mt-2">
						<label for="email" class="inputLabelMedium">Email
							<span class="redLabel">*</span>
							<span class="text-sm text-gray-400 font-light">(Must be unique)</span>
							<span class="text-sm text-gray-400 font-light"> (Admin added users will not go
								through verifications)</span>
						</label>
						<input type="text" name="email" class="normalInput" value="{{ $user?->email ?? null }}">
					</div>

				</div>
				<!-- -->

				<div class="grid grid-cols-2 gap-4">

					<div class="mb-5 mt-2">
						<label for="phone" class="inputLabelMedium">Phone
							<span class="grayLabel">(Optional)</span>
						</label>
						<input type="text" name="phone" class="normalInput" placeholder="max 255-characters"
							value="{{ $user?->phone ?? null }}">
					</div>

					<div class="mb-5 mt-2">
						<label for="address" class="inputLabelMedium">Residential Address
							<span class="grayLabel">(Optional)</span>
						</label>
						<input type="text" name="address" class="normalInput" placeholder="max 255-characters"
							value="{{ $user?->address ?? null }}">
					</div>

				</div>
				<!-- -->

				<div class="grid grid-cols-2 gap-4">

					<div class="mb-5 mt-2">
						<label for="guardian" class="inputLabelMedium">Parent/Guardian
							<span class="grayLabel">(Optional) (For students only)</span>
						</label>
						<input type="text" name="guardian" class="normalInput" placeholder="max 255-characters" value="{{ $user?->guardian ?? null }}">
					</div>

					<div class="mb-5 mt-2">
						<label for="position" class="inputLabelMedium">Position/Instrument
							<span class="grayLabel">(Optional) (For teachers only)</span>
						</label>
						</label>
						<select name="position" class="dropdownSelectInputs">
							<option value="">--Select Postion--</option>
							@foreach ($positions as $item)
								<option value="{{ $item->id }}" {{ (int)$item?->id === (int)$user?->position ? 'selected' : '' }}>
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
							<span class="text-sm text-gray-400 font-light">(At least 8 characters)</span>
						</label>
						<input type="text" name="password" class="normalInput" placeholder="type new password">
					</div>

					<div class="mb-5 mt-2">
						<label for="password_confirmation" class="inputLabelMedium">Confirm Password
						</label>
						<input type=" text" name="password_confirmation" class="normalInput">
					</div>

				</div>
				<!-- -->

				<div class="mb-5">
					<label for="avatar" class="inputLabelMedium">User Avatar
						<span class="text-sm text-gray-400 font-light"> (Optional)</span>
						<span class="text-sm text-gray-400 font-light"> (Max 10 MB)</span>
					</label>

					<input type="file" id="avatarDropify" name="avatar" class="dropify" data-height="150" @if ($user?->avatar) data-default-file="{{ $userAvatar }}" @endif />

					<input type="hidden" name="remove_image" id="remove_avatar" value="0">
				</div>
		</div>

		<button type="submit" class="showButton my-4">Update User</button>
		</form>
		<!------------ UPDATE USER ------------>
	</div>

	<!------------ CALENDER ------------>
	@if($user->hasRoleKey('admin', 'super_admin', 'teacher'))
	<div class="bg-white dark:bg-neutral-700 shadow rounded-lg p-6">
		<h4 class="text-lg font-semibold mb-4">Weekly Routine</h4>

		<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-7 gap-4">
			@foreach ($calendarDays ?? [] as $calendarDay)
				<div class="rounded-lg border border-gray-200 dark:border-neutral-600 bg-gray-50 dark:bg-neutral-800 overflow-hidden">
					<div class="px-4 py-3 border-b border-gray-200 dark:border-neutral-600 bg-white dark:bg-neutral-750">
						<p class="text-sm font-bold text-gray-900 dark:text-gray-500">{{ $calendarDay['day_label'] }}</p>
						<p class="text-xs text-gray-500 dark:text-gray-400">{{ $calendarDay['date_label'] }}</p>
						<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
							{{ collect($calendarDay['all_items'])->count() }} class(s)
						</p>
					</div>

					<div class="p-3 space-y-3 min-h-55">
						@if (collect($calendarDay['all_items'])->isEmpty())
							<div class="h-full min-h-45 flex items-center justify-center rounded-lg border border-dashed border-gray-300 dark:border-neutral-600">
								<p class="text-sm text-gray-400 dark:text-gray-500">No scheduled class</p>
							</div>
						@else
							@foreach ($calendarDay['all_items'] as $calendarItem)
								@php
									$classItem = $calendarItem['model'];
									$isGroup = $calendarItem['type'] === 'group';
									$itemTime = $classItem?->start_time && $classItem?->end_time
										? \Carbon\Carbon::parse($classItem->start_time)->format('h:i A') . ' - ' . \Carbon\Carbon::parse($classItem->end_time)->format('h:i A')
										: 'Time N/A';
								@endphp

								<div class="rounded-lg p-3 {{ $isGroup ? 'bg-emerald-100 dark:bg-emerald-900/30' : 'bg-indigo-100 dark:bg-indigo-900/30' }}">
									<a href="{{ $isGroup ? route('backend_group_lesson_show', $classItem->id) : route('backend_lesson_show', $classItem->id) }}">
										<div class="flex items-start justify-between gap-2 mb-2">
											<span class="text-[11px] px-2 py-1 rounded-full font-semibold {{ $isGroup ? 'bg-emerald-200 text-emerald-800 dark:bg-emerald-800 dark:text-emerald-100' : 'bg-indigo-200 text-indigo-800 dark:bg-indigo-800 dark:text-indigo-100' }}">
												{{ $isGroup ? 'Group' : 'Lesson' }}
											</span>
											<span class="text-[11px] text-gray-600 dark:text-gray-300 text-right">{{ $itemTime }}</span>
										</div>
	
										<p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
											{{ $classItem?->title ?? 'Untitled Class' }}
										</p>
										<p class="text-xs text-gray-400 dark:text-gray-500 mt-1 break-all italic">
											UID: {{ $classItem?->lesson_uid ?? $classItem?->group_uid ?? 'N/A' }}
										</p>
										<p class="text-xs text-gray-600 dark:text-gray-300 mt-1">
											Instrument: {{ $classItem?->lessonRelatingBackTo_Intrument?->name ?? $classItem?->groupRelatingBackTo_Instrument?->name ?? 'N/A' }}
										</p>
										<p class="text-xs text-gray-600 dark:text-gray-300 mt-1">
											Student{{ $isGroup ? 's' : '' }}:
											{{ $isGroup ? ($classItem?->groupRelationWith_Students?->count() ?? 0) : ($classItem?->studentRelatingBackTo_User?->name ?? 'N/A') }}
										</p>
									</a>
								</div>
							@endforeach
						@endif
					</div>
				</div>
			@endforeach
		</div>
	</div>
	@endif
	<!------------ CALENDER ------------>

	<!------------ SETTINGS PREFERENCE ------------>
	<div class="bg-white dark:bg-neutral-700 shadow rounded-lg p-6">
		<h4 class="text-lg font-semibold mb-4">Settings Preference</h4>

		<table class="w-full">
			<thead>
				<tr>
					<th class="dark:text-gray-200">In-App Notification</th>
					<th class="dark:text-gray-200">Email Notification</th>
					<th class="dark:text-gray-200">Push Notification</th>
					<th class="dark:text-gray-200">Activity Log</th>
					<th class="dark:text-gray-200">Data Status</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="{{ $settingsPreference?->in_app_notification ? 'text-green-600' : 'text-red-600' }}">
						{{ $settingsPreference?->in_app_notification ? 'ON' : 'NO' }}
					</td>
					<td class="{{ $settingsPreference?->email_notification ? 'text-green-600' : 'text-red-600' }}">
						{{ $settingsPreference?->email_notification ? 'ON' : 'NO' }}
					</td>
					<td class="{{ $settingsPreference?->push_notification ? 'text-green-600' : 'text-red-600' }}">
						{{ $settingsPreference?->push_notification ? 'ON' : 'NO' }}
					</td>
					<td class="{{ $settingsPreference?->activity_log ? 'text-green-600' : 'text-red-600' }}">
						{{ $settingsPreference?->activity_log ? 'ON' : 'NO' }}
					</td>
					<td class="{{ $settingsPreference?->status ? 'text-green-600' : 'text-red-600' }}">
						{{ $settingsPreference?->status ? 'ON' : 'NO' }}
					</td>
				</tr>
			</tbody>
		</table>

	</div>
	<!------------ SETTINGS PREFERENCE ------------>

	<!------------ VERIFICATION INFO ------------>
	<div class="bg-white dark:bg-neutral-700 shadow rounded-lg p-6">
		<h4 class="text-lg font-semibold mb-4">Verification Details</h4>
		@if ($verification)
			<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
				<p class="dark:text-gray-200"><strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $verification->type)) }}
				</p>
				@php
					$displayOTP = "You don't have permissions to view the OTP";
					$currentUser = auth()->user();

					if ($currentUser->hasRoleKey('super_admin') || !($user->hasRoleKey('admin', 'super_admin') && $currentUser->id !== $user->id)) {
						$displayOTP = $verification->otp;
					}
				@endphp
				<p class="dark:text-gray-200"><strong>OTP:</strong> {{ $displayOTP }}</p>

				<p class="dark:text-gray-200"> <strong>Expires At:</strong>
					{{ Carbon\Carbon::parse($verification->expires_at)->timezone($tz)->format('d M Y, H:i') }}
					UTC{{ $timezoneOffset }}
					{{ $verification?->expiry_duration ? "(" . $verification?->expiry_duration . " mins)" : null }}
				</p>

				<p class="dark:text-gray-200">
					<strong>Created At:</strong>
					{{ Carbon\Carbon::parse($verification->created_at)->timezone($tz)->format('d M Y, H:i') }}
					UTC{{ $timezoneOffset }}
				</p>

				<p class="dark:text-gray-200">
					<strong>OTP Status:</strong>
					@if (\Carbon\Carbon::now()->gt($verification->expires_at))
						<span class="text-red-600 font-medium">Expired</span>
					@else
						<span class="text-green-600 font-medium">Valid</span>
					@endif
				</p>

				<p class="dark:text-gray-200">
					<strong>New Value:</strong> {{ $verification?->new_value ?? null }}
				</p>
			</div>
		@else
			<p class="text-gray-500">No verification record found for this user. Or the user might already be
				verified.
			</p>
		@endif
	</div>
	<!------------ VERIFICATION INFO ------------>

	</div>
	<!-- -------------------------------- APP AREA Starts -------------------------------- -->

	<style scoped>
		table tr th,
		table tr td {
			text-align: center;
			border: 1px solid #6a7282;
		}
	</style>
</x-app-layout>
