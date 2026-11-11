<button class="hs-accordion-toggle PrelineAccordionButton" aria-expanded="false"
   aria-controls="hs-destroy-collapse-one">
   {{ $toggleLabel }}
   <span class="hs-accordion-active:block hidden size-3.5">
      {!! \App\Helpers\IconPack::chevronUp(['class' => 'iconPackItem w-4 h-4 text-gray-500 dark:text-gray-200']) !!}
   </span>
   <span class="hs-accordion-active:hidden block size-3.5">
      {!! \App\Helpers\IconPack::chevronDown(['class' => 'iconPackItem w-4 h-4 text-gray-500 dark:text-gray-200']) !!}
   </span>
</button>