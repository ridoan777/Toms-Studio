@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'p-2 dark:text-gray-300 border-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) }}>
