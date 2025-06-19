<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-frappe-lavender leading-tight">
            {{ __('Booking Details') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-xl mx-auto">
        <div class="bg-frappe-surface0 rounded shadow p-6">
            <div class="mb-2"><strong>{{ __('Service:') }}</strong> {{ $booking->service->name }}</div>
            <div class="mb-2"><strong>{{ __('Employee:') }}</strong> {{ $booking->employee->name }}</div>
            <div class="mb-2"><strong>{{ __('Client:') }}</strong> {{ $booking->client->name }}</div>
            <div class="mb-2"><strong>{{ __('Start:') }}</strong> {{ $booking->start_time }}</div>
            <div class="mb-2"><strong>{{ __('End:') }}</strong> {{ $booking->end_time }}</div>
            <div class="mb-2"><strong>{{ __('Status:') }}</strong> {{ ucfirst($booking->status) }}</div>
            <div class="mb-2"><strong>{{ __('Notes:') }}</strong> {{ $booking->notes }}</div>
            @if (auth()->user()->role === 'admin' || auth()->user()->role === 'provider')
                <form method="POST" action="{{ route('bookings.update', $booking->id) }}" class="mt-4 flex gap-2">
                    @csrf
                    @method('PUT')
                    <select name="status" class="rounded border px-2 py-1">
                        @foreach (['pending', 'confirmed', 'completed', 'cancelled'] as $status)
                            <option value="{{ $status }}" @selected($booking->status === $status)>{{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                    <x-primary-button class="bg-frappe-blue hover:bg-frappe-sapphire">
                        {{ __('Update Status') }}
                    </x-primary-button>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
