@props([
   'name',
   'value' => '',
   'height' => 250,
   'label' => null,
   'placeholder' => '',
   'disabled' => false,
])

@if($label)
   <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
      {{ $label }}
   </label>

  @endif

<textarea @disabled($disabled) 
    id="{{ $name }}" name="{{ $name }}" 
    {{ $attributes->merge(['class' => 'summernote w-full rounded-md shadow-sm border border-gray-300 dark:border-gray-600']) }}
    placeholder="{{ $placeholder }}"
>
{!! old($name, $value) !!}
</textarea>