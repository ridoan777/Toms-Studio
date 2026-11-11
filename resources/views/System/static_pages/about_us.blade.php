<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <meta name="csrf-token" content="{{ csrf_token() }}">

   <title>{{ config('app.name', 'Laravel') }}</title>
   @php
      $siteFavicon = App\Models\System\Settings\OptionSiteSetup::where('type', 'site_basic')->where('name', 'favicon')->value('value') ?? null;
   @endphp
   <link rel="icon" type="image/png" href="{{ asset($siteFavicon) }}" />

   @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
   <main class="px-12 flex flex-col bg-[##F4F5F7] pb-4 transition-all duration-300 dark:bg-neutral-800">
      <div class="min-h-screen w-full px-0 pt-15 sm:px-6 lg:px-8 flex flex-col justify-start sm:justify-start gap-2">

         <!-- ---------------- OUR CONTENTS STARTS HERE ---------------- -->
         <h1>{{ $static?->title ?? "About Us" }}</h1>
         <p>{!! $static?->body ?? null !!}</p>
         <!-- ---------------- OUR CONTENTS ENDS HERE ---------------- -->

         <div></div>
   </main>

</body>