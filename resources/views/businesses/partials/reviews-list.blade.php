@if (isset($otherReviews) && $otherReviews->count() > 0)
    <div class="space-y-3">
        <!-- Other Reviews -->
        @foreach ($otherReviews as $review)
            <div class="bg-frappe-surface0/30 rounded-lg p-4 border border-frappe-surface2/30"
                data-review-id="{{ $review->id }}">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-frappe-blue/20 rounded-full flex items-center justify-center overflow-hidden">
                            @if ($review->user->avatar)
                                <img src="{{ $review->user->getAvatarUrl() }}" alt="{{ $review->user->name }}"
                                    class="w-full h-full object-cover rounded-full">
                            @else
                                <span
                                    class="text-frappe-blue font-semibold">{{ substr($review->user->name, 0, 1) }}</span>
                            @endif
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-frappe-text">{{ $review->user->name }}</span>
                                @if ($review->has_booking)
                                    <span
                                        class="inline-flex items-center gap-1 bg-green-500/20 text-green-400 px-2 py-1 rounded-full text-xs">
                                        <x-heroicon-s-check-badge class="w-4 h-4" />
                                        {{ __('messages.verified_booking') }}
                                    </span>
                                @endif
                            </div>
                            <div class="flex items-center gap-1 mt-1">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $review->rating)
                                        <x-heroicon-s-star class="w-4 h-4 text-yellow-400" />
                                    @else
                                        <x-heroicon-o-star class="w-4 h-4 text-gray-300" />
                                    @endif
                                @endfor
                                <span
                                    class="text-frappe-subtext1 text-sm ml-2">{{ $review->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <p class="text-frappe-text mb-3">{{ $review->comment }}</p>

                <!-- Vote buttons -->
                @auth
                    @if (auth()->id() !== $review->user_id)
                        <div class="flex items-center gap-4 mb-3">
                            <button onclick="voteReview({{ $review->id }}, true)"
                                class="vote-button flex items-center gap-1 px-3 py-1 rounded-full text-sm transition-colors
                                       {{ $review->getUserVoteType(auth()->id()) === true ? 'bg-green-500/20 text-green-400' : 'bg-frappe-surface0/50 text-frappe-subtext1 hover:bg-green-500/10' }}">
                                <x-heroicon-o-hand-thumb-up class="w-4 h-4" />
                                <span class="upvote-count">{{ $review->upvotes()->count() }}</span>
                            </button>
                            <button onclick="voteReview({{ $review->id }}, false)"
                                class="vote-button flex items-center gap-1 px-3 py-1 rounded-full text-sm transition-colors
                                       {{ $review->getUserVoteType(auth()->id()) === false ? 'bg-red-500/20 text-red-400' : 'bg-frappe-surface0/50 text-frappe-subtext1 hover:bg-red-500/10' }}">
                                <x-heroicon-o-hand-thumb-down class="w-4 h-4" />
                                <span class="downvote-count">{{ $review->downvotes()->count() }}</span>
                            </button>
                        </div>
                    @endif
                @endauth

                <!-- Business Response -->
                @if ($review->response)
                    <div
                        class="response-content-{{ $review->response->id }} bg-frappe-surface1/30 rounded-lg p-3 border-l-4 border-frappe-blue">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <x-heroicon-o-building-storefront class="w-4 h-4 text-frappe-blue" />
                                <span class="text-sm font-medium text-frappe-blue">{{ __('messages.response_from') }}
                                    {{ $business->name }}</span>
                                <span
                                    class="text-frappe-subtext1 text-xs">{{ $review->response->created_at->diffForHumans() }}</span>
                            </div>
                            @if (auth()->check() && auth()->id() === $business->user_id)
                                <div class="flex items-center gap-2">
                                    <button onclick="editResponse({{ $review->response->id }})"
                                        data-response="{{ htmlspecialchars($review->response->response, ENT_QUOTES, 'UTF-8') }}"
                                        class="edit-button text-white px-3 py-2 rounded-lg inline-flex items-center gap-2 text-sm transition-all"
                                        title="{{ __('messages.edit_response') }}">
                                        <x-heroicon-o-pencil class="w-4 h-4" />
                                        <span class="hidden sm:inline">{{ __('messages.edit') }}</span>
                                    </button>
                                    <button
                                        onclick="showDeleteResponseModal({{ $review->response->id }}, '{{ addslashes($business->name) }}')"
                                        class="delete-button text-white px-3 py-2 rounded-lg inline-flex items-center gap-2 text-sm transition-all"
                                        title="{{ __('messages.delete_response') }}">
                                        <x-heroicon-o-trash class="w-4 h-4" />
                                        <span class="hidden sm:inline">{{ __('messages.delete') }}</span>
                                    </button>
                                </div>
                            @endif
                        </div>
                        <p class="text-frappe-text text-sm">{{ $review->response->response }}</p>
                    </div>
                @elseif (auth()->check() && auth()->id() === $business->user_id)
                    <!-- Response Form for Business Owner -->
                    <div class="bg-frappe-surface1/30 rounded-lg p-3 border-l-4 border-frappe-blue">
                        <form onsubmit="submitResponse(event, {{ $review->id }})">
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
        @endforeach
    </div>
@else
    @if (!isset($userReview) || !$userReview)
        <div class="text-center py-8">
            <x-heroicon-o-chat-bubble-left-ellipsis class="w-12 h-12 text-frappe-subtext1 mx-auto mb-3" />
            <p class="text-frappe-subtext1">{{ __('messages.no_reviews') }}.
                {{ __('messages.first_review') }}</p>
        </div>
    @endif
@endif
