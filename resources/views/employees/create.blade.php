<x-app-layout>
    <x-slot name="header">
        <div class="frosted-glass">
            <h2 class="font-semibold text-xl text-frappe-lavender leading-tight">
                {{ __('Add Employee for') }} {{ $business->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6 max-w-md mx-auto">
        <div class="frosted-card rounded-xl shadow-lg p-6">
            <form method="POST" action="{{ route('employees.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="business_id" value="{{ $business->id }}">

                <div class="mb-4">
                    <x-input-label for="name" :value="__('Name')" />
                    <x-text-input id="name" name="name" type="text" class="block w-full mt-1"
                        :value="old('name')" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2 text-frappe-red text-sm" />
                </div>

                <div class="mb-4">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" name="email" type="email" class="block w-full mt-1"
                        :value="old('email')" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-frappe-red text-sm" />
                </div>

                <div class="mb-4">
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" name="password" type="password" class="block w-full mt-1" required />
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-frappe-red text-sm" />
                </div>

                <div class="mb-4">
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                        class="block w-full mt-1" required />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-frappe-red text-sm" />
                </div>

                <div class="mb-4">
                    <x-input-label for="bio" :value="__('Bio')" />
                    <textarea id="bio" name="bio"
                        class="block w-full mt-1 bg-frappe-surface1 border-frappe-surface2 text-frappe-text">{{ old('bio') }}</textarea>
                    <x-input-error :messages="$errors->get('bio')" class="mt-2 text-frappe-red text-sm" />
                </div>

                <div class="mb-4">
                    <x-input-label for="avatar" :value="__('Avatar')" />
                    <input id="avatar" name="avatar" type="file" class="block w-full mt-1" accept="image/*" />
                    <x-input-error :messages="$errors->get('avatar')" class="mt-2 text-frappe-red text-sm" />
                </div>

                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="active" value="1" checked class="form-checkbox">
                        <span class="ml-2">{{ __('Active') }}</span>
                    </label>
                </div>

                <x-primary-button class="bg-frappe-blue hover:bg-frappe-sapphire">
                    <x-heroicon-o-plus class="w-4 h-4" /> {{ __('Add') }}
                </x-primary-button>
            </form>
        </div>
    </div>
</x-app-layout>
