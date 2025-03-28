<x-guest-layout>
    <div class="bg-frappe-base rounded-xl shadow-lg p-6 max-w-md mx-auto">
        <!-- Session Status -->
        <x-auth-session-status class="mb-4 text-frappe-green" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" class="text-frappe-text" />
                <x-text-input 
                    id="email" 
                    class="block mt-1 w-full bg-frappe-surface0 border-frappe-surface1 text-frappe-text focus:ring-frappe-blue focus:border-frappe-blue" 
                    type="email" 
                    name="email" 
                    :value="old('email')" 
                    required 
                    autofocus 
                    autocomplete="username" 
                />
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-frappe-red text-sm" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" class="text-frappe-text" />
                <x-text-input 
                    id="password" 
                    class="block mt-1 w-full bg-frappe-surface0 border-frappe-surface1 text-frappe-text focus:ring-frappe-blue focus:border-frappe-blue"
                    type="password"
                    name="password"
                    required 
                    autocomplete="current-password" 
                />
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-frappe-red text-sm" />
            </div>

            <!-- Remember Me -->
            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input 
                        id="remember_me" 
                        type="checkbox" 
                        class="rounded bg-frappe-surface0 border-frappe-surface1 text-frappe-blue focus:ring-frappe-blue" 
                        name="remember"
                    >
                    <span class="ms-2 text-sm text-frappe-text">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-between mt-4">
                @if (Route::has('password.request'))
                    <a class="text-sm text-frappe-blue hover:text-frappe-sapphire" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <x-primary-button class="bg-frappe-blue hover:bg-frappe-sapphire">
                    {{ __('Log in') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>