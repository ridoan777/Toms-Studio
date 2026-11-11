@props([
	'id' => null,
	'exportRoute' => null,
	'importRoute' => null,
	'bulkDeleteRoute' => null,
	'spreadsheetButton' => "false",
	'pdfButton' => "false",
	'importButton' => "false",
	'deleteButton' => "false"
])

<div id="buttonGroup" class="	mb-8 flex justify-end items-center gap-4">

	<!------------- EXPORT EXCEL ------------->
	@if($spreadsheetButton === 'true')
		<div class="hs-tooltip [--placement:top] inline-block">
			<button type="button" class="hs-tooltip-toggle tooltipButtonStyle">
				<a href="{{ $exportRoute ? route($exportRoute, 'xlsx') : '#' }}" class="spreadsheetButton">
					{!! \App\Helpers\IconPack::excel(['class' => 'iconPackItem w-4 h-4 text-gray-100']) !!}
				</a>
				<!----->
				<span class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible tooltipText" role="tooltip">Export Excel</span>
			</button>
		</div>

	<!------------- EXPORT CSV ------------->
		<div class="hs-tooltip [--placement:top] inline-block">
			<button type="button" class="hs-tooltip-toggle tooltipButtonStyle">
				<a href="{{ $exportRoute ? route($exportRoute, 'csv') : '#' }}" class="spreadsheetButton">
					{!! \App\Helpers\IconPack::csv(['class' => 'iconPackItem w-4 h-4 text-gray-100']) !!}
				</a>
				<!----->
				<span class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible tooltipText" role="tooltip">Export CSV</span>
			</button>
		</div>
	@endif

	<!------------- EXPORT PDF ------------->
	@if($pdfButton === 'true')
		<div class="hs-tooltip [--placement:top] inline-block">
			<button type="button" class="hs-tooltip-toggle tooltipButtonStyle">
				<a href="{{ $exportRoute ? route($exportRoute, 'pdf') : '#' }}" class="spreadsheetButton bg-red-500">
					{!! \App\Helpers\IconPack::pdf(['class' => 'iconPackItem w-4 h-4 text-gray-100']) !!}
				</a>
				<!----->
				<span class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible tooltipText" role="tooltip">Export PDF</span>
			</button>
		</div>

	@endif

	<!------------- IMPORT BUTTON ------------->
	@if($importButton === 'true')
		<div class="hs-tooltip [--placement:top] inline-block">
			<div class="hs-tooltip-toggle inline-block">
				<button type="button"
						data-modal-target="hs-confirm-modal"
						data-modal-toggle="hs-confirm-modal"
						data-action-url="{{ $importRoute ? route($importRoute) : '' }}"
						class="importButton">
						{!! \App\Helpers\IconPack::uploadFile(['class' => 'iconPackItem w-4 h-4 text-gray-100']) !!}
				</button>
			</div>
			<!----->
			<span class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible tooltipText"
					role="tooltip">
				Import Excel/CSV
			</span>
		</div>
	@endif

	<!------------- BULK DELETE BUTTON ------------->
	@if($deleteButton === 'true')
		<div class="hs-tooltip [--placement:top] inline-block">
			<div class="hs-tooltip-toggle inline-block">
				<button type="submit"
						
						class="deleteButton text-xs"
						onclick="return confirm('Are you sure?')">
						Bulk {!! \App\Helpers\IconPack::trash(['class' => 'iconPackItem w-3 h-3 text-gray-100']) !!}
				</button>
			</div>
			<!----->
			<span class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible tooltipText"
					role="tooltip">
				Bulk delete
			</span>
		</div>
	@endif
</div>
