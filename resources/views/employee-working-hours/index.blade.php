<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-frappe-lavender leading-tight">
            {{ __('Working Hours for') }} {{ $employee->name }}
        </h2>
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
                                <button
                                    class="delete-button text-white px-3 py-1 rounded-lg flex items-center gap-1 text-sm"
                                    onclick="showDeleteModal({{ $hour->id }}, '{{ ucfirst($hour->day_of_week) }} {{ $hour->start_time }}-{{ $hour->end_time }}')"
                                    title="{{ __('Delete') }}">
                                    <x-heroicon-o-trash class="w-4 h-4" /> {{ __('Delete') }}
                                </button>
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

    <div id="deleteModal"
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm z-50 hidden">
        <div class="frosted-modal p-6 rounded-2xl shadow-2xl w-full max-w-md mx-4">
            <h3 class="text-xl font-semibold mb-4 text-frappe-red">{{ __('Delete Working Hour') }}</h3>
            <p class="mb-6 text-frappe-text opacity-90">{{ __('Are you sure you want to delete') }} <span
                    id="modalWorkingHour" class="font-bold text-frappe-lavender"></span>?</p>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="hideDeleteModal()"
                        class="px-6 py-2 bg-gradient-to-r from-gray-500/20 to-gray-600/20 backdrop-blur-sm border border-gray-400/30 text-gray-300 rounded-lg hover:from-gray-500/30 hover:to-gray-600/30 transition-all">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit"
                        class="px-6 py-2 bg-gradient-to-r from-red-500/30 to-pink-500/30 backdrop-blur-sm border border-red-400/40 text-red-300 rounded-lg hover:from-red-500/40 hover:to-pink-500/40 transition-all">
                        {{ __('Delete') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showDeleteModal(hourId, hourInfo) {
            document.getElementById('modalWorkingHour').textContent = hourInfo;
            document.getElementById('deleteForm').action = "{{ url('/employee-working-hours') }}/" + hourId;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function hideDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
