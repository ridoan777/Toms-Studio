@include('components.blocks.header')

<body class="font-sans antialiased">
   <div id="app_blade" class="bg-gray-100">
      {{-- @include('Mastering.Layouts.navigation') --}}

      <!-- Page Content -->
      <x-blocks.sidebar />
      <x-blocks.navbar />

      <main
         class="md:ps-65 md:hs-overlay-minified:ps-13 flex flex-col bg-[##F4F5F7] pb-4 transition-all duration-300 dark:bg-neutral-800">
         <div class="min-h-screen w-full px-0 pt-15 sm:px-6 lg:px-8 flex flex-col justify-start sm:justify-start gap-8">

            <!-- ---------------- OUR CONTENTS STARTS HERE ---------------- -->
            {{ $slot }}
            <!-- ---------------- OUR CONTENTS ENDS HERE ---------------- -->

      </main>
      <!-- Page Content -->

   </div>

   @include('components.blocks.scripts')
</body>

</html>