<x-app-layout>

	@php
		$breadCrumbs = [
			['label' => 'Create Role', 'url' => 'backend_roles_create', 'icon' => 'userTrippleLine'],
			['label' => 'Roles'],
		];
	 @endphp

	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />

	<!----------- Start Main Widgets ----------->
	<x-indicators.page_header_widget pageName="Roles and Permissions" />
	<!----------- End Main Widgets ----------->
	
	<!----------- Announcement Banner ----------->
	<x-ui_items.banners.announcement_banner rolesToShow="super_admin"
		message="⚠️Be Careful! You are logged-in as Super Admin. Any changes you do, will have direct-immediate effect without verifications! Better log-in as an Admin." bgColor="bg-yellow-300" />
	<!----------- End Announcement Banner ----------->

	<!----------- Alert Component ----------->
	<x-indicators.alert-component message="Form Submission" />
	<!----------- Alert Component ----------->

	<!-- ---------------- APP AREA Starts --s-------------- -->
	<div class="p-6">

		<a href="{{ route('backend_roles_create') }}" class="mb-4 darkButton">Create</a>

		<table id="rolePermissionTable" class="w-full text-left border dark:border-gray-200">
			<thead class="bg-gray-100">
				<tr>
					<th class="p-2 border">ID</th>
					<th class="w-32 p-2 border">Role DB ID</th>
					<th class="p-2 border">Name</th>
					<th class="p-2 border">Role Key (Global)</th>
					<th class="p-2 border">Type</th>
					<th class="p-2 border">Permissions</th>
					<th class="p-2 border">Action</th>
				</tr>
			</thead>
			<tbody>
				@foreach($roles as $index => $role)
					<tr class="border-b dark:border-white">
						<td class="p-2 border">{{ $index + 1 }}</td>
						<td class="p-2 border">{{ $role?->id }}</td>
						<td class="p-2 border">{{ $role?->name }}</td>
						<td class="p-2 border">{{ $role?->role_key ?? null }}</td>
						<td class="p-2 border">{{ $role?->role_type ? strtoupper($role?->role_type) : null }}</td>
						<td class="p-2 border">
							@php
								$grouped = $role->permissions->groupBy('group');
							@endphp

							@forelse ($grouped as $group => $permissions)
								<div class="mb-2">
									<div class="mb-2 text-xs flex items-center flex-wrap">
										<span class="font-semibold text-gray-500 uppercase mr-2">{{ $group }} :</span>

										@foreach ($permissions as $permission)
											<span class="px-2 py-1 text-xs rounded bg-gray-200 dark:bg-neutral-400 text-gray-800 dark:text-gray-700 mr-1 inline-block">
												{{ $permission->name }}
											</span>
										@endforeach
									</div>
								</div>
							@empty
								<span class="text-gray-400">N/A</span>
							@endforelse
						</td>
						<td class="p-2 flex_xy_center	gap-2">
							<a href="{{ route('backend_roles_show', $role?->id) }}" class="showButton">Show</a>

							@if(strtolower($role?->role_type) !== 'core')
							<button type="button" data-modal-target="hs-delete-modal" data-modal-toggle="hs-delete-modal"
							data-delete-url="{{ route('backend_roles_delete', $role?->id) }}"
							class="deleteButton">
								{!! \App\Helpers\IconPack::trash(['class' => 'iconPackItem w-3 h-3 text-gray-100']) !!}
							</button>
							@endif
						</td>
					</tr>
				@endforeach
				<x-ui_items.modals.delete-modal id="hs-delete-modal" title="Confirm Deletion" confirmText="Yes, delete it"
					cancelText="Cancel" message="Are you sure you want to delete? This action cannot be undone" />
			</tbody>
		</table>
	</div>

	<!-- ---------------- APP AREA Starts ---------------- -->
	<style>
		#rolePermissionTable td {
			text-align: center;
		}
		.dark #rolePermissionTable td {
			color: white;
		}
	</style>

</x-app-layout>