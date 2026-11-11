<x-app-layout>

	@php
		$breadCrumbs = [
			['label' => 'Create Instrument', 'url' => 'backend_instrument_create', 'icon' => 'plus'],
			['label' => 'Instrument List', 'url' => 'backend_instrument_index', 'icon' => 'handsHolding'],
			['label' => 'Single Instrument'],
		];
	 @endphp

	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />
	
	<!-- Start Main Widgets -->
	<x-indicators.page_header_widget pageName="Modify instrument" />
	<!-- End Main Widgets -->
	
	
	<!-- Alert Component -->
	<x-indicators.alert-component message="Form Submission" />
	<!-- Alert Component -->
	<!-- ---------------- APP AREA Starts ---------------- -->


	<form method="POST" action="{{ route('backend_instrument_store') }}" class="rounded-lg p-6 bg-white dark:bg-neutral-700"
		enctype="multipart/form-data">
		@csrf

		<!--- IDENTIFIER HIDDEN INPUTS -->
		<input type="hidden" name="id" class="smallInput" value="{{ $instrument->id }}">
		<input type="hidden" name="flag" class="smallInput" value="update">
		<!--- IDENTIFIER HIDDEN INPUTS -->

		<div>

			<div class="mb-5 mt-2">
				<label for="name" class="inputLabelMedium">Instrument/Instrument name
					<span class="redLabel">*</span>
					<span class="grayLabel">(e.g., Piano, dance, vocal, etc.)</span>
				</label>
				<input type="text" name="name" class="mediumInput" placeholder="max 255-characters" value="{{ $instrument?->name ?? null }}">
			</div>
			<!-- -->

			<div class="mb-5 mt-2">
				<label for="note" class="inputLabelMedium">Note
					<span class="grayLabel">(optional)</span>
				</label>
				<input type="text" name="note" class="mediumInput" placeholder="max 255-characters" value="{{ $instrument?->note ?? null }}">
			</div>

		</div>

		<button type="submit" id="submitButton" class="showButton my-4">Update</button>
	</form>
	<!-- ---------------- APP AREA Starts ---------------- -->

</x-app-layout>