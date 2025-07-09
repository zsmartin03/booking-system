@props(['business'])

<div class="frosted-card overflow-hidden shadow-lg sm:rounded-xl border border-frappe-surface2 hover:shadow-2xl transition-all duration-300 hover:scale-105 hover:transform hover:-translate-y-2 flex flex-col h-full">
    <!-- Main content area that grows to fill available space -->
    <div class="p-4 sm:p-6 flex-1 flex flex-col">
        <!-- Top section with business info and logo -->
        <div class="flex gap-4 mb-4">
            <!-- Left Column: Business Info -->
            <div class="flex-1">
                <a href="{{ route('businesses.show', $business->id) }}"
                    class="text-frappe-blue hover:text-frappe-sapphire text-lg sm:text-xl font-semibold block mb-2 transition-colors">
                    {{ $business->name }}
                </a>

                <!-- Average Rating Display -->
                <div class="flex items-center gap-2 mb-3">
                    <div class="flex items-center">
                        @for ($i = 1; $i <= 5; $i++)
                            @if ($i <= floor($business->average_rating))
                                <x-heroicon-s-star class="w-4 h-4 text-yellow-400" />
                            @elseif ($i - 0.5 <= $business->average_rating)
                                <x-heroicon-s-star class="w-4 h-4 text-yellow-400" />
                            @else
                                <x-heroicon-o-star class="w-4 h-4 text-gray-300" />
                            @endif
                        @endfor
                    </div>
                    <span class="text-sm font-medium text-frappe-text">
                        {{ number_format($business->average_rating, 1) }}
                    </span>
                    <span class="text-frappe-subtext1 text-xs">
                        ({{ $business->reviews_count }} {{ __('messages.reviews_count') }})
                    </span>
                </div>

                <!-- Categories -->
                @if ($business->categories->count() > 0)
                    <div class="flex flex-wrap gap-1 mb-2">
                        @foreach ($business->categories as $category)
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

                <p class="text-frappe-subtext1 text-sm opacity-80">{{ $business->address }}</p>
                @if ($business->description)
                    <p class="text-frappe-subtext0 text-sm mt-2 opacity-70 line-clamp-3">
                        {{ $business->description }}
                    </p>
                @endif
            </div>

            <!-- Right Column: Business Logo -->
            @if ($business->logo)
                <div class="flex-shrink-0">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 flex items-center justify-center">
                        <img src="{{ $business->logo_url }}" alt="{{ $business->name }} Logo" 
                            class="max-w-full max-h-full object-contain shadow-md">
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Button always at bottom -->
    <div class="px-4 sm:px-6 pb-4 sm:pb-6 pt-2">
        <div class="flex justify-center">
            <a href="{{ route('businesses.show', $business->id) }}"
                class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-500/20 to-indigo-500/20 backdrop-blur-sm border border-blue-400/30 text-blue-300 px-4 py-2 rounded-lg text-sm hover:from-blue-500/30 hover:to-indigo-500/30 transition-all">
                <x-heroicon-o-eye class="w-4 h-4" />
                {{ __('messages.view_details') }}
            </a>
        </div>
    </div>
</div>
