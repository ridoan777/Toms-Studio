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

   <!----------- FONTS ---------->
   <link rel="preconnect" href="https://fonts.bunny.net">
   <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
   <!----------- FONTS ---------->


   <!----------- WYSIWIG: SUMMERNOTE ---------->
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

   <link href="/site_assets/tool_add_ons/wysiwig_summernote_0.9.0/summernote-lite.min.css" rel="stylesheet">
   <script src="/site_assets/tool_add_ons/wysiwig_summernote_0.9.0/summernote-lite.min.js"></script>
   {{-- <link href="/site_assets/tool_add_ons/wysiwig_summernote_0.9.0/summernote-math-katex.min.css" rel="stylesheet">
   <script src="/site_assets/tool_add_ons/wysiwig_summernote_0.9.0/summernote-math-katex.min.js"></script>
   <script src="/site_assets/tool_add_ons/wysiwig_summernote_0.9.0/summernote-math.js"></script> --}}
   <!----------- WYSIWIG: SUMMERNOTE ---------->


   <!----------- DATABASE ---------->
   <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
   <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
   <!----------- DATABASE ---------->
   
   <!----------- FILEPOND:CHUNK-FILE ---------->
   <link href="{{ asset('/site_assets/tool_add_ons/filepond/filepond.min.css') }}" rel="stylesheet">
   <script src="{{ asset('/site_assets/tool_add_ons/filepond/filepond.min.js') }}"></script>
   <script src="{{ asset('/site_assets/tool_add_ons/filepond/file-validate-size.js') }}"></script>
   <script src="{{ asset('/site_assets/tool_add_ons/filepond/file-validate-type.js') }}"></script>
   <!----------- FILEPOND:CHUNK-FILE ---------->

   <!----------- DROPIFY ---------->
   <link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css">
   <x-head.dropify />
   <!----------- DROPIFY ---------->

   <!----------- DARK MODE TRIGGER ---------->
   <x-head.dark_mode />
   <!----------- DARK MODE TRIGGER ---------->

   @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>