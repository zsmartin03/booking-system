<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['text' => __('messages.businesses'), 'url' => route('businesses.index')],
            ['text' => __('messages.create_business'), 'url' => null]
        ]" />
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="frosted-card overflow-hidden shadow-lg sm:rounded-xl">
                <div class="p-6">
                    <form method="POST" action="{{ route('businesses.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <x-input-label for="name" :value="__('messages.business_name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                :value="old('name')" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="description" :value="__('messages.business_description')" />
                            <textarea id="description" name="description"
                                class="mt-1 block w-full bg-frappe-surface1 border-frappe-surface2 text-frappe-text" required>{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="address" :value="__('messages.address')" />
                            <x-text-input id="address" name="address" type="text" class="mt-1 block w-full"
                                :value="old('address')" required />
                            <x-input-error :messages="$errors->get('address')" class="mt-2" />
                            <p class="text-sm text-frappe-subtext1 mt-1">Type an address or click on the map to set
                                location</p>
                        </div>

                        <!-- Map Container -->
                        <div class="mb-4">
                            <x-input-label :value="__('Location Map')" />
                            <div id="business-form-map"
                                class="w-full h-96 bg-frappe-surface0/30 rounded-lg mt-2 border border-frappe-surface2/50">
                            </div>
                            <p class="text-sm text-frappe-subtext1 mt-1">Click on the map to set the exact location or
                                type an address above</p>
                        </div>

                        <!-- Hidden coordinate inputs -->
                        <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}">
                        <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}">

                        <div class="mb-4">
                            <x-input-label for="phone_number" :value="__('messages.phone')" />
                            <x-text-input id="phone_number" name="phone_number" type="text" class="mt-1 block w-full"
                                :value="old('phone_number')" required />
                            <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="email" :value="__('messages.email')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                                :value="old('email')" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="website" :value="__('messages.website')" />
                            <x-text-input id="website" name="website" type="url" class="mt-1 block w-full"
                                :value="old('website')" />
                            <x-input-error :messages="$errors->get('website')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="logo" :value="__('messages.business_logo')" />

                            <div class="flex items-start gap-6">
                                <!-- Logo Preview -->
                                <div class="flex flex-col items-center">
                                    <div class="relative group">
                                        <div id="logo-preview"
                                            class="w-24 h-24 rounded-lg overflow-hidden bg-gradient-to-br from-frappe-blue/20 to-frappe-sapphire/20 border-2 border-frappe-surface2/30 flex items-center justify-center transition-all duration-300 group-hover:border-frappe-blue/50">
                                            <div id="logo-placeholder"
                                                class="w-full h-full flex items-center justify-center">
                                                <x-heroicon-o-building-office-2 class="w-8 h-8 text-frappe-blue" />
                                            </div>
                                        </div>
                                        <!-- Loading indicator -->
                                        <div id="logo-loading"
                                            class="absolute inset-0 bg-frappe-surface0/80 backdrop-blur-sm rounded-lg flex items-center justify-center hidden">
                                            <div class="animate-spin rounded-full h-6 w-6 border-2 border-frappe-blue border-t-transparent"></div>
                                        </div>
                                    </div>
                                    <span class="text-xs text-frappe-subtext1 mt-2 text-center">{{ __('messages.business_logo') }}</span>
                                </div>

                                <!-- Upload Controls -->
                                <div class="flex-1">
                                    <!-- Custom File Input -->
                                    <div class="relative">
                                        <input id="logo" name="logo" type="file" class="hidden"
                                            accept="image/*" onchange="previewLogo(this)">
                                        <label for="logo"
                                            class="frosted-button inline-flex items-center gap-2 px-4 py-2 rounded-lg cursor-pointer transition-all">
                                            <x-heroicon-o-photo class="w-5 h-5" />
                                            {{ __('messages.choose_photo') }}
                                        </label>
                                    </div>

                                    <!-- File Info -->
                                    <div id="file-info" class="mt-2 hidden">
                                        <div class="bg-frappe-surface0/30 rounded-lg p-3 border border-frappe-surface2/30">
                                            <div class="flex items-center gap-2">
                                                <x-heroicon-o-document-arrow-up class="w-4 h-4 text-frappe-green" />
                                                <span id="file-name" class="text-sm text-frappe-text font-medium"></span>
                                                <span id="file-size" class="text-xs text-frappe-subtext1"></span>
                                            </div>
                                            <button type="button" onclick="clearFileInput()"
                                                class="text-xs text-frappe-red hover:text-frappe-red/80 mt-1">
                                                {{ __('messages.remove') }}
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Upload Guidelines -->
                                    <div class="mt-4 text-xs text-frappe-subtext1">
                                        <p>{{ __('messages.logo_guidelines') }}</p>
                                        <ul class="list-disc list-inside mt-1 space-y-1">
                                            <li>{{ __('messages.max_file_size_2mb') }}</li>
                                            <li>{{ __('messages.supported_formats') }}</li>
                                            <li>{{ __('messages.recommended_logo_size') }}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <x-input-error :messages="$errors->get('logo')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="categories" :value="__('messages.categories')" />

                            <!-- Category Search Input -->
                            <div class="relative mt-2">
                                <input type="text" id="categorySearch"
                                    placeholder="{{ __('messages.search_categories') }}"
                                    class="w-full px-3 py-2 bg-frappe-surface1 border border-frappe-surface2 rounded-lg text-frappe-text placeholder-frappe-subtext1 focus:outline-none focus:ring-2 focus:ring-frappe-blue focus:border-transparent">

                                <!-- Dropdown -->
                                <div id="categoryDropdown"
                                    class="absolute z-10 w-full mt-1 bg-frappe-mantle/90 backdrop-blur-sm border border-frappe-surface2/50 rounded-lg shadow-2xl hidden max-h-60 overflow-y-auto frosted-card">
                                    @foreach ($categories as $category)
                                        <div class="category-option px-4 py-3 hover:bg-frappe-surface0/30 cursor-pointer transition-all duration-200 border-b border-frappe-surface2/30 last:border-b-0"
                                            data-id="{{ $category->id }}" data-name="{{ $category->name }}"
                                            data-slug="{{ $category->slug }}" data-color="{{ $category->color }}"
                                            data-badge-classes="{{ $category->badge_classes }}"
                                            data-badge-styles="{{ $category->badge_styles }}"
                                            data-badge-hover-styles="{{ $category->badge_hover_styles }}">
                                            <div class="flex items-center justify-between">
                                                <div class="flex-1">
                                                    <div class="font-medium text-frappe-text">{{ $category->name }}
                                                    </div>
                                                    @if ($category->description)
                                                        <div class="text-xs text-frappe-subtext1 opacity-70 mt-1">
                                                            {{ $category->description }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="ml-3">
                                                    <span class="{{ $category->badge_classes }}"
                                                        style="{{ $category->badge_styles }}">{{ $category->name }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Selected Categories Display -->
                            <div class="mt-3">
                                <div class="text-sm text-frappe-subtext1 mb-2">
                                    {{ __('messages.selected_categories') }}:
                                </div>
                                <div id="selectedCategories"
                                    class="flex flex-wrap gap-2 min-h-[2rem] p-3 bg-frappe-surface0/30 backdrop-blur-sm border border-frappe-surface2/50 rounded-lg">
                                    <div id="noCategoriesText" class="text-sm text-frappe-subtext1 opacity-60">
                                        {{ __('messages.no_categories_selected') }}</div>
                                </div>
                            </div>

                            <!-- Hidden inputs for form submission -->
                            <div id="hiddenInputs"></div>

                            <x-input-error :messages="$errors->get('categories')" class="mt-2" />
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4 sm:gap-6">
                            <button type="submit"
                                class="frosted-button text-white px-6 py-3 rounded-lg font-medium transition-all w-full sm:w-auto">
                                {{ __('messages.create') }}
                            </button>
                            <a href="{{ route('businesses.index') }}"
                                class="frosted-button-cancel px-6 py-3 rounded-lg font-medium transition-all w-full sm:w-auto inline-flex items-center justify-center gap-2">
                                {{ __('messages.cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categorySearch = document.getElementById('categorySearch');
            const categoryDropdown = document.getElementById('categoryDropdown');
            const selectedCategories = document.getElementById('selectedCategories');
            const hiddenInputs = document.getElementById('hiddenInputs');
            const noCategoriesText = document.getElementById('noCategoriesText');
            const categoryOptions = document.querySelectorAll('.category-option');

            let selectedCategoryIds = @json(old('categories', []));

            if (selectedCategoryIds.length > 0) {
                selectedCategoryIds.forEach(categoryId => {
                    const option = document.querySelector(`[data-id="${categoryId}"]`);
                    if (option) {
                        addCategory(categoryId, option.dataset.name, option.dataset.color);
                    }
                });
            }

            categorySearch.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                let hasVisibleOptions = false;

                categoryOptions.forEach(option => {
                    const name = option.dataset.name.toLowerCase();
                    const description = option.querySelector('.text-xs')?.textContent
                        .toLowerCase() || '';
                    const isVisible = name.includes(searchTerm) || description.includes(searchTerm);
                    option.style.display = isVisible ? 'block' : 'none';
                    if (isVisible) hasVisibleOptions = true;
                });

                categoryDropdown.classList.toggle('hidden', !hasVisibleOptions);
            });

            categorySearch.addEventListener('focus', function() {
                categoryOptions.forEach(option => {
                    option.style.display = 'block';
                });
                categoryDropdown.classList.remove('hidden');
            });

            document.addEventListener('click', function(e) {
                if (!e.target.closest('#categorySearch') && !e.target.closest('#categoryDropdown')) {
                    categoryDropdown.classList.add('hidden');
                }
            });

            categoryOptions.forEach(option => {
                option.addEventListener('click', function() {
                    const categoryId = this.dataset.id;
                    const categoryName = this.dataset.name;
                    const categoryColor = this.dataset.color || '#8B5CF6';

                    if (!selectedCategoryIds.includes(categoryId)) {
                        addCategory(categoryId, categoryName, categoryColor);
                        categorySearch.value = '';
                        categoryDropdown.classList.add('hidden');
                    }
                });
            });

            function addCategory(categoryId, categoryName, categoryColor = '#8B5CF6') {
                selectedCategoryIds.push(categoryId);

                const categoryOption = document.querySelector(`[data-id="${categoryId}"]`);
                const badgeClasses = categoryOption ? categoryOption.dataset.badgeClasses ||
                    'inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-medium transition-all cursor-pointer border' :
                    'inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-medium transition-all cursor-pointer border';
                const badgeStyles = categoryOption ? categoryOption.dataset.badgeStyles || '' : '';
                const badgeHoverStyles = categoryOption ? categoryOption.dataset.badgeHoverStyles || '' : '';

                const badge = document.createElement('div');
                badge.className = badgeClasses;
                badge.style.cssText = badgeStyles;
                badge.innerHTML = `
                    <span>${categoryName}</span>
                    <button type="button" class="hover:opacity-70 transition-colors" onclick="removeCategory('${categoryId}')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                `;
                badge.dataset.categoryId = categoryId;

                if (badgeHoverStyles) {
                    badge.addEventListener('mouseenter', function() {
                        this.style.cssText = badgeHoverStyles + '; color: ' + categoryColor +
                            '; border-color: ' + categoryColor + '50;';
                    });
                    badge.addEventListener('mouseleave', function() {
                        this.style.cssText = badgeStyles;
                    });
                }

                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'categories[]';
                hiddenInput.value = categoryId;
                hiddenInput.dataset.categoryId = categoryId;

                selectedCategories.appendChild(badge);
                hiddenInputs.appendChild(hiddenInput);

                updateNoCategoriesText();
            }

            window.removeCategory = function(categoryId) {
                selectedCategoryIds = selectedCategoryIds.filter(id => id !== categoryId);

                const badge = document.querySelector(`[data-category-id="${categoryId}"]`);
                if (badge) badge.remove();

                const hiddenInput = document.querySelector(`input[data-category-id="${categoryId}"]`);
                if (hiddenInput) hiddenInput.remove();

                updateNoCategoriesText();
            };

            function updateNoCategoriesText() {
                noCategoriesText.style.display = selectedCategoryIds.length === 0 ? 'block' : 'none';
            }
        });

        // Logo preview function
        function previewLogo(input) {
            const preview = document.getElementById('logo-preview');
            const placeholder = document.getElementById('logo-placeholder');
            const fileInfo = document.getElementById('file-info');
            const fileName = document.getElementById('file-name');
            const fileSize = document.getElementById('file-size');

            if (input.files && input.files[0]) {
                const file = input.files[0];
                const reader = new FileReader();

                reader.onload = function(e) {
                    // Hide placeholder
                    if (placeholder) placeholder.classList.add('hidden');

                    // Create or update preview image
                    let previewImg = document.getElementById('new-logo-preview');
                    if (!previewImg) {
                        previewImg = document.createElement('img');
                        previewImg.id = 'new-logo-preview';
                        previewImg.className = 'w-full h-full object-contain';
                        previewImg.alt = 'Logo Preview';
                        preview.appendChild(previewImg);
                    }

                    previewImg.src = e.target.result;
                    previewImg.classList.remove('hidden');

                    // Show file info
                    fileName.textContent = file.name;
                    fileSize.textContent = formatFileSize(file.size);
                    fileInfo.classList.remove('hidden');
                }

                reader.readAsDataURL(file);
            } else {
                clearFileInput();
            }
        }

        function clearFileInput() {
            const input = document.getElementById('logo');
            const preview = document.getElementById('logo-preview');
            const placeholder = document.getElementById('logo-placeholder');
            const previewImg = document.getElementById('new-logo-preview');
            const fileInfo = document.getElementById('file-info');

            input.value = '';

            // Hide preview and file info
            if (previewImg) previewImg.classList.add('hidden');
            fileInfo.classList.add('hidden');

            // Show placeholder
            if (placeholder) placeholder.classList.remove('hidden');
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
    </script>
</x-app-layout>
