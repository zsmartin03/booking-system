<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                <!-- Role Card -->
                <div
                    class="frosted-card rounded-xl p-8 flex flex-col items-center justify-center shadow-lg hover:shadow-2xl transition-all duration-300 hover:scale-105 hover:transform hover:-translate-y-2 min-h-[200px] md:col-span-2">
                    <x-heroicon-o-user class="w-12 h-12 text-frappe-blue mb-4" />

                    <!-- Welcome Message -->
                    <div class="text-frappe-text text-2xl mb-2 text-center">
                        {{ __('messages.welcome_user', ['name' => auth()->user()->name]) }}
                    </div>

                    <div class="text-frappe-subtext1 text-lg mb-2">{{ __('messages.role') }}</div>
                    <span class="font-bold text-frappe-blue text-xl mb-4">
                        {{ ucfirst(auth()->user()->role) }}
                    </span>

                    <!-- Profile and Logout Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 mt-auto">
                        <a href="{{ route('profile.edit') }}"
                            class="frosted-button text-white px-6 py-2 rounded-lg hover:transform hover:-translate-y-1 transition-all flex items-center gap-2">
                            <x-heroicon-o-user-circle class="w-5 h-5" />
                            {{ __('messages.profile') }}
                        </a>

                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit"
                                class="bg-gradient-to-r from-red-500/30 to-pink-500/30 backdrop-blur-sm border border-red-400/40 text-red-300 rounded-lg hover:from-red-500/40 hover:to-pink-500/40 transition-all px-6 py-2 flex items-center gap-2 shadow-lg hover:transform hover:-translate-y-1">
                                <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5" />
                                {{ __('messages.logout') }}
                            </button>
                        </form>
                    </div>
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
