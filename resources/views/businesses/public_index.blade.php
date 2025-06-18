<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-frappe-lavender leading-tight">
            {{ __('All Businesses') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-frappe-surface0 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <ul>
                        @forelse($businesses as $business)
                            <li class="mb-4">
                                <a href="{{ route('businesses.show', $business->id) }}"
                                    class="text-frappe-blue hover:underline text-lg font-semibold">
                                    {{ $business->name }}
                                </a>
                                <div class="text-frappe-subtext1">{{ $business->address }}</div>
                            </li>
                        @empty
                            <li>{{ __('No businesses found.') }}</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
