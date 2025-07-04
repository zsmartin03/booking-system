<x-guest-layout>
    <div class="frosted-card rounded-xl shadow-lg p-6 max-w-md mx-auto">
        <!-- Forgot Password Section -->
        <h2 class="text-lg font-semibold text-frappe-text mb-4">
            {{ __('messages.forgot_password') }}
        </h2>
        <p class="text-sm text-frappe-subtext1 mb-6">
            {{ __('messages.password_reset_instruction') }}
        </p>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4 text-frappe-green" :status="session('status')" />

        <!-- Password Reset Form -->
        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Email Address Input -->
            <div class="mb-4">
                <x-input-label for="email" :value="__('messages.email')" class="text-frappe-text" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                    required autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-frappe-red text-sm" />
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end mt-4">
                <x-primary-button>
                    {{ __('messages.send_password_reset_link') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
