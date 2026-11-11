<x-app-layout>
	@php
		$breadCrumbs = [
			['label' => 'Navbar', 'url' => 'dashboard', 'icon' => 'map'],
			['label' => 'Update Profile'],
		];
	 @endphp
	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />
	<!-- ---------------- APP AREA Starts ---------------- -->

	<!-- Start Main Widgets -->
	<x-indicators.page_header_widget pageName="Profile Update" />
	<!-- End Main Widgets -->


	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">Profile</h2>
	</x-slot>

	<div class="py-4">
		<div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-6">
			<div class="p-4 sm:p-8 bg-white dark:bg-neutral-700 shadow sm:rounded-lg">
				<div class="mx-auto px-8 py-4">
					@include('Mastering.Profile.partials.update-profile-information-form')
				</div>
			</div>

			<div class="p-4 sm:p-8 bg-white dark:bg-neutral-700 shadow sm:rounded-lg">
				<div class="mx-auto px-8 py-4">
					@include('Mastering.Profile.partials.update-password-form')
				</div>
			</div>

			<div class="hidden p-4 sm:p-8 bg-white dark:bg-neutral-700 shadow sm:rounded-lg disabled:">
				<div class="mx-auto px-8 py-4">
					<button class="deleteButton">Delete</button>
					<span class="text-sm text-gray-400">&nbsp;Admins deletion is disabled for safety. Tweak database directly if required.</span>
					
				</div>
			</div>

		</div>
	</div>
</x-app-layout>