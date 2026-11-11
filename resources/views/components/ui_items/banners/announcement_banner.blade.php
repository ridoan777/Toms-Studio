@props([
	'rolesToShow' => null,
	'message' => "Hi",
	'redirect' => null,
	'redirectText' => null,
	'bgColor' => 'bg-indigo-200',
	'textColor' => 'text-gray-800',
])
@php
	$visibility = 'hidden';
	if($rolesToShow){
		$currentUser = auth()->user();
		$roles = preg_split('/[,\|]/', $rolesToShow, -1, PREG_SPLIT_NO_EMPTY);

		if ($currentUser->hasRoleKey(...$roles)) {
			$visibility = null;
		}
	}
@endphp
<div id="ab-full-width-with-dismiss-button-on-blue-bg" class="px-4 sm:px-6 lg:px-8 {{ $visibility }} rounded-lg hs-removing:-translate-y-full">
	<div class="w-full px-4 py-4 mx-auto {{ $bgColor }}">
		<div class="flex_between_center">
			<div class="">
				<p class="text-foreground-inverse {{ $textColor }}">
					{{ $message }}
					 <a class="decoration-2 underline font-medium hover:text-foreground-inverse/80 focus:outline-hidden focus:text-foreground-inverse/80"
						href="{{ $redirect ? route($redirect) : '#' }}">{{ $redirectText ?? null}}</a>
				</p>
			</div>

			<div class="ps-3 ms-auto">
				<button type="button"
					class="inline-flex rounded-lg p-1.5 text-foreground-inverse/80 hover:bg-plain/10 focus:outline-hidden focus:bg-plain/10 hover:bg-indigo-400"
					data-hs-remove-element="#ab-full-width-with-dismiss-button-on-blue-bg">
					<span class="sr-only">Dismiss</span>
					{!! \App\Helpers\IconPack::cross(['class' => 'iconPackItem w-4 h-4 text-black']) !!}
				</button>
			</div>
		</div>
	</div>
</div>

{{-- 
	USE CASE:
	<x-ui_items.banners.announcement_banner rolesToShow="admin,super_admin" message="hello" redirect="backend_roles_index" redirectText="Visit here" bgColor="bg-yellow-300" textColor="text-gray-800" />
	or,
	<x-ui_items.banners.announcement_banner message="hello" />

--}}
