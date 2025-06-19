<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-frappe-lavender leading-tight">
            {{ __('Bookings') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto">
        <div class="bg-frappe-surface0 rounded shadow p-4">
            <table class="w-full">
                <thead>
                    <tr>
                        <th>{{ __('Service') }}</th>
                        <th>{{ __('Employee') }}</th>
                        <th>{{ __('Client') }}</th>
                        <th>{{ __('Start') }}</th>
                        <th>{{ __('End') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                        <tr>
                            <td>{{ $booking->service->name }}</td>
                            <td>{{ $booking->employee->name }}</td>
                            <td>{{ $booking->client->name }}</td>
                            <td>{{ $booking->start_time }}</td>
                            <td>{{ $booking->end_time }}</td>
                            <td>{{ ucfirst($booking->status) }}</td>
                            <td>
                                <a href="{{ route('bookings.show', $booking->id) }}"
                                    class="bg-frappe-blue text-white px-2 py-1 rounded hover:bg-frappe-sapphire transition">
                                    {{ __('View') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-frappe-subtext1">{{ __('No bookings found.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $bookings->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
