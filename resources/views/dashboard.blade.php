<x-app-layout>
    <div class="py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:space-x-8 space-y-6 md:space-y-0 justify-center items-stretch">

                <!-- Role Card -->
                <div
                    class="flex-1 frosted-card rounded-xl p-6 flex flex-col items-center justify-center shadow-lg hover:shadow-2xl transition-all duration-300 hover:scale-105 hover:transform hover:-translate-y-2">
                    <x-heroicon-o-user class="w-8 h-8 text-frappe-blue mb-2" />
                    <div class="text-frappe-text text-lg mb-1">{{ __('messages.role') }}</div>
                    <span class="font-bold text-frappe-blue text-xl">
                        {{ ucfirst(auth()->user()->role) }}
                    </span>
                </div>

                <!-- Manage Businesses (admin/provider only) -->
                @if (auth()->user()->role === 'admin' || auth()->user()->role === 'provider')
                    <div
                        class="flex-1 frosted-card rounded-xl p-6 flex flex-col items-center justify-center shadow-lg hover:shadow-2xl transition-all duration-300 hover:scale-105 hover:transform hover:-translate-y-2">
                        <x-heroicon-o-briefcase class="w-8 h-8 text-frappe-lavender mb-2" />
                        <div class="text-frappe-text text-lg mb-1">{{ __('messages.manage_businesses') }}</div>
                        <a href="{{ route('businesses.index') }}"
                            class="mt-2 frosted-button text-white px-4 py-2 rounded-lg hover:transform hover:-translate-y-1 transition-all flex items-center gap-2">
                            <x-heroicon-o-cog class="w-5 h-5" />
                            {{ __('messages.manage') }}
                        </a>
                    </div>
                @endif

                <!-- Browse Businesses (all users) -->
                <div
                    class="flex-1 frosted-card rounded-xl p-6 flex flex-col items-center justify-center shadow-lg hover:shadow-2xl transition-all duration-300 hover:scale-105 hover:transform hover:-translate-y-2">
                    <x-heroicon-o-building-storefront class="w-8 h-8 text-frappe-green mb-2" />
                    <div class="text-frappe-text text-lg mb-1">{{ __('messages.available_businesses') }}</div>
                    <a href="{{ route('businesses.public.index') }}"
                        class="mt-2 frosted-button text-white px-4 py-2 rounded-lg hover:transform hover:-translate-y-1 transition-all flex items-center gap-2">
                        <x-heroicon-o-eye class="w-5 h-5" />
                        {{ __('messages.view') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
