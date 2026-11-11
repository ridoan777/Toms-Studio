<x-guest-layout>
	<div class="mb-4 text-sm text-gray-600">
		<p style="text-align:center;">{{ __('Thanks for signing up! An email has been sent to ') }}<span style="color:dodgerblue; font-weight: bold;">{{ $requestEmail }}</span>.</p>
		<p style="text-align:center;">{{ __('Before getting started, you need to verify your email address by clicking on the link we just emailed to you. If you didn\'t receive the email, we will gladly send you another.') }}</p>
	</div>

	@if (session('status') == 'verification-link-sent')
		<div class="mb-4 font-medium text-sm text-green-600">
			{{ __('A new verification link has been sent to the email address you provided during registration.') }}
		</div>
	@endif

	<div class="mt-4 flex items-center justify-between">
		<form method="POST" action="{{ route('verification.send') }}">
			@csrf

			<div>
				<button type="submit" class="darkButton">Resend Verification Email</button>
			</div>
		</form>

		<form method="POST" action="{{ route('logout') }}">
			@csrf

			<button type="submit"
				class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
				{{ __('Log Out') }}
			</button>
		</form>
	</div>
</x-guest-layout>