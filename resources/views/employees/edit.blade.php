<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-frappe-lavender leading-tight">
            {{ __('Edit Employee for') }} {{ $business->name }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-md mx-auto">
        <div class="frosted-card rounded-xl shadow-lg p-6">
            <form method="POST" action="{{ route('employees.update', $employee->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <x-input-label for="name" :value="__('Name')" />
                    <x-text-input id="name" name="name" type="text" class="block w-full mt-1"
                        :value="old('name', $employee->name)" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2 text-frappe-red text-sm" />
                </div>

                <div class="mb-4">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" name="email" type="email" class="block w-full mt-1"
                        :value="old('email', $employee->email)" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-frappe-red text-sm" />
                </div>

                <div class="mb-4">
                    <x-input-label for="bio" :value="__('Bio')" />
                    <textarea id="bio" name="bio"
                        class="block w-full mt-1 bg-frappe-surface1 border-frappe-surface2 text-frappe-text">{{ old('bio', $employee->bio) }}</textarea>
                    <x-input-error :messages="$errors->get('bio')" class="mt-2 text-frappe-red text-sm" />
                </div>

                <div class="mb-4">
                    <x-input-label for="avatar" :value="__('Avatar')" />
                    <input id="avatar" name="avatar" type="file" class="block w-full mt-1" accept="image/*" />
                    @if ($employee->avatar)
                        <img src="{{ asset('storage/' . $employee->avatar) }}" alt="Avatar"
                            class="w-16 h-16 rounded-full mt-2">
                    @endif
                    <x-input-error :messages="$errors->get('avatar')" class="mt-2 text-frappe-red text-sm" />
                </div>

                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="active" value="1"
                            {{ old('active', $employee->active) ? 'checked' : '' }} class="form-checkbox">
                        <span class="ml-2">{{ __('Active') }}</span>
                    </label>
                </div>

                <x-primary-button class="bg-frappe-blue hover:bg-frappe-sapphire">
                    <x-heroicon-o-pencil class="w-4 h-4" /> {{ __('Update') }}
                </x-primary-button>
            </form>
        </div>
    </div>
</x-app-layout>
