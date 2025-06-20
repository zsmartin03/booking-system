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
                                <form action="{{ route('employees.destroy', $employee->id) }}" method="POST"
                                    onsubmit="return confirm('Delete this employee?');">
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
                                {{ __('No employees set.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
