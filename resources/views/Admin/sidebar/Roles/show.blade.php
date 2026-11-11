<x-app-layout>

	@php
		$breadCrumbs = [
			['label' => 'Create Role', 'url' => 'backend_roles_create', 'icon' => 'userTrippleLine'],
			['label' => 'Role List', 'url' => 'backend_roles_index', 'icon' => 'userSingleLine'],
			['label' => 'Role'],
		];
	 @endphp

	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />

	<!-- Start Main Widgets -->
	<x-indicators.page_header_widget pageName="Modify The Role {{ $role->name ? strtoupper($role->name) : null }}" />
	<!-- End Main Widgets -->

	<!-- Alert Component -->
	<x-indicators.alert-component message="Form Submission" />
	<!-- Alert Component -->

	<!-- ---------------- APP AREA Starts ---------------- -->
	<div class="p-6">

		<form method="POST" action="{{ route('backend_roles_update', $role->id) }}" id="roleForm">
			@csrf
			@method('PUT')

			@php
				$grouped = $permissions->groupBy('group');
			@endphp

			<div class="my-6 p-4 bg-gray-200 dark:bg-neutral-700 rounded-lg shadow-lg">

				<div class="flex items-center gap-4 mb-6">
					<label for="name" class="w-72 inputLabelMedium m-0">Role Name:
						<span class="grayLabel"> (Type your role name)</span>
					</label>
					<input type="text" name="name" id="name" class="normalInput" value="{{ $role?->name }}">
				</div>
				<div class="flex_between_center gap-4 mb-6">
					<label for="role_key" class="inputLabelMedium m-0">Role Key:
						<span class="w-72 grayLabel"> (MUST NOT BE CHANGED! This is the unique role identifier)</span>
					</label>
					<input type="text" name="role_key" id="role_key" class="normalInput"
						value="{{ $role?->role_key ?? null }}" readonly>
				</div>

				<div class="mt-4 mb-2">
					<div class="flex justify-between">
						<label class="inputLabelMedium">
							<span class="text-2xl">Permissions</span>
						</label>
					</div>
				</div>

				<div class="flex justify-end mb-4">
					<button type="button" onclick="toggleAll(this)" class="showButton">Check All</button>
				</div>

				<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">

					@foreach($grouped as $group => $items)
						<div class="p-3 bg-gray-200 dark:bg-neutral-600 rounded shadow">

							<div class="flex justify-start items-center gap-2 mb-2">
								<div class="font-semibold text-sm text-gray-600 dark:text-gray-200 uppercase">
									{{ $group }}
								</div>

								<button type="button" onclick="toggleGroup('{{ Str::slug($group) }}', this)"
									class="text-xs px-2 py-1 bg-blue-500 text-white rounded">
									All
								</button>
							</div>

							@foreach($items as $item)
								<label class="px-4 py-1 flex items-baseline gap-2 mb-1 checkbox">
									<input type="checkbox" class="perm-group-{{ Str::slug($group) }}"
										name="permission_list[{{ $item->name }}]" value="{{ $item->name }}" {{ $role->hasPermissionTo($item->name) ? 'checked' : '' }}>
									<div class="min-w-0 overflow">
										<p class="text-sm wrap-break-word dark:text-gray-300">{{ $item->name }}</p>

										<p class="text-xs text-gray-500 dark:text-gray-400">{{ $item->note ?? null }}</p>
									</div>
								</label>
							@endforeach

						</div>
					@endforeach
				</div>
				<div class="flex justify-end">
					<button type="submit" class="my-4 showButton">Save</button>
				</div>
			</div>
		</form>
	</div>
	<!----------->
	<div class="p-4 ">
		<div class="p-4 bg-gray-200 dark:bg-neutral-700 rounded-lg shadow-lg">
			<h2>Important!</h2>
			<ul class="dark:text-gray-200">
				<li>- These two permission groups are vital: "ROLE, ADMIN"</li>
				<li>- Admin should not have all permissions from "ROLE" & "ADMIN" groups. They are for Super Admins.</li>
				<li>- Admins can safely create these permissions for custom roles:
					<div class="mx-12">
						<p>i. role_index</p>
						<p>ii. role_show</p>
						<p>iii. role_create</p>
					</div>
					<p class="text-gray-500">However, avoid assigning 'role_core_roles' to admins.</p>
				</li>
				<li>- Admin who has these permission can alter Super Admin's credentials too:
					<div class="mx-12">
						<p>i. admin_create</p>
						<p>ii. admin_update</p>
						<p>iii. admin_email</p>
						<p>iv. admin_password</p>
						<p>v. role_core_roles</p>
					</div>
					<p class="text-gray-500">However, they still might need to verify email to alter that.</p>
				</li>
				<li>- For any role, you MUST add all permissions from "GENERAL" group. Otherwise, they cannot access basic
					features.</li>
			</ul>
		</div>
	</div>
	<!-- ---------------- APP AREA Starts ---------------- -->
	<style>
		.checkbox:hover {
			cursor: pointer;
			border-radius: 8px;
			background-color: #99a1af;
		}
	</style>
	<script>
		function toggleGroup(group, btn) {
			let form = document.getElementById('roleForm');
			let items = form.querySelectorAll('.perm-group-' + group);
			let allChecked = [...items].every(el => el.checked);

			items.forEach(el => el.checked = !allChecked);
		}

		function toggleAll(btn) {
			let form = document.getElementById('roleForm');
			let items = form.querySelectorAll('input[type="checkbox"]'); // scoped now
			let allChecked = [...items].every(el => el.checked);

			items.forEach(el => el.checked = !allChecked);
			btn.innerText = allChecked ? 'Check All' : 'Uncheck All';
		}
	</script>
</x-app-layout>