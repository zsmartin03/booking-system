<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-frappe-lavender leading-tight">
            {{ __('messages.profile') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <!-- Status Messages -->
            @if (session('status'))
                <div class="mb-4 p-4 bg-frappe-green/20 text-frappe-green border border-frappe-green rounded-lg">
                    {{ session('status') }}
                </div>
            @endif

            <div class="frosted-card overflow-hidden shadow-lg sm:rounded-xl">
                <div class="p-6">
                    <!-- Profile Update Form -->
                    <div class="mb-10">
                        <h3 class="text-lg font-medium text-frappe-blue mb-4">{{ __('messages.profile') }}</h3>

                        <form method="POST" action="{{ route('profile.update') }}">
                            @csrf
                            @method('patch')

                            <div class="space-y-4">
                                <!-- Name -->
                                <div>
                                    <x-input-label for="name" :value="__('messages.name')" class="text-frappe-subtext1" />
                                    <x-text-input id="name" name="name" type="text" class="block mt-1 w-full"
                                        :value="old('name', $user->name)" required autofocus />
                                    <x-input-error :messages="$errors->get('name')" class="mt-1 text-frappe-red" />
                                </div>

                                <!-- Email -->
                                <div>
                                    <x-input-label for="email" :value="__('messages.email')" class="text-frappe-subtext1" />
                                    <x-text-input id="email" name="email" type="email" class="block mt-1 w-full"
                                        :value="old('email', $user->email)" required />
                                    <x-input-error :messages="$errors->get('email')" class="mt-1 text-frappe-red" />
                                </div>

                                <!-- Phone Number -->
                                <div>
                                    <x-input-label for="phone_number" :value="__('messages.phone')" class="text-frappe-subtext1" />
                                    <x-text-input id="phone_number" name="phone_number" type="tel"
                                        class="block mt-1 w-full" :value="old('phone_number', $user->phone_number)" />
                                    <x-input-error :messages="$errors->get('phone_number')" class="mt-1 text-frappe-red" />
                                </div>

                                <div class="flex items-center justify-end">
                                    <x-primary-button>
                                        {{ __('messages.save_profile') }}
                                    </x-primary-button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Password Update Form -->
                    <div class="border-t border-frappe-surface1 pt-8">
                        <h3 class="text-lg font-medium text-frappe-blue mb-4">{{ __('messages.update_password') }}</h3>

                        <form method="POST" action="{{ route('profile.update-password') }}">
                            @csrf

                            <div class="space-y-4">
                                <!-- Current Password -->
                                <div>
                                    <x-input-label for="current_password" :value="__('messages.current_password')"
                                        class="text-frappe-subtext1" />
                                    <x-text-input id="current_password" name="current_password" type="password"
                                        class="block mt-1 w-full" required />
                                    <x-input-error :messages="$errors->get('current_password')" class="mt-1 text-frappe-red" />
                                </div>

                                <!-- New Password -->
                                <div>
                                    <x-input-label for="password" :value="__('messages.new_password')" class="text-frappe-subtext1" />
                                    <x-text-input id="password" name="password" type="password"
                                        class="block mt-1 w-full" required />
                                    <x-input-error :messages="$errors->get('password')" class="mt-1 text-frappe-red" />
                                </div>

                                <!-- Confirm Password -->
                                <div>
                                    <x-input-label for="password_confirmation" :value="__('messages.confirm_new_password')"
                                        class="text-frappe-subtext1" />
                                    <x-text-input id="password_confirmation" name="password_confirmation"
                                        type="password" class="block mt-1 w-full" required />
                                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-frappe-red" />
                                </div>

                                <div class="flex items-center justify-end">
                                    <x-primary-button>
                                        {{ __('messages.update_password') }}
                                    </x-primary-button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
