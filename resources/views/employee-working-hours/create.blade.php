<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['text' => __('messages.businesses'), 'url' => route('businesses.index')],
            ['text' => __('messages.employees') . ' - ' . $employee->business->name, 'url' => route('employees.index', ['business_id' => $employee->business->id])],
            ['text' => __('messages.working_hours') . ' - ' . $employee->name, 'url' => route('employee-working-hours.index', ['employee_id' => $employee->id])],
            ['text' => __('messages.add_working_hour'), 'url' => null]
        ]" />
    </x-slot>

    <div class="py-6 max-w-md mx-auto">
        <div class="frosted-card rounded-xl shadow-lg p-6">
            <form method="POST" action="{{ route('employee-working-hours.store') }}">
                @csrf
                <input type="hidden" name="employee_id" value="{{ $employee->id }}">

                <div class="mb-4">
                    <x-input-label for="day_of_week" :value="__('messages.day_of_week')" />
                    <select id="day_of_week" name="day_of_week" class="block w-full mt-1">
                        @foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                            <option value="{{ $day }}" @selected(old('day_of_week') == $day)>
                                {{ __('messages.' . $day) }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('day_of_week')" class="mt-2 text-frappe-red text-sm" />
                </div>

                <div class="mb-4">
                    <x-input-label for="start_time" :value="__('messages.start_time')" />
                    <x-text-input id="start_time" name="start_time" type="time" class="block w-full mt-1"
                        :value="old('start_time')" required />
                    <x-input-error :messages="$errors->get('start_time')" class="mt-2 text-frappe-red text-sm" />
                </div>

                <div class="mb-4">
                    <x-input-label for="end_time" :value="__('messages.end_time')" />
                    <x-text-input id="end_time" name="end_time" type="time" class="block w-full mt-1"
                        :value="old('end_time')" required />
                    <x-input-error :messages="$errors->get('end_time')" class="mt-2 text-frappe-red text-sm" />
                </div>

                <x-primary-button class="save-button">
                    <x-heroicon-o-plus class="w-4 h-4" /> {{ __('messages.add') }}
                </x-primary-button>
            </form>
        </div>
    </div>
</x-app-layout>
