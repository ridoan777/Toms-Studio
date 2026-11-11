@include('components.blocks.header')

<div class="flex flex-col items-center justify-center min-h-screen bg-gray-100 dark:bg-neutral-500 text-center px-4">
   <div class="bg-white dark:bg-neutral-300 shadow-lg rounded-xl p-8 max-w-md w-full">
      <h1 class="text-4xl font-extrabold text-red-600 mb-4">{{ $code }}</h1>
      {{-- <h2 class="text-2xl font-semibold text-gray-800 mb-2">{{ $error_header }}</h2> --}}
      <h2 class="text-lg uppercase text-gray-600 mb-2">{{ $error_throwable }}</h2>
      <br>
      <p class="text-gray-600 mb-6">
         {{ $error_message }}
      </p>

      {{-- <a href="{{ url()->previous() }}" 
         class="inline-block px-6 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700">
         Go Back
      </a> --}}
      <div class="flex_xy_center gap-4">
         <button onclick="window.history.length > 1 ? history.back() : window.location.href='{{ route('dashboard') }}'"
            class="showButton">
            Go Back
         </button>
          <x-ui_items.forms.logout buttonText="Log Out" class="deleteButton" iconClass="w-5 h-5 text-gray-200" :hasIcon="false"/>
      </div>
   </div>

   <p class="mt-8 text-gray-500 dark:text-gray-200 text-sm">
      &copy; {{ date('Y') }} {{ config('app.name') }} Admin Panel
   </p>
</div>
<script>
   if (localStorage.getItem('hs_theme') === 'dark') {
      document.documentElement.classList.add('dark');
   } else {
      document.documentElement.classList.remove('dark');
   }
</script>