<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-frappe-lavender leading-tight">
            {{ __('messages.manage_bookings') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Business Selection/Info Card -->
        <div class="mb-6">
            <div class="frosted-card rounded-xl shadow-lg p-6">
                @if (auth()->user()->role === 'admin' || $businesses->count() > 1)
                    <!-- Show business selector for admins or providers with multiple businesses -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-frappe-lavender">
                                {{ __('messages.manage_bookings') }}
                            </h3>
                            <p class="text-sm text-frappe-subtext1 mt-1">
                                @if ($selectedBusiness)
                                    {{ __('messages.bookings_for_business') }}: {{ $selectedBusiness->name }}
                                @else
                                    {{ __('messages.showing_all_bookings') }}
                                @endif
                            </p>
                        </div>
                        <div class="flex-shrink-0">
                            <form method="GET" action="{{ route('bookings.manage') }}" class="flex gap-2">
                                <select name="business_id"
                                    class="bg-frappe-surface0/50 border-frappe-surface1/30 text-frappe-text rounded-md shadow-sm backdrop-blur-sm focus:border-frappe-blue focus:ring-frappe-blue/50"
                                    onchange="this.form.submit()">
                                    <option value="">{{ __('messages.all_businesses') }}</option>
                                    @foreach ($businesses as $business)
                                        <option value="{{ $business->id }}"
                                            {{ $selectedBusiness && $selectedBusiness->id === $business->id ? 'selected' : '' }}>
                                            {{ $business->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    </div>
                @else
                    <!-- Single business or no businesses -->
                    <div>
                        <h3 class="text-lg font-semibold text-frappe-lavender mb-2">
                            {{ __('messages.manage_bookings') }}
                            @if ($selectedBusiness)
                                - {{ $selectedBusiness->name }}
                            @endif
                        </h3>
                        @if ($selectedBusiness)
                            <p class="text-frappe-subtext1">
                                {{ __('messages.bookings_for_business') }}: {{ $selectedBusiness->name }}
                            </p>
                        @else
                            <p class="text-frappe-subtext1">
                                {{ __('messages.no_businesses_found') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Bookings Table -->
        <div class="frosted-card rounded-xl shadow-lg overflow-hidden">
            @if ($bookings->count() > 0)
                <!-- Desktop Table View -->
                <div class="hidden md:block">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-full">
                            <thead>
                                <tr class="border-b border-frappe-surface1/30">
                                    @if (!$selectedBusiness)
                                        <th class="text-left py-3 px-4 font-medium text-frappe-text">
                                            {{ __('messages.business') }}</th>
                                    @endif
                                    <th class="text-left py-3 px-4 font-medium text-frappe-text">
                                        {{ __('messages.service') }}</th>
                                    <th class="text-left py-3 px-4 font-medium text-frappe-text">
                                        {{ __('messages.employee') }}</th>
                                    <th class="text-left py-3 px-4 font-medium text-frappe-text">
                                        {{ __('messages.client') }}</th>
                                    <th class="text-left py-3 px-4 font-medium text-frappe-text">
                                        {{ __('messages.start') }}</th>
                                    <th class="text-left py-3 px-4 font-medium text-frappe-text">
                                        {{ __('messages.end') }}</th>
                                    <th class="text-left py-3 px-4 font-medium text-frappe-text">
                                        {{ __('messages.status') }}</th>
                                    <th class="text-left py-3 px-4 font-medium text-frappe-text">
                                        {{ __('messages.price') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bookings as $booking)
                                    <tr
                                        class="border-b border-frappe-surface1/20 hover:bg-frappe-surface0/20 transition-colors">
                                        @if (!$selectedBusiness)
                                            <td class="py-3 px-4 text-frappe-text">
                                                {{ $booking->service->business->name }}</td>
                                        @endif
                                        <td class="py-3 px-4 text-frappe-text">{{ $booking->service->name }}</td>
                                        <td class="py-3 px-4 text-frappe-text">{{ $booking->employee->name }}</td>
                                        <td class="py-3 px-4 text-frappe-text">{{ $booking->client->name }}</td>
                                        <td class="py-3 px-4 text-frappe-subtext1 text-sm">
                                            {{ $booking->start_time->format('M d, Y H:i') }}
                                        </td>
                                        <td class="py-3 px-4 text-frappe-subtext1 text-sm">
                                            {{ $booking->end_time->format('M d, Y H:i') }}
                                        </td>
                                        <td class="py-3 px-4">
                                            <form method="POST" action="{{ route('bookings.update', $booking->id) }}"
                                                class="inline-block">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="from_manage" value="1">
                                                <select name="status" onchange="this.form.submit()"
                                                    class="text-xs px-2 py-1 rounded border border-frappe-surface2 bg-frappe-surface0/80 text-frappe-text backdrop-blur-sm focus:border-frappe-blue focus:ring-frappe-blue/50
                                                        {{ $booking->status === 'confirmed'
                                                            ? 'bg-green-500/20 text-green-300'
                                                            : ($booking->status === 'pending'
                                                                ? 'bg-yellow-500/20 text-yellow-300'
                                                                : ($booking->status === 'completed'
                                                                    ? 'bg-blue-500/20 text-blue-300'
                                                                    : 'bg-red-500/20 text-red-300')) }}">
                                                    @foreach (['pending', 'confirmed', 'completed', 'cancelled'] as $status)
                                                        <option value="{{ $status }}"
                                                            @selected($booking->status === $status)
                                                            class="{{ $status === 'confirmed'
                                                                ? 'bg-green-500/20 text-green-300'
                                                                : ($status === 'pending'
                                                                    ? 'bg-yellow-500/20 text-yellow-300'
                                                                    : ($status === 'completed'
                                                                        ? 'bg-blue-500/20 text-blue-300'
                                                                        : 'bg-red-500/20 text-red-300')) }}">
                                                            {{ __('messages.' . $status) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </form>
                                        </td>
                                        <td class="py-3 px-4 text-frappe-text">
                                            {{ \App\Models\Service::formatPrice(
                                                $booking->total_price,
                                                $businessSettings['currency'] ?? ($booking->businessSettings['currency'] ?? 'USD'),
                                            ) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Card View -->
                <div class="md:hidden">
                    <div class="divide-y divide-frappe-surface1/20">
                        @foreach ($bookings as $booking)
                            <div class="p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex-1">
                                        <h3 class="font-medium text-frappe-text">{{ $booking->service->name }}</h3>
                                        <p class="text-sm text-frappe-subtext1">{{ $booking->employee->name }}</p>
                                    </div>
                                    <form method="POST" action="{{ route('bookings.update', $booking->id) }}"
                                        class="inline-block">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="from_manage" value="1">
                                        <select name="status" onchange="this.form.submit()"
                                            class="text-xs px-2 py-1 rounded border border-frappe-surface2 bg-frappe-surface0/80 text-frappe-text backdrop-blur-sm focus:border-frappe-blue focus:ring-frappe-blue/50
                                                {{ $booking->status === 'confirmed'
                                                    ? 'bg-green-500/20 text-green-300'
                                                    : ($booking->status === 'pending'
                                                        ? 'bg-yellow-500/20 text-yellow-300'
                                                        : ($booking->status === 'completed'
                                                            ? 'bg-blue-500/20 text-blue-300'
                                                            : 'bg-red-500/20 text-red-300')) }}">
                                            @foreach (['pending', 'confirmed', 'completed', 'cancelled'] as $status)
                                                <option value="{{ $status }}" @selected($booking->status === $status)
                                                    class="{{ $status === 'confirmed'
                                                        ? 'bg-green-500/20 text-green-300'
                                                        : ($status === 'pending'
                                                            ? 'bg-yellow-500/20 text-yellow-300'
                                                            : ($status === 'completed'
                                                                ? 'bg-blue-500/20 text-blue-300'
                                                                : 'bg-red-500/20 text-red-300')) }}">
                                                    {{ __('messages.' . $status) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </div>
                                <div class="text-sm text-frappe-subtext1 mb-2">
                                    @if (!$selectedBusiness)
                                        <p>{{ __('messages.business') }}: {{ $booking->service->business->name }}</p>
                                    @endif
                                    <p>{{ __('messages.client') }}: {{ $booking->client->name }}</p>
                                    <p>{{ __('messages.start') }}:
                                        {{ $booking->start_time->format('M d, Y H:i') }}</p>
                                    <p>{{ __('messages.price') }}:
                                        {{ \App\Models\Service::formatPrice(
                                            $booking->total_price,
                                            $businessSettings['currency'] ?? ($booking->businessSettings['currency'] ?? 'USD'),
                                        ) }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Pagination -->
                @if ($bookings->hasPages())
                    <div class="px-6 py-4 border-t border-frappe-surface1/30">
                        {{ $bookings->appends(request()->query())->links() }}
                    </div>
                @endif
            @else
                <div class="p-8 text-center">
                    <x-heroicon-o-calendar class="w-16 h-16 text-frappe-subtext1 mx-auto mb-4" />
                    <h3 class="text-lg font-medium text-frappe-text mb-2">{{ __('messages.no_bookings_found') }}
                    </h3>
                    <p class="text-frappe-subtext1">{{ __('messages.no_bookings_for_selected_business') }}</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
