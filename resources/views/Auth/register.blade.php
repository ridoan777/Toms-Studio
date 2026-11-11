<x-guest-layout>
    <!-- Alert Component -->
    <x-indicators.alert-component message="Form Submission" />
    <!-- Alert Component -->
    
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-ui_items.forms.input-label for="name" :value="__('Name')" />
            <x-ui_items.forms.text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')"
                required autofocus autocomplete="name" />
            <x-ui_items.forms.input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-ui_items.forms.input-label for="email" :value="__('Email')" />
            <x-ui_items.forms.text-input id="email" class="block mt-1 w-full" type="email" name="email"
                :value="old('email')" required autocomplete="username" />
            <x-ui_items.forms.input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-ui_items.forms.input-label for="password" :value="__('Password')" />

            <x-ui_items.forms.text-input id="password" class="block mt-1 w-full" type="password" name="password"
                placeholder="at least 8 characters" required autocomplete="new-password" />

            <x-ui_items.forms.input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-ui_items.forms.input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-ui_items.forms.text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                name="password_confirmation" required autocomplete="new-password" />

            <x-ui_items.forms.input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-blue-700 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                href="{{ route('login') }}">
                {{ __('Already registered? Login here.') }}
            </a>

            <button type="submit" class="darkButton ms-4">Save</button>
        </div>
    </form>
</x-guest-layout>
