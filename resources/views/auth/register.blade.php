<x-guest-layout>
    <div class="frosted-card rounded-xl shadow-lg p-6 max-w-md mx-auto">
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div>
                <x-input-label for="name" :value="__('messages.name')" class="text-frappe-text" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')"
                    required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2 text-frappe-red text-sm" />
            </div>

            <!-- Email Address -->
            <div class="mt-4">
                <x-input-label for="email" :value="__('messages.email')" class="text-frappe-text" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                    required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-frappe-red text-sm" />
            </div>

            <!-- Role -->
            <div class="mt-4">
                <x-input-label for="role" :value="__('messages.i_want_to')" class="text-frappe-text" />
                <div class="mt-1 grid grid-cols-2 gap-2">
                    <label class="flex items-center space-x-2">
                        <input type="radio" name="role" value="client"
                            class="h-4 w-4 text-frappe-blue focus:ring-frappe-blue border-frappe-surface1"
                            {{ old('role', 'client') === 'client' ? 'checked' : '' }}>
                        <span class="text-sm text-frappe-text">{{ __('messages.book_services') }}</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="radio" name="role" value="provider"
                            class="h-4 w-4 text-frappe-blue focus:ring-frappe-blue border-frappe-surface1"
                            {{ old('role') === 'provider' ? 'checked' : '' }}>
                        <span class="text-sm text-frappe-text">{{ __('messages.provide_services') }}</span>
                    </label>
                </div>
                <x-input-error :messages="$errors->get('role')" class="mt-2 text-frappe-red text-sm" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('messages.password')" class="text-frappe-text" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                    autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-frappe-red text-sm" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-input-label for="password_confirmation" :value="__('messages.confirm_password')" class="text-frappe-text" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                    name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-frappe-red text-sm" />
            </div>

            <div class="flex items-center justify-between mt-6">
                <a class="text-sm text-frappe-blue hover:text-frappe-sapphire" href="{{ route('login') }}">
                    {{ __('messages.already_have_account') }}
                </a>

                <x-register-button>
                    {{ __('messages.register') }}
                </x-register-button>
            </div>
        </form>
    </div>
</x-guest-layout>
