<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-frappe-lavender leading-tight">
            {{ __('messages.edit_availability_exception') }}: {{ $employee->name }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-2xl mx-auto">
        <div class="frosted-card rounded-xl shadow-lg p-6">
            <form method="POST" action="{{ route('availability-exceptions.update', $exception->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <x-input-label for="date" :value="__('messages.date')" />
                    <x-text-input id="date" name="date" type="date" class="block w-full mt-1" :value="old('date', $exception->date)"
                        min="{{ now()->format('Y-m-d') }}" required />
                    <x-input-error :messages="$errors->get('date')" class="mt-2 text-frappe-red text-sm" />
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <x-input-label for="start_time" :value="__('messages.start_time')" />
                        <x-text-input id="start_time" name="start_time" type="time" class="block w-full mt-1"
                            :value="old('start_time', \Carbon\Carbon::parse($exception->start_time)->format('H:i'))" required />
                        <x-input-error :messages="$errors->get('start_time')" class="mt-2 text-frappe-red text-sm" />
                    </div>

                    <div>
                        <x-input-label for="end_time" :value="__('messages.end_time')" />
                        <x-text-input id="end_time" name="end_time" type="time" class="block w-full mt-1"
                            :value="old('end_time', \Carbon\Carbon::parse($exception->end_time)->format('H:i'))" required />
                        <x-input-error :messages="$errors->get('end_time')" class="mt-2 text-frappe-red text-sm" />
                    </div>
                </div>

                <div class="mb-4">
                    <x-input-label for="type" :value="__('messages.exception_type')" />
                    <select id="type" name="type" class="block w-full mt-1" required>
                        <option value="">{{ __('messages.select_type') }}</option>
                        <option value="available" @selected(old('type', $exception->type) == 'available')>
                            {{ __('messages.available_override_regular_hours') }}
                        </option>
                        <option value="unavailable" @selected(old('type', $exception->type) == 'unavailable')>
                            {{ __('messages.unavailable_block_time_off') }}
                        </option>
                    </select>
                    <x-input-error :messages="$errors->get('type')" class="mt-2 text-frappe-red text-sm" />
                    <p class="mt-1 text-sm text-frappe-subtext1">
                        <strong>
                            {{ __('messages.available') }}
                        </strong>
                        {{ __('messages.available_description') }}<br>
                        <strong>
                            {{ __('messages.unavailable') }}</strong>
                        {{ __('messages.unavailable_description') }}
                    </p>
                </div>

                <div class="mb-6">
                    <x-input-label for="note" :value="__('messages.note_optional')" />
                    <textarea id="note" name="note" rows="3"
                        class="block w-full mt-1 bg-frappe-surface0/50 border-frappe-surface1/30 text-frappe-text rounded-md shadow-sm backdrop-blur-sm focus:border-frappe-blue focus:ring-frappe-blue/50"
                        placeholder="{{ __('messages.note_placeholder') }}">{{ old('note', $exception->note) }}</textarea>
                    <x-input-error :messages="$errors->get('note')" class="mt-2 text-frappe-red text-sm" />
                </div>

                @if ($errors->has('time'))
                    <div class="mb-4 p-3 bg-frappe-red/20 text-frappe-red rounded">
                        {{ $errors->first('time') }}
                    </div>
                @endif

                <div class="flex gap-3">
                    <x-primary-button class="save-button">
                        <x-heroicon-o-pencil class="w-4 h-4" /> {{ __('messages.update_exception') }}
                    </x-primary-button>

                    <a href="{{ route('availability-exceptions.index', ['employee_id' => $employee->id]) }}"
                        class="px-4 py-2 bg-frappe-surface0/50 border border-frappe-surface1/30 text-frappe-text rounded-lg hover:bg-frappe-surface0/70 transition-all backdrop-blur-sm inline-flex items-center gap-1">
                        <x-heroicon-o-arrow-left class="w-4 h-4" /> {{ __('messages.cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
