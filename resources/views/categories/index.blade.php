<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[['text' => __('messages.categories'), 'url' => null]]" />
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <a href="{{ route('categories.create') }}"
                    class="frosted-button text-white px-6 py-3 rounded-lg font-medium inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    {{ __('messages.create_category') }}
                </a>
            </div>

            <!-- Search Form -->
            <div class="frosted-card overflow-hidden shadow-lg sm:rounded-xl border border-frappe-surface2 mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('categories.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Search by name or description -->
                            <div>
                                <label for="search" class="block text-sm font-medium text-frappe-text mb-2">
                                    {{ __('messages.search_categories') }}
                                </label>
                                <input type="text" id="search" name="search" value="{{ request('search') }}"
                                    placeholder="{{ __('messages.search_by_slug') }}"
                                    class="w-full px-3 py-2 bg-frappe-mantle border border-frappe-surface2 rounded-lg text-frappe-text placeholder-frappe-subtext1 focus:outline-none focus:ring-2 focus:ring-frappe-blue focus:border-transparent">
                            </div>
                        </div>

                        <!-- Action buttons -->
                        <div class="flex flex-wrap gap-3">
                            <button type="submit"
                                class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-500/20 to-indigo-500/20 backdrop-blur-sm border border-blue-400/30 text-blue-300 px-4 py-2 rounded-lg text-sm hover:from-blue-500/30 hover:to-indigo-500/30 transition-all">
                                <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                                {{ __('messages.search') }}
                            </button>

                            @if (request('search'))
                                <a href="{{ route('categories.index') }}"
                                    class="inline-flex items-center gap-2 bg-gradient-to-r from-gray-500/20 to-gray-600/20 backdrop-blur-sm border border-gray-400/30 text-gray-300 px-4 py-2 rounded-lg text-sm hover:from-gray-500/30 hover:to-gray-600/30 transition-all">
                                    <x-heroicon-o-x-mark class="w-4 h-4" />
                                    {{ __('messages.clear_filters') }}
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            @if ($categories->count() > 0)
                <div class="frosted-card rounded-xl shadow-lg overflow-hidden">
                    <div class="divide-y divide-frappe-surface2/30">
                        @foreach ($categories as $category)
                            <div class="p-4 sm:p-6 hover:bg-frappe-surface0/20 transition-all duration-200">
                                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-start gap-4">
                                            <!-- Category Color -->
                                            <div class="flex-shrink-0">
                                                <div class="w-6 h-6 rounded-full border border-frappe-surface2/50"
                                                    style="background-color: {{ $category->color }}"></div>
                                            </div>

                                            <div class="flex-1">
                                                <div class="flex items-center gap-3 mb-2">
                                                    <h3 class="text-xl font-semibold text-frappe-text">
                                                        {{ $category->name }}</h3>
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-blue-500/20 to-indigo-500/20 text-blue-300 border border-blue-400/30">
                                                        {{ $category->businesses_count }}
                                                        {{ __('messages.businesses') }}
                                                    </span>
                                                </div>

                                                @if ($category->translated_description)
                                                    <p class="text-frappe-subtext1 text-sm mb-2">
                                                        {{ $category->translated_description }}</p>
                                                @endif

                                                <div class="flex items-center gap-4 text-sm text-frappe-subtext1">
                                                    <span>{{ __('messages.slug') }}: {{ $category->slug }}</span>
                                                    @if ($category->icon)
                                                        <span>{{ __('Icon') }}: {{ $category->icon }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action buttons -->
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('categories.show', $category->id) }}"
                                            class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-500/20 to-indigo-500/20 backdrop-blur-sm border border-blue-400/30 text-blue-300 px-3 py-2 rounded-lg text-sm hover:from-blue-500/30 hover:to-indigo-500/30 transition-all">
                                            <x-heroicon-o-eye class="w-4 h-4" />
                                            {{ __('messages.view') }}
                                        </a>

                                        <a href="{{ route('categories.edit', $category->id) }}"
                                            class="inline-flex items-center gap-2 bg-gradient-to-r from-yellow-500/20 to-orange-500/20 backdrop-blur-sm border border-yellow-400/30 text-yellow-300 px-3 py-2 rounded-lg text-sm hover:from-yellow-500/30 hover:to-orange-500/30 transition-all">
                                            <x-heroicon-o-pencil class="w-4 h-4" />
                                            {{ __('messages.edit') }}
                                        </a>

                                        @if ($category->businesses_count == 0)
                                            <button
                                                class="inline-flex items-center gap-2 bg-gradient-to-r from-red-500/20 to-red-600/20 backdrop-blur-sm border border-red-400/30 text-red-300 px-3 py-2 rounded-lg text-sm hover:from-red-500/30 hover:to-red-600/30 transition-all"
                                                onclick="showDeleteModal({{ $category->id }}, '{{ addslashes($category->name) }}')"
                                                title="{{ __('messages.delete') }}">
                                                <x-heroicon-o-trash class="w-4 h-4" />
                                                {{ __('messages.delete') }}
                                            </button>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-2 bg-gradient-to-r from-gray-500/20 to-gray-600/20 backdrop-blur-sm border border-gray-400/30 text-gray-400 px-3 py-2 rounded-lg text-sm cursor-not-allowed">
                                                <x-heroicon-o-trash class="w-4 h-4" />
                                                {{ __('messages.delete') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="frosted-card overflow-hidden shadow-lg sm:rounded-xl">
                    <div class="p-6 text-center">
                        <p class="text-frappe-subtext1 opacity-80">{{ __('messages.no_categories_found') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteModal"
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm z-50 hidden">
        <div class="frosted-modal p-6 rounded-2xl shadow-2xl w-full max-w-md mx-4">
            <h3 class="text-xl font-semibold mb-4 text-frappe-red">{{ __('messages.delete_category') }}</h3>
            <p class="mb-6 text-frappe-text opacity-90">{{ __('messages.are_you_sure_delete') }} <span
                    id="modalCategoryName" class="font-bold text-frappe-lavender"></span>?</p>
            <p class="mb-6 text-frappe-subtext1 text-sm">{{ __('messages.this_action_cannot_be_undone') }}</p>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="hideDeleteModal()"
                        class="px-6 py-2 bg-gradient-to-r from-gray-500/20 to-gray-600/20 backdrop-blur-sm border border-gray-400/30 text-gray-300 rounded-lg hover:from-gray-500/30 hover:to-gray-600/30 transition-all">
                        {{ __('messages.cancel') }}
                    </button>
                    <button type="submit"
                        class="px-6 py-2 bg-gradient-to-r from-red-500/30 to-pink-500/30 backdrop-blur-sm border border-red-400/40 text-red-300 rounded-lg hover:from-red-500/40 hover:to-pink-500/40 transition-all">
                        {{ __('messages.delete') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showDeleteModal(categoryId, categoryName) {
            document.getElementById('modalCategoryName').textContent = categoryName;
            document.getElementById('deleteForm').action = "{{ url('/categories') }}/" + categoryId;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function hideDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
