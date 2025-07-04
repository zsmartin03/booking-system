<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-frappe-lavender leading-tight">
            {{ __('messages.add_service_for') }} {{ $business->name }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="frosted-card rounded-xl shadow-lg p-4 sm:p-6">
            <form method="POST" action="{{ route('services.store') }}">
                @csrf
                <input type="hidden" name="business_id" value="{{ $business->id }}">

                <div class="mb-4">
                    <x-input-label for="name" :value="__('messages.name')" />
                    <x-text-input id="name" name="name" type="text" class="block w-full mt-1"
                        :value="old('name')" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2 text-frappe-red text-sm" />
                </div>

                <div class="mb-4">
                    <x-input-label for="description" :value="__('messages.description')" />
                    <textarea id="description" name="description"
                        class="block w-full mt-1 bg-frappe-surface1 border-frappe-surface2 text-frappe-text" required>{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2 text-frappe-red text-sm" />
                </div>

                <div class="mb-4">
                    <x-input-label for="price" :value="__('messages.price')" />
                    <x-text-input id="price" name="price" type="number" step="0.01" class="block w-full mt-1"
                        :value="old('price')" required min="0" />
                    <x-input-error :messages="$errors->get('price')" class="mt-2 text-frappe-red text-sm" />
                </div>

                <div class="mb-4">
                    <x-input-label for="duration" :value="__('messages.duration_minutes')" />
                    <x-text-input id="duration" name="duration" type="number" class="block w-full mt-1"
                        :value="old('duration')" required min="1" />
                    <x-input-error :messages="$errors->get('duration')" class="mt-2 text-frappe-red text-sm" />
                </div>

                <div class="mb-4">
                    <x-input-label for="employees" :value="__('messages.assign_employees')" />
                    <select id="employees" name="employees[]" class="block w-full mt-1" multiple>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('employees')" class="mt-2 text-frappe-red text-sm" />
                </div>

                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="active" value="1" checked class="form-checkbox">
                        <span class="ml-2">{{ __('messages.active') }}</span>
                    </label>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 sm:gap-6">
                    <button type="submit"
                        class="frosted-button text-white px-6 py-3 rounded-lg font-medium transition-all inline-flex items-center justify-center gap-2 w-full sm:w-auto">
                        <x-heroicon-o-plus class="w-4 h-4" /> {{ __('messages.add_service') }}
                    </button>
                    <a href="{{ route('services.index', ['business_id' => $business->id]) }}"
                        class="bg-gray-500/20 backdrop-blur-sm border border-gray-400/30 text-gray-300 px-6 py-3 rounded-lg font-medium hover:bg-gray-500/30 transition-all inline-flex items-center justify-center gap-2 w-full sm:w-auto">
                        {{ __('messages.cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
    </div>
    </div>
</x-app-layout>
