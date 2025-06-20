<x-app-layout>
    <x-slot name="header">
        <div class="frosted-glass">
            <h2 class="font-semibold text-xl text-frappe-lavender leading-tight">
                {{ __('Working Hours for') }} {{ $employee->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6 max-w-2xl mx-auto">
        <a href="{{ route('employee-working-hours.create', ['employee_id' => $employee->id]) }}"
            class="action-button text-white px-4 py-2 rounded-lg mb-4 inline-flex items-center gap-1">
            <x-heroicon-o-plus class="w-5 h-5" /> {{ __('Add Working Hour') }}
        </a>

        @if (session('success'))
            <div class="mb-4 p-3 bg-frappe-green/20 text-frappe-green rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="frosted-card rounded-xl shadow-lg p-4">
            <table class="w-full">
                <thead>
                    <tr>
                        <th class="text-left py-2 text-frappe-subtext1">{{ __('Day') }}</th>
                        <th class="text-left py-2 text-frappe-subtext1">{{ __('Start') }}</th>
                        <th class="text-left py-2 text-frappe-subtext1">{{ __('End') }}</th>
                        <th class="py-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($workingHours as $hour)
                        <tr>
                            <td class="py-2">{{ ucfirst($hour->day_of_week) }}</td>
                            <td class="py-2">{{ $hour->start_time }}</td>
                            <td class="py-2">{{ $hour->end_time }}</td>
                            <td class="py-2 flex gap-2">
                                <a href="{{ route('employee-working-hours.edit', $hour->id) }}"
                                    class="edit-button text-white px-3 py-1 rounded-lg flex items-center gap-1 text-sm">
                                    <x-heroicon-o-pencil class="w-4 h-4" /> {{ __('Edit') }}
                                </a>
                                <form action="{{ route('employee-working-hours.destroy', $hour->id) }}" method="POST"
                                    onsubmit="return confirm('Delete this working hour?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="delete-button text-white px-3 py-1 rounded-lg flex items-center gap-1 text-sm">
                                        <x-heroicon-o-trash class="w-4 h-4" /> {{ __('Delete') }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-4 text-center text-frappe-subtext1">
                                {{ __('No working hours set.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
