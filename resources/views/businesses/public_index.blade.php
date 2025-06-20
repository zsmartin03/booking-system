<x-app-layout>
    <x-slot name="header">
        <div class="frosted-glass">
            <h2 class="font-semibold text-xl text-frappe-lavender leading-tight">
                {{ __('All Businesses') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            @forelse($businesses as $business)
                @if ($loop->first)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @endif

                <div
                    class="frosted-card overflow-hidden shadow-lg sm:rounded-xl border border-frappe-surface2 hover:shadow-2xl transition-all duration-300 hover:scale-105 hover:transform hover:-translate-y-2">
                    <div class="p-6">
                        <div class="mb-4">
                            <a href="{{ route('businesses.show', $business->id) }}"
                                class="text-frappe-blue hover:text-frappe-sapphire text-xl font-semibold block mb-2 transition-colors">
                                {{ $business->name }}
                            </a>
                            <p class="text-frappe-subtext1 text-sm opacity-80">{{ $business->address }}</p>
                            @if ($business->description)
                                <p class="text-frappe-subtext0 text-sm mt-2 opacity-70">{{ $business->description }}</p>
                            @endif
                        </div>

                        <div class="flex justify-center">
                            <a href="{{ route('businesses.show', $business->id) }}"
                                class="inline-flex items-center gap-1 bg-gradient-to-r from-blue-500/20 to-indigo-500/20 backdrop-blur-sm border border-blue-400/30 text-blue-300 px-4 py-2 rounded-lg text-sm hover:from-blue-500/30 hover:to-indigo-500/30 transition-all">
                                <x-heroicon-o-eye class="w-4 h-4" />
                                {{ __('View Details') }}
                            </a>
                        </div>
                    </div>
                </div>

                @if ($loop->last)
        </div>
        @endif
    @empty
        <div class="frosted-card overflow-hidden shadow-lg sm:rounded-xl">
            <div class="p-6 text-center">
                <p class="text-frappe-subtext1 opacity-80">{{ __('No businesses found.') }}</p>
            </div>
        </div>
        @endforelse
    </div>
    </div>
</x-app-layout>
