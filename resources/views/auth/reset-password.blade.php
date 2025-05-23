<x-guest-layout>
    <div class="bg-frappe-base rounded-xl shadow-lg p-6 max-w-md mx-auto">
        <form method="POST" action="{{ route('password.store') }}">
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" class="text-frappe-text" />
                <x-text-input 
                    id="email" 
                    class="block mt-1 w-full bg-frappe-surface0 border-frappe-surface1 text-frappe-text focus:ring-frappe-blue focus:border-frappe-blue" 
                    type="email" 
                    name="email" 
                    :value="old('email', $request->email)" 
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
                    autocomplete="new-password" 
                />
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-frappe-red text-sm" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-frappe-text" />
                <x-text-input 
                    id="password_confirmation" 
                    class="block mt-1 w-full bg-frappe-surface0 border-frappe-surface1 text-frappe-text focus:ring-frappe-blue focus:border-frappe-blue"
                    type="password"
                    name="password_confirmation" 
                    required 
                    autocomplete="new-password" 
                />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-frappe-red text-sm" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-primary-button class="bg-frappe-blue hover:bg-frappe-sapphire">
                    {{ __('Reset Password') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>