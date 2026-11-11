<ol class="flex items-center whitespace-nowrap border-y border-gray-200 bg-gray-100 dark:bg-neutral-700 p-2 dark:border-neutral-700 sticky top-14 z-50 shadow-lg">

   <li class="inline-flex items-center">
      <a class="focus:outline-hidden flex items-center text-[12px] text-gray-500 hover:text-blue-600 focus:text-blue-600 md:text-sm dark:text-neutral-200 dark:hover:text-blue-500 dark:focus:text-blue-500"
         href="{{ route('dashboard') }}">
         {!! \App\Helpers\IconPack::homeLines(['class' => 'w-5 h-5 mx-2 text-gray-500 dark:text-neutral-200']) !!}
         Dashboard
      </a>
      {!! \App\Helpers\IconPack::chevronRight([
          'class' => 'w-4 h-4 mx-2 shrink-0 text-gray-400 dark:text-neutral-200',
      ]) !!}
   </li>

   @if(isset($crumbs) && $crumbs)
      @foreach ($crumbs as $crumb)
         <li class="inline-flex items-center">

            {{-- Icon if provided --}}
            @if (!empty($crumb['icon']))
               {!! \App\Helpers\IconPack::{$crumb['icon']}(['class' => 'w-5 h-5 mx-2 text-gray-500']) !!}
            @endif

            {{-- Link or current page --}}
            @if (isset($crumb['url']))
               <a href="{{ route($crumb['url'], $crumb['params'] ?? []) }}"
                  class="focus:outline-hidden flex items-center text-[12px] text-gray-500 hover:text-blue-600 focus:text-blue-600 md:text-sm dark:text-neutral-200 dark:hover:text-blue-500 dark:focus:text-blue-500">
                  {{ $crumb['label'] }}
               </a>
            @else
               <span class="truncate text-[12px] font-semibold text-gray-800 md:text-sm dark:text-neutral-200"
                  aria-current="page">
                  {{ $crumb['label'] }}
               </span>
            @endif

            @if (!$loop->last)
               {!! \App\Helpers\IconPack::chevronRight([
                  'class' => 'w-4 h-4 mx-2 shrink-0 text-gray-400 dark:text-neutral-600',
               ]) !!}
            @endif

         </li>
      @endforeach
   @endif
</ol>
