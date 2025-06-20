<x-app-layout>
    <x-slot name="header">
        <div class="frosted-glass">
            <h2 class="font-semibold text-xl text-frappe-lavender leading-tight">
                {{ __('Edit Service for') }} {{ $business->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6 max-w-md mx-auto">
        <div class="frosted-card rounded-xl shadow-lg p-6">
            <form method="POST" action="{{ route('services.update', $service->id) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="business_id" value="{{ $business->id }}">

                <div class="mb-4">
                    <x-input-label for="name" :value="__('Name')" />
                    <x-text-input id="name" name="name" type="text" class="block w-full mt-1"
                        :value="old('name', $service->name)" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2 text-frappe-red text-sm" />
                </div>

                <div class="mb-4">
                    <x-input-label for="description" :value="__('Description')" />
                    <textarea id="description" name="description"
                        class="block w-full mt-1 bg-frappe-surface1 border-frappe-surface2 text-frappe-text" required>{{ old('description', $service->description) }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2 text-frappe-red text-sm" />
                </div>

                <div class="mb-4">
                    <x-input-label for="price" :value="__('Price (in cents)')" />
                    <x-text-input id="price" name="price" type="number" class="block w-full mt-1"
                        :value="old('price', $service->price)" required min="0" />
                    <x-input-error :messages="$errors->get('price')" class="mt-2 text-frappe-red text-sm" />
                </div>

                <div class="mb-4">
                    <x-input-label for="duration" :value="__('Duration (minutes)')" />
                    <x-text-input id="duration" name="duration" type="number" class="block w-full mt-1"
                        :value="old('duration', $service->duration)" required min="1" />
                    <x-input-error :messages="$errors->get('duration')" class="mt-2 text-frappe-red text-sm" />
                </div>

                <div class="mb-4">
                    <x-input-label for="employees" :value="__('Assign Employees')" />
                    <select id="employees" name="employees[]" class="block w-full mt-1" multiple>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}" @selected(in_array($employee->id, old('employees', $service->employees->pluck('id')->toArray())))>{{ $employee->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('employees')" class="mt-2 text-frappe-red text-sm" />
                </div>

                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="active" value="1" class="form-checkbox"
                            {{ old('active', $service->active) ? 'checked' : '' }}>
                        <span class="ml-2">{{ __('Active') }}</span>
                    </label>
                </div>

                <x-primary-button class="bg-frappe-blue hover:bg-frappe-sapphire">
                    <x-heroicon-o-pencil class="w-4 h-4" /> {{ __('Update') }}
                </x-primary-button>
            </form>
        </div>
</x-app-layout>
