{{-- <img src="{{ $setLogo}}" alt="App-Logo" style="width:120px; height:auto;">
<h3>{{ $bodyTitle ?? ("Hello " . ($targetUserName ?? " There") . "!") }}</h3>
<p>{{ $bodyContent ?? "An unidentified change has been done to your account." }}</p>

@if ($attachmentPath)
<p style="font-size:10px; color:#6a7282;" class="text-gray-500">An attachment has been added to this mail.</p>
@endif --}}

<div style="width:100%; padding: 40px 0px; background-color: #f2f2f2;">
   <div style="width:600px; margin: 40px auto; padding:20px; background-color: rgba(255, 255, 255, 0.918); border-radius:16px;">
      {{-- Logo --}}
      <div style="text-align:center; margin-bottom:20px;">
         <img src="{{ $setLogo }}" alt="App-Logo" style="width:120px; height:auto;">
      </div>
      {{-- Logo --}}

      {{-- Title --}}
      <h3 style="text-align: center">
         {{ $bodyTitle ?? ("Hello " . ($targetUserName ?? "There") . "!") }}
      </h3>

      {{-- Content --}}
      <p style="text-align: center">{{ $bodyContent ?? "An unidentified change has been done to your account." }}</p>

      {{-- Attachment Notice --}}
      @if ($attachmentPath)
         <p style="font-size:12px; color:#6a7282; margin-top:10px;">
            📎 An attachment has been added to this email.
         </p>
      @endif

      {{-- Divider --}}
      <p style="margin: 40px 0px 0px 8px;">
         Regards,<br>
         {{ config('app.name') }}
      </div>
      </p>

      {{-- Footer --}}
</div>

{{--
DISK_FOLDER = config('filesystems.default');

Mail::to($originalUser?->email ?? 'fallback@example.com')->send(new AlertMail($originalUser, "Email change alert", "Your
email {$originalUser->email} has been changed to {$secondEmail} {$adminChangeMessage}.", "If you are not the one who did
this, please check.", true, $DISK_FOLDER, $originalUser->avatar));
--}}