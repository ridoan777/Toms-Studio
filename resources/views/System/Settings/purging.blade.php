<x-app-layout>

	@php
		$breadCrumbs = [
			['label' => 'Settings', 'url' => 'backend_settings_index', 'icon' => 'gearLine'],
			['label' => 'Static Pages'],
		];
	 @endphp

	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />
	<!-- ---------------- APP AREA Starts ---------------- -->

	<!-- Start Main Widgets -->
	<x-indicators.page_header_widget pageName="Settings: Purging" />
	<!-- End Main Widgets -->


	<!-- Alert Component -->
	<x-indicators.alert-component message="Form Submission" />
	<!-- Alert Component -->

	@php
		$CARDS = [
			'unapproved' => [
				'title' => "Purge Unapproved Services",
				'detail' => "Removes unapproved services from database requested by providers, failed or missed user verification records that has been passed at least 3 days.",
				'route_link' => 'backend_settings_purging_unapproved',
			],
			'disk_cleanup' => [
				'title' => "Disk Cleanup",
				'detail' => "Removes junk files (e.g., unused/stale images, videos, etc.) from the storage. Might take some time to complete.",
				'route_link' => 'backend_settings_purging_discleanup',
			],
		];
	@endphp

	<!------------------------------------------------------------------------------------------------>

	<div class="w-full mx-auto grid grid-cols-1 gap-4">

		@foreach ($CARDS as $key => $card)
			<div class="p-2 flex justify-between items-center border border-gray-200 rounded-lg shadow-lg">
				<div>
					<p class="text-gray-600">{{ $loop->iteration }}. {{ $card['title'] }}</p>
					<p class="ml-4 mt-4 text-sm text-gray-400">{{ $card['detail'] }}</p>
				</div>

				<button type="button" data-modal-target="hs-delete-modal" data-modal-toggle="hs-delete-modal"
					data-delete-url="{{ route($card['route_link']) }}"
					class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-red-600 text-white hover:bg-red-700 focus:outline-hidden focus:bg-red-700">
					{!! \App\Helpers\IconPack::trash(['class' => 'iconPackItem w-4 h-4 text-gray-100']) !!}
					Purge
				</button>
			</div>
		@endforeach


	</div>
	{{-- Single shared modal for all purge buttons --}}
	<x-ui_items.modals.delete-modal id="hs-delete-modal" title="Confirm Deletion" confirmText="Yes, delete it"
		cancelText="Cancel" message="Are you sure you want to delete? This action cannot be undone" />


	<!------------------------------------------------------------------------------------------------>
	<!-- ---------------- APP AREA Starts ---------------- -->



</x-app-layout>