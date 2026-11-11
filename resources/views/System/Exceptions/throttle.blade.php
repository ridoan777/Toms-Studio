@include('components.blocks.header')

<div class="flex flex-col items-center justify-center min-h-screen bg-gray-100 text-center px-4">
   <div class="bg-white shadow-lg rounded-xl p-8 max-w-md w-full">
      <h1 class="text-4xl font-extrabold text-red-600 mb-4">429</h1>
      <h2 class="text-2xl font-semibold text-gray-800 mb-2">Too Many Requests</h2>
      <p class="text-gray-600 mb-6">
         You’ve sent too many requests in a short time.<br>
         @if(isset($retry_after))
            Please try again after <strong>{{ $retry_after }}</strong> seconds.
         @else
            Please wait a moment before trying again.
         @endif
      </p>

      <a href="{{ url()->previous() }}"
         class="inline-block px-6 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700">
         Go Back
      </a>
   </div>

   <p class="mt-8 text-gray-500 text-sm">
      &copy; {{ date('Y') }} {{ config('app.name') }} Admin Panel
   </p>
</div>