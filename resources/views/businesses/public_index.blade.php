<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-frappe-lavender leading-tight">
            {{ __('messages.all_businesses') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Search and Filter Form -->
            <div class="frosted-card overflow-hidden shadow-lg sm:rounded-xl border border-frappe-surface2 mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('businesses.public.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Search by name -->
                            <div>
                                <label for="search" class="block text-sm font-medium text-frappe-text mb-2">
                                    {{ __('messages.search_by_name') }}
                                </label>
                                <input type="text" id="search" name="search" value="{{ request('search') }}"
                                    placeholder="{{ __('messages.enter_business_name') }}"
                                    class="w-full px-3 py-2 bg-frappe-mantle border border-frappe-surface2 rounded-lg text-frappe-text placeholder-frappe-subtext1 focus:outline-none focus:ring-2 focus:ring-frappe-blue focus:border-transparent">
                            </div>

                            <!-- Filter by category -->
                            <div>
                                <label for="category" class="block text-sm font-medium text-frappe-text mb-2">
                                    {{ __('messages.filter_by_category') }}
                                </label>
                                <select id="category" name="category"
                                    class="w-full px-3 py-2 bg-frappe-mantle border border-frappe-surface2 rounded-lg text-frappe-text focus:outline-none focus:ring-2 focus:ring-frappe-blue focus:border-transparent">
                                    <option value="">{{ __('messages.all_categories') }}</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->slug }}"
                                            {{ request('category') == $category->slug ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Action buttons -->
                        <div class="flex flex-wrap gap-3">
                            <button type="submit"
                                class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-500/20 to-indigo-500/20 backdrop-blur-sm border border-blue-400/30 text-blue-300 px-4 py-2 rounded-lg text-sm hover:from-blue-500/30 hover:to-indigo-500/30 transition-all">
                                <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                                {{ __('messages.search') }}
                            </button>

                            @if (request('search') || request('category'))
                                <a href="{{ route('businesses.public.index') }}"
                                    class="inline-flex items-center gap-2 bg-gradient-to-r from-gray-500/20 to-gray-600/20 backdrop-blur-sm border border-gray-400/30 text-gray-300 px-4 py-2 rounded-lg text-sm hover:from-gray-500/30 hover:to-gray-600/30 transition-all">
                                    <x-heroicon-o-x-mark class="w-4 h-4" />
                                    {{ __('messages.clear_filters') }}
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Results info -->
            @if (request('search') || request('category'))
                <div class="mb-6">
                    <div class="frosted-card overflow-hidden shadow-lg sm:rounded-xl border border-frappe-surface2">
                        <div class="p-4">
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0">
                                    <div
                                        class="w-8 h-8 bg-gradient-to-r from-blue-500/20 to-indigo-500/20 rounded-full flex items-center justify-center">
                                        <x-heroicon-o-information-circle class="w-5 h-5 text-blue-300" />
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <p class="text-frappe-text text-sm font-medium">
                                        @if (request('search') && request('category'))
                                            {{ __('messages.showing_businesses_matching_in_category', ['search' => request('search'), 'category' => $categories->where('slug', request('category'))->first()?->name]) }}
                                        @elseif(request('search'))
                                            {{ __('messages.showing_businesses_matching', ['search' => request('search')]) }}
                                        @elseif(request('category'))
                                            {{ __('messages.showing_businesses_in_category', ['category' => $categories->where('slug', request('category'))->first()?->name]) }}
                                        @endif
                                    </p>
                                    <p class="text-frappe-subtext1 text-xs mt-1">
                                        {{ $businesses->count() }} {{ __('messages.results') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @forelse($businesses as $business)
                @if ($loop->first)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                @endif

                <div
                    class="frosted-card overflow-hidden shadow-lg sm:rounded-xl border border-frappe-surface2 hover:shadow-2xl transition-all duration-300 hover:scale-105 hover:transform hover:-translate-y-2">
                    <div class="p-4 sm:p-6">
                        <div class="mb-4">
                            <a href="{{ route('businesses.show', $business->id) }}"
                                class="text-frappe-blue hover:text-frappe-sapphire text-lg sm:text-xl font-semibold block mb-2 transition-colors">
                                {{ $business->name }}
                            </a>

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
                                    {{ $business->description }}</p>
                            @endif
                        </div>

                        <div class="flex justify-center">
                            <a href="{{ route('businesses.show', $business->id) }}"
                                class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-500/20 to-indigo-500/20 backdrop-blur-sm border border-blue-400/30 text-blue-300 px-4 py-2 rounded-lg text-sm hover:from-blue-500/30 hover:to-indigo-500/30 transition-all">
                                <x-heroicon-o-eye class="w-4 h-4" />
                                {{ __('messages.view_details') }}
                            </a>
                        </div>
                    </div>
                </div>

                @if ($loop->last)
        </div>
        @endif
    @empty
        <div class="frosted-card overflow-hidden shadow-lg sm:rounded-xl">
            <div class="p-6 text-center">
                <p class="text-frappe-subtext1 opacity-80">{{ __('messages.no_businesses_found') }}</p>
            </div>
        </div>
        @endforelse
    </div>
    </div>
</x-app-layout>
