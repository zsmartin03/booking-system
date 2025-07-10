<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['text' => __('messages.businesses'), 'url' => route('businesses.index')],
            [
                'text' => __('messages.employees') . ' - ' . $business->name,
                'url' => route('employees.index', ['business_id' => $business->id]),
            ],
            ['text' => 'Edit ' . $employee->name, 'url' => null],
        ]" />
    </x-slot>

    <div class="py-6 max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="frosted-card rounded-xl shadow-lg p-4 sm:p-6">
            <form method="POST" action="{{ route('employees.update', $employee->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <x-input-label for="name" :value="__('messages.name')" />
                    <x-text-input id="name" name="name" type="text" class="block w-full mt-1" :value="old('name', $employee->name)"
                        required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2 text-frappe-red text-sm" />
                </div>

                <div class="mb-4">
                    <x-input-label for="email" :value="__('messages.email')" />
                    <x-text-input id="email" name="email" type="email" class="block w-full mt-1"
                        :value="old('email', $employee->email)" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-frappe-red text-sm" />
                </div>

                <div class="mb-4">
                    <x-input-label for="bio" :value="__('messages.bio')" />
                    <textarea id="bio" name="bio"
                        class="block w-full mt-1 bg-frappe-surface1 border-frappe-surface2 text-frappe-text">{{ old('bio', $employee->bio) }}</textarea>
                    <x-input-error :messages="$errors->get('bio')" class="mt-2 text-frappe-red text-sm" />
                </div>

                <div class="mb-4">
                    <x-input-label for="avatar" :value="__('messages.avatar')" />
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
                        <span class="ml-2">{{ __('messages.active') }}</span>
                    </label>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 sm:gap-6">
                    <button type="submit"
                        class="frosted-button text-white px-6 py-3 rounded-lg font-medium transition-all inline-flex items-center justify-center gap-2 w-full sm:w-auto">
                        <x-heroicon-o-pencil class="w-4 h-4" /> {{ __('messages.update_employee') }}
                    </button>
                    <a href="{{ route('employees.index', ['business_id' => $employee->business_id]) }}"
                        class="frosted-button-cancel px-6 py-3 rounded-lg font-medium transition-all inline-flex items-center justify-center gap-2 w-full sm:w-auto">
                        {{ __('messages.cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
    </div>
</x-app-layout>
