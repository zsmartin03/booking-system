<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-frappe-lavender leading-tight">
            {{ __('messages.add_employee_for') }} {{ $business->name }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="frosted-card rounded-xl shadow-lg p-4 sm:p-6">
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
                    <x-input-label for="password_confirmation" :value="__('messages.confirm_password')" />
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                        class="block w-full mt-1" required />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-frappe-red text-sm" />
                </div>

                <div class="mb-4">
                    <x-input-label for="bio" :value="__('messages.bio')" />
                    <textarea id="bio" name="bio"
                        class="block w-full mt-1 bg-frappe-surface1 border-frappe-surface2 text-frappe-text">{{ old('bio') }}</textarea>
                    <x-input-error :messages="$errors->get('bio')" class="mt-2 text-frappe-red text-sm" />
                </div>

                <div class="mb-4">
                    <x-input-label for="avatar" :value="__('messages.avatar')" />
                    <input id="avatar" name="avatar" type="file" class="block w-full mt-1" accept="image/*" />
                    <x-input-error :messages="$errors->get('avatar')" class="mt-2 text-frappe-red text-sm" />
                </div>

                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="active" value="1" checked class="form-checkbox">
                        <span class="ml-2">{{ __('Active') }}</span>
                    </label>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 sm:gap-6">
                    <button type="submit"
                        class="frosted-button text-white px-6 py-3 rounded-lg font-medium hover:transform hover:-translate-y-1 transition-all inline-flex items-center justify-center gap-2 w-full sm:w-auto">
                        <x-heroicon-o-plus class="w-4 h-4" /> {{ __('Add Employee') }}
                    </button>
                    <a href="{{ route('employees.index', ['business_id' => $business->id]) }}"
                        class="bg-gray-500/20 backdrop-blur-sm border border-gray-400/30 text-gray-300 px-6 py-3 rounded-lg font-medium hover:bg-gray-500/30 transition-all inline-flex items-center justify-center gap-2 w-full sm:w-auto">
                        {{ __('Cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
