<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                <!-- Role Card -->
                <div
                    class="frosted-card rounded-xl p-8 flex flex-col items-center justify-center shadow-lg hover:shadow-2xl transition-all duration-300 hover:scale-105 hover:transform hover:-translate-y-2 min-h-[200px]">
                    <x-heroicon-o-user class="w-12 h-12 text-frappe-blue mb-4" />
                    <div class="text-frappe-text text-xl mb-2">{{ __('messages.role') }}</div>
                    <span class="font-bold text-frappe-blue text-2xl">
                        {{ ucfirst(auth()->user()->role) }}
                    </span>
                </div>

                <!-- Your Bookings (all users) -->
                <div
                    class="frosted-card rounded-xl p-8 flex flex-col items-center justify-center shadow-lg hover:shadow-2xl transition-all duration-300 hover:scale-105 hover:transform hover:-translate-y-2 min-h-[200px]">
                    <x-heroicon-o-calendar class="w-12 h-12 text-frappe-peach mb-4" />
                    <div class="text-frappe-text text-xl mb-2">{{ __('messages.your_bookings') }}</div>
                    <a href="{{ route('bookings.index') }}"
                        class="mt-4 frosted-button text-white px-6 py-3 rounded-lg hover:transform hover:-translate-y-1 transition-all flex items-center gap-2 text-lg">
                        <x-heroicon-o-calendar-days class="w-6 h-6" />
                        {{ __('messages.view') }}
                    </a>
                </div>

                <!-- Browse Businesses (all users) -->
                <div
                    class="frosted-card rounded-xl p-8 flex flex-col items-center justify-center shadow-lg hover:shadow-2xl transition-all duration-300 hover:scale-105 hover:transform hover:-translate-y-2 min-h-[200px]">
                    <x-heroicon-o-building-storefront class="w-12 h-12 text-frappe-green mb-4" />
                    <div class="text-frappe-text text-xl mb-2">{{ __('messages.available_businesses') }}</div>
                    <a href="{{ route('businesses.public.index') }}"
                        class="mt-4 frosted-button text-white px-6 py-3 rounded-lg hover:transform hover:-translate-y-1 transition-all flex items-center gap-2 text-lg">
                        <x-heroicon-o-eye class="w-6 h-6" />
                        {{ __('messages.view') }}
                    </a>
                </div>

                <!-- Manage Businesses (admin/provider only) -->
                @if (auth()->user()->role === 'admin' || auth()->user()->role === 'provider')
                    <div
                        class="frosted-card rounded-xl p-8 flex flex-col items-center justify-center shadow-lg hover:shadow-2xl transition-all duration-300 hover:scale-105 hover:transform hover:-translate-y-2 min-h-[200px]">
                        <x-heroicon-o-briefcase class="w-12 h-12 text-frappe-lavender mb-4" />
                        <div class="text-frappe-text text-xl mb-2">{{ __('messages.manage_businesses') }}</div>
                        <a href="{{ route('businesses.index') }}"
                            class="mt-4 frosted-button text-white px-6 py-3 rounded-lg hover:transform hover:-translate-y-1 transition-all flex items-center gap-2 text-lg">
                            <x-heroicon-o-cog class="w-6 h-6" />
                            {{ __('messages.manage') }}
                        </a>
                    </div>

                    <!-- Manage Bookings (admin/provider only) -->
                    <div
                        class="frosted-card rounded-xl p-8 flex flex-col items-center justify-center shadow-lg hover:shadow-2xl transition-all duration-300 hover:scale-105 hover:transform hover:-translate-y-2 min-h-[200px]">
                        <x-heroicon-o-clipboard-document-list class="w-12 h-12 text-frappe-mauve mb-4" />
                        <div class="text-frappe-text text-xl mb-2">{{ __('messages.manage_bookings') }}</div>
                        <a href="{{ route('bookings.manage') }}"
                            class="mt-4 frosted-button text-white px-6 py-3 rounded-lg hover:transform hover:-translate-y-1 transition-all flex items-center gap-2 text-lg">
                            <x-heroicon-o-cog-6-tooth class="w-6 h-6" />
                            {{ __('messages.manage') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
