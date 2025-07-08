<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-frappe-lavender leading-tight">
            {{ $category->name }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="frosted-card overflow-hidden shadow-lg sm:rounded-xl">
                <div class="p-4 sm:p-6">
                    <!-- Category Info -->
                    <div class="mb-6">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-8 h-8 rounded-full border border-frappe-surface2/50"
                                style="background-color: {{ $category->color }}"></div>
                            <h1 class="text-3xl font-bold text-frappe-text">{{ $category->name }}</h1>
                        </div>

                        @if ($category->description)
                            <p class="text-frappe-subtext1 text-base mb-4">{{ $category->description }}</p>
                        @endif

                        <!-- Badge Preview -->
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-frappe-text mb-3">{{ __('messages.preview') }}</h4>
                            <div class="p-4 rounded-lg bg-frappe-surface0/20">
                                <span id="category-preview"
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium transition-all cursor-pointer border">
                                    <span>{{ $category->name }}</span>
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-frappe-surface0/30 rounded-lg p-3">
                                <div class="text-sm text-frappe-subtext1 mb-1">{{ __('messages.slug') }}</div>
                                <div class="text-frappe-text font-mono">{{ $category->slug }}</div>
                            </div>

                            <div class="bg-frappe-surface0/30 rounded-lg p-3">
                                <div class="text-sm text-frappe-subtext1 mb-1">{{ __('messages.color') }}</div>
                                <div class="flex items-center gap-2">
                                    <div class="w-4 h-4 rounded border"
                                        style="background-color: {{ $category->color }}"></div>
                                    <span class="text-frappe-text font-mono">{{ $category->color }}</span>
                                </div>
                            </div>

                            <div class="bg-frappe-surface0/30 rounded-lg p-3">
                                <div class="text-sm text-frappe-subtext1 mb-1">{{ __('messages.businesses_count') }}
                                </div>
                                <div class="text-frappe-text">{{ $category->businesses->count() }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Action buttons -->
                    <div class="flex flex-wrap gap-3 mb-6">
                        <a href="{{ route('categories.edit', $category->id) }}"
                            class="inline-flex items-center gap-2 bg-gradient-to-r from-yellow-500/20 to-orange-500/20 backdrop-blur-sm border border-yellow-400/30 text-yellow-300 px-4 py-2 rounded-lg text-sm hover:from-yellow-500/30 hover:to-orange-500/30 transition-all">
                            <x-heroicon-o-pencil class="w-4 h-4" />
                            {{ __('messages.edit') }}
                        </a>

                        <a href="{{ route('businesses.public.index', ['category' => $category->slug]) }}"
                            class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-500/20 to-indigo-500/20 backdrop-blur-sm border border-blue-400/30 text-blue-300 px-4 py-2 rounded-lg text-sm hover:from-blue-500/30 hover:to-indigo-500/30 transition-all">
                            <x-heroicon-o-eye class="w-4 h-4" />
                            {{ __('messages.view_public_listing') }}
                        </a>

                        <a href="{{ route('categories.index') }}"
                            class="inline-flex items-center gap-2 bg-gradient-to-r from-gray-500/20 to-gray-600/20 backdrop-blur-sm border border-gray-400/30 text-gray-300 px-4 py-2 rounded-lg text-sm hover:from-gray-500/30 hover:to-gray-600/30 transition-all">
                            <x-heroicon-o-arrow-left class="w-4 h-4" />
                            {{ __('messages.back') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Businesses in this category -->
            @if ($category->businesses->count() > 0)
                <div class="frosted-card overflow-hidden shadow-lg sm:rounded-xl mt-6">
                    <div class="p-4 sm:p-6">
                        <h3 class="text-xl font-semibold text-frappe-text mb-4">
                            {{ __('messages.businesses_in_this_category') }} ({{ $category->businesses->count() }})
                        </h3>
                        <div class="divide-y divide-frappe-surface2/30">
                            @foreach ($category->businesses as $business)
                                <div class="py-4 first:pt-0 last:pb-0">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-frappe-text mb-1">
                                                <a href="{{ route('businesses.show', $business->id) }}"
                                                    class="text-frappe-blue hover:text-frappe-sapphire transition-colors">
                                                    {{ $business->name }}
                                                </a>
                                            </h4>
                                            <p class="text-frappe-subtext1 text-sm">{{ $business->address }}</p>
                                            @if ($business->description)
                                                <p class="text-frappe-subtext0 text-sm mt-1 line-clamp-2">
                                                    {{ $business->description }}</p>
                                            @endif
                                        </div>
                                        <div class="flex gap-2">
                                            <a href="{{ route('businesses.show', $business->id) }}"
                                                class="inline-flex items-center gap-1 bg-gradient-to-r from-blue-500/20 to-indigo-500/20 backdrop-blur-sm border border-blue-400/30 text-blue-300 px-3 py-1 rounded text-sm hover:from-blue-500/30 hover:to-indigo-500/30 transition-all">
                                                <x-heroicon-o-eye class="w-3 h-3" />
                                                {{ __('messages.view') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <div class="frosted-card overflow-hidden shadow-lg sm:rounded-xl mt-6">
                    <div class="p-6 text-center">
                        <p class="text-frappe-subtext1 opacity-80">{{ __('messages.no_businesses_in_category_yet') }}
                        </p>
                    </div>
                </div>
            @endif
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

        // Initialize the preview with the category color
        document.addEventListener('DOMContentLoaded', function() {
            const categoryColor = '{{ $category->color }}';
            const preview = document.getElementById('category-preview');
            const styles = generateGradientStyles(categoryColor);

            if (styles) {
                preview.style.background = styles.background;
                preview.style.color = styles.color;
                preview.style.borderColor = styles.borderColor;
            }
        });
    </script>
</x-app-layout>
