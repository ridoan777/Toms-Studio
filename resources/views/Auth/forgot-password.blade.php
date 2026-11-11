<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    <!-- Alert Component -->
    <x-indicators.alert-component message="Login Attempt" />
    <!-- Alert Component -->

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-ui_items.forms.input-label for="email" :value="__('Email')" />
            <x-ui_items.forms.text-input id="email" class="p-2 block mt-1 w-full" type="email" name="email"
                :value="old('email')" required autofocus />
            <x-ui_items.forms.input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <button type="submit" class="darkButton">Email Password Reset Link</button>
        </div>
    </form>
</x-guest-layout>