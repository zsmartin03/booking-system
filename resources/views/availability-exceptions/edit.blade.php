<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-frappe-lavender leading-tight">
            {{ __('Edit Availability Exception for') }} {{ $employee->name }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-md mx-auto">
        <div class="frosted-card rounded-xl shadow-lg p-6">
            <form method="POST" action="{{ route('availability-exceptions.update', $exception->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <x-input-label for="date" :value="__('Date')" />
                    <x-text-input id="date" name="date" type="date" class="block w-full mt-1" :value="old('date', $exception->date)"
                        min="{{ now()->format('Y-m-d') }}" required />
                    <x-input-error :messages="$errors->get('date')" class="mt-2 text-frappe-red text-sm" />
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <x-input-label for="start_time" :value="__('Start Time')" />
                        <x-text-input id="start_time" name="start_time" type="time" class="block w-full mt-1"
                            :value="old('start_time', \Carbon\Carbon::parse($exception->start_time)->format('H:i'))" required />
                        <x-input-error :messages="$errors->get('start_time')" class="mt-2 text-frappe-red text-sm" />
                    </div>

                    <div>
                        <x-input-label for="end_time" :value="__('End Time')" />
                        <x-text-input id="end_time" name="end_time" type="time" class="block w-full mt-1"
                            :value="old('end_time', \Carbon\Carbon::parse($exception->end_time)->format('H:i'))" required />
                        <x-input-error :messages="$errors->get('end_time')" class="mt-2 text-frappe-red text-sm" />
                    </div>
                </div>

                <div class="mb-4">
                    <x-input-label for="type" :value="__('Exception Type')" />
                    <select id="type" name="type" class="block w-full mt-1" required>
                        <option value="">{{ __('Select Type') }}</option>
                        <option value="available" @selected(old('type', $exception->type) == 'available')>
                            {{ __('Available (Override regular hours)') }}
                        </option>
                        <option value="unavailable" @selected(old('type', $exception->type) == 'unavailable')>
                            {{ __('Unavailable (Block time off)') }}
                        </option>
                    </select>
                    <x-input-error :messages="$errors->get('type')" class="mt-2 text-frappe-red text-sm" />
                    <p class="mt-1 text-sm text-frappe-subtext1">
                        <strong>Available:</strong>
                        {{ __('Employee will be available during this time even if normally not working') }}<br>
                        <strong>Unavailable:</strong>
                        {{ __('Employee will not be available during this time even if normally working') }}
                    </p>
                </div>

                <div class="mb-6">
                    <x-input-label for="note" :value="__('Note (Optional)')" />
                    <textarea id="note" name="note" rows="3"
                        class="block w-full mt-1 bg-frappe-surface0/50 border-frappe-surface1/30 text-frappe-text rounded-md shadow-sm backdrop-blur-sm focus:border-frappe-blue focus:ring-frappe-blue/50"
                        placeholder="{{ __('e.g., Vacation, Special event, Overtime...') }}">{{ old('note', $exception->note) }}</textarea>
                    <x-input-error :messages="$errors->get('note')" class="mt-2 text-frappe-red text-sm" />
                </div>

                @if ($errors->has('time'))
                    <div class="mb-4 p-3 bg-frappe-red/20 text-frappe-red rounded">
                        {{ $errors->first('time') }}
                    </div>
                @endif

                <div class="flex gap-3">
                    <x-primary-button class="save-button">
                        <x-heroicon-o-pencil class="w-4 h-4" /> {{ __('Update Exception') }}
                    </x-primary-button>

                    <a href="{{ route('availability-exceptions.index', ['employee_id' => $employee->id]) }}"
                        class="px-4 py-2 bg-frappe-surface0/50 border border-frappe-surface1/30 text-frappe-text rounded-lg hover:bg-frappe-surface0/70 transition-all backdrop-blur-sm inline-flex items-center gap-1">
                        <x-heroicon-o-arrow-left class="w-4 h-4" /> {{ __('Cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
