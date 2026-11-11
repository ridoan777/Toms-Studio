<x-app-layout>

	@php
		$breadCrumbs = [
			['label' => 'Artifical Intelligence', 'url' => 'backend_project_create', 'icon' => 'layers'],
			['label' => 'AI'],
		];
	 @endphp

	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />
	<!-- ---------------- APP AREA Starts ---------------- -->

	<!-- Start Main Widgets -->
	<x-indicators.page_header_widget pageName="AI: Playground" />
	<!-- End Main Widgets -->


	<!-- Alert Component -->
	<x-indicators.alert-component message="Form Submission" />
	<!-- Alert Component -->

	<!---------------------------------------------------------------------------------------------- accordion -->
	<div>
		@php
			if(!isset($json) || !$json)
			{
				$json = null;
			}
			@endphp
			{!! nl2br($json) !!}
	</div>
	<!-- ---------------- APP AREA Starts ---------------- -->

</x-app-layout>