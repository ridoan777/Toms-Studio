@props([
    'buttonText' => 'Log out',
    'class' => 'navbarDropdownMenuButton w-full text-left',
    'hasIcon' => true,
    'icon' => 'bracketRight',
    'iconClass' => 'w-5 h-5 text-gray-500',
])

<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="{{ $class }}">
        @if($hasIcon && $icon)
            {!! call_user_func([\App\Helpers\IconPack::class, $icon], ['class' => $iconClass]) !!}
        @endif
        {{ $buttonText }}
    </button>
</form>
{{-- 
    USAGE::
        <x-ui_items.forms.logout buttonText="Log Out" />
--}}