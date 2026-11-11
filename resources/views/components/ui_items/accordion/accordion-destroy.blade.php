   <div class="flex flex-wrap gap-2 mt-4">
      <button type="button" id="hs-destroy-accordion"
         class="py-1 px-2 inline-flex items-center gap-x-1 text-sm rounded-lg border border-gray-200 bg-white text-gray-800 hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:text-white dark:border-neutral-700 dark:hover:bg-neutral-800">
         <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 6 6 18"></path>
            <path d="m6 6 12 12"></path>
         </svg>
         <!-- Destroy accordion -->
      </button>
      <button type="button" id="hs-auto-init-accordion"
         class="py-1 px-2 inline-flex items-center gap-x-1 text-sm rounded-lg border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200 disabled:opacity-50 disabled:pointer-events-none dark:bg-white/10 dark:hover:bg-white/20 dark:text-white dark:hover:text-white"
         disabled="">
         <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
            <path d="M3 3v5h5"></path>
            <path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"></path>
            <path d="M16 16h5v5"></path>
         </svg>
         <!-- Reinitialize accordion -->
      </button>
   </div>

   <script>
      window.addEventListener('load', () => {
         (function () {
            const accordions = document.querySelectorAll('.hs-accordion-to-destroy');
            const destroy = document.querySelector('#hs-destroy-accordion');
            const autoInit = document.querySelector('#hs-auto-init-accordion');

            destroy.addEventListener('click', () => {
               accordions.forEach((el) => {
                  const { element } = HSAccordion.getInstance(el, true);

                  element.destroy();
               });

               destroy.setAttribute('disabled', 'disabled');
               autoInit.removeAttribute('disabled');
            });

            autoInit.addEventListener('click', () => {
               HSAccordion.autoInit();

               autoInit.setAttribute('disabled', 'disabled');
               destroy.removeAttribute('disabled');
            });
         })();
      });
   </script>