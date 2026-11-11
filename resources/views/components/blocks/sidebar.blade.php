<div id="hs-pro-sidebar"
   class="hs-overlay hs-overlay-open:translate-x-0 w-65 hs-overlay-minified:w-13 z-60 fixed inset-y-0 inset-s-0 hidden -translate-x-full transform overflow-hidden border-e border-gray-200 bg-gray-50 shadow-lg transition-all duration-300 [--auto-close:md] md:bottom-0 md:inset-e-auto md:block md:translate-x-0 dark:border-neutral-700 dark:bg-neutral-800"
   role="dialog" tabindex="-1" aria-label="Sidebar">

   <div class="relative flex h-full max-h-full flex-col">

      <!-- --------------------- Sidebar Header --------------------- -->

      <header class="flex items-center justify-between gap-x-2 px-4 py-2.5">
         <div class="-ms-2 flex items-center gap-x-1">

            <div class="md:hs-overlay-minified:hidden">
               <a href="/" aria-label="Preline">
                  {{-- <x-ui_items.pieces.application-logo logoClass="ridoan" classLight="mx-auto w-full"
                     classDark="mx-auto full" /> --}}
                  <x-ui_items.pieces.application-logo logoClass="app_logo" classLight="mx-auto" classDark="mx-auto" />
               </a>
            </div>

            <!-- Sidebar Toggle (TO EXPAND) -->
            <button type="button" class="md:hs-overlay-minified:flex sidebarToggleButtonNonSmallDevice"
               aria-haspopup="dialog" aria-expanded="true" aria-controls="hs-pro-sidebar" aria-label="Minify navigation"
               data-hs-overlay-minifier="#hs-pro-sidebar">

               <span class="hs-overlay-minified:block hidden">
                  {!! \App\Helpers\IconPack::bracketRight(['class' => 'iconPackItem w-5 h-5 text-gray-500 dark:text-gray-200']) !!}
               </span>
               <span class="hs-overlay-minified:hidden">
                  {!! \App\Helpers\IconPack::bracketRight(['class' => 'iconPackItem w-5 h-5 text-gray-500 dark:text-gray-200']) !!}
               </span>
               <span class="sr-only">Sidebar Toggle</span>

            </button>
            <!-- End Sidebar Toggle (TO EXPAND) -->

            <div class="md:hs-overlay-minified:hidden hidden sm:block"></div>
         </div>

         <!-- Sidebar Toggle (TO COLLAPSE) -->
         <button type="button" class="md:hs-overlay-minified:hidden sidebarToggleButtonNonSmallDevice md:flex"
            aria-haspopup="dialog" aria-expanded="true" aria-controls="hs-pro-sidebar" aria-label="Minify navigation"
            data-hs-overlay-minifier="#hs-pro-sidebar">

            <span class="hs-overlay-minified:block hidden">
               {!! \App\Helpers\IconPack::bracketLeft(['class' => 'iconPackItem w-5 h-5 text-gray-500 dark:text-gray-200']) !!}
            </span>
            <span class="hs-overlay-minified:hidden">
               {!! \App\Helpers\IconPack::bracketLeft(['class' => 'iconPackItem w-5 h-5 text-gray-500 dark:text-gray-200']) !!}
            </span>
            <span class="sr-only">Sidebar Toggle</span>

         </button>
         <!-- End Sidebar Toggle (TO COLLAPSE) -->

         <!-- Sidebar Toggle OPENED in SM devices -->
         <button type="button"
            class="focus:outline-hidden flex size-6 items-center justify-center gap-x-3 rounded-full border border-gray-200 bg-gray-200 text-sm text-gray-600 hover:bg-gray-100 focus:bg-gray-100 disabled:pointer-events-none disabled:opacity-50 md:hidden dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-200 dark:focus:bg-neutral-700 dark:focus:text-neutral-200"
            data-hs-overlay="#hs-pro-sidebar" aria-label="Close Sidebar">

            {!! \App\Helpers\IconPack::closeX(['class' => 'iconPackItem w-5 h-5 text-gray-500 dark:text-gray-200']) !!}

            <span class="sr-only">Close</span>
         </button>
         <!-- End Sidebar Toggle OPENED in SM devices -->

      </header>
      <!-- --------------------- /Sidebar Header --------------------- -->


      <!-- --------------------- Sidebar-Body --------------------- -->
      <div
         class="h-full overflow-hidden overflow-y-auto py-5 transition-all duration-300 ease-in-out [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500 [&::-webkit-scrollbar-track]:bg-gray-100 dark:[&::-webkit-scrollbar-track]:bg-neutral-700 [&::-webkit-scrollbar]:w-2"
         id="sidebarBody">

         <nav class="hs-accordion-group flex w-full flex-col flex-wrap" data-hs-accordion-always-open>

            <ul class="flex flex-col space-y-1">
               <li>
                  <a class="dropdownMenuButton" href="{{ route('dashboard') }}">
                     {!! \App\Helpers\IconPack::homeLines(['class' => 'iconPackItem w-5 h-5 mr-2 text-gray-500 dark:text-gray-200']) !!}
                     Dashboard
                  </a>
               </li>

               @if(app()->environment(['local', 'staging']))
                  <li class="hidden my-1" id="users-accordion-7">
                     <button type="button" class="dropdownMenuButton w-full" data-target="users-accordion-child-7"
                        data-state="closed">
                        <span>
                           {!! \App\Helpers\IconPack::hammer(['class' => 'iconPackItem w-5 h-5 mr-2 text-gray-500 dark:text-gray-200']) !!}
                        </span>
                        Project Management
                        <span class="accordion-chevron-up ms-auto hidden">
                           {!! \App\Helpers\IconPack::chevronUp(['class' => 'iconPackItem w-4 h-4 text-gray-500 dark:text-gray-200']) !!}
                        </span>
                        <span class="accordion-chevron-down ms-auto block">
                           {!! \App\Helpers\IconPack::chevronDown(['class' => 'iconPackItem w-4 h-4 text-gray-500 dark:text-gray-200']) !!}
                        </span>
                     </button>
                     <div id="users-accordion-child-7" class="custom-accordion-content w-full overflow-hidden"
                        style="max-height: 0; opacity: 0;">
                        <ul class="space-y-1 ps-8 pt-1">
                           <li>
                              <a class="sidebarAnchorTagStyle" href="{{ route('backend_project_create')}}">➕ Add</a>
                           </li>
                           <li>
                              <a class="sidebarAnchorTagStyle" href="{{ route('backend_project_index')}}">All Project</a>
                           </li>
                        </ul>
                     </div>
                  </li>
               @endif
               
               <li class="my-1">
                  <a class="sidebarNoDropdownButton" href="{{ route('backend_instrument_index') }}">
                     {!! \App\Helpers\IconPack::music(['class' => 'iconPackItem w-5 h-5 mr-2 text-gray-500 dark:text-gray-200']) !!}
                     Instruments
                  </a>
               </li>

               <li class="my-1" id="users-accordion-8">
                  <button type="button" class="dropdownMenuButton w-full" data-target="users-accordion-child-8"
                     data-state="closed">
                     <span>
                        {!! \App\Helpers\IconPack::hammer(['class' => 'iconPackItem w-5 h-5 mr-2 text-gray-500 dark:text-gray-200']) !!}
                     </span>
                     Lesson Management
                     <span class="accordion-chevron-up ms-auto hidden">
                        {!! \App\Helpers\IconPack::chevronUp(['class' => 'iconPackItem w-4 h-4 text-gray-500 dark:text-gray-200']) !!}
                     </span>
                     <span class="accordion-chevron-down ms-auto block">
                        {!! \App\Helpers\IconPack::chevronDown(['class' => 'iconPackItem w-4 h-4 text-gray-500 dark:text-gray-200']) !!}
                     </span>
                  </button>
                  <div id="users-accordion-child-8" class="custom-accordion-content w-full overflow-hidden"
                     style="max-height: 0; opacity: 0;">
                     <ul class="space-y-1 ps-8 pt-1">
                        <li>
                           <a class="sidebarAnchorTagStyle" href="{{ route('backend_lesson_create') }}">➕ Add Lesson</a>
                        </li>
                        <li>
                           <a class="sidebarAnchorTagStyle" href="{{ route('backend_group_lesson_create') }}">➕ Add Group Lesson</a>
                        </li>
                        <li>
                           <a class="sidebarAnchorTagStyle" href="{{ route('backend_lesson_index')}}">All Lessons</a>
                        </li>
                        <li>
                           <a class="sidebarAnchorTagStyle" href="{{ route('backend_group_lesson_index')}}">All Grouped Lessons</a>
                        </li>
                     </ul>
                  </div>
               </li>

               <li class="my-1" id="users-accordion-9">
                  <button type="button" class="dropdownMenuButton w-full" data-target="users-accordion-child-9"
                     data-state="closed">
                     <span>
                        {!! \App\Helpers\IconPack::documentLined(['class' => 'iconPackItem w-5 h-5 mr-2 text-gray-500 dark:text-gray-200']) !!}
                     </span>
                     Lesson Notes
                     <span class="accordion-chevron-up ms-auto hidden">
                        {!! \App\Helpers\IconPack::chevronUp(['class' => 'iconPackItem w-4 h-4 text-gray-500 dark:text-gray-200']) !!}
                     </span>
                     <span class="accordion-chevron-down ms-auto block">
                        {!! \App\Helpers\IconPack::chevronDown(['class' => 'iconPackItem w-4 h-4 text-gray-500 dark:text-gray-200']) !!}
                     </span>
                  </button>
                  <div id="users-accordion-child-9" class="custom-accordion-content w-full overflow-hidden"
                     style="max-height: 0; opacity: 0;">
                     <ul class="space-y-1 ps-8 pt-1">
                        <li>
                           <a class="sidebarAnchorTagStyle" href="{{ route('backend_lesson_note_create') }}">➕ Add Notes</a>
                        </li>
                        <li>
                           <a class="sidebarAnchorTagStyle" href="{{ route('backend_lesson_note_index')}}">All Notes</a>
                        </li>
                     </ul>
                  </div>
               </li>

               @php
                  $isHistoryActive = request()->routeIs('backend_lesson_histories_*');
               @endphp

               <li class="my-1">
                  <a class="sidebarNoDropdownButton" href="{{ route('backend_lesson_histories_index') }}">
                     {!! \App\Helpers\IconPack::clock(['class' => 'iconPackItem w-5 h-5 mr-2 text-gray-500 dark:text-gray-200']) !!}
                     Schedule Histories
                  </a>
               </li>

               @php
                  $isBarActive = request()->routeIs('backend_bar_*');
               @endphp


               <li class="my-1">
                  <a href="{{ route('dashboard') }}" class="sidebarSectionText">
                     <span class="invisible">
                        {!! \App\Helpers\IconPack::calenderLine(['class' => 'iconPackItem w-5 h-5 mr-2 text-gray-500 dark:text-gray-200']) !!}
                     </span>
                     <span class="text-gray-500 dark:text-gray-200">
                        MANAGEMENT
                     </span>
                  </a>
               </li>

               <li class="my-1 hidden">
                  <a class="sidebarNoDropdownButton" href="{{ route('backend_analytics_index') }}">
                     {!! \App\Helpers\IconPack::chartLine(['class' => 'iconPackItem w-5 h-5 mr-2 text-gray-500 dark:text-gray-200']) !!}
                     Analytics
                  </a>
               </li>

               <li class="my-1" id="users-accordion-6">
                  <button type="button" class="dropdownMenuButton w-full" data-target="users-accordion-child-6"
                     data-state="closed">
                     <span>
                        {!! \App\Helpers\IconPack::dollarSack(['class' => 'iconPackItem w-5 h-5 mr-2 text-gray-500 dark:text-gray-200']) !!}
                     </span>
                     Billings
                     <span class="accordion-chevron-up ms-auto hidden">
                        {!! \App\Helpers\IconPack::chevronUp(['class' => 'iconPackItem w-4 h-4 text-gray-500 dark:text-gray-200']) !!}
                     </span>
                     <span class="accordion-chevron-down ms-auto block">
                        {!! \App\Helpers\IconPack::chevronDown(['class' => 'iconPackItem w-4 h-4 text-gray-500 dark:text-gray-200']) !!}
                     </span>
                  </button>
                  <div id="users-accordion-child-6" class="custom-accordion-content w-full overflow-hidden"
                     style="max-height: 0; opacity: 0;">
                     <ul class="space-y-1 ps-8 pt-1">
                        {{-- <li class="hidden">
                           <a class="sidebarAnchorTagStyle"
                              href="route('backend_subscription_tiers_index')">Subscription Tiers </a>
                        </li> --}}
                        <li>
                           <a class="sidebarAnchorTagStyle"
                              href="{{ route('backend_billings_payables_index')}}">Payable Lists</a>
                        </li>
                        {{-- <li class="hidden">
                           <a class="sidebarAnchorTagStyle"
                              href="{{ route('backend_billings_customers_index') }}">Customers</a>
                        </li> --}}
                        <li>
                           <a class="sidebarAnchorTagStyle"
                              href="{{ route('backend_transactions_index')}}">Transactions</a>
                        </li>
                     </ul>
                  </div>
               </li>

               <li class="hidden my-1" id="users-accordion-5">
                  <button type="button" class="dropdownMenuButton w-full" data-target="users-accordion-child-5"
                     data-state="closed">
                     <span>
                        {!! \App\Helpers\IconPack::message(['class' => 'iconPackItem w-5 h-5 mr-2 text-gray-500 dark:text-gray-200']) !!}
                     </span>
                     Communications
                     <span class="accordion-chevron-up ms-auto hidden">
                        {!! \App\Helpers\IconPack::chevronUp(['class' => 'iconPackItem w-4 h-4 text-gray-500 dark:text-gray-200']) !!}
                     </span>
                     <span class="accordion-chevron-down ms-auto block">
                        {!! \App\Helpers\IconPack::chevronDown(['class' => 'iconPackItem w-4 h-4 text-gray-500 dark:text-gray-200']) !!}
                     </span>
                  </button>
                  <div id="users-accordion-child-5" class="custom-accordion-content w-full overflow-hidden"
                     style="max-height: 0; opacity: 0;">
                     <ul class="space-y-1 ps-8 pt-1">
                        <li>
                           <a class="sidebarAnchorTagStyle"
                              href="{{ route('backend_chat_blocklist_index')}}">Chat::Blocklist</a>
                        </li>
                        <li>
                           <a class="sidebarAnchorTagStyle"
                              href="{{ route('backend_chat_report_index')}}">Chat::Reports</a>
                        </li>
                     </ul>
                  </div>
               </li>

               @php
                  $isBarActive = request()->routeIs('backend_bar_*');

                  $isUserActive = request()->routeIs(
                     'backend_all_user',
                     'backend_create_users',
                     'backend_user_pending',
                     'backend_user_blacklist'
                  );
               @endphp

               <li class="my-1" id="users-accordion-1">
                  <button type="button" class="dropdownMenuButton w-full" data-target="users-accordion-child-1"
                     data-state="{{ $isUserActive ? 'open' : 'closed' }}">

                     <span>
                        {!! \App\Helpers\IconPack::userTripple(['class' => 'iconPackItem w-5 h-5 mr-2 text-gray-500 dark:text-gray-200']) !!}
                     </span>

                     Users

                     <span class="accordion-chevron-up ms-auto {{ $isUserActive ? 'block' : 'hidden' }}">
                        {!! \App\Helpers\IconPack::chevronUp(['class' => 'iconPackItem w-4 h-4']) !!}
                     </span>

                     <span class="accordion-chevron-down ms-auto {{ $isUserActive ? 'hidden' : 'block' }}">
                        {!! \App\Helpers\IconPack::chevronDown(['class' => 'iconPackItem w-4 h-4']) !!}
                     </span>
                  </button>

                  <div id="users-accordion-child-1" class="custom-accordion-content w-full overflow-hidden"
                     style="max-height: {{ $isUserActive ? '500px' : '0' }}; opacity: {{ $isUserActive ? '1' : '0' }};">

                     <ul class="space-y-1 ps-8 pt-1">

                        <li>
                           <a class="sidebarAnchorTagStyle {{ request()->routeIs('backend_create_users') ? 'text-blue-500 font-medium' : '' }}"
                              href="{{ route('backend_create_users') }}">
                              + Create New Users
                           </a>
                        </li>

                        <li>
                           <a class="sidebarAnchorTagStyle {{ request()->routeIs('backend_all_user') ? 'text-blue-500 font-medium' : '' }}"
                              href="{{ route('backend_all_user') }}">
                              All Users
                           </a>
                        </li>

                        <li>
                           <a class="sidebarAnchorTagStyle {{ request()->routeIs('backend_user_admins') ? 'text-blue-500 font-medium' : '' }}"
                              href="{{ route('backend_user_admins') }}">
                              Admins
                           </a>
                        </li>

                        <li>
                           <a class="sidebarAnchorTagStyle {{ request()->routeIs('backend_user_role_based', ['customRole' => 'teacher']) ? 'text-blue-500 font-medium' : '' }}"
                              href="{{ route('backend_user_role_based', ['customRole' => 'teacher']) }}">
                              All Teachers
                           </a>
                        </li>

                        <li>
                           <a class="sidebarAnchorTagStyle {{ request()->routeIs('backend_user_role_based', ['customRole' => 'student']) ? 'text-blue-500 font-medium' : '' }}"
                              href="{{ route('backend_user_role_based', ['customRole' => 'student']) }}">
                              All Students
                           </a>
                        </li>

                        {{-- <li>
                           <a class="sidebarAnchorTagStyle {{ request()->routeIs('backend_user_students') ? 'text-blue-500 font-medium' : '' }}"
                              href="{{ route('backend_user_students') }}">
                              All Students
                           </a>
                        </li> --}}

                        <li>
                           <a class="sidebarAnchorTagStyle {{ request()->routeIs('backend_user_pending') ? 'text-blue-500 font-medium' : '' }}"
                              href="{{ route('backend_user_pending') }}">
                              Pending Users
                           </a>
                        </li>

                        <li>
                           <a class="sidebarAnchorTagStyle {{ request()->routeIs('backend_user_blacklist') ? 'text-blue-500 font-medium' : '' }}"
                              href="{{ route('backend_user_blacklist') }}">
                              Blacklisted
                           </a>
                        </li>

                     </ul>
                  </div>
               </li>

               <li class="my-1" id="users-accordion-2">
                  <button type="button" class="dropdownMenuButton w-full" data-target="users-accordion-child-2"
                     data-state="closed">
                     <span>
                        {!! \App\Helpers\IconPack::userLock(['class' => 'iconPackItem w-5 h-5 mr-2 text-gray-500 dark:text-gray-200']) !!}
                     </span>
                     Role Management
                     <span class="accordion-chevron-up ms-auto hidden">
                        {!! \App\Helpers\IconPack::chevronUp(['class' => 'iconPackItem w-4 h-4 text-gray-500 dark:text-gray-200']) !!}
                     </span>
                     <span class="accordion-chevron-down ms-auto block">
                        {!! \App\Helpers\IconPack::chevronDown(['class' => 'iconPackItem w-4 h-4 text-gray-500 dark:text-gray-200']) !!}
                     </span>
                  </button>
                  <div id="users-accordion-child-2" class="custom-accordion-content w-full overflow-hidden"
                     style="max-height: 0; opacity: 0;">
                     <ul class="space-y-1 ps-8 pt-1">
                        <li>
                           <a class="sidebarAnchorTagStyle" href="{{ route('backend_roles_index') }}">
                              Roles
                              <span>
                                 {!! \App\Helpers\IconPack::plus(['class' => 'iconPackItem w-4 h-4 text-gray-400']) !!}
                              </span>
                           </a>
                        </li>
                     </ul>
                  </div>
               </li>

               <li class="hidden my-1" id="users-accordion-3">
                  <button type="button" class="dropdownMenuButton w-full" data-target="users-accordion-child-3"
                     data-state="closed">
                     <span>
                        {!! \App\Helpers\IconPack::handsHolding(['class' => 'iconPackItem w-5 h-5 mr-2 text-gray-500 dark:text-gray-200']) !!}
                     </span>
                     Dispute Management
                     <span class="accordion-chevron-up ms-auto hidden">
                        {!! \App\Helpers\IconPack::chevronUp(['class' => 'iconPackItem w-4 h-4 text-gray-500 dark:text-gray-200']) !!}
                     </span>
                     <span class="accordion-chevron-down ms-auto block">
                        {!! \App\Helpers\IconPack::chevronDown(['class' => 'iconPackItem w-4 h-4 text-gray-500 dark:text-gray-200']) !!}
                     </span>
                  </button>
                  <div id="users-accordion-child-3" class="custom-accordion-content w-full overflow-hidden"
                     style="max-height: 0; opacity: 0;">
                     <ul class="space-y-1 ps-8 pt-1">
                        <li>
                           <a class="sidebarAnchorTagStyle" href="{{ route('backend_disputes_report_index') }}">
                              Disputes
                              <span>
                                 {!! \App\Helpers\IconPack::plus(['class' => 'iconPackItem w-4 h-4 text-gray-400']) !!}
                              </span>
                           </a>
                        </li>
                     </ul>
                  </div>
               </li>

               <li class="my-1">
                  <a href="{{ route('dashboard') }}" class="sidebarSectionText">
                     <span class="invisible">
                        {!! \App\Helpers\IconPack::calenderLine(['class' => 'iconPackItem w-5 h-5 mr-2 text-gray-500 dark:text-gray-200']) !!}
                     </span>
                     <span class="text-gray-500 dark:text-gray-200">
                        LOGS
                     </span>
                  </a>
               </li>

               <li class="my-1" id="users-accordion-4">
                  <button type="button" class="dropdownMenuButton w-full" data-target="users-accordion-child-4"
                     data-state="closed">
                     <span>
                        {!! \App\Helpers\IconPack::binocular(['class' => 'iconPackItem w-5 h-5 mr-2 text-gray-500 dark:text-gray-200']) !!}
                     </span>
                     Logs & Notifications
                     <span class="accordion-chevron-up ms-auto hidden">
                        {!! \App\Helpers\IconPack::chevronUp(['class' => 'iconPackItem w-4 h-4 text-gray-500 dark:text-gray-200']) !!}
                     </span>
                     <span class="accordion-chevron-down ms-auto block">
                        {!! \App\Helpers\IconPack::chevronDown(['class' => 'iconPackItem w-4 h-4 text-gray-500 dark:text-gray-200']) !!}
                     </span>
                  </button>
                  <div id="users-accordion-child-4" class="custom-accordion-content w-full overflow-hidden"
                     style="max-height: 0; opacity: 0;">
                     <ul class="space-y-1 ps-8 pt-1">
                        <li>
                           <a class="sidebarAnchorTagStyle" href="{{ route('backend_system_in_app_notify_index') }}">
                              Notifications
                              <span>
                                 {!! \App\Helpers\IconPack::plus(['class' => 'iconPackItem w-4 h-4 text-gray-400']) !!}
                              </span>
                           </a>
                        </li>
                        <li class="hidden">
                           <a class="sidebarAnchorTagStyle" href="{{ route('backend_roles_index') }}">
                              Admin Logins
                              <span>
                                 {!! \App\Helpers\IconPack::plus(['class' => 'iconPackItem w-4 h-4 text-gray-400']) !!}
                              </span>
                           </a>
                        </li>
                     </ul>
                  </div>
               </li>

               <li class="hidden my-1">
                  <a href="{{ route('dashboard') }}" class="sidebarSectionText">
                     <span class="invisible">
                        {!! \App\Helpers\IconPack::calenderLine(['class' => 'iconPackItem w-5 h-5 mr-2 text-gray-500 dark:text-gray-200']) !!}
                     </span>
                     <span class="text-gray-500 dark:text-gray-200">
                        ASSISTANCE
                     </span>
                  </a>
               </li>

               <li class="hidden my-1">
                  <a class="sidebarNoDropdownButton" href="{{ route('backend.components') }}">
                     {!! \App\Helpers\IconPack::hourglassHalf(['class' => 'iconPackItem w-5 h-5 mr-2 text-gray-500 dark:text-gray-200']) !!}
                     Components
                  </a>
               </li>

               <li class="hidden my-1">
                  <a class="sidebarNoDropdownButton" href="#">
                     {!! \App\Helpers\IconPack::book(['class' => 'iconPackItem w-5 h-5 mr-2 text-gray-500 dark:text-gray-200']) !!}
                     Documentation
                  </a>
               </li>
            </ul>

         </nav>
      </div>
      <!-- --------------------- Sidebar-Body --------------------- -->
   </div>

