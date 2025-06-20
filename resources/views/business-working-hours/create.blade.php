<x-app-layout>
    <x-slot name="header">
        <div class="frosted-glass">
            <h2 class="font-semibold text-xl text-frappe-lavender leading-tight">
                {{ __('Add Working Hour for') }} {{ $business->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6 max-w-md mx-auto">
        <div class="frosted-card rounded-xl shadow-lg p-6">
            <form method="POST" action="{{ route('business-working-hours.store') }}">
                @csrf
                <input type="hidden" name="business_id" value="{{ $business->id }}">

                <div class="mb-4">
                    <x-input-label for="day_of_week" :value="__('Day of Week')" />
                    <select id="day_of_week" name="day_of_week"
                        class="block w-full mt-1 bg-frappe-surface0 border-frappe-surface1 text-frappe-text">
                        @foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                            <option value="{{ $day }}" @selected(old('day_of_week') == $day)>
                                {{ ucfirst($day) }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('day_of_week')" class="mt-2 text-frappe-red text-sm" />
                </div>

                <div class="mb-4">
                    <x-input-label for="start_time" :value="__('Start Time')" />
                    <x-text-input id="start_time" name="start_time" type="time" class="block w-full mt-1"
                        :value="old('start_time')" required />
                    <x-input-error :messages="$errors->get('start_time')" class="mt-2 text-frappe-red text-sm" />
                </div>

                <div class="mb-4">
                    <x-input-label for="end_time" :value="__('End Time')" />
                    <x-text-input id="end_time" name="end_time" type="time" class="block w-full mt-1"
                        :value="old('end_time')" required />
                    <x-input-error :messages="$errors->get('end_time')" class="mt-2 text-frappe-red text-sm" />
                </div>

                <x-primary-button class="bg-frappe-blue hover:bg-frappe-sapphire">
                    <x-heroicon-o-plus class="w-4 h-4" /> {{ __('Add') }}
                </x-primary-button>
            </form>
        </div>
</x-app-layout>
