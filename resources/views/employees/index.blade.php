<x-app-layout>
    <x-slot name="header">
        <div class="frosted-glass">
            <h2 class="font-semibold text-xl text-frappe-lavender leading-tight">
                {{ __('Employees for') }} {{ $business->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6 max-w-6xl mx-auto">
        <a href="{{ route('employees.create', ['business_id' => $business->id]) }}"
            class="frosted-button text-white px-4 py-2 rounded-lg hover:transform hover:-translate-y-1 transition-all mb-4 inline-block">
            <x-heroicon-o-plus class="w-5 h-5 inline" /> {{ __('Add Employee') }}
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
                        <th class="text-left py-2">{{ __('Name') }}</th>
                        <th class="text-left py-2">{{ __('Email') }}</th>
                        <th class="text-left py-2">{{ __('Active') }}</th>
                        <th class="py-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                        <tr>
                            <td class="py-2">{{ $employee->name }}</td>
                            <td class="py-2">{{ $employee->email }}</td>
                            <td class="py-2">
                                @if ($employee->active)
                                    <span class="text-frappe-green">Yes</span>
                                @else
                                    <span class="text-frappe-red">No</span>
                                @endif
                            </td>
                            <td class="py-2 flex gap-2">
                                <a href="{{ route('employee-working-hours.index', ['employee_id' => $employee->id]) }}"
                                    class="action-button text-white px-3 py-1 rounded-lg flex items-center gap-1 text-sm">
                                    <x-heroicon-o-clock class="w-4 h-4" /> {{ __('Working Hours') }}
                                </a>
                                <a href="{{ route('employees.edit', $employee->id) }}"
                                    class="edit-button text-white px-3 py-1 rounded-lg flex items-center gap-1 text-sm">
                                    <x-heroicon-o-pencil class="w-4 h-4" /> {{ __('Edit') }}
                                </a>
                                <button
                                    class="delete-button text-white px-3 py-1 rounded-lg flex items-center gap-1 text-sm"
                                    onclick="showDeleteModal({{ $employee->id }}, '{{ addslashes($employee->name) }}')"
                                    title="{{ __('Delete') }}">
                                    <x-heroicon-o-trash class="w-4 h-4" /> {{ __('Delete') }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-4 text-center text-frappe-subtext1">
                                {{ __('No employees set.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="deleteModal"
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm z-50 hidden">
        <div class="frosted-modal p-6 rounded-2xl shadow-2xl w-full max-w-md mx-4">
            <h3 class="text-xl font-semibold mb-4 text-frappe-red">{{ __('Delete Employee') }}</h3>
            <p class="mb-6 text-frappe-text opacity-90">{{ __('Are you sure you want to delete') }} <span
                    id="modalEmployeeName" class="font-bold text-frappe-lavender"></span>?</p>
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
        function showDeleteModal(employeeId, employeeName) {
            document.getElementById('modalEmployeeName').textContent = employeeName;
            document.getElementById('deleteForm').action = "{{ url('/employees') }}/" + employeeId;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function hideDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
