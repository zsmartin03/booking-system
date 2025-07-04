<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-frappe-lavender leading-tight">
            {{ __('messages.bookings') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="frosted-card rounded-xl shadow-lg overflow-hidden">
            <!-- Desktop Table View -->
            <div class="hidden md:block">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-full">
                        <thead>
                            <tr class="border-b border-frappe-surface1/30">
                                <th class="text-left py-3 px-4 font-medium text-frappe-text">
                                    {{ __('messages.business') }}
                                </th>
                                <th class="text-left py-3 px-4 font-medium text-frappe-text">{{ __('messages.service') }}
                                </th>
                                <th class="text-left py-3 px-4 font-medium text-frappe-text">
                                    {{ __('messages.employee') }}</th>
                                <th class="text-left py-3 px-4 font-medium text-frappe-text">{{ __('messages.client') }}
                                </th>
                                <th class="text-left py-3 px-4 font-medium text-frappe-text">{{ __('messages.start') }}
                                </th>
                                <th class="text-left py-3 px-4 font-medium text-frappe-text">{{ __('messages.end') }}
                                </th>
                                <th class="text-left py-3 px-4 font-medium text-frappe-text">{{ __('messages.status') }}
                                </th>
                                <th class="text-left py-3 px-4 font-medium text-frappe-text">{{ __('messages.price') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $booking)
                                <tr
                                    class="border-b border-frappe-surface1/20 hover:bg-frappe-surface0/20 transition-colors">
                                    <td class="py-3 px-4 text-frappe-text">
                                        <a href="{{ route('businesses.show', $booking->service->business->id) }}"
                                            class="text-frappe-blue hover:text-frappe-sapphire transition-colors">
                                            {{ $booking->service->business->name }}
                                        </a>
                                    </td>
                                    <td class="py-3 px-4 text-frappe-text">{{ $booking->service->name }}</td>
                                    <td class="py-3 px-4 text-frappe-text">{{ $booking->employee->name }}</td>
                                    <td class="py-3 px-4 text-frappe-text">{{ $booking->client->name }}</td>
                                    <td class="py-3 px-4 text-frappe-subtext1 text-sm">{{ $booking->start_time }}</td>
                                    <td class="py-3 px-4 text-frappe-subtext1 text-sm">{{ $booking->end_time }}</td>
                                    <td class="py-3 px-4">
                                        <span
                                            class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                            {{ $booking->status === 'confirmed'
                                                ? 'bg-green-500/20 text-green-300'
                                                : ($booking->status === 'pending'
                                                    ? 'bg-yellow-500/20 text-yellow-300'
                                                    : ($booking->status === 'completed'
                                                        ? 'bg-blue-500/20 text-blue-300'
                                                        : 'bg-red-500/20 text-red-300')) }}">
                                            {{ __('messages.' . $booking->status) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-frappe-text">
                                        {{ \App\Models\Service::formatPrice($booking->total_price, $booking->businessSettings['currency'] ?? 'USD') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-frappe-subtext1 py-8">
                                        {{ __('messages.no_bookings_found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mobile Card View -->
            <div class="md:hidden">
                @forelse($bookings as $booking)
                    <div class="p-4 border-b border-frappe-surface1/20 last:border-b-0">
                        <div class="space-y-2">
                            <div class="flex justify-between items-start">
                                <h3 class="font-medium text-frappe-text">{{ $booking->service->name }}</h3>
                                <span
                                    class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                    {{ $booking->status === 'confirmed'
                                        ? 'bg-green-500/20 text-green-300'
                                        : ($booking->status === 'pending'
                                            ? 'bg-yellow-500/20 text-yellow-300'
                                            : ($booking->status === 'completed'
                                                ? 'bg-blue-500/20 text-blue-300'
                                                : 'bg-red-500/20 text-red-300')) }}">
                                    {{ __('messages.' . $booking->status) }}
                                </span>
                            </div>
                            <div class="text-sm text-frappe-subtext1">
                                <div><strong>{{ __('messages.business') }}:</strong>
                                    <a href="{{ route('businesses.show', $booking->service->business->id) }}"
                                        class="text-frappe-blue hover:text-frappe-sapphire transition-colors">
                                        {{ $booking->service->business->name }}
                                    </a>
                                </div>
                                <div><strong>{{ __('messages.employee') }}:</strong> {{ $booking->employee->name }}
                                </div>
                                <div><strong>{{ __('messages.client') }}:</strong> {{ $booking->client->name }}</div>
                                <div><strong>{{ __('messages.time') }}:</strong> {{ $booking->start_time }} -
                                    {{ $booking->end_time }}</div>
                                <div><strong>{{ __('messages.price') }}:</strong>
                                    {{ \App\Models\Service::formatPrice($booking->total_price, $booking->businessSettings['currency'] ?? 'USD') }}
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-frappe-subtext1">{{ __('messages.no_bookings_found') }}</div>
                @endforelse
            </div>
            <div class="mt-4">
                {{ $bookings->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
