<div class="PageHeaderBlock flex justify-center px-4 sm:px-6 lg:px-8">
   <div class="w-full">
      <div class="rounded-4xl shadow-xl"
         style="background: linear-gradient(135deg, #1e3a8a, #3b82f6); box-shadow: 0 12px 30px rgba(0,0,0,0.25);">

         <div class="flex items-center gap-3 px-4 py-2 text-center sm:flex-row sm:justify-start sm:text-left">

            <!-- Animated SVG -->
            <div class="flex-shrink-0">
               <svg id="waveIcon" class="h-14 w-14 sm:h-20 sm:w-20" viewBox="0 0 64 64"
                  xmlns="http://www.w3.org/2000/svg">
                  <circle cx="32" cy="32" r="30" fill="#ffffff22" />
                  <path style="animation: wave 2.5s ease-in-out 0.5s infinite; transform-origin: center;" fill="#ffffff"
                     d="M27 20h4v24h-4V20zm6 4h4v20h-4V24zm6 4h4v16h-4V28z" />
               </svg>

               <style>
                  @keyframes wave {
                     0% {
                        transform: rotate(0deg) scale(1);
                     }

                     10% {
                        transform: rotate(-6deg) scale(1);
                     }

                     20% {
                        transform: rotate(6deg) scale(1);
                     }

                     30% {
                        transform: rotate(0deg) scale(1);
                     }

                     50% {
                        transform: rotate(0deg) scale(1.15);
                     }

                     70% {
                        transform: rotate(0deg) scale(1);
                     }

                     100% {
                        transform: rotate(0deg) scale(1);
                     }
                  }
               </style>

               {{-- --}}
            </div>

            <!-- Welcome Text -->
            <h2 class="m-0 text-lg font-bold text-gray-100 sm:text-xl lg:text-2xl">
               {{ $pageName }}
            </h2>

         </div>
      </div>
   </div>
</div>
