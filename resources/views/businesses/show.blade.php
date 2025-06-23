<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-frappe-lavender leading-tight">
            {{ $business->name }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="frosted-card overflow-hidden shadow-lg sm:rounded-xl">
                <div class="p-6">
                    <p class="mb-2 text-frappe-subtext1">{{ $business->description }}</p>
                    <p class="mb-1"><strong>Address:</strong> {{ $business->address }}</p>
                    <p class="mb-1"><strong>Phone:</strong> {{ $business->phone_number }}</p>
                    <p class="mb-1"><strong>Email:</strong> {{ $business->email }}</p>
                    @if ($business->website)
                        <p class="mb-1"><strong>Website:</strong>
                            <a href="{{ $business->website }}" target="_blank" class="text-frappe-blue hover:underline">
                                {{ $business->website }}
                            </a>
                        </p>
                    @endif
                    @if ($business->logo)
                        <div class="mt-4">
                            <img src="{{ $business->logo }}" alt="Logo" class="max-w-xs rounded shadow">
                        </div>
                    @endif
                </div>
            </div>
            @auth
                @if (in_array(auth()->user()->role, ['client', 'provider', 'admin']))
                    <div class="mt-6">
                        <a href="{{ route('bookings.create', ['business_id' => $business->id]) }}"
                            class="frosted-button text-white px-6 py-3 rounded-lg hover:transform hover:-translate-y-1 transition-all inline-flex items-center gap-2">
                            <x-heroicon-o-calendar class="w-5 h-5" /> {{ __('Book Now') }}
                        </a>
                    </div>
                @endif
            @else
                <div class="mt-6">
                    <a href="{{ route('login') }}"
                        class="frosted-button text-white px-6 py-3 rounded-lg hover:transform hover:-translate-y-1 transition-all inline-flex items-center gap-2">
                        <x-heroicon-o-calendar class="w-5 h-5" /> {{ __('Book Now') }}
                    </a>
                    <p class="text-frappe-subtext1 text-sm mt-2">{{ __('Please sign in to make a booking') }}</p>
                </div>
            @endauth
        </div>
    </div>
</x-app-layout>
