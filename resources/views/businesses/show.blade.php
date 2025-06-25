<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-frappe-lavender leading-tight">
            {{ $business->name }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="frosted-card overflow-hidden shadow-lg sm:rounded-xl">
                <div class="p-4 sm:p-6">
                    @if ($business->description)
                        <div class="mb-6">
                            <p class="text-frappe-subtext1 text-base">{{ $business->description }}</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div class="space-y-3">
                            <div class="bg-frappe-surface0/30 rounded-lg p-3">
                                <div class="text-sm text-frappe-subtext1 mb-1">{{ __('messages.address') }}</div>
                                <div class="text-frappe-text">{{ $business->address }}</div>
                            </div>
                            <div class="bg-frappe-surface0/30 rounded-lg p-3">
                                <div class="text-sm text-frappe-subtext1 mb-1">{{ __('messages.phone') }}</div>
                                <div class="text-frappe-text">
                                    <a href="tel:{{ $business->phone_number }}"
                                        class="text-frappe-blue hover:text-frappe-sapphire">
                                        {{ $business->phone_number }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div class="bg-frappe-surface0/30 rounded-lg p-3">
                                <div class="text-sm text-frappe-subtext1 mb-1">{{ __('messages.email') }}</div>
                                <div class="text-frappe-text">
                                    <a href="mailto:{{ $business->email }}"
                                        class="text-frappe-blue hover:text-frappe-sapphire break-all">
                                        {{ $business->email }}
                                    </a>
                                </div>
                            </div>
                            @if ($business->website)
                                <div class="bg-frappe-surface0/30 rounded-lg p-3">
                                    <div class="text-sm text-frappe-subtext1 mb-1">{{ __('messages.website') }}</div>
                                    <div class="text-frappe-text">
                                        <a href="{{ $business->website }}" target="_blank"
                                            class="text-frappe-blue hover:text-frappe-sapphire break-all">
                                            {{ $business->website }}
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if ($business->logo)
                        <div class="mt-6 text-center">
                            <img src="{{ $business->logo }}" alt="{{ $business->name }} Logo"
                                class="max-w-full h-32 mx-auto rounded shadow-lg">
                        </div>
                    @endif
                </div>
            </div>

            @auth
                @if (in_array(auth()->user()->role, ['client', 'provider', 'admin']))
                    <div class="mt-6 text-center sm:text-left">
                        <a href="{{ route('bookings.create', ['business_id' => $business->id]) }}"
                            class="frosted-button text-white px-6 py-3 rounded-lg transition-all inline-flex items-center gap-2">
                            <x-heroicon-o-calendar class="w-5 h-5" /> {{ __('messages.book_now') }}
                        </a>
                    </div>
                @endif
            @else
                <div class="mt-6 text-center sm:text-left">
                    <a href="{{ route('login') }}"
                        class="frosted-button text-white px-6 py-3 rounded-lg transition-all inline-flex items-center gap-2">
                        <x-heroicon-o-calendar class="w-5 h-5" /> {{ __('messages.book_now') }}
                    </a>
                    <p class="text-frappe-subtext1 text-sm mt-2">{{ __('messages.please_sign_in_to_book') }}</p>
                </div>
            @endauth
        </div>
    </div>
</x-app-layout>
