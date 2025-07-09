<div class="relative inline-block text-left language-switcher" x-data="languageSwitcher()">
    <div>
        <button @click="toggle()" type="button"
            class="frosted-button inline-flex w-full justify-center items-center gap-x-2 rounded-md px-3 py-2 text-sm font-semibold shadow-sm hover:bg-opacity-60"
            id="language-menu-button" aria-expanded="true" aria-haspopup="true">
            <span x-show="currentLanguage.code === 'en'">
                <x-flag-language-en class="w-4 h-4" />
            </span>
            <span x-show="currentLanguage.code === 'hu'">
                <x-flag-language-hu class="w-4 h-4" />
            </span>
            <span x-text="currentLanguage.name"></span>
            <svg class="-mr-1 h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd"
                    d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                    clip-rule="evenodd" />
            </svg>
        </button>
    </div>

    <div x-show="open" x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-md frosted-card dropdown-menu shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
        role="menu" aria-orientation="vertical" aria-labelledby="language-menu-button" tabindex="-1"
        @click.outside="close()">
        <div class="py-1" role="none">
            <a href="#" @click.prevent="switchLanguage('en')"
                class="text-frappe-text flex items-center gap-3 px-4 py-2 text-sm hover:bg-frappe-surface1 hover:text-frappe-lavender transition-colors"
                role="menuitem" tabindex="-1">
                <x-flag-language-en class="w-4 h-4" />
                <span>{{ __('messages.english') }}</span>
            </a>
            <a href="#" @click.prevent="switchLanguage('hu')"
                class="text-frappe-text flex items-center gap-3 px-4 py-2 text-sm hover:bg-frappe-surface1 hover:text-frappe-lavender transition-colors"
                role="menuitem" tabindex="-1">
                <x-flag-language-hu class="w-4 h-4" />
                <span>{{ __('messages.hungarian') }}</span>
            </a>
        </div>
    </div>
</div>

<script>
    function languageSwitcher() {
        return {
            open: false,
            currentLanguage: {
                code: '{{ app()->getLocale() }}',
                name: '{{ app()->getLocale() === "en" ? __("messages.english") : __("messages.hungarian") }}'
            },

            init() {
                const savedLocale = localStorage.getItem('language') || '{{ app()->getLocale() }}';
                this.setCurrentLanguage(savedLocale);
            },

            toggle() {
                this.open = !this.open;
            },

            close() {
                this.open = false;
            },

            setCurrentLanguage(code) {
                if (code === 'en') {
                    this.currentLanguage = {
                        code: 'en',
                        name: '{{ __('messages.english') }}'
                    };
                } else if (code === 'hu') {
                    this.currentLanguage = {
                        code: 'hu',
                        name: '{{ __('messages.hungarian') }}'
                    };
                }
            },

            async switchLanguage(code) {
                try {
                    localStorage.setItem('language', code);

                    const response = await fetch('/locale', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            locale: code
                        })
                    });

                    if (response.ok) {
                        this.setCurrentLanguage(code);
                        this.close();

                        window.location.reload();
                    } else {
                        console.error('Failed to switch language');
                    }
                } catch (error) {
                    console.error('Error switching language:', error);
                }
            }
        };
    }
</script>
