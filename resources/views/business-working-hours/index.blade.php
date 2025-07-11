<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['text' => __('messages.businesses'), 'url' => route('businesses.index')],
            ['text' => __('messages.working_hours') . ' - ' . $business->name, 'url' => null],
        ]" />
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="mb-4 p-3 bg-frappe-green/20 text-frappe-green rounded">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-4 p-3 bg-frappe-red/20 text-frappe-red rounded">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="frosted-card rounded-xl shadow-lg p-6">
            <form method="POST"
                action="{{ route('business-working-hours.bulk-update', ['business_id' => $business->id]) }}"
                x-data="businessHoursManager()">
                @csrf
                <input type="hidden" name="business_id" value="{{ $business->id }}">
                <input type="hidden" name="confirm_delete_conflicts" x-ref="confirmDeleteConflicts">
                <div class="space-y-4">
                    @foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                        <div class="flex items-center gap-4 py-2 border-b border-frappe-surface1/20 last:border-b-0">
                            <label class="flex items-center gap-2 min-w-[110px]">
                                <input type="checkbox" name="working_hours[{{ $day }}][enabled]"
                                    x-model="businessHours['{{ $day }}'].enabled">
                                <span class="font-medium">{{ __('messages.' . $day) }}</span>
                                <span class="italic text-frappe-subtext1"
                                    x-show="!businessHours['{{ $day }}'].enabled">{{ __('messages.closed') }}</span>
                            </label>
                            <div class="flex items-center gap-2 flex-1"
                                x-show="businessHours['{{ $day }}'].enabled">
                                <x-input-label :for="'start_time_' . $day" :value="__('messages.start_time')" class="sr-only" />
                                <input type="time" :id="'start_time_' + '{{ $day }}'"
                                    name="working_hours[{{ $day }}][start_time]"
                                    class="block w-32 rounded px-2 py-1 bg-frappe-surface0 border border-frappe-surface1 text-frappe-text"
                                    x-model="businessHours['{{ $day }}'].start_time" step="60"
                                    autocomplete="off">
                                <span>-</span>
                                <x-input-label :for="'end_time_' . $day" :value="__('messages.end_time')" class="sr-only" />
                                <input type="time" :id="'end_time_' + '{{ $day }}'"
                                    name="working_hours[{{ $day }}][end_time]"
                                    class="block w-32 rounded px-2 py-1 bg-frappe-surface0 border border-frappe-surface1 text-frappe-text"
                                    x-model="businessHours['{{ $day }}'].end_time" step="60"
                                    autocomplete="off">
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-6 flex justify-end">
                    <x-primary-button class="bg-frappe-blue hover:bg-frappe-sapphire">
                        <x-heroicon-o-check class="w-4 h-4" /> {{ __('messages.save') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
        @if (session('conflicts') || (isset($conflicts) && $conflicts))
            @php $conflicts = session('conflicts', $conflicts ?? []); @endphp
            <div id="conflictModal"
                class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm z-50">
                <div class="frosted-modal p-6 rounded-2xl shadow-2xl w-full max-w-lg mx-4">
                    <h3 class="text-xl font-semibold mb-4 text-frappe-red">
                        {{ __('messages.conflicting_employee_hours') }}</h3>
                    <p class="mb-4 text-frappe-text opacity-90">{{ __('messages.conflicting_employee_hours_desc') }}
                    </p>
                    <div class="mb-6 max-h-64 overflow-y-auto">
                        @foreach ($conflicts as $day => $list)
                            <div class="mb-2">
                                <div class="font-semibold">{{ __('messages.' . $day) }}</div>
                                <ul class="ml-4 list-disc">
                                    @foreach ($list as $conflict)
                                        <li>
                                            <a href="{{ route('employee-working-hours.index', ['employee_id' => $conflict['employee']->id]) }}"
                                                class="text-frappe-blue hover:underline font-medium" target="_blank">
                                                {{ $conflict['employee']->name }}
                                            </a>
                                            ({{ substr($conflict['start_time'], 0, 5) }} -
                                            {{ substr($conflict['end_time'], 0, 5) }})
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                    <form method="POST"
                        action="{{ route('business-working-hours.bulk-update', ['business_id' => $business->id]) }}">
                        @csrf
                        <input type="hidden" name="business_id" value="{{ $business->id }}">
                        @foreach (old('working_hours', []) as $day => $data)
                            <input type="hidden" name="working_hours[{{ $day }}][enabled]"
                                value="{{ isset($data['enabled']) ? 'on' : '' }}">
                            <input type="hidden" name="working_hours[{{ $day }}][start_time]"
                                value="{{ $data['start_time'] ?? '' }}">
                            <input type="hidden" name="working_hours[{{ $day }}][end_time]"
                                value="{{ $data['end_time'] ?? '' }}">
                        @endforeach
                        <input type="hidden" name="confirm_delete_conflicts" value="1">
                        <div class="flex justify-end gap-3">
                            <button type="button"
                                onclick="document.getElementById('conflictModal').style.display='none'"
                                class="px-6 py-2 bg-gradient-to-r from-gray-500/20 to-gray-600/20 backdrop-blur-sm border border-gray-400/30 text-gray-300 rounded-lg hover:from-gray-500/30 hover:to-gray-600/30 transition-all">{{ __('messages.cancel') }}</button>
                            <button type="submit"
                                class="px-6 py-2 bg-gradient-to-r from-red-500/30 to-pink-500/30 backdrop-blur-sm border border-red-400/40 text-red-300 rounded-lg hover:from-red-500/40 hover:to-pink-500/40 transition-all">{{ __('messages.delete_and_save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
    <script>
        function businessHoursManager() {
            return {
                businessHours: {
                    @foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                        '{{ $day }}': {
                            enabled: {{ old('working_hours.' . $day . '.enabled', $workingHours->where('day_of_week', $day)->first() ? true : false) ? 'true' : 'false' }},
                            start_time: '{{ old('working_hours.' . $day . '.start_time', optional($workingHours->where('day_of_week', $day)->first())->start_time ?? '') }}',
                            end_time: '{{ old('working_hours.' . $day . '.end_time', optional($workingHours->where('day_of_week', $day)->first())->end_time ?? '') }}',
                        },
                    @endforeach
                }
            }
        }
    </script>
</x-app-layout>