</div>

<!--- -------------------- Style+Script to smoother sidebar menus -------------------- -->

<script>
   document.querySelectorAll('.dropdownMenuButton[data-target]').forEach(button => {
      button.addEventListener('click', function () {
         const targetId = this.dataset.target;
         const content = document.getElementById(targetId);
         const state = this.dataset.state;
         const chevronUp = this.querySelector('.accordion-chevron-up');
         const chevronDown = this.querySelector('.accordion-chevron-down');

         if (!content) return;

         content.style.transition = 'max-height 0.4s ease, opacity 0.3s ease';

         if (state === 'closed') {
            // --- Expand ---
            content.style.maxHeight = content.scrollHeight + 'px';
            content.style.opacity = '1';
            this.dataset.state = 'open';
            chevronUp?.classList.remove('hidden');
            chevronDown?.classList.add('hidden');

            // If it's child accordion, expand parent too
            const parentAccordion = this.closest('.custom-accordion-content');
            if (parentAccordion) {
               parentAccordion.style.maxHeight =
                  parentAccordion.scrollHeight + 500 + 'px';
            }

         } else {
            // --- Collapse ---
            content.style.maxHeight = '0px';
            content.style.opacity = '0';
            this.dataset.state = 'closed';
            chevronUp?.classList.add('hidden');
            chevronDown?.classList.remove('hidden');

            // If it's child accordion, shrink parent too
            const parentAccordion = this.closest('.custom-accordion-content');
            if (parentAccordion) {
               parentAccordion.style.maxHeight =
                  parentAccordion.scrollHeight - 500 + 'px';
            }
         }
      });
   });
</script>
<!--- -------------------- Style+Script to smoother sidebar menus -------------------- -->


<!-- -------------------- Sidebar Toggle Button -------------------- -->
<script>
   document.addEventListener('DOMContentLoaded', function () {
      // Fix mobile sidebar overlay issue
      const sidebarToggle = document.querySelector('[data-hs-overlay="#hs-pro-sidebar"]');
      const sidebar = document.getElementById('hs-pro-sidebar');

      if (sidebarToggle && sidebar) {
         sidebarToggle.addEventListener('click', function () {
            // Remove any stuck backdrop after a short delay
            setTimeout(() => {
               const backdrop = document.getElementById('hs-pro-sidebar-backdrop');
               if (backdrop && !sidebar.classList.contains('open')) {
                  backdrop.remove();
               }
            }, 300);
         });

         // Also handle clicks on the backdrop itself
         document.addEventListener('click', function (e) {
            if (e.target && e.target.id === 'hs-pro-sidebar-backdrop') {
               e.target.remove();
            }
         });
      }
   });
</script>
<!-- -------------------- Sidebar Toggle Button -------------------- -->