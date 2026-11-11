<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Projects Report</title>
	<style>
		{!! file_get_contents(resource_path('css/pdf.css')) !!}
	</style>
</head>

<body>

	{{-- Header --}}
	<div class="margin-top">
		<table class="w-full">
			<tr>
				<td class="w-half">
					@php
						$logo = public_path('site_assets/logo_n_icons/app_logo.png');
						$fallback = public_path('site_assets/dummies/dummy_man.webp');
						$finalLogo = file_exists($logo) ? $logo : $fallback;
					  @endphp
					<img src="{{ $finalLogo }}" width="180">
				</td>

				<td class="w-half">
					<h3>Projects Overview</h3>
					<div class="cell-both">
						Date: {{ now()->format('Y-m-d H:i') }}
					</div>
				</td>
			</tr>
		</table>
	</div>

	<hr class="hr-line">

	{{-- Projects Table --}}
	<table class="tableWrapper">
		<tr>
			<th>SN</th>
			<th>Image</th>
			<th>Project | PID</th>
			<th>Manager</th>
			<th>Phase</th>
			<th>Location</th>
			<th>Dates</th>
		</tr>

		@foreach ($Projects as $project)
			@php
				$imagePath = $project->image ? public_path('storage/' . $project->image) : null;
				$avatarPath = $project?->projectRelatingBackTo_User?->avatar ? public_path('storage/' . $project?->projectRelatingBackTo_User?->avatar) : $fallback;
			@endphp

			<tr class="items">
				<td>{{ $loop->iteration }}</td>

				<td>
					@if ($imagePath && file_exists($imagePath))
						<img src="{{ $imagePath }}" width="70">
					@else
						—
					@endif
				</td>

				<td>
					<strong>{{ $project->title }}</strong><br>
					<p style="color: #6b7280; font-size: 10px;">{{ $project->project_uid }}</p>
				</td>

				<td>
					@if ($avatarPath && file_exists($avatarPath))
						<img src="{{ $avatarPath }}" width="36">
					@else
						—
					@endif
					<p>{{ $project->projectRelatingBackTo_User->name ?? '—' }}<br></p>
					<span style="color: #6b7280; font-size: 10px;">{{ $project->projectRelatingBackTo_User->email ?? '' }}</span>
				</td>

				<td>
					@php
						$phaseClass = '';
						if(strtolower($project?->phase) === 'on-track' || strtolower($project?->phase) === 'running')
							$phaseClass = "color: #15803d; background-color:#86efac;";
						elseif(strtolower($project?->phase) === 'completed')
							$phaseClass = "color: #0369a1; background-color:#7dd3fc;";
						elseif(strtolower($project?->phase) === 'disputed' || strtolower($project?->phase) === 'issued')
							$phaseClass = "color: #a21caf; background-color:#f0abfc;";
						elseif(strtolower($project?->phase) === 'cancelled' || strtolower($project?->phase) === 'canceled')
							$phaseClass = "color: #c2410c; background-color:#fdba74;";
						else
							$phaseClass = "color: #000000;"
					@endphp
					<p style="{{ $phaseClass }}">{{ ucfirst($project->phase ?? '—') }}</p>
				</td>

				<td>
					{{ $project->location ?? '—' }}
				</td>

				<td>
					Start: {{ $project->start_date ?? '—' }}<br>
					Target: {{ $project->target_date ?? '—' }}
				</td>

			</tr>

			@if ($project->description)
				<tr class="items">
					<td colspan="8">
						<strong>Description:</strong>
						{{ strip_tags($project->description) }}
					</td>
				</tr>
			@endif
		@endforeach
	</table>

	<div class="footer margin-top">
		<div class="text-center">
			This document is system generated and intended for internal records.
		</div>
	</div>

</body>

</html>