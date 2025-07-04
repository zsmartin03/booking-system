<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-frappe-lavender leading-tight">
            {{ __('messages.edit_working_hour_for') }} {{ $employee->name }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-md mx-auto">
        <div class="frosted-card rounded-xl shadow-lg p-6">
            <form method="POST" action="{{ route('employee-working-hours.update', $workingHour->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <x-input-label for="day_of_week" :value="__('messages.day_of_week')" />
                    <select id="day_of_week" name="day_of_week" class="block w-full mt-1">
                        @foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                            <option value="{{ $day }}" @selected(old('day_of_week', $workingHour->day_of_week) == $day)>
                                {{ __('messages.' . $day) }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('day_of_week')" class="mt-2 text-frappe-red text-sm" />
                </div>

                <div class="mb-4">
                    <x-input-label for="start_time" :value="__('messages.start_time')" />
                    <x-text-input id="start_time" name="start_time" type="time" class="block w-full mt-1"
                        :value="old('start_time', $workingHour->start_time)" required />
                    <x-input-error :messages="$errors->get('start_time')" class="mt-2 text-frappe-red text-sm" />
                </div>

                <div class="mb-4">
                    <x-input-label for="end_time" :value="__('messages.end_time')" />
                    <x-text-input id="end_time" name="end_time" type="time" class="block w-full mt-1"
                        :value="old('end_time', $workingHour->end_time)" required />
                    <x-input-error :messages="$errors->get('end_time')" class="mt-2 text-frappe-red text-sm" />
                </div>

                <x-primary-button class="save-button">
                    <x-heroicon-o-pencil class="w-4 h-4" /> {{ __('messages.update') }}
                </x-primary-button>
            </form>
        </div>
    </div>
</x-app-layout>
