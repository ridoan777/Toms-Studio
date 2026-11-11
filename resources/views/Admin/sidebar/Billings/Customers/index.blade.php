<x-app-layout>

	@php
		$breadCrumbs = [
			['label' => 'Customers', 'url' => 'backend_billings_customers_index', 'icon' => 'userTrippleLine'],
		];
	 @endphp

	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />

	<!-- Start Main Widgets -->
	<x-indicators.page_header_widget pageName="Customers" />
	<!-- End Main Widgets -->

	<!-- Alert Component -->
	<x-indicators.alert-component message="Form Submission" />
	<!-- Alert Component -->

	<!-- -------------------------------- APP AREA Starts -------------------------------- -->
	<div class="px-6 py-2 grid grid-cols-2 gap-4 ">
		<a href="{{ route('backend_billings_customers_subscribers') }}">
			<div class="p-4 text-center rounded-lg bg-emerald-300 hover:bg-emerald-500 shadow-lg">
				<h3 class="text-black">Subscribers</h3>
				<p class="text-sm text-gray-700">Customers by buying recurring subscription</p>
			</div>
		</a>
		<a href="{{ route('backend_billings_customers_buyers') }}">
			<div class="p-4 text-center rounded-lg bg-orange-300 hover:bg-orange-500 shadow-lg">
				<h3 class="text-black">Buyers</h3>
				<p class="text-sm text-gray-700">Customers who paid non-recurringly/one-time</p>
			</div>
		</a>
	</div>

	<!-- -------------------------------- APP AREA Starts -------------------------------- -->

</x-app-layout>