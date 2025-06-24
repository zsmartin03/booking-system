<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-frappe-lavender leading-tight">
            {{ __('messages.employees') }} - {{ $business->name }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-4">
            <a href="{{ route('employees.create', ['business_id' => $business->id]) }}"
                class="frosted-button text-white px-4 py-2 rounded-lg hover:transform hover:-translate-y-1 transition-all inline-flex items-center gap-2">
                <x-heroicon-o-plus class="w-5 h-5" /> {{ __('messages.create_employee') }}
            </a>
        </div>

        @if (session('success'))
            <div class="mb-4 p-3 bg-frappe-green/20 text-frappe-green rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="frosted-card rounded-xl shadow-lg overflow-hidden">
            <!-- Desktop Table View -->
            <div class="hidden lg:block">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-full">
                        <thead>
                            <tr class="border-b border-frappe-surface1/30">
                                <th class="text-left py-3 px-4 font-medium text-frappe-text">{{ __('messages.name') }}
                                </th>
                                <th class="text-left py-3 px-4 font-medium text-frappe-text">{{ __('messages.email') }}
                                </th>
                                <th class="text-left py-3 px-4 font-medium text-frappe-text">{{ __('messages.active') }}
                                </th>
                                <th class="py-3 px-4 font-medium text-frappe-text">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employees as $employee)
                                <tr
                                    class="border-b border-frappe-surface1/20 hover:bg-frappe-surface0/20 transition-colors">
                                    <td class="py-3 px-4 text-frappe-text font-medium">{{ $employee->name }}</td>
                                    <td class="py-3 px-4 text-frappe-subtext1">{{ $employee->email }}</td>
                                    <td class="py-3 px-4">
                                        @if ($employee->active)
                                            <span
                                                class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-500/20 text-green-300">{{ __('messages.active') }}</span>
                                        @else
                                            <span
                                                class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-red-500/20 text-red-300">{{ __('messages.inactive') }}</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex flex-wrap gap-2 justify-center">
                                            <a href="{{ route('employee-working-hours.index', ['employee_id' => $employee->id]) }}"
                                                class="action-button text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2 hover:transform hover:-translate-y-1 transition-all"
                                                title="{{ __('Working Hours') }}">
                                                <x-heroicon-o-clock class="w-4 h-4" />
                                                {{ __('Hours') }}
                                            </a>
                                            <a href="{{ route('availability-exceptions.index', ['employee_id' => $employee->id]) }}"
                                                class="inline-flex items-center gap-2 bg-gradient-to-r from-orange-500/20 to-red-500/20 backdrop-blur-sm border border-orange-400/30 text-orange-300 px-4 py-2 rounded-lg text-sm hover:from-orange-500/30 hover:to-red-500/30 hover:transform hover:-translate-y-1 transition-all"
                                                title="{{ __('Availability Exceptions') }}">
                                                <x-heroicon-o-calendar-days class="w-4 h-4" />
                                                {{ __('Exceptions') }}
                                            </a>
                                            <a href="{{ route('employees.edit', $employee->id) }}"
                                                class="edit-button text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2 hover:transform hover:-translate-y-1 transition-all"
                                                title="{{ __('Edit') }}">
                                                <x-heroicon-o-pencil class="w-4 h-4" />
                                                {{ __('Edit') }}
                                            </a>
                                            <button
                                                class="delete-button text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2 hover:transform hover:-translate-y-1 transition-all"
                                                onclick="showDeleteModal({{ $employee->id }}, '{{ addslashes($employee->name) }}')"
                                                title="{{ __('Delete') }}">
                                                <x-heroicon-o-trash class="w-4 h-4" />
                                                {{ __('Delete') }}
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-frappe-subtext1">
                                        {{ __('No employees set.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mobile/Tablet Card View -->
            <div class="lg:hidden">
                @forelse($employees as $employee)
                    <div class="p-4 border-b border-frappe-surface1/20 last:border-b-0">
                        <div class="space-y-3">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-medium text-frappe-text">{{ $employee->name }}</h3>
                                    <p class="text-sm text-frappe-subtext1">{{ $employee->email }}</p>
                                </div>
                                @if ($employee->active)
                                    <span
                                        class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-500/20 text-green-300">{{ __('Active') }}</span>
                                @else
                                    <span
                                        class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-red-500/20 text-red-300">{{ __('Inactive') }}</span>
                                @endif
                            </div>

                            <div class="flex flex-wrap gap-2 justify-center sm:justify-start">
                                <a href="{{ route('employee-working-hours.index', ['employee_id' => $employee->id]) }}"
                                    class="action-button text-white px-4 py-2 rounded-lg flex items-center gap-2 text-sm hover:transform hover:-translate-y-1 transition-all">
                                    <x-heroicon-o-clock class="w-4 h-4" /> {{ __('Hours') }}
                                </a>
                                <a href="{{ route('availability-exceptions.index', ['employee_id' => $employee->id]) }}"
                                    class="inline-flex items-center gap-2 bg-gradient-to-r from-orange-500/20 to-red-500/20 backdrop-blur-sm border border-orange-400/30 text-orange-300 px-4 py-2 rounded-lg text-sm hover:from-orange-500/30 hover:to-red-500/30 hover:transform hover:-translate-y-1 transition-all">
                                    <x-heroicon-o-calendar-days class="w-4 h-4" /> {{ __('Exceptions') }}
                                </a>
                                <a href="{{ route('employees.edit', $employee->id) }}"
                                    class="edit-button text-white px-4 py-2 rounded-lg flex items-center gap-2 text-sm hover:transform hover:-translate-y-1 transition-all">
                                    <x-heroicon-o-pencil class="w-4 h-4" /> {{ __('Edit') }}
                                </a>
                                <button
                                    class="delete-button text-white px-4 py-2 rounded-lg flex items-center gap-2 text-sm hover:transform hover:-translate-y-1 transition-all"
                                    onclick="showDeleteModal({{ $employee->id }}, '{{ addslashes($employee->name) }}')"
                                    title="{{ __('Delete') }}">
                                    <x-heroicon-o-trash class="w-4 h-4" /> {{ __('Delete') }}
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-frappe-subtext1">{{ __('messages.no_employees_set') }}</div>
                @endforelse
            </div>
        </div>
    </div>

    <div id="deleteModal"
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm z-50 hidden">
        <div class="frosted-modal p-6 rounded-2xl shadow-2xl w-full max-w-md mx-4">
            <h3 class="text-xl font-semibold mb-4 text-frappe-red">{{ __('messages.delete_employee') }}</h3>
            <p class="mb-6 text-frappe-text opacity-90">{{ __('messages.are_you_sure_delete') }} <span
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
