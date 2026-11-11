<!-- Alert Component -->
<x-indicators.alert-component message="Form Submission" />
<!-- Alert Component -->

<section>
	<header>
		<h2 class="text-lg font-medium text-gray-900 dark:text-white">
			{{ __('Profile Information') }}
		</h2>

		<p class="mt-1 text-sm text-gray-600 dark:text-blue-300">
			{{ __("Update your account's profile information and email address.") }}
		</p>
	</header>

	<form id="send-verification" method="post" action="{{ route('verification.send') }}">
		@csrf
	</form>

	<form method="post" action="{{ route('profile_update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
		@csrf
		@method('patch')

		{{-- Name --}}
		<div>
			<x-ui_items.forms.input-label for="name" :value="__('Name')" />
			<x-ui_items.forms.text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
			<x-ui_items.forms.input-error class="mt-2" :messages="$errors->get('name')" />
		</div>

		{{-- Email --}}
		<div>
			<x-ui_items.forms.input-label for="email" :value="__('Email')" />
			<x-ui_items.forms.text-input id="email" name="email" type="email" class="mt-1 block w-full"
				:value="old('email', $user->email)" required autocomplete="username" />
			<x-ui_items.forms.input-error class="mt-2" :messages="$errors->get('email')" />

			@if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
				<div>
					<p class="text-sm mt-2 text-gray-800">
						{{ __('Your email address is unverified.') }}

						<button form="send-verification"
							class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
							{{ __('Click here to send the verification email.') }}
						</button>
					</p>

					@if (session('status') === 'verification-link-sent')
						<p class="mt-2 font-medium text-sm text-green-600">
							{{ __('A new verification link has been sent to your email address.') }}
						</p>
					@endif
				</div>
			@endif
		</div>

		{{-- DOB --}}
		<div class="hidden">
			<x-ui_items.forms.input-label for="birth_date" :value="__('Birth date')" />
			<x-ui_items.forms.text-input id="birth_date" name="birth_date" type="date" class="mt-1 block w-full"
				:value="old('birth_date', $user->birth_date)" autocomplete="date" />
			<x-ui_items.forms.input-error class="mt-2" :messages="$errors->get('birth_date')" />
		</div>

		{{-- user_role --}}
		<div>
			<x-ui_items.forms.input-label for="user_role" :value="__('user_role')" />
			<x-ui_items.forms.text-input id="user_role" name="user_role" type="text" class="mt-1 block w-full"
				:value="old('user_role', $user->user_role)" autocomplete="street-user_role" readonly/>
			<x-ui_items.forms.input-error class="mt-2" :messages="$errors->get('user_role')" />
		</div>

		{{-- Avatar --}}
		<div>
			<x-ui_items.forms.input-label :value="__('Avatar')" />
			<input type="file" id="avatar" name="avatar" class="dropify" data-height="150" @if ($user?->avatar)
			data-default-file="{{ Storage::url($user->avatar) }}" @endif />
			<x-ui_items.forms.input-error class="mt-2" :messages="$errors->get('avatar')" />

			<input type="hidden" name="remove_image" id="remove_image" value="0">
		</div>

		{{-- About --}}
		<div class="hidden">
			<x-ui_items.forms.input-label for="about" :value="__('About')" />
			<x-ui_items.forms.summernote name="about" :value="$user->about ?? ''" height="300" />

			<x-ui_items.forms.input-error class="mt-2" :messages="$errors->get('about')" />
		</div>

		{{-- Save button --}}
		<div class="flex items-center gap-4">
			<button type="submit" class="darkButton">Save</button>

			@if (session('status') === 'profile-updated')
				<p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
					class="text-sm text-gray-600">
					{{ __('Saved.') }}
				</p>
			@endif
		</div>
	</form>

</section>

<script>
	$('.dropify').on('dropify.afterClear', function (event, element) {
		document.getElementById('remove_image').value = '1';
	});
</script>