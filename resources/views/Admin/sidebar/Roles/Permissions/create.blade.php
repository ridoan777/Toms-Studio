<x-app-layout>

	@php
		$breadCrumbs = [
			['label' => 'Creatre Role'],
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

		<form method="POST" action="{{ route('backend_roles_store') }}">
			@csrf

			<div class="flex justify-between items-center gap-4">
				<label for="name" class="w-32">Role Name: </label><br>
				<input type="text" name="name" id="name" class="normalInput" value="{{ old('name') }}">
				@error('name')
					<div>{{ $message }}</div>
				@enderror
			</div>

			<!-- loading permissions -->

			<div class="my-12 p-4 bg-gray-200 rounded-lg shadow-lg">
				<div class="mt-4 mb-2">
					<div class="flex justify-between">
						<label class="inputLabelMedium">
							<span class="text-2xl">Permissions</span>
						</label>
						<a href="{{ route('backend_roles_create') }}" class="mb-4 darkButton">Create permission(s)</a>
					</div>
					@foreach($permissions as $item)
						<label>
							<input type="checkbox" name="permission_list[{{ $item->name }}]" value="{{ $item->name }}">
							{{ $item->name }}
						</label>
						<br>
					@endforeach
				</div>
			</div>

			<div>
				<button type="submit" class="saveButton">Register</button>
			</div>
		</form>
	</div>


	<!-- ---------------- APP AREA Starts ---------------- -->

</x-app-layout>