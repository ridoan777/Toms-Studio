<header
   class="md:ms-65 xl:hs-overlay-layout-open:me-96 md:hs-overlay-minified:ms-13 z-48 md:z-61 fixed inset-x-0 top-0 flex flex-wrap bg-blue-200 py-2.5 transition-all duration-300 md:flex-nowrap md:justify-start dark:bg-neutral-700">
   <nav class="sm:px-5.5 mx-auto flex w-full basis-full items-center justify-between px-4">
      <!-- Navbar LEFT Button Group Starts -->
      <div class="flex items-center truncate sm:gap-x-1.5">

         <!-- Sidebar Toggle Button for Mobile -->
         <button type="button"
            class="focus:outline-hidden flex size-9 flex-none items-center justify-center gap-x-3 rounded-lg text-sm text-gray-500 hover:bg-gray-100 focus:bg-gray-100 disabled:pointer-events-none disabled:opacity-50 md:hidden dark:text-neutral-400 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700"
            aria-haspopup="dialog" aria-expanded="true" aria-controls="hs-pro-sidebar" aria-label="Minify navigation"
            data-hs-overlay="#hs-pro-sidebar">
            <span>
               {!! \App\Helpers\IconPack::bracketRight(['class' => 'w-5 h-5 text-gray-500']) !!}
            </span>
            <span class="sr-only">Sidebar Toggle</span>
         </button>
         <!-- Sidebar Toggle Button for Mobile -->

         <span class="truncate text-sm font-medium text-gray-800 sm:text-base dark:text-neutral-200">
            Admin Dashboard
         </span>

         <div class="mx-4 dark:text-white">
            <span class="blink-dot" aria-hidden="true"></span>
            <a href="{{ env('FRONTEND_URL') ?? null }}">Live Site</a>
         </div>

      </div>
      <!-- Navbar LEFT Button Group Starts -->


      <!-- Navbar RIGHT Button Group Starts -->
      <div class="flex items-center sm:gap-x-1.5">

         <!-- Chat Buttons -->
         <a href="{{ route('backend_messenger_index') }}" class="hidden mx-4 hover:text-gray-100">
            {!! \App\Helpers\IconPack::message(['class' => 'iconPackItem w-5 h-5 text-gray-500 dark:text-gray-200 hover:text-gray-100']) !!}
         </a>
         <!-- Chat Buttons -->

         <!-- Dark-Mode Buttons -->
         <div class="navbarDropdownDiv hs-dropdown">
            <x-ui_items.pieces.dark-button />
         </div>
         <!--Dark-Mode Buttons -->

         <!-- Three-Dots -->
         <div class="navbarDropdownDiv hs-dropdown">
            <button id="hs-pro-ainmd" type="button" class="navbarDropdownButton">
               {!! \App\Helpers\IconPack::dotsVertical(['class' => 'w-5 h-5 text-gray-500']) !!}
            </button>

            <!-- Three-Dots Dropdown -->
            <div
               class="hs-dropdown-menu hs-dropdown-open:opacity-100 duration z-11 hidden w-32 rounded-xl border border-gray-200 bg-white opacity-0 shadow-lg transition-[opacity,margin] before:absolute before:-top-4 before:inset-s-0 before:h-5 before:w-full dark:border-neutral-700 dark:bg-neutral-950"
               role="menu" aria-orientation="vertical" aria-labelledby="hs-pro-ainmd">
               <div class="space-y-0.5 p-1">
                  <button type="button" class="navbarDropdownMenuButton">
                     <svg class="size-3.5 shrink-0" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M12 2v13" />
                        <path d="m16 6-4-4-4 4" />
                        <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8" />
                     </svg>
                     Share
                  </button>
                  <button type="button" class="navbarDropdownMenuButton">
                     <svg class="size-3.5 shrink-0" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path
                           d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z" />
                        <path d="m15 5 4 4" />
                     </svg>
                     Rename
                  </button>
                  <button type="button" class="navbarDropdownMenuButton">
                     <svg class="size-3.5 shrink-0" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <rect width="20" height="5" x="2" y="3" rx="1" />
                        <path d="M4 8v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8" />
                        <path d="M10 12h4" />
                     </svg>
                     Archive
                  </button>
                  <button type="button"
                     class="focus:outline-hidden flex w-full items-center gap-x-3 rounded-lg px-2 py-1.5 text-sm text-red-600 hover:bg-red-50 focus:bg-red-50 disabled:pointer-events-none disabled:opacity-50 dark:text-red-500 dark:hover:bg-red-500/20 dark:focus:bg-red-500/20">
                     <svg class="size-3.5 shrink-0" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M3 6h18" />
                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                        <line x1="10" x2="10" y1="11" y2="17" />
                        <line x1="14" x2="14" y1="11" y2="17" />
                     </svg>
                     Delete
                  </button>
               </div>
            </div>
            <!-- Three-Dots Dropdown -->
         </div>
         <!--Three-Dots -->

         <!-- Language Button -->
         <div class="navbarDropdownDiv hs-dropdown">
            <button id="hs-pro-aimtlg" type="button" class="navbarDropdownButton">
               {!! \App\Helpers\IconPack::language(['class' => 'w-5 h-5 text-gray-500']) !!}

               <span class="sr-only">Language</span>
            </button>

            <!-- Language Dropdown -->
            <div
               class="hs-dropdown-menu hs-dropdown-open:opacity-100 duration z-11 hidden w-40 rounded-xl border border-gray-200 bg-white opacity-0 shadow-lg transition-[opacity,margin] before:absolute before:-top-4 before:inset-s-0 before:h-5 before:w-full dark:border-neutral-700 dark:bg-neutral-950"
               role="menu" aria-orientation="vertical" aria-labelledby="hs-pro-aimtlg">
               <div class="space-y-0.5 p-1">
                  <button type="button" class="navbarDropdownMenuButton">
                     English (US)
                  </button>
                  <button type="button" class="navbarDropdownMenuButton">
                     English (UK)
                  </button>
                  <button type="button" class="navbarDropdownMenuButton">
                     Deutsch
                  </button>
                  <button type="button" class="navbarDropdownMenuButton">
                     Dansk
                  </button>
                  <button type="button" class="navbarDropdownMenuButton">
                     Italiano
                  </button>
                  <button type="button" class="navbarDropdownMenuButton">
                     中文 (繁體)
                  </button>
               </div>
            </div>
            <!-- End Language Dropdown -->
         </div>
         <!-- Language Button -->

         <div class="h-8">
            <!-- Account Dropdown (PARENT-1) -->
            <div class="navbarDropdownDiv hs-dropdown text-start [--auto-close:inside]">
               <button id="hs-dnad" type="button"
                  class="focus:outline-hidden inline-flex shrink-0 items-center gap-x-3 rounded-full p-0.5 text-start hover:bg-gray-200 focus:bg-gray-200 dark:text-neutral-500 dark:hover:bg-neutral-800 dark:hover:text-neutral-200 dark:focus:bg-neutral-800 dark:focus:text-neutral-200"
                  aria-haspopup="menu" aria-expanded="false" aria-label="Dropdown">

                  @php
                     $user = Auth::user();
                     $avatarUrl = null;

                     if ($user && !empty($user->avatar)) {
                        /** @var \App\Models\Users\User $user*/
                        if (filter_var($user->avatar, FILTER_VALIDATE_URL)) {
                           // social / external avatar
                           $avatarUrl = $user->avatar;
                        } else {
                           // local stored avatar
                           $avatarUrl = Storage::url($user->avatar);
                        }
                     }

                     $fallbackAvatar = asset('site_assets/dummies/dummy_man.webp');
                  @endphp


                  <img src="{{ $avatarUrl ?? asset($fallbackAvatar) }}"
                     alt="{{ $user?->name ? $user->name . '\'s Avatar' : 'Default Avatar' }}"
                     class="size-8 shrink-0 rounded-full object-cover" />

               </button>

               <!-- Account Dropdown (PARENT-2) -->
               <div
                  class="hs-dropdown-menu hs-dropdown-open:opacity-100 duration z-20 hidden w-60 rounded-xl border border-gray-200 bg-white opacity-0 shadow-xl transition-[opacity,margin] dark:border-neutral-700 dark:bg-neutral-900"
                  role="menu" aria-orientation="vertical" aria-labelledby="hs-dnad">
                  <div class="px-3.5 py-2">
                     <span class="font-medium text-gray-800 dark:text-neutral-300">
                        {{ auth()->user()->name }}
                     </span>
                     <p class="text-sm text-gray-500 dark:text-neutral-500">
                        {{ auth()->user()->email }}
                     </p>
                  </div>
                  <div class="border-t border-gray-200 px-4 py-2 dark:border-neutral-800">
                     <!-- Switch/Toggle -->
                     <div class="flex flex-wrap items-center justify-between gap-2">
                        <span class="flex-1 cursor-pointer text-sm text-gray-600 dark:text-neutral-400">Theme</span>
                        <div class="inline-flex cursor-pointer rounded-full bg-gray-100 p-0.5 dark:bg-neutral-800">
                           <button type="button"
                              class="hs-auto-mode-active:bg-transparent hs-auto-mode-active:shadow-none hs-dark-mode-active:bg-transparent hs-dark-mode-active:shadow-none flex size-7 items-center justify-center rounded-full bg-white text-gray-800 shadow-sm dark:text-neutral-200"
                              data-hs-theme-click-value="default">

                              {!! \App\Helpers\IconPack::lightMode(['class' => 'w-5 h-5 text-gray-500']) !!}

                              <span class="sr-only">Default (Light)</span>
                           </button>
                           <button type="button"
                              class="hs-dark-mode-active:bg-white hs-dark-mode-active:shadow-sm hs-dark-mode-active:text-neutral-800 flex size-7 items-center justify-center rounded-full text-gray-800 dark:text-neutral-200"
                              data-hs-theme-click-value="dark">

                              {!! \App\Helpers\IconPack::darkMode(['class' => 'w-5 h-5 text-gray-500']) !!}

                              <span class="sr-only">Dark</span>
                           </button>
                           <button type="button"
                              class="hs-auto-light-mode-active:bg-white hs-auto-dark-mode-active:bg-red-800 hs-auto-mode-active:shadow-sm flex size-7 items-center justify-center rounded-full text-gray-800 dark:text-neutral-200"
                              data-hs-theme-click-value="auto">

                              {!! \App\Helpers\IconPack::monitor(['class' => 'w-5 h-5 text-gray-500']) !!}

                              <span class="sr-only">Auto (System)</span>
                           </button>
                        </div>
                     </div>
                     <!-- End Switch/Toggle -->
                  </div>
                  <div class="border-t border-gray-200 p-1 dark:border-neutral-800">
                     <a class="navbarDropdownMenuButton" href="{{route('Mastering_Profile_edit')}}">
                        {!! \App\Helpers\IconPack::userSingleLine(['class' => 'w-5 h-5 text-gray-500']) !!}
                        Profile
                     </a>
                     <a class="navbarDropdownMenuButton" href="{{ route('backend_settings_index') }}">
                        {!! \App\Helpers\IconPack::gear(['class' => 'w-5 h-5 text-gray-500']) !!}
                        Settings
                     </a>
                     <x-ui_items.forms.logout buttonText="Log Out" />
                  </div>
               </div>
               <!-- End Account Dropdown (PARENT-2) -->
            </div>
            <!-- End Account Dropdown (PARENT-1) -->
         </div>
      </div>
      <!-- Navbar RIGHT Button Group Ends -->
   </nav>
</header>
<style>
   .blink-dot {
      display: inline-block;
      width: 12px;
      height: 12px;
      border-radius: 9999px;
      background-color: transparent;
      animation: blink-green 1.0s ease-in-out infinite;
   }

   @keyframes blink-green {

      0%,
      100% {
         background-color: transparent;
      }

      50% {
         background-color: #22c55e;
      }
   }
</style>