<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-frappe-lavender leading-tight">
            {{ __('Services for') }} {{ $business->name }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-2xl mx-auto">
        <a href="{{ route('services.create', ['business_id' => $business->id]) }}"
            class="bg-frappe-blue text-white px-4 py-2 rounded hover:bg-frappe-sapphire transition mb-4 inline-block">
            <x-heroicon-o-plus class="w-5 h-5 inline" /> {{ __('Add Service') }}
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
                        <th class="text-left py-2">{{ __('Duration') }}</th>
                        <th class="text-left py-2">{{ __('Price') }}</th>
                        <th class="text-left py-2">{{ __('Employees') }}</th>
                        <th class="py-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($services as $service)
                        <tr>
                            <td class="py-2">{{ $service->name }}</td>
                            <td class="py-2">{{ $service->duration }} min</td>
                            <td class="py-2">{{ number_format($service->price / 100, 2) }}</td>
                            <td class="py-2">
                                @foreach ($service->employees as $employee)
                                    <span
                                        class="inline-block bg-frappe-surface1 px-2 py-1 rounded text-xs mr-1">{{ $employee->name }}</span>
                                @endforeach
                            </td>
                            <td class="py-2 flex gap-2">
                                <a href="{{ route('services.edit', $service->id) }}"
                                    class="bg-frappe-blue text-white px-2 py-1 rounded hover:bg-frappe-sapphire transition flex items-center gap-1">
                                    <x-heroicon-o-pencil class="w-4 h-4" /> {{ __('Edit') }}
                                </a>
                                <form action="{{ route('services.destroy', $service->id) }}" method="POST"
                                    onsubmit="return confirm('Delete this service?');">
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
                            <td colspan="5" class="py-4 text-center text-frappe-subtext1">
                                {{ __('No services set.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
