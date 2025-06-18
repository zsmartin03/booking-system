<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-frappe-lavender leading-tight">
            {{ __('Employees for') }} {{ $business->name }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-6xl mx-auto">
        <a href="{{ route('employees.create', ['business_id' => $business->id]) }}"
            class="bg-frappe-blue text-white px-4 py-2 rounded hover:bg-frappe-sapphire transition mb-4 inline-block">
            <x-heroicon-o-plus class="w-5 h-5 inline" /> {{ __('Add Employee') }}
        </a>

        @if (session('success'))
            <div class="mb-4 p-3 bg-frappe-green/20 text-frappe-green rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-frappe-surface0 rounded shadow p-4">
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
                                    class="bg-frappe-green text-white px-2 py-1 rounded hover:bg-frappe-teal transition flex items-center gap-1">
                                    <x-heroicon-o-clock class="w-4 h-4" /> {{ __('Working Hours') }}
                                </a>
                                <a href="{{ route('employees.edit', $employee->id) }}"
                                    class="bg-frappe-blue text-white px-2 py-1 rounded hover:bg-frappe-sapphire transition flex items-center gap-1">
                                    <x-heroicon-o-pencil class="w-4 h-4" /> {{ __('Edit') }}
                                </a>
                                <form action="{{ route('employees.destroy', $employee->id) }}" method="POST"
                                    onsubmit="return confirm('Delete this employee?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="bg-frappe-red text-white px-2 py-1 rounded hover:bg-frappe-maroon transition flex items-center gap-1">
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
