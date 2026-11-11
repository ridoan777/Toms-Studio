<x-app-layout>

	@php
		$breadCrumbs = [
			['label' => 'Settings', 'url' => 'backend_settings_index', 'icon' => 'gearLine'],
			['label' => 'Seeder'],
		];
	 @endphp

	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />
	<!-- ---------------- APP AREA Starts ---------------- -->

	<!-- Start Main Widgets -->
	<x-indicators.page_header_widget pageName="Settings: Database Seeder" />
	<!-- End Main Widgets -->


	<!-- Alert Component -->
	<x-indicators.alert-component message="Form Submission" />
	<!-- Alert Component -->

	@php
		$CARDS = [
			'staticPages' => [
				'title' => "Static Pages",
				'detail' => "Fill your database with some few pre-built values for about use, privacy policy, terms and condition, help & supports. However, exisiting data will not be recreated but overwritten.",
				'route_link' => 'backend_url_direct_seeding',
				'seed_page' => 'static_pages',
			],
			'users' => [
				'title' => "Users",
				'detail' => "Fill your database with some few pre-built values for users. Two roles with admin & one role with just user. However, exisiting data based on emails will not be recreated but overwritten.",
				'route_link' => 'backend_url_direct_seeding',
				'seed_page' => 'user',
			],
		];
	@endphp

	<!------------------------------------------------------------------------------------------------>

	<div class="w-full mx-auto grid grid-cols-1 gap-4">

		@foreach ($CARDS as $key => $card)
			<div class="p-2 flex justify-between items-center border border-gray-200 rounded-lg shadow-lg">
				<div>
					<p class="text-gray-600 dark:text-gray-200">{{ $loop->iteration }}. {{ $card['title'] }}</p>
					<p class="ml-4 mt-4 text-sm text-gray-400">{{ $card['detail'] }}</p>
				</div>

				<button type="button" data-modal-target="hs-confirm-modal" data-modal-toggle="hs-confirm-modal"
					data-delete-url="{{ route($card['route_link'], ['page' => $card['seed_page']]) }}"
					class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-indigo-600 text-white hover:bg-blue-700 focus:outline-hidden focus:bg-blue-700">
					{!! \App\Helpers\IconPack::uploadCloud(['class' => 'iconPackItem w-4 h-4 text-gray-100']) !!}
					Seed
				</button>

			</div>
		@endforeach


	</div>
	{{-- Single shared modal for all purge buttons --}}
	<x-ui_items.modals.confirm-modal id="hs-confirm-modal" title="Confirm Seeding" confirmText="Yes, seed it"
		confirmColor="bg-indigo-600 hover:bg-indigo-800 focus:ring-indigo-300 dark:focus:ring-indigo-800"
		cancelText="Cancel" message="Are you sure you want to populate database? This action cannot be undone" />

		

	<!------------------------------------------------------------------------------------------------>
	<!-- ---------------- APP AREA Starts ---------------- -->



</x-app-layout>