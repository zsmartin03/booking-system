<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-frappe-lavender leading-tight">
            {{ $business->name }}
        </h2>
    </x-slot>

    <style>
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="frosted-card overflow-hidden shadow-lg sm:rounded-xl">
                <div class="p-4 sm:p-6">
                    <!-- Business Name -->
                    <div class="mb-6">
                        <h1 class="text-3xl font-bold text-frappe-text mb-2">{{ $business->name }}</h1>
                    </div>

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

                    <!-- Categories -->
                    @if ($business->categories->count() > 0)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-frappe-text mb-3">{{ __('messages.categories') }}
                            </h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($business->categories as $category)
                                    <a href="{{ route('businesses.public.index', ['category' => $category->slug]) }}"
                                        class="{{ $category->badge_classes }}" style="{{ $category->badge_styles }}"
                                        onmouseover="this.style.cssText = '{{ $category->badge_styles }} {{ $category->badge_hover_styles }}'"
                                        onmouseout="this.style.cssText = '{{ $category->badge_styles }}'">
                                        {{ $category->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if ($business->logo)
                        <div class="mt-6 text-center">
                            <img src="{{ $business->logo }}" alt="{{ $business->name }} Logo"
                                class="max-w-full h-32 mx-auto rounded shadow-lg">
                        </div>
                    @endif

                    {{-- Move the book_now button here, inside the card --}}
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
            </div> <!-- End .frosted-card -->

            <!-- Services Section -->
            @if ($business->services->count() > 0)
                <div class="frosted-card overflow-hidden shadow-lg sm:rounded-xl mt-6">
                    <div class="p-4 sm:p-6">
                        <h3 class="text-xl font-semibold text-frappe-text mb-4">{{ __('messages.services') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach ($business->services as $service)
                                <div class="bg-frappe-surface0/30 rounded-lg p-4 border border-frappe-surface2/30">
                                    <h4 class="font-semibold text-frappe-text mb-2">{{ $service->name }}</h4>
                                    @if ($service->description)
                                        <p class="text-frappe-subtext1 text-sm mb-3">{{ $service->description }}</p>
                                    @endif
                                    <div class="flex justify-between items-center">
                                        <span class="text-frappe-blue font-semibold">{{ $service->price }} EUR</span>
                                        <span class="text-frappe-subtext1 text-sm">{{ $service->duration }} min</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Related Businesses Carousel -->
            @if ($relatedBusinesses->count() > 0)
                <div class="frosted-card overflow-hidden shadow-lg sm:rounded-xl mt-6">
                    <div class="p-4 sm:p-6">
                        <h3 class="text-xl font-semibold text-frappe-text mb-4">{{ __('messages.similar_businesses') }}
                        </h3>
                        <div class="relative">
                            <div class="overflow-x-auto scrollbar-hide">
                                <div class="flex gap-4 pb-4" style="width: max-content;">
                                    @foreach ($relatedBusinesses as $relatedBusiness)
                                        <div
                                            class="flex-none w-80 bg-frappe-surface0/30 rounded-lg border border-frappe-surface2/30 hover:shadow-lg transition-all duration-300">
                                            <div class="p-4">
                                                <h4 class="font-semibold text-frappe-text mb-2">
                                                    <a href="{{ route('businesses.show', $relatedBusiness->id) }}"
                                                        class="text-frappe-blue hover:text-frappe-sapphire transition-colors">
                                                        {{ $relatedBusiness->name }}
                                                    </a>
                                                </h4>
                                                @if ($relatedBusiness->categories->count() > 0)
                                                    <div class="flex flex-wrap gap-1 mb-2">
                                                        @foreach ($relatedBusiness->categories as $category)
                                                            <a href="{{ route('businesses.public.index', ['category' => $category->slug]) }}"
                                                                class="{{ $category->badge_classes }}"
                                                                style="{{ $category->badge_styles }}"
                                                                onmouseover="this.style.cssText = '{{ $category->badge_styles }} {{ $category->badge_hover_styles }}'"
                                                                onmouseout="this.style.cssText = '{{ $category->badge_styles }}'">
                                                                {{ $category->name }}
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                @endif
                                                <p class="text-frappe-subtext1 text-sm mb-3">
                                                    {{ $relatedBusiness->address }}</p>
                                                @if ($relatedBusiness->description)
                                                    <p class="text-frappe-subtext0 text-sm mb-3 line-clamp-2">
                                                        {{ $relatedBusiness->description }}</p>
                                                @endif
                                                <a href="{{ route('businesses.show', $relatedBusiness->id) }}"
                                                    class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-500/20 to-indigo-500/20 backdrop-blur-sm border border-blue-400/30 text-blue-300 px-3 py-2 rounded-lg text-sm hover:from-blue-500/30 hover:to-indigo-500/30 transition-all">
                                                    <x-heroicon-o-eye class="w-4 h-4" />
                                                    {{ __('messages.view_details') }}
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
