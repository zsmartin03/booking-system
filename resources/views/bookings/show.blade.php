<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-frappe-lavender leading-tight">
            {{ __('Booking Details') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="frosted-card rounded-xl shadow-lg overflow-hidden">
            <div class="p-4 sm:p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="space-y-3">
                        <div class="bg-frappe-surface0/30 rounded-lg p-3">
                            <div class="text-sm text-frappe-subtext1 mb-1">{{ __('Service') }}</div>
                            <div class="font-medium text-frappe-text">{{ $booking->service->name }}</div>
                        </div>
                        <div class="bg-frappe-surface0/30 rounded-lg p-3">
                            <div class="text-sm text-frappe-subtext1 mb-1">{{ __('Employee') }}</div>
                            <div class="font-medium text-frappe-text">{{ $booking->employee->name }}</div>
                        </div>
                        <div class="bg-frappe-surface0/30 rounded-lg p-3">
                            <div class="text-sm text-frappe-subtext1 mb-1">{{ __('Client') }}</div>
                            <div class="font-medium text-frappe-text">{{ $booking->client->name }}</div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="bg-frappe-surface0/30 rounded-lg p-3">
                            <div class="text-sm text-frappe-subtext1 mb-1">{{ __('Start Time') }}</div>
                            <div class="font-medium text-frappe-text">{{ $booking->start_time }}</div>
                        </div>
                        <div class="bg-frappe-surface0/30 rounded-lg p-3">
                            <div class="text-sm text-frappe-subtext1 mb-1">{{ __('End Time') }}</div>
                            <div class="font-medium text-frappe-text">{{ $booking->end_time }}</div>
                        </div>
                        <div class="bg-frappe-surface0/30 rounded-lg p-3">
                            <div class="text-sm text-frappe-subtext1 mb-1">{{ __('Status') }}</div>
                            <div class="font-medium">
                                <span
                                    class="inline-flex px-3 py-1 text-sm font-medium rounded-full
                                    {{ $booking->status === 'confirmed'
                                        ? 'bg-green-500/20 text-green-300'
                                        : ($booking->status === 'pending'
                                            ? 'bg-yellow-500/20 text-yellow-300'
                                            : ($booking->status === 'completed'
                                                ? 'bg-blue-500/20 text-blue-300'
                                                : 'bg-red-500/20 text-red-300')) }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($booking->notes)
                    <div class="bg-frappe-surface0/30 rounded-lg p-3 mb-6">
                        <div class="text-sm text-frappe-subtext1 mb-1">{{ __('Notes') }}</div>
                        <div class="text-frappe-text">{{ $booking->notes }}</div>
                    </div>
                @endif

                @if (auth()->user()->role === 'admin' || auth()->user()->role === 'provider')
                    <div class="border-t border-frappe-surface1/30 pt-6">
                        <h3 class="text-lg font-medium text-frappe-text mb-4">{{ __('Update Status') }}</h3>
                        <form method="POST" action="{{ route('bookings.update', $booking->id) }}"
                            class="flex flex-col sm:flex-row gap-3">
                            @csrf
                            @method('PUT')
                            <select name="status"
                                class="flex-1 rounded-lg border border-frappe-surface2 px-3 py-2 bg-frappe-surface0/80 text-frappe-text backdrop-blur-sm">
                                @foreach (['pending', 'confirmed', 'completed', 'cancelled'] as $status)
                                    <option value="{{ $status }}" @selected($booking->status === $status)>
                                        {{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                            <x-primary-button class="bg-frappe-blue hover:bg-frappe-sapphire whitespace-nowrap">
                                {{ __('Update Status') }}
                            </x-primary-button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
