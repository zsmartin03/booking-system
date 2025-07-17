<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['text' => __('messages.businesses'), 'url' => route('businesses.public.index')],
            ['text' => $business->name, 'url' => null],
        ]" />
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
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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
                                <div class="text-frappe-text">{{ $business->formatted_address ?? $business->address }}
                                </div>
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

                    <!-- Location Map -->
                    @if ($business->latitude && $business->longitude)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-frappe-text mb-3">{{ __('messages.location') }}</h3>
                            <div class="bg-frappe-surface0/30 rounded-lg p-4 border border-frappe-surface2/30">
                                <div id="business-location-map" class="w-full h-80 rounded-lg"></div>
                                <p class="text-frappe-subtext1 text-sm mt-2">
                                    <x-heroicon-o-map-pin class="w-4 h-4 inline" />
                                    {{ $business->formatted_address ?? $business->address }}
                                </p>
                            </div>
                        </div>
                    @endif

                    @if ($business->logo)
                        <div class="mt-6 text-center">
                            <div class="inline-flex items-center justify-center max-w-full h-32">
                                <img src="{{ $business->logo_url }}" alt="{{ $business->name }} Logo"
                                    class="max-w-full max-h-full object-contain">
                            </div>
                        </div>
                    @endif

                    @auth
                        @if (in_array(auth()->user()->role, ['client', 'provider', 'admin']))
                            <div class="mt-6 text-center sm:text-left">
                                <a href="{{ route('bookings.create', $business->id) }}"
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

            <!-- Reviews Section -->
            <div class="frosted-card overflow-hidden shadow-lg sm:rounded-xl mt-6">
                <div class="p-4 sm:p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-semibold text-frappe-text">{{ __('messages.reviews') }}</h3>
                        <div class="flex items-center gap-2">
                            <div class="text-2xl font-bold text-frappe-text">
                                {{ number_format($business->average_rating, 1) }}</div>
                            <div class="flex items-center">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= floor($business->average_rating))
                                        <x-heroicon-s-star class="w-5 h-5 text-yellow-400" />
                                    @elseif ($i - 0.5 <= $business->average_rating)
                                        <x-heroicon-s-star class="w-5 h-5 text-yellow-400" />
                                    @else
                                        <x-heroicon-o-star class="w-5 h-5 text-gray-300" />
                                    @endif
                                @endfor
                            </div>
                            <span class="text-frappe-subtext1 text-sm">({{ $business->reviews_count }}
                                {{ __('messages.reviews_count') }})</span>
                        </div>
                    </div>

                    @auth
                        @if (
                            (auth()->user()->role === 'client' || auth()->user()->role === 'admin') &&
                                !auth()->user()->isAffiliatedWithBusiness($business->id))
                            @if (!$userReview)
                                <!-- Write Review Form -->
                                <div class="bg-frappe-surface0/30 rounded-lg p-4 mb-6 border border-frappe-surface2/30">
                                    <h4 class="font-semibold text-frappe-text mb-4">{{ __('messages.write_review') }}</h4>
                                    <form id="reviewForm" onsubmit="submitReview(event)" autocomplete="off">
                                        @csrf
                                        <div class="mb-4">
                                            <label
                                                class="block text-sm font-medium text-frappe-text mb-2">{{ __('messages.rating') }}</label>
                                            <div class="flex items-center gap-1">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <button type="button" onclick="setRating({{ $i }})"
                                                        onmouseover="hoverRating({{ $i }})"
                                                        onmouseout="resetRating()"
                                                        class="star-button text-gray-300 hover:text-yellow-400 transition-colors">
                                                        <x-heroicon-s-star class="w-6 h-6" />
                                                    </button>
                                                @endfor
                                            </div>
                                            <input type="hidden" name="rating" id="rating" value=""
                                                autocomplete="off">
                                        </div>
                                        <div class="mb-4">
                                            <label for="comment"
                                                class="block text-sm font-medium text-frappe-text mb-2">{{ __('messages.comment') }}</label>
                                            <textarea name="comment" id="comment" rows="4"
                                                class="w-full px-3 py-2 border border-frappe-surface2/30 rounded-md bg-frappe-surface0/50 text-frappe-text focus:outline-none focus:ring-2 focus:ring-frappe-blue/50"
                                                placeholder="{{ __('messages.share_your_experience') }}" autocomplete="off"></textarea>
                                        </div>
                                        <button type="submit"
                                            class="frosted-button text-white px-4 py-2 rounded-lg transition-all">
                                            {{ __('messages.submit_review') }}
                                        </button>
                                    </form>
                                </div>
                            @else
                                <!-- User has already reviewed - show message -->
                                <div class="bg-frappe-surface0/30 rounded-lg p-4 mb-6 border border-frappe-surface2/30">
                                    <div class="flex items-center gap-2">
                                        <x-heroicon-o-check-circle class="w-5 h-5 text-green-400" />
                                        <span
                                            class="text-frappe-text font-medium">{{ __('messages.already_reviewed') }}</span>
                                    </div>
                                    <p class="text-frappe-subtext1 text-sm mt-2">
                                        {{ __('messages.edit_delete_review_info') }}
                                    </p>
                                </div>
                            @endif
                        @endif
                    @endauth

                    <!-- Sort and Filter Controls -->
                    @if ((isset($otherReviews) && $otherReviews->count() > 0) || (isset($userReview) && $userReview))
                        <div class="mb-6 p-4 bg-frappe-surface0/20 rounded-lg border border-frappe-surface2/20">
                            <!-- Filter Status Info -->
                            @if (request('sort') || request('rating') || request('booking'))
                                <div class="mb-3 p-2 bg-frappe-blue/10 border border-frappe-blue/20 rounded-md">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2 text-sm text-frappe-blue">
                                            <x-heroicon-o-funnel class="w-4 h-4" />
                                            <span>
                                                @if (isset($otherReviews))
                                                    {{ __('messages.showing') }} {{ $otherReviews->count() }}
                                                    {{ __('messages.of') }}
                                                    {{ $business->reviews_count - (isset($userReview) && $userReview ? 1 : 0) }}
                                                    {{ __('messages.reviews') }}
                                                @endif
                                                @if (request('rating'))
                                                    • {{ request('rating') }} {{ __('messages.stars') }}
                                                @endif
                                                @if (request('booking') === 'verified')
                                                    • {{ __('messages.verified_only') }}
                                                @endif
                                            </span>
                                        </div>
                                        <button onclick="clearFilters()"
                                            class="text-xs px-2 py-1 bg-frappe-red/20 text-frappe-red border border-frappe-red/30 rounded hover:bg-frappe-red/30 transition-all">
                                            {{ __('messages.clear_filters') }}
                                        </button>
                                    </div>
                                </div>
                            @endif

                            <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                                <div class="flex flex-col sm:flex-row gap-4">
                                    <!-- Sort Options -->
                                    <div class="flex items-center gap-2">
                                        <label
                                            class="text-sm font-medium text-frappe-text">{{ __('messages.sort_reviews') }}:</label>
                                        <select id="sortReviews" onchange="applyFilters()"
                                            class="px-3 py-1 bg-frappe-surface1/30 border border-frappe-surface2/30 rounded-md text-frappe-text text-sm focus:outline-none focus:ring-2 focus:ring-frappe-blue/50">
                                            <option value="helpful"
                                                {{ request('sort') === 'helpful' || !request('sort') ? 'selected' : '' }}>
                                                {{ __('messages.sort_by_helpful') }}</option>
                                            <option value="rating_high"
                                                {{ request('sort') === 'rating_high' ? 'selected' : '' }}>
                                                {{ __('messages.sort_by_rating_high') }}</option>
                                            <option value="rating_low"
                                                {{ request('sort') === 'rating_low' ? 'selected' : '' }}>
                                                {{ __('messages.sort_by_rating_low') }}</option>
                                            <option value="date_new"
                                                {{ request('sort') === 'date_new' ? 'selected' : '' }}>
                                                {{ __('messages.sort_by_date_new') }}</option>
                                            <option value="date_old"
                                                {{ request('sort') === 'date_old' ? 'selected' : '' }}>
                                                {{ __('messages.sort_by_date_old') }}</option>
                                        </select>
                                    </div>

                                    <!-- Filter by Rating -->
                                    <div class="flex items-center gap-2">
                                        <label
                                            class="text-sm font-medium text-frappe-text">{{ __('messages.filter_reviews') }}:</label>
                                        <select id="filterRating" onchange="applyFilters()"
                                            class="px-3 py-1 bg-frappe-surface1/30 border border-frappe-surface2/30 rounded-md text-frappe-text text-sm focus:outline-none focus:ring-2 focus:ring-frappe-blue/50">
                                            <option value="" {{ !request('rating') ? 'selected' : '' }}>
                                                {{ __('messages.all_ratings') }}</option>
                                            <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>
                                                ⭐⭐⭐⭐⭐ (5)</option>
                                            <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>
                                                ⭐⭐⭐⭐ (4)</option>
                                            <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>
                                                ⭐⭐⭐ (3)</option>
                                            <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>⭐⭐
                                                (2)</option>
                                            <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>⭐
                                                (1)</option>
                                        </select>
                                    </div>

                                    <!-- Filter by Booking Status -->
                                    <div class="flex items-center gap-2">
                                        <label
                                            class="flex items-center gap-2 text-sm font-medium text-frappe-text cursor-pointer">
                                            <input type="checkbox" id="filterBooking" onchange="applyFilters()"
                                                {{ request('booking') === 'verified' ? 'checked' : '' }}
                                                class="rounded border-frappe-surface2/30 bg-frappe-surface0/50 text-frappe-blue focus:ring-frappe-blue/50">
                                            {{ __('messages.with_booking') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Reviews List -->
                    <div class="space-y-4" id="reviewsList">
                        @auth
                            @if ($userReview)
                                <!-- User's Own Review (shown first) -->
                                <div class="bg-frappe-surface0/30 rounded-lg p-4 border border-frappe-surface2/30 ring-2 ring-frappe-blue/30"
                                    data-review-id="{{ $userReview->id }}">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 bg-frappe-blue/20 rounded-full flex items-center justify-center overflow-hidden">
                                                @if ($userReview->user->avatar)
                                                    <img src="{{ $userReview->user->getAvatarUrl() }}"
                                                        alt="{{ $userReview->user->name }}"
                                                        class="w-full h-full object-cover rounded-full">
                                                @else
                                                    <span
                                                        class="text-frappe-blue font-semibold">{{ substr($userReview->user->name, 0, 1) }}</span>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <span
                                                        class="font-medium text-frappe-text">{{ $userReview->user->name }}</span>
                                                    <span
                                                        class="inline-flex items-center gap-1 bg-frappe-blue/20 text-frappe-blue px-2 py-1 rounded-full text-xs">
                                                        <x-heroicon-s-user class="w-4 h-4" />
                                                        {{ __('messages.your_review') }}
                                                    </span>
                                                    @if ($userReview->has_booking)
                                                        <span
                                                            class="inline-flex items-center gap-1 bg-green-500/20 text-green-400 px-2 py-1 rounded-full text-xs">
                                                            <x-heroicon-s-check-badge class="w-4 h-4" />
                                                            {{ __('messages.verified_booking') }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="flex items-center gap-1 mt-1">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        @if ($i <= $userReview->rating)
                                                            <x-heroicon-s-star class="w-4 h-4 text-yellow-400" />
                                                        @else
                                                            <x-heroicon-o-star class="w-4 h-4 text-gray-300" />
                                                        @endif
                                                    @endfor
                                                    <span
                                                        class="text-frappe-subtext1 text-sm ml-2">{{ $userReview->created_at->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        @if (auth()->id() === $userReview->user_id)
                                            <!-- Edit/Delete buttons for review owner -->
                                            <div class="flex items-center gap-2">
                                                <button onclick="editReview({{ $userReview->id }})"
                                                    data-rating="{{ $userReview->rating }}"
                                                    data-comment="{{ htmlspecialchars($userReview->comment, ENT_QUOTES, 'UTF-8') }}"
                                                    class="edit-button text-white px-4 py-2 rounded-lg inline-flex items-center gap-2 text-sm transition-all"
                                                    title="{{ __('messages.edit') }} {{ __('messages.reviews') }}">
                                                    <x-heroicon-o-pencil class="w-4 h-4" />
                                                    <span class="hidden sm:inline">{{ __('messages.edit') }}</span>
                                                </button>
                                                <button
                                                    onclick="showDeleteReviewModal({{ $userReview->id }}, '{{ addslashes($business->name) }}')"
                                                    class="delete-button text-white px-4 py-2 rounded-lg inline-flex items-center gap-2 text-sm transition-all"
                                                    title="{{ __('messages.delete') }} {{ __('messages.reviews') }}">
                                                    <x-heroicon-o-trash class="w-4 h-4" />
                                                    <span class="hidden sm:inline">{{ __('messages.delete') }}</span>
                                                </button>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Review Content -->
                                    <div class="review-content-{{ $userReview->id }}">
                                        <p class="text-frappe-text mb-3">{{ $userReview->comment }}</p>
                                    </div>

                                    <!-- Edit Form (initially hidden) -->
                                    <div class="review-edit-form-{{ $userReview->id }} hidden">
                                        <form onsubmit="updateReview(event, {{ $userReview->id }})">
                                            @csrf
                                            <div class="mb-4">
                                                <label
                                                    class="block text-sm font-medium text-frappe-text mb-2">{{ __('messages.rating') }}</label>
                                                <div class="flex items-center gap-1">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <button type="button"
                                                            onclick="setEditRating({{ $userReview->id }}, {{ $i }})"
                                                            onmouseover="hoverEditRating({{ $userReview->id }}, {{ $i }})"
                                                            onmouseout="resetEditRating({{ $userReview->id }})"
                                                            class="edit-star-button-{{ $userReview->id }} text-gray-300 hover:text-yellow-400 transition-colors">
                                                            <x-heroicon-s-star class="w-6 h-6" />
                                                        </button>
                                                    @endfor
                                                </div>
                                                <input type="hidden" name="rating"
                                                    id="edit-rating-{{ $userReview->id }}"
                                                    value="{{ $userReview->rating }}">
                                            </div>
                                            <div class="mb-4">
                                                <label
                                                    class="block text-sm font-medium text-frappe-text mb-2">{{ __('messages.comment') }}</label>
                                                <textarea name="comment" id="edit-comment-{{ $userReview->id }}" rows="4"
                                                    class="w-full px-3 py-2 border border-frappe-surface2/30 rounded-md bg-frappe-surface0/50 text-frappe-text focus:outline-none focus:ring-2 focus:ring-frappe-blue/50"
                                                    placeholder="{{ __('messages.share_your_experience') }}">{{ $userReview->comment }}</textarea>
                                            </div>
                                            <div class="flex gap-2">
                                                <button type="submit"
                                                    class="frosted-button text-white px-4 py-2 rounded-lg transition-all text-sm">
                                                    {{ __('messages.update_review') }}
                                                </button>
                                                <button type="button" onclick="cancelEditReview({{ $userReview->id }})"
                                                    class="bg-frappe-surface0/50 text-frappe-text px-4 py-2 rounded-lg hover:bg-frappe-surface1/50 transition-all text-sm">
                                                    {{ __('messages.cancel') }}
                                                </button>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- Business Response -->
                                    @if ($userReview->response)
                                        <div
                                            class="response-content-{{ $userReview->response->id }} bg-frappe-surface1/30 rounded-lg p-3 border-l-4 border-frappe-blue mt-3">
                                            <div class="flex items-center justify-between mb-2">
                                                <div class="flex items-center gap-2">
                                                    <x-heroicon-o-building-storefront class="w-4 h-4 text-frappe-blue" />
                                                    <span
                                                        class="text-sm font-medium text-frappe-blue">{{ __('messages.response_from') }}
                                                        {{ $business->name }}</span>
                                                    <span
                                                        class="text-frappe-subtext1 text-xs">{{ $userReview->response->created_at->diffForHumans() }}</span>
                                                </div>
                                                @if (auth()->check() && auth()->id() === $business->user_id)
                                                    <div class="flex items-center gap-2">
                                                        <button onclick="editResponse({{ $userReview->response->id }})"
                                                            data-response="{{ htmlspecialchars($userReview->response->response, ENT_QUOTES, 'UTF-8') }}"
                                                            class="edit-button text-white px-3 py-2 rounded-lg inline-flex items-center gap-2 text-sm transition-all"
                                                            title="{{ __('messages.edit_response') }}">
                                                            <x-heroicon-o-pencil class="w-4 h-4" />
                                                            <span
                                                                class="hidden sm:inline">{{ __('messages.edit') }}</span>
                                                        </button>
                                                        <button
                                                            onclick="showDeleteResponseModal({{ $userReview->response->id }}, '{{ addslashes($business->name) }}')"
                                                            class="delete-button text-white px-3 py-2 rounded-lg inline-flex items-center gap-2 text-sm transition-all"
                                                            title="{{ __('messages.delete_response') }}">
                                                            <x-heroicon-o-trash class="w-4 h-4" />
                                                            <span
                                                                class="hidden sm:inline">{{ __('messages.delete') }}</span>
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                            <p class="text-frappe-text text-sm">{{ $userReview->response->response }}</p>
                                        </div>
                                    @elseif (auth()->check() && auth()->id() === $business->user_id)
                                        <!-- Response Form for Business Owner -->
                                        <div
                                            class="bg-frappe-surface1/30 rounded-lg p-3 border-l-4 border-frappe-blue mt-3">
                                            <form onsubmit="submitResponse(event, {{ $userReview->id }})">
                                                @csrf
                                                <div class="mb-3">
                                                    <label
                                                        class="block text-sm font-medium text-frappe-text mb-2">{{ __('messages.respond_to_review') }}</label>
                                                    <textarea name="response" rows="3"
                                                        class="w-full px-3 py-2 border border-frappe-surface2/30 rounded-md bg-frappe-surface0/50 text-frappe-text focus:outline-none focus:ring-2 focus:ring-frappe-blue/50"
                                                        placeholder="{{ __('messages.write_your_response') }}"></textarea>
                                                </div>
                                                <button type="submit"
                                                    class="frosted-button text-white px-4 py-2 rounded-lg transition-all text-sm">
                                                    {{ __('messages.submit_response') }}
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endauth

                        @if (isset($otherReviews) && $otherReviews->count() > 0)
                            <!-- Other Reviews Container -->
                            <div id="otherReviewsContainer">
                                @include('businesses.partials.reviews-list', [
                                    'otherReviews' => $otherReviews,
                                    'business' => $business,
                                ])
                            </div>
                        @endif

                        @if ((!isset($userReview) || !$userReview) && (!isset($otherReviews) || $otherReviews->count() === 0))
                            <div class="text-center py-8">
                                <x-heroicon-o-chat-bubble-left-ellipsis
                                    class="w-12 h-12 text-frappe-subtext1 mx-auto mb-3" />
                                <p class="text-frappe-subtext1">{{ __('messages.no_reviews') }}.
                                    {{ __('messages.first_review') }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Pagination Controls -->
                    <div id="paginationContainer"
                        class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4"
                        style="display: none;">
                        <div class="text-sm text-frappe-subtext1">
                            <span
                                id="paginationInfo">{{ __('messages.showing_reviews', ['from' => 'FROM_PLACEHOLDER', 'to' => 'TO_PLACEHOLDER', 'total' => 'TOTAL_PLACEHOLDER']) }}</span>
                        </div>
                        <div class="flex items-center gap-2" id="paginationButtons">
                            <!-- Pagination buttons will be dynamically generated -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- Related Businesses Carousel -->
            @if ($relatedBusinesses->count() > 0)
                <div class="frosted-card overflow-hidden shadow-lg sm:rounded-xl mt-6">
                    <div class="p-4 sm:p-6">
                        <h3 class="text-xl font-semibold text-frappe-text mb-4">
                            {{ __('messages.similar_businesses') }}
                        </h3>
                        <div class="relative">
                            <!-- Navigation Arrows -->
                            @if ($relatedBusinesses->count() > 1)
                                <button id="prevBtn"
                                    class="absolute -left-4 top-1/2 -translate-y-1/2 z-20 bg-frappe-surface0/95 hover:bg-frappe-surface1/95 border border-frappe-surface2/50 rounded-full p-3 shadow-xl transition-all duration-300 hover:shadow-2xl hover:scale-110">
                                    <x-heroicon-o-chevron-left class="w-6 h-6 text-frappe-text" />
                                </button>
                                <button id="nextBtn"
                                    class="absolute -right-4 top-1/2 -translate-y-1/2 z-20 bg-frappe-surface0/95 hover:bg-frappe-surface1/95 border border-frappe-surface2/50 rounded-full p-3 shadow-xl transition-all duration-300 hover:shadow-2xl hover:scale-110">
                                    <x-heroicon-o-chevron-right class="w-6 h-6 text-frappe-text" />
                                </button>
                            @endif

                            <div class="overflow-hidden mx-16">
                                <div id="carousel" class="flex transition-transform duration-500 ease-in-out">
                                    @foreach ($relatedBusinesses as $relatedBusiness)
                                        <div
                                            class="flex-none w-80 mx-2 bg-frappe-surface0/30 rounded-lg border border-frappe-surface2/30 hover:shadow-lg transition-all duration-300">
                                            <div class="p-4">
                                                <h4 class="font-semibold text-frappe-text mb-2">
                                                    <a href="{{ route('businesses.show', $relatedBusiness->id) }}"
                                                        class="text-frappe-blue hover:text-frappe-sapphire transition-colors">
                                                        {{ $relatedBusiness->name }}
                                                    </a>
                                                </h4>

                                                <!-- Average Rating Display -->
                                                <div class="flex items-center gap-2 mb-3">
                                                    <div class="flex items-center">
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            @if ($i <= floor($relatedBusiness->average_rating))
                                                                <x-heroicon-s-star class="w-4 h-4 text-yellow-400" />
                                                            @elseif ($i - 0.5 <= $relatedBusiness->average_rating)
                                                                <x-heroicon-s-star class="w-4 h-4 text-yellow-400" />
                                                            @else
                                                                <x-heroicon-o-star class="w-4 h-4 text-gray-300" />
                                                            @endif
                                                        @endfor
                                                    </div>
                                                    <span class="text-sm font-medium text-frappe-text">
                                                        {{ number_format($relatedBusiness->average_rating, 1) }}
                                                    </span>
                                                    <span class="text-frappe-subtext1 text-xs">
                                                        ({{ $relatedBusiness->reviews_count }}
                                                        {{ __('messages.reviews_count') }})
                                                    </span>
                                                </div>
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

                <!-- Carousel JavaScript -->
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const carousel = document.getElementById('carousel');
                        const prevBtn = document.getElementById('prevBtn');
                        const nextBtn = document.getElementById('nextBtn');
                        const totalItems = {{ $relatedBusinesses->count() }};

                        if (totalItems > 1) {
                            const cardWidth = 336; // 320px width + 16px margins
                            let currentIndex = 0;
                            let cardsToShow = 1;
                            let isTransitioning = false;

                            // Calculate how many cards fit on screen
                            function calculateCardsToShow() {
                                const containerWidth = carousel.parentElement.offsetWidth;
                                cardsToShow = Math.floor(containerWidth / cardWidth);
                                if (cardsToShow === 0) cardsToShow = 1;

                                // Always show navigation arrows when there are more than 1 card
                                if (totalItems > 1) {
                                    if (prevBtn) prevBtn.style.display = 'block';
                                    if (nextBtn) nextBtn.style.display = 'block';
                                } else {
                                    if (prevBtn) prevBtn.style.display = 'none';
                                    if (nextBtn) nextBtn.style.display = 'none';
                                }
                            }

                            // Clone cards for seamless infinite scrolling
                            function setupInfiniteScroll() {
                                const cards = carousel.children;
                                const originalCards = Array.from(cards);

                                // Clear any existing clones
                                carousel.querySelectorAll('.clone').forEach(clone => clone.remove());

                                // Clone last few cards and prepend them
                                for (let i = Math.min(3, totalItems) - 1; i >= 0; i--) {
                                    const clone = originalCards[totalItems - 1 - i].cloneNode(true);
                                    clone.classList.add('clone');
                                    carousel.insertBefore(clone, carousel.firstChild);
                                }

                                // Clone first few cards and append them
                                for (let i = 0; i < Math.min(3, totalItems); i++) {
                                    const clone = originalCards[i].cloneNode(true);
                                    clone.classList.add('clone');
                                    carousel.appendChild(clone);
                                }

                                // Set initial position to show first real card
                                currentIndex = Math.min(3, totalItems);
                                updateCarousel(false);
                            }

                            function updateCarousel(animate = true) {
                                const translateX = -currentIndex * cardWidth;
                                if (animate) {
                                    carousel.style.transition = 'transform 0.5s ease-in-out';
                                } else {
                                    carousel.style.transition = 'none';
                                }
                                carousel.style.transform = `translateX(${translateX}px)`;
                            }

                            function nextSlide() {
                                if (isTransitioning) return;
                                isTransitioning = true;

                                currentIndex += 1;
                                updateCarousel();

                                setTimeout(() => {
                                    // Check if we need to loop back to start
                                    if (currentIndex >= totalItems + Math.min(3, totalItems)) {
                                        currentIndex = Math.min(3, totalItems);
                                        updateCarousel(false);
                                    }
                                    isTransitioning = false;
                                }, 500);
                            }

                            function autoNextSlide() {
                                if (isTransitioning) return;
                                isTransitioning = true;

                                currentIndex += 1;
                                updateCarousel();

                                setTimeout(() => {
                                    // Check if we need to loop back to start
                                    if (currentIndex >= totalItems + Math.min(3, totalItems)) {
                                        currentIndex = Math.min(3, totalItems);
                                        updateCarousel(false);
                                    }
                                    isTransitioning = false;
                                }, 500);
                            }

                            function prevSlide() {
                                if (isTransitioning) return;
                                isTransitioning = true;

                                currentIndex -= 1;
                                updateCarousel();

                                setTimeout(() => {
                                    // Check if we need to loop back to end
                                    if (currentIndex < Math.min(3, totalItems)) {
                                        currentIndex = totalItems + Math.min(3, totalItems) - 1;
                                        updateCarousel(false);
                                    }
                                    isTransitioning = false;
                                }, 500);
                            }

                            // Initialize
                            calculateCardsToShow();
                            setupInfiniteScroll();

                            // Event listeners
                            if (nextBtn) nextBtn.addEventListener('click', nextSlide);
                            if (prevBtn) prevBtn.addEventListener('click', prevSlide);

                            // Recalculate on window resize
                            window.addEventListener('resize', function() {
                                calculateCardsToShow();
                                setupInfiniteScroll();
                            });

                            // Auto-scroll every 5 seconds (one card at a time)
                            setInterval(autoNextSlide, 5000);
                        }
                    });
                </script>
            @endif
        </div>
    </div>

    <!-- Delete Review Confirmation Modal -->
    <div id="deleteReviewModal"
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm z-50 hidden">
        <div class="frosted-modal p-6 rounded-2xl shadow-2xl w-full max-w-md mx-4">
            <h3 class="text-xl font-semibold mb-4 text-frappe-red">{{ __('messages.delete_review_modal_title') }}
            </h3>
            <p class="mb-6 text-frappe-text opacity-90" id="deleteReviewConfirmText"></p>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="hideDeleteReviewModal()"
                    class="px-6 py-2 bg-gradient-to-r from-gray-500/20 to-gray-600/20 backdrop-blur-sm border border-gray-400/30 text-gray-300 rounded-lg hover:from-gray-500/30 hover:to-gray-600/30 transition-all">
                    {{ __('messages.cancel') }}
                </button>
                <button type="button" onclick="confirmDeleteReview()"
                    class="px-6 py-2 bg-gradient-to-r from-red-500/30 to-pink-500/30 backdrop-blur-sm border border-red-400/40 text-red-300 rounded-lg hover:from-red-500/40 hover:to-pink-500/40 transition-all">
                    {{ __('messages.delete') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Delete Response Confirmation Modal -->
    <div id="deleteResponseModal" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="frosted-modal p-6 rounded-2xl shadow-2xl w-full max-w-md">
                <h3 class="text-xl font-semibold mb-4 text-frappe-red">
                    {{ __('messages.delete_response_modal_title') }}</h3>
                <p class="mb-6 text-frappe-text opacity-90" id="deleteResponseConfirmText"></p>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="hideDeleteResponseModal()"
                        class="px-6 py-2 bg-gradient-to-r from-gray-500/20 to-gray-600/20 backdrop-blur-sm border border-gray-400/30 text-gray-300 rounded-lg hover:from-gray-500/30 hover:to-gray-600/30 transition-all">
                        {{ __('messages.cancel') }}
                    </button>
                    <button type="button" onclick="confirmDeleteResponse()"
                        class="px-6 py-2 bg-gradient-to-r from-red-500/30 to-pink-500/30 backdrop-blur-sm border border-red-400/40 text-red-300 rounded-lg hover:from-red-500/40 hover:to-pink-500/40 transition-all">
                        {{ __('messages.delete') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const translations = {
            pleaseSelectRating: '{{ __('messages.please_select_rating') }}',
            pleaseWriteComment: '{{ __('messages.please_write_comment') }}',
            pleaseWriteResponse: '{{ __('messages.please_write_response') }}',
            failedToSubmitReview: '{{ __('messages.failed_to_submit_review') }}',
            failedToUpdateReview: '{{ __('messages.failed_to_update_review') }}',
            failedToDeleteReview: '{{ __('messages.failed_to_delete_review') }}',
            failedToVote: '{{ __('messages.failed_to_vote') }}',
            failedToSubmitResponse: '{{ __('messages.failed_to_submit_response') }}',
            failedToUpdateResponse: '{{ __('messages.failed_to_update_response') }}',
            failedToDeleteResponse: '{{ __('messages.failed_to_delete_response') }}',
            submitting: '{{ __('messages.submitting') }}',
            updating: '{{ __('messages.updating') }}',
            deleteReviewConfirm: '{{ __('messages.delete_review_confirm') }}',
            deleteResponseConfirm: '{{ __('messages.delete_response_confirm') }}'
        };

        let selectedRating = 0;
        let editRatings = {};

        document.addEventListener('DOMContentLoaded', function() {
            const commentInput = document.getElementById('comment');
            const ratingInput = document.getElementById('rating');

            if (commentInput) {
                commentInput.value = '';
            }
            if (ratingInput) {
                ratingInput.value = '';
            }

            selectedRating = 0;
            updateStarDisplay(0);
        });

        function setRating(rating) {
            selectedRating = rating;
            document.getElementById('rating').value = rating;
            updateStarDisplay(rating);
        }

        function hoverRating(rating) {
            updateStarDisplay(rating);
        }

        function resetRating() {
            updateStarDisplay(selectedRating);
        }

        function updateStarDisplay(rating) {
            const stars = document.querySelectorAll('.star-button');
            stars.forEach((star, index) => {
                const starIcon = star.querySelector('svg');
                if (index < rating) {
                    starIcon.classList.remove('text-gray-300');
                    starIcon.classList.add('text-yellow-400');
                } else {
                    starIcon.classList.remove('text-yellow-400');
                    starIcon.classList.add('text-gray-300');
                }
            });
        }

        function setEditRating(reviewId, rating) {
            editRatings[reviewId] = rating;
            document.getElementById(`edit-rating-${reviewId}`).value = rating;
            updateEditStarDisplay(reviewId, rating);
        }

        function hoverEditRating(reviewId, rating) {
            updateEditStarDisplay(reviewId, rating);
        }

        function resetEditRating(reviewId) {
            const currentRating = editRatings[reviewId] || 0;
            updateEditStarDisplay(reviewId, currentRating);
        }

        function updateEditStarDisplay(reviewId, rating) {
            const stars = document.querySelectorAll(`.edit-star-button-${reviewId}`);
            stars.forEach((star, index) => {
                const starIcon = star.querySelector('svg');
                if (index < rating) {
                    starIcon.classList.remove('text-gray-300');
                    starIcon.classList.add('text-yellow-400');
                } else {
                    starIcon.classList.remove('text-yellow-400');
                    starIcon.classList.add('text-gray-300');
                }
            });
        }

        function editReview(reviewId) {
            const button = event.target.closest('button');
            const rating = parseInt(button.getAttribute('data-rating'));
            const comment = button.getAttribute('data-comment');

            editRatings[reviewId] = rating;
            document.getElementById(`edit-rating-${reviewId}`).value = rating;
            document.getElementById(`edit-comment-${reviewId}`).value = comment;

            updateEditStarDisplay(reviewId, rating);

            document.querySelector(`.review-content-${reviewId}`).classList.add('hidden');
            document.querySelector(`.review-edit-form-${reviewId}`).classList.remove('hidden');
        }

        function cancelEditReview(reviewId) {
            document.querySelector(`.review-content-${reviewId}`).classList.remove('hidden');
            document.querySelector(`.review-edit-form-${reviewId}`).classList.add('hidden');
        }

        function deleteReview(reviewId) {
            fetch(`/reviews/${reviewId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        resetReviewForm();

                        document.querySelector(`[data-review-id="${reviewId}"]`).remove();

                        location.reload();
                    } else {
                        alert(data.error || translations.failedToDeleteReview);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert(translations.failedToDeleteReview);
                });
        }

        function resetReviewForm() {
            selectedRating = 0;
            const ratingInput = document.getElementById('rating');
            if (ratingInput) {
                ratingInput.value = '';
            }

            const commentInput = document.getElementById('comment');
            if (commentInput) {
                commentInput.value = '';
            }

            const stars = document.querySelectorAll('.star-button');
            stars.forEach(star => {
                const starIcon = star.querySelector('svg');
                starIcon.classList.remove('text-yellow-400');
                starIcon.classList.add('text-gray-300');
            });
        }

        let reviewToDelete = null;

        function showDeleteReviewModal(reviewId, businessName) {
            reviewToDelete = reviewId;
            const confirmText = translations.deleteReviewConfirm.replace(':business', businessName);
            document.getElementById('deleteReviewConfirmText').textContent = confirmText;
            document.getElementById('deleteReviewModal').classList.remove('hidden');
        }

        function hideDeleteReviewModal() {
            reviewToDelete = null;
            document.getElementById('deleteReviewModal').classList.add('hidden');
        }

        function confirmDeleteReview() {
            if (reviewToDelete) {
                deleteReview(reviewToDelete);
                hideDeleteReviewModal();
            }
        }

        function updateReview(event, reviewId) {
            event.preventDefault();

            const form = event.target;
            const rating = document.getElementById(`edit-rating-${reviewId}`).value;
            const comment = document.getElementById(`edit-comment-${reviewId}`).value;

            if (!rating) {
                alert(translations.pleaseSelectRating);
                return;
            }

            if (!comment.trim()) {
                alert(translations.pleaseWriteComment);
                return;
            }

            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = translations.updating;
            submitBtn.disabled = true;

            fetch(`/reviews/${reviewId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        rating: parseInt(rating),
                        comment: comment
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.error || translations.failedToUpdateReview);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert(translations.failedToUpdateReview);
                })
                .finally(() => {
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                });
        }

        function submitReview(event) {
            event.preventDefault();

            const form = event.target;
            const rating = document.getElementById('rating').value;
            const comment = document.getElementById('comment').value;

            if (!rating) {
                alert(translations.pleaseSelectRating);
                return;
            }

            if (!comment.trim()) {
                alert(translations.pleaseWriteComment);
                return;
            }

            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = translations.submitting;
            submitBtn.disabled = true;

            fetch(`/businesses/{{ $business->id }}/reviews`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        rating: parseInt(rating),
                        comment: comment
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.error || translations.failedToSubmitReview);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert(translations.failedToSubmitReview);
                })
                .finally(() => {
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                });
        }

        function voteReview(reviewId, isUpvote) {
            fetch(`/reviews/${reviewId}/vote`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        is_upvote: isUpvote
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const reviewDiv = document.querySelector(`[data-review-id="${reviewId}"]`);

                        const upvoteBtn = reviewDiv.querySelector(`button[onclick="voteReview(${reviewId}, true)"]`);
                        const upvoteCount = upvoteBtn.querySelector('.upvote-count');
                        upvoteCount.textContent = data.upvotes;

                        const downvoteBtn = reviewDiv.querySelector(`button[onclick="voteReview(${reviewId}, false)"]`);
                        const downvoteCount = downvoteBtn.querySelector('.downvote-count');
                        downvoteCount.textContent = data.downvotes;

                        upvoteBtn.className = upvoteBtn.className.replace(
                            /bg-green-500\/20 text-green-400|bg-frappe-surface0\/50 text-frappe-subtext1 hover:bg-green-500\/10/g,
                            '');
                        downvoteBtn.className = downvoteBtn.className.replace(
                            /bg-red-500\/20 text-red-400|bg-frappe-surface0\/50 text-frappe-subtext1 hover:bg-red-500\/10/g,
                            '');

                        if (data.user_vote === true) {
                            upvoteBtn.className += ' bg-green-500/20 text-green-400';
                            downvoteBtn.className += ' bg-frappe-surface0/50 text-frappe-subtext1 hover:bg-red-500/10';
                        } else if (data.user_vote === false) {
                            upvoteBtn.className += ' bg-frappe-surface0/50 text-frappe-subtext1 hover:bg-green-500/10';
                            downvoteBtn.className += ' bg-red-500/20 text-red-400';
                        } else {
                            upvoteBtn.className += ' bg-frappe-surface0/50 text-frappe-subtext1 hover:bg-green-500/10';
                            downvoteBtn.className += ' bg-frappe-surface0/50 text-frappe-subtext1 hover:bg-red-500/10';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert(translations.failedToVote);
                });
        }

        function submitResponse(event, reviewId) {
            event.preventDefault();

            const form = event.target;
            const response = form.querySelector('textarea[name="response"]').value;

            if (!response.trim()) {
                alert(translations.pleaseWriteResponse);
                return;
            }

            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = translations.submitting;
            submitBtn.disabled = true;

            fetch(`/reviews/${reviewId}/respond`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        response: response
                    })
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', [...response.headers.entries()]);

                    return response.text().then(text => {
                        console.log('Response text:', text);

                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}, body: ${text}`);
                        }

                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            throw new Error('Response is not valid JSON: ' + text);
                        }
                    });
                })
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.error || 'Failed to submit response');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert(translations.failedToSubmitResponse + ': ' + error.message);
                })
                .finally(() => {
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                });
        }

        function editResponse(responseId) {
            const button = event.target.closest('button');
            const responseText = button.getAttribute('data-response');

            document.getElementById(`edit-response-${responseId}`).value = responseText;

            document.querySelector(`.response-content-${responseId}`).classList.add('hidden');
            document.querySelector(`.response-edit-form-${responseId}`).classList.remove('hidden');
        }

        function cancelEditResponse(responseId) {
            document.querySelector(`.response-content-${responseId}`).classList.remove('hidden');
            document.querySelector(`.response-edit-form-${responseId}`).classList.add('hidden');
        }

        function updateResponse(event, responseId) {
            event.preventDefault();

            const form = event.target;
            const response = document.getElementById(`edit-response-${responseId}`).value;

            if (!response.trim()) {
                alert(translations.pleaseWriteResponse);
                return;
            }

            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = translations.updating;
            submitBtn.disabled = true;

            fetch(`/review-responses/${responseId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        response: response
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.error || translations.failedToUpdateResponse);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert(translations.failedToUpdateResponse);
                })
                .finally(() => {
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                });
        }

        function deleteResponse(responseId) {
            fetch(`/review-responses/${responseId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.error || translations.failedToDeleteResponse);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert(translations.failedToDeleteResponse);
                });
        }

        let responseToDelete = null;

        function showDeleteResponseModal(responseId, businessName) {
            responseToDelete = responseId;
            const confirmText = translations.deleteResponseConfirm.replace(':name', businessName);
            document.getElementById('deleteResponseConfirmText').textContent = confirmText;
            document.getElementById('deleteResponseModal').classList.remove('hidden');
        }

        function hideDeleteResponseModal() {
            responseToDelete = null;
            document.getElementById('deleteResponseModal').classList.add('hidden');
        }

        function confirmDeleteResponse() {
            if (responseToDelete) {
                deleteResponse(responseToDelete);
                hideDeleteResponseModal();
            }
        }

        function loadReviewsPage(page) {
            const sortBy = document.getElementById('sortReviews').value;
            const filterRating = document.getElementById('filterRating').value;
            const filterBooking = document.getElementById('filterBooking') ? (document.getElementById('filterBooking')
                .checked ? 'verified' : '') : '';

            const container = document.getElementById('otherReviewsContainer');
            if (container) {
                container.innerHTML =
                    '<div class="text-center py-8"><div class="text-frappe-subtext1">{{ __('messages.loading_reviews') }}</div></div>';
            }

            const params = new URLSearchParams({
                page: page,
                sort: sortBy
            });

            if (filterRating) params.append('rating', filterRating);
            if (filterBooking) params.append('booking', filterBooking);

            fetch(`{{ route('businesses.show', $business->id) }}?${params}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (container) {
                        container.innerHTML = data.html;
                    }

                    updatePaginationControls(data.pagination);
                })
                .catch(error => {
                    console.error('Error loading reviews:', error);
                    if (container) {
                        container.innerHTML = '<div class="text-center py-8 text-red-500">Error loading reviews</div>';
                    }
                });
        }

        function updatePaginationControls(pagination) {
            const paginationContainer = document.getElementById('paginationContainer');
            if (!paginationContainer) return;

            if (pagination.last_page <= 1) {
                paginationContainer.style.display = 'none';
                return;
            }

            paginationContainer.style.display = 'flex';

            paginationContainer.style.display = 'flex';

            const paginationInfo = document.getElementById('paginationInfo');
            if (paginationInfo) {
                paginationInfo.textContent =
                    '{{ __('messages.showing_reviews', ['from' => 'FROM_PLACEHOLDER', 'to' => 'TO_PLACEHOLDER', 'total' => 'TOTAL_PLACEHOLDER']) }}'
                    .replace('FROM_PLACEHOLDER', pagination.from || 0)
                    .replace('TO_PLACEHOLDER', pagination.to || 0)
                    .replace('TOTAL_PLACEHOLDER', pagination.total);
            }

            const buttonsContainer = document.getElementById('paginationButtons');
            if (buttonsContainer) {
                buttonsContainer.innerHTML = '';

                if (pagination.current_page > 1) {
                    const prevBtn = document.createElement('button');
                    prevBtn.onclick = () => loadReviewsPage(pagination.current_page - 1);
                    prevBtn.className =
                        'inline-flex items-center gap-2 bg-gradient-to-r from-blue-500/20 to-indigo-500/20 backdrop-blur-sm border border-blue-400/30 text-blue-300 px-4 py-2 rounded-lg text-sm hover:from-blue-500/30 hover:to-indigo-500/30 transition-all';
                    prevBtn.textContent = '{{ __('messages.previous') }}';
                    buttonsContainer.appendChild(prevBtn);
                }

                const startPage = Math.max(1, pagination.current_page - 2);
                const endPage = Math.min(pagination.last_page, pagination.current_page + 2);

                for (let i = startPage; i <= endPage; i++) {
                    const pageBtn = document.createElement('button');
                    pageBtn.onclick = () => loadReviewsPage(i);
                    pageBtn.className = i === pagination.current_page ?
                        'inline-flex items-center justify-center bg-gradient-to-r from-blue-600/40 to-indigo-600/40 backdrop-blur-sm border border-blue-400/50 text-white px-3 py-2 rounded-lg text-sm font-medium shadow-lg' :
                        'inline-flex items-center justify-center bg-gradient-to-r from-blue-500/20 to-indigo-500/20 backdrop-blur-sm border border-blue-400/30 text-blue-300 px-3 py-2 rounded-lg text-sm hover:from-blue-500/30 hover:to-indigo-500/30 transition-all';
                    pageBtn.textContent = i;
                    buttonsContainer.appendChild(pageBtn);
                }

                if (pagination.current_page < pagination.last_page) {
                    const nextBtn = document.createElement('button');
                    nextBtn.onclick = () => loadReviewsPage(pagination.current_page + 1);
                    nextBtn.className =
                        'inline-flex items-center gap-2 bg-gradient-to-r from-blue-500/20 to-indigo-500/20 backdrop-blur-sm border border-blue-400/30 text-blue-300 px-4 py-2 rounded-lg text-sm hover:from-blue-500/30 hover:to-indigo-500/30 transition-all';
                    nextBtn.textContent = '{{ __('messages.next') }}';
                    buttonsContainer.appendChild(nextBtn);
                }
            }
        }

        function applyFilters() {
            loadReviewsPage(1);
        }

        function clearFilters() {
            const url = new URL(window.location);
            url.searchParams.delete('sort');
            url.searchParams.delete('rating');
            url.searchParams.delete('booking');
            window.location.href = url.toString();
        }

        document.addEventListener('DOMContentLoaded', function() {
            @if (isset($otherReviews))
                const paginationData = {
                    current_page: {{ $otherReviews->currentPage() ?? 1 }},
                    last_page: {{ $otherReviews->lastPage() ?? 1 }},
                    per_page: {{ $otherReviews->perPage() ?? 10 }},
                    total: {{ $otherReviews->total() ?? 0 }},
                    from: {{ $otherReviews->firstItem() ?? 0 }},
                    to: {{ $otherReviews->lastItem() ?? 0 }}
                };
                updatePaginationControls(paginationData);
            @endif
        });
    </script>

    <!-- Business Location Map Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if ($business->latitude && $business->longitude)
                if (window.geocodingMaps) {
                    window.geocodingMaps.setupDisplayMap(
                        'business-location-map',
                        {{ $business->latitude }},
                        {{ $business->longitude }},
                        @json($business->name)
                    );
                }
            @endif
        });
    </script>
</x-app-layout>
