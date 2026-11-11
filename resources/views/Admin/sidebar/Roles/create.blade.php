<x-app-layout>

	@php
		$breadCrumbs = [
			['label' => 'Create Role'],
			['label' => 'Role List', 'url' => 'backend_roles_index', 'icon' => 'userSingleLine'],
		];
	 @endphp

	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />

	<!-- Start Main Widgets -->
	<x-indicators.page_header_widget pageName="Create A New Role" />
	<!-- End Main Widgets -->

	<!-- Alert Component -->
	<x-indicators.alert-component message="Form Submission" />
	<!-- Alert Component -->

	<!-- ---------------- APP AREA Starts --s-------------- -->
	<div class="p-6">

		<form method="POST" action="{{ route('backend_roles_store') }}" id="roleForm">
			@csrf

			<div class="my-12 p-4 bg-gray-200 dark:bg-neutral-700 rounded-lg shadow-lg">
				<div class="flex justify-between items-center gap-4">
					<label for="name" class="w-32 inputLabelMedium">Role Name: <span class="redLabel">*</span>
					</label><br>
					<input type="text" name="name" id="name" class="normalInput" value="{{ old('name') }}">
					@error('name')
						<div>{{ $message }}</div>
					@enderror
				</div>

				@php
					$grouped = $permissions->groupBy('group');
				@endphp


				<div class="mt-4 mb-2">
					<div class="">
						<label class="inputLabelMedium">
							<span class="text-2xl">Permissions</span>
							<span class="redLabel">*</span>
						</label>
					</div>
				</div>

				<div class="flex justify-end mb-4">
					<button type="button" onclick="toggleAll(this)" class="showButton">Check All</button>
				</div>

				<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">

					@foreach($grouped as $group => $items)
						<div class="p-3 bg-gray-200 dark:bg-neutral-600 rounded shadow">

							<div class="font-semibold text-sm text-gray-600 dark:text-gray-200 uppercase mb-2">
								{{ $group }}
							</div>
							<button type="button" onclick="toggleGroup('{{ Str::slug($group) }}', this)"
								class="text-xs px-2 py-1 bg-blue-500 text-white rounded">
								All
							</button>

							@foreach($items as $item)
								<label class="px-4 py-1 flex items-center gap-2 mb-1 checkbox">
									<input type="checkbox" class="perm-group-{{ Str::slug($group) }}"
										name="permission_list[{{ $item->name }}]" value="{{ $item->name }}">
									<span class="text-sm dark:text-gray-300">{{ $item->name }}</span>
								</label>
							@endforeach

						</div>
					@endforeach

				</div>

				<p class="my-4 font-light text-gray-400">
					(Permissions are generic actions that a user with a role can do. However, Permission have higher
					precedence than Roles in this system. So attach roles with user(s) carefully!)
				</p>

				<div class="flex justify-end">
					<button type="submit" class="saveButton">Register</button>
				</div>
			</div>

		</form>
	</div>
	<!----------->
	<div class="p-4">
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