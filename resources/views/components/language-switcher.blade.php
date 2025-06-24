<div class="relative inline-block text-left" x-data="languageSwitcher()">
    <div>
        <button @click="toggle()" type="button"
            class="frosted-button inline-flex w-full justify-center gap-x-1.5 rounded-md px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-opacity-60"
            id="language-menu-button" aria-expanded="true" aria-haspopup="true">
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
        class="absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-md frosted-card shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
        role="menu" aria-orientation="vertical" aria-labelledby="language-menu-button" tabindex="-1"
        @click.outside="close()">
        <div class="py-1" role="none">
            <template x-for="lang in languages" :key="lang.code">
                <a href="#" @click.prevent="switchLanguage(lang.code)"
                    class="text-frappe-text block px-4 py-2 text-sm hover:bg-frappe-surface1 hover:text-frappe-lavender transition-colors"
                    role="menuitem" tabindex="-1" x-text="lang.name">
                </a>
            </template>
        </div>
    </div>
</div>

<script>
    function languageSwitcher() {
        return {
            open: false,
            currentLanguage: {
                code: 'en',
                name: '{{ __('messages.english') }}'
            },
            languages: [{
                    code: 'en',
                    name: '{{ __('messages.english') }}'
                },
                {
                    code: 'hu',
                    name: '{{ __('messages.hungarian') }}'
                }
            ],

            init() {
                // Get language from localStorage or use current app locale
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
                const lang = this.languages.find(l => l.code === code);
                if (lang) {
                    this.currentLanguage = lang;
                }
            },

            async switchLanguage(code) {
                try {
                    // Save to localStorage
                    localStorage.setItem('language', code);

                    // Make AJAX request to switch language on server
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
                        // Update current language display
                        this.setCurrentLanguage(code);
                        this.close();

                        // Reload page to apply new language
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
