<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['text' => __('messages.categories'), 'url' => route('categories.index')],
            ['text' => __('messages.edit_category') . ' - ' . $category->name, 'url' => null],
        ]" />
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="frosted-card overflow-hidden shadow-lg sm:rounded-xl">
                <div class="p-6">
                    <form method="POST" action="{{ route('categories.update', $category->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <x-input-label for="slug" :value="__('messages.slug')" />
                            <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full"
                                :value="old('slug', $category->slug)" required autofocus />
                            <p class="mt-1 text-sm text-frappe-subtext1">{{ __('messages.url_friendly_slug_manual') }}
                            </p>
                            <x-input-error :messages="$errors->get('slug')" class="mt-2" />
                        </div>

                        <!-- English Translations -->
                        <div class="mb-6">
                            <h4 class="text-lg font-medium text-frappe-text mb-3 flex items-center gap-2">
                                <x-flag-language-en class="w-5 h-5" />
                                {{ __('messages.english_translation') }}
                            </h4>

                            <div class="mb-4">
                                <x-input-label for="name_en" :value="__('messages.name') . ' (English)'" />
                                <x-text-input id="name_en" name="name_en" type="text" class="mt-1 block w-full"
                                    :value="old('name_en', $translations['en']['name'] ?? '')" required />
                                <x-input-error :messages="$errors->get('name_en')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="description_en" :value="__('messages.description') . ' (English)'" />
                                <textarea id="description_en" name="description_en" rows="3"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required>{{ old('description_en', $translations['en']['description'] ?? '') }}</textarea>
                                <x-input-error :messages="$errors->get('description_en')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Hungarian Translations -->
                        <div class="mb-6">
                            <h4 class="text-lg font-medium text-frappe-text mb-3 flex items-center gap-2">
                                <x-flag-language-hu class="w-5 h-5" />
                                {{ __('messages.hungarian_translation') }}
                            </h4>

                            <div class="mb-4">
                                <x-input-label for="name_hu" :value="__('messages.name') . ' (Magyar)'" />
                                <x-text-input id="name_hu" name="name_hu" type="text" class="mt-1 block w-full"
                                    :value="old('name_hu', $translations['hu']['name'] ?? '')" required />
                                <x-input-error :messages="$errors->get('name_hu')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="description_hu" :value="__('messages.description') . ' (Magyar)'" />
                                <textarea id="description_hu" name="description_hu" rows="3"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required>{{ old('description_hu', $translations['hu']['description'] ?? '') }}</textarea>
                                <x-input-error :messages="$errors->get('description_hu')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mb-4">
                            <x-input-label for="color" :value="__('messages.color')" />
                            <div class="mt-1 flex items-center gap-3">
                                <input id="color" name="color" type="color"
                                    class="h-10 w-20 border border-gray-300 rounded cursor-pointer"
                                    value="{{ old('color', $category->color) }}" required />
                                <input type="text"
                                    class="flex-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    value="{{ old('color', $category->color) }}" readonly id="color-text">
                            </div>
                            <p class="mt-1 text-sm text-frappe-subtext1">{{ __('messages.choose_color_for_badge') }}</p>
                            <x-input-error :messages="$errors->get('color')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="slug" :value="__('messages.slug')" />
                            <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full"
                                :value="$category->slug" readonly />
                            <p class="mt-1 text-sm text-gray-600">
                                {{ __('messages.url_friendly_slug_auto_generated') }}</p>
                        </div>

                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-frappe-text mb-3">{{ __('messages.preview') }}</h4>
                            <div class="p-4 rounded-lg">
                                <span id="category-preview"
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium transition-all cursor-pointer border">
                                    <span
                                        id="preview-text">{{ old('name_en', $translations['en']['name'] ?? ($category->name ?? __('messages.category_name'))) }}</span>
                                </span>
                            </div>
                        </div>

                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-frappe-text mb-3">{{ __('messages.statistics') }}</h4>
                            <div class="p-4 bg-frappe-surface0/30 rounded-lg">
                                <p class="text-sm text-frappe-subtext1">
                                    {{ __('messages.category_used_by_businesses', ['count' => $category->businesses()->count()]) }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('categories.index') }}"
                                class="inline-flex items-center gap-2 bg-gradient-to-r from-gray-500/20 to-gray-600/20 backdrop-blur-sm border border-gray-400/30 text-gray-300 px-6 py-2 rounded-lg hover:from-gray-500/30 hover:to-gray-600/30 transition-all">
                                {{ __('messages.cancel') }}
                            </a>

                            <x-primary-button>
                                {{ __('messages.update') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function hexToRgb(hex) {
            const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
            return result ? {
                r: parseInt(result[1], 16),
                g: parseInt(result[2], 16),
                b: parseInt(result[3], 16)
            } : null;
        }

        function rgbToHsl(r, g, b) {
            r /= 255;
            g /= 255;
            b /= 255;
            const max = Math.max(r, g, b);
            const min = Math.min(r, g, b);
            let h, s, l = (max + min) / 2;

            if (max === min) {
                h = s = 0;
            } else {
                const d = max - min;
                s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
                switch (max) {
                    case r:
                        h = (g - b) / d + (g < b ? 6 : 0);
                        break;
                    case g:
                        h = (b - r) / d + 2;
                        break;
                    case b:
                        h = (r - g) / d + 4;
                        break;
                }
                h /= 6;
            }

            return {
                h: Math.round(h * 360),
                s: Math.round(s * 100),
                l: Math.round(l * 100)
            };
        }

        function hslToRgb(h, s, l) {
            h /= 360;
            s /= 100;
            l /= 100;
            let r, g, b;

            if (s === 0) {
                r = g = b = l;
            } else {
                const hue2rgb = (p, q, t) => {
                    if (t < 0) t += 1;
                    if (t > 1) t -= 1;
                    if (t < 1 / 6) return p + (q - p) * 6 * t;
                    if (t < 1 / 2) return q;
                    if (t < 2 / 3) return p + (q - p) * (2 / 3 - t) * 6;
                    return p;
                };

                const q = l < 0.5 ? l * (1 + s) : l + s - l * s;
                const p = 2 * l - q;
                r = hue2rgb(p, q, h + 1 / 3);
                g = hue2rgb(p, q, h);
                b = hue2rgb(p, q, h - 1 / 3);
            }

            return {
                r: Math.round(r * 255),
                g: Math.round(g * 255),
                b: Math.round(b * 255)
            };
        }

        function generateGradientStyles(color) {
            const rgb = hexToRgb(color);
            if (!rgb) return;

            const hsl = rgbToHsl(rgb.r, rgb.g, rgb.b);
            const complementaryHsl = {
                h: (hsl.h + 30) % 360,
                s: Math.max(20, hsl.s - 10),
                l: Math.min(70, hsl.l + 10)
            };
            const complementaryRgb = hslToRgb(complementaryHsl.h, complementaryHsl.s, complementaryHsl.l);

            return {
                background: `linear-gradient(135deg, rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 0.2), rgba(${complementaryRgb.r}, ${complementaryRgb.g}, ${complementaryRgb.b}, 0.1))`,
                color: color,
                borderColor: `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 0.3)`
            };
        }

        function updatePreview(color) {
            document.getElementById('color-text').value = color;
            const preview = document.getElementById('category-preview');
            const styles = generateGradientStyles(color);

            if (styles) {
                preview.style.background = styles.background;
                preview.style.color = styles.color;
                preview.style.borderColor = styles.borderColor;
            }
        }

        updatePreview('{{ old('color', $category->color) }}');

        document.getElementById('color').addEventListener('input', function() {
            updatePreview(this.value);
        });

        document.getElementById('name_en').addEventListener('input', function() {
            const name = this.value || '{{ __('messages.category_name') }}';
            document.getElementById('preview-text').textContent = name;
        });

        document.getElementById('color-text').addEventListener('input', function() {
            const color = this.value;
            if (/^#[0-9A-F]{6}$/i.test(color)) {
                document.getElementById('color').value = color;
                updatePreview(color);
            }
        });
    </script>
</x-app-layout>
