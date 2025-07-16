<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[['text' => __('messages.all_businesses'), 'url' => null]]" />
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Search and Filter Form -->
            <div class="frosted-card overflow-hidden shadow-lg sm:rounded-xl border border-frappe-surface2 mb-6">
                <div class="p-6">
                    <form id="filterForm" method="GET" action="{{ route('businesses.public.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
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

                            <!-- Filter by minimum rating -->
                            <div>
                                <label for="min_rating" class="block text-sm font-medium text-frappe-text mb-2">
                                    {{ __('messages.minimum_rating') }}
                                </label>
                                <select id="min_rating" name="min_rating"
                                    class="w-full px-3 py-2 bg-frappe-mantle border border-frappe-surface2 rounded-lg text-frappe-text focus:outline-none focus:ring-2 focus:ring-frappe-blue focus:border-transparent">
                                    <option value="">{{ __('messages.any_rating') }}</option>
                                    <option value="4" {{ request('min_rating') == '4' ? 'selected' : '' }}>
                                        4+ ⭐⭐⭐⭐
                                    </option>
                                    <option value="3" {{ request('min_rating') == '3' ? 'selected' : '' }}>
                                        3+ ⭐⭐⭐
                                    </option>
                                    <option value="2" {{ request('min_rating') == '2' ? 'selected' : '' }}>
                                        2+ ⭐⭐
                                    </option>
                                </select>
                            </div>

                            <!-- Sort by -->
                            <div>
                                <label for="sort" class="block text-sm font-medium text-frappe-text mb-2">
                                    {{ __('messages.sort_by') }}
                                </label>
                                <select id="sort" name="sort"
                                    class="w-full px-3 py-2 bg-frappe-mantle border border-frappe-surface2 rounded-lg text-frappe-text focus:outline-none focus:ring-2 focus:ring-frappe-blue focus:border-transparent">
                                    <option value="best" {{ request('sort') == 'best' ? 'selected' : '' }}>
                                        {{ __('messages.best') }}
                                    </option>
                                    <option value="name"
                                        {{ request('sort') == 'name' || !request('sort') ? 'selected' : '' }}>
                                        {{ __('messages.name_a_to_z') }}
                                    </option>
                                    <option value="rating_high"
                                        {{ request('sort') == 'rating_high' ? 'selected' : '' }}>
                                        {{ __('messages.rating_high_to_low') }}
                                    </option>
                                    <option value="rating_low" {{ request('sort') == 'rating_low' ? 'selected' : '' }}>
                                        {{ __('messages.rating_low_to_high') }}
                                    </option>
                                    <option value="reviews_count"
                                        {{ request('sort') == 'reviews_count' ? 'selected' : '' }}>
                                        {{ __('messages.most_reviewed') }}
                                    </option>
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

                            @if (request('search') || request('category') || request('min_rating') || request('sort'))
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
            @if (request('search') || request('category') || request('min_rating') || request('sort'))
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
                                        @else
                                            {{ __('messages.showing_filtered_businesses') }}
                                        @endif
                                        @if (request('min_rating'))
                                            {{ __('messages.with_rating_above', ['rating' => request('min_rating')]) }}
                                        @endif
                                    </p>
                                    <p class="text-frappe-subtext1 text-xs mt-1">
                                        {{ $businesses->total() }} {{ __('messages.results') }}
                                        @if (request('sort'))
                                            • {{ __('messages.sorted_by') }}
                                            @switch(request('sort'))
                                                @case('rating_high')
                                                    {{ __('messages.rating_high_to_low') }}
                                                @break

                                                @case('rating_low')
                                                    {{ __('messages.rating_low_to_high') }}
                                                @break

                                                @case('reviews_count')
                                                    {{ __('messages.most_reviewed') }}
                                                @break

                                                @case('best')
                                                    {{ __('messages.best') }}
                                                @break

                                                @default
                                                    {{ __('messages.best') }}
                                            @endswitch
                                        @endif
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

                <x-business-card :business="$business" />

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

        <!-- Pagination -->
        @if ($businesses->hasPages())
            <div class="mt-8">
                <div class="frosted-card overflow-hidden shadow-lg sm:rounded-xl border border-frappe-surface2">
                    <div class="p-6">
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                            <!-- Results info -->
                            <div class="text-sm text-frappe-subtext1">
                                {{ __('messages.showing') }}
                                <span class="font-medium text-frappe-text">{{ $businesses->firstItem() }}</span>
                                {{ __('messages.to') }}
                                <span class="font-medium text-frappe-text">{{ $businesses->lastItem() }}</span>
                                {{ __('messages.of') }}
                                <span class="font-medium text-frappe-text">{{ $businesses->total() }}</span>
                                {{ __('messages.results') }}
                            </div>

                            <!-- Pagination links -->
                            <div class="flex items-center gap-2">
                                {{-- Previous Page Link --}}
                                @if (!$businesses->onFirstPage())
                                    <a href="{{ $businesses->previousPageUrl() }}"
                                        class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-500/20 to-indigo-500/20 backdrop-blur-sm border border-blue-400/30 text-blue-300 px-4 py-2 rounded-lg text-sm hover:from-blue-500/30 hover:to-indigo-500/30 transition-all">
                                        <x-heroicon-o-chevron-left class="w-4 h-4" />
                                        {{ __('messages.previous') }}
                                    </a>
                                @endif

                                {{-- Pagination Elements --}}
                                @php
                                    $currentPage = $businesses->currentPage();
                                    $lastPage = $businesses->lastPage();
                                    $startPage = max(1, $currentPage - 2);
                                    $endPage = min($lastPage, $currentPage + 2);
                                @endphp

                                @if ($startPage > 1)
                                    <a href="{{ $businesses->url(1) }}"
                                        class="inline-flex items-center justify-center bg-gradient-to-r from-blue-500/20 to-indigo-500/20 backdrop-blur-sm border border-blue-400/30 text-blue-300 px-3 py-2 rounded-lg text-sm hover:from-blue-500/30 hover:to-indigo-500/30 transition-all">
                                        1
                                    </a>
                                    @if ($startPage > 2)
                                        <span
                                            class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-frappe-subtext1 bg-frappe-surface0/50 border border-frappe-surface2/50 leading-5 rounded-md">
                                            ...
                                        </span>
                                    @endif
                                @endif

                                @for ($page = $startPage; $page <= $endPage; $page++)
                                    @if ($page == $currentPage)
                                        <span
                                            class="inline-flex items-center justify-center bg-gradient-to-r from-blue-600/40 to-indigo-600/40 backdrop-blur-sm border border-blue-400/50 text-white px-3 py-2 rounded-lg text-sm font-medium shadow-lg">
                                            {{ $page }}
                                        </span>
                                    @else
                                        <a href="{{ $businesses->url($page) }}"
                                            class="inline-flex items-center justify-center bg-gradient-to-r from-blue-500/20 to-indigo-500/20 backdrop-blur-sm border border-blue-400/30 text-blue-300 px-3 py-2 rounded-lg text-sm hover:from-blue-500/30 hover:to-indigo-500/30 transition-all">
                                            {{ $page }}
                                        </a>
                                    @endif
                                @endfor

                                @if ($endPage < $lastPage)
                                    @if ($endPage < $lastPage - 1)
                                        <span
                                            class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-frappe-subtext1 bg-frappe-surface0/50 border border-frappe-surface2/50 leading-5 rounded-md">
                                            ...
                                        </span>
                                    @endif
                                    <a href="{{ $businesses->url($lastPage) }}"
                                        class="inline-flex items-center justify-center bg-gradient-to-r from-blue-500/20 to-indigo-500/20 backdrop-blur-sm border border-blue-400/30 text-blue-300 px-3 py-2 rounded-lg text-sm hover:from-blue-500/30 hover:to-indigo-500/30 transition-all">
                                        {{ $lastPage }}
                                    </a>
                                @endif

                                {{-- Next Page Link --}}
                                @if ($businesses->hasMorePages())
                                    <a href="{{ $businesses->nextPageUrl() }}"
                                        class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-500/20 to-indigo-500/20 backdrop-blur-sm border border-blue-400/30 text-blue-300 px-4 py-2 rounded-lg text-sm hover:from-blue-500/30 hover:to-indigo-500/30 transition-all">
                                        {{ __('messages.next') }}
                                        <x-heroicon-o-chevron-right class="w-4 h-4" />
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('filterForm');
            const categorySelect = document.getElementById('category');
            const minRatingSelect = document.getElementById('min_rating');
            const sortSelect = document.getElementById('sort');
            const searchInput = document.getElementById('search');

            categorySelect.addEventListener('change', function() {
                form.submit();
            });

            minRatingSelect.addEventListener('change', function() {
                form.submit();
            });

            sortSelect.addEventListener('change', function() {
                form.submit();
            });

            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    form.submit();
                }, 800);
            });
        });
    </script>
</x-app-layout>
