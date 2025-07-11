<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['text' => __('messages.businesses'), 'url' => route('businesses.index')],
            [
                'text' => __('messages.employees') . ' - ' . $employee->business->name,
                'url' => route('employees.index', ['business_id' => $employee->business->id]),
            ],
            ['text' => __('messages.working_hours') . ' - ' . $employee->name, 'url' => null],
        ]" />
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
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
        <div class="flex flex-col md:flex-row gap-6">
            <!-- Business Hours Card -->
            <div class="frosted-card rounded-xl shadow-lg p-6 w-full md:w-1/3">
                <h2 class="text-lg font-semibold mb-4">{{ __('messages.business_hours') }}</h2>
                <ul class="space-y-2">
                    @foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                        <li class="flex justify-between items-center">
                            <span class="font-medium">{{ __('messages.' . $day) }}</span>
                            <span>
                                @if ($businessWorkingHours[$day]['enabled'])
                                    {{ substr($businessWorkingHours[$day]['start_time'], 0, 5) }} -
                                    {{ substr($businessWorkingHours[$day]['end_time'], 0, 5) }}
                                @else
                                    <span class="italic text-frappe-subtext1">{{ __('messages.closed') }}</span>
                                @endif
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>
            <!-- Employee Working Hours Form Card -->
            <div class="frosted-card rounded-xl shadow-lg p-6 w-full md:w-2/3 flex flex-col justify-between">
                <form method="POST"
                    action="{{ route('employee-working-hours.bulk-update', ['employee_id' => $employee->id]) }}"
                    x-data="workingHoursManager()">
                    @csrf
                    <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                    <div class="space-y-4">
                        @foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                            <div
                                class="flex items-center gap-4 py-2 border-b border-frappe-surface1/20 last:border-b-0">
                                <label
                                    class="flex items-center gap-2 min-w-[110px] @if (!$businessWorkingHours[$day]['enabled']) opacity-50 cursor-not-allowed @endif">
                                    <input type="checkbox" name="working_hours[{{ $day }}][enabled]"
                                        x-model="workingHours['{{ $day }}'].enabled"
                                        @if (!$businessWorkingHours[$day]['enabled']) disabled @endif>
                                    <span class="font-medium">{{ __('messages.' . $day) }}</span>
                                    <span class="italic text-frappe-subtext1"
                                        x-show="!workingHours['{{ $day }}'].enabled">{{ __('messages.not_working') }}</span>
                                </label>
                                <div class="flex items-center gap-2 flex-1"
                                    x-show="workingHours['{{ $day }}'].enabled">
                                    <x-input-label :for="'start_time_' . $day" :value="__('messages.start_time')" class="sr-only" />
                                    <input type="time" :id="'start_time_' + '{{ $day }}'"
                                        name="working_hours[{{ $day }}][start_time]"
                                        class="block w-32 rounded px-2 py-1 bg-frappe-surface0 border border-frappe-surface1 text-frappe-text"
                                        x-model="workingHours['{{ $day }}'].start_time" step="60"
                                        autocomplete="off" @if (!$businessWorkingHours[$day]['enabled']) disabled @endif>
                                    <span>-</span>
                                    <x-input-label :for="'end_time_' . $day" :value="__('messages.end_time')" class="sr-only" />
                                    <input type="time" :id="'end_time_' + '{{ $day }}'"
                                        name="working_hours[{{ $day }}][end_time]"
                                        class="block w-32 rounded px-2 py-1 bg-frappe-surface0 border border-frappe-surface1 text-frappe-text"
                                        x-model="workingHours['{{ $day }}'].end_time" step="60"
                                        autocomplete="off" @if (!$businessWorkingHours[$day]['enabled']) disabled @endif>
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
                <div class="mt-8">
                    <div
                        class="bg-frappe-surface1/60 border border-frappe-surface1/30 rounded-lg px-4 py-3 text-frappe-subtext1 text-sm flex items-center gap-2">
                        <x-heroicon-o-information-circle class="w-5 h-5 text-frappe-blue" />
                        <span>{{ __('messages.employee_hours_within_business') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function workingHoursManager() {
            return {
                workingHours: {
                    @foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                        '{{ $day }}': {
                            enabled: {{ old('working_hours.' . $day . '.enabled', null) !== null ? 'true' : ($employeeWorkingHours[$day]['enabled'] ?? false ? 'true' : 'false') }},
                            start_time: '{{ old('working_hours.' . $day . '.start_time', $employeeWorkingHours[$day]['start_time'] ?? '') }}',
                            end_time: '{{ old('working_hours.' . $day . '.end_time', $employeeWorkingHours[$day]['end_time'] ?? '') }}',
                        },
                    @endforeach
                }
            }
        }
    </script>
</x-app-layout>
