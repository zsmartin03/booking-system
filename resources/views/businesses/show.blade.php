<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-frappe-lavender leading-tight">
            {{ $business->name }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-frappe-surface0 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <p class="mb-2 text-frappe-subtext1">{{ $business->description }}</p>
                    <p class="mb-1"><strong>Address:</strong> {{ $business->address }}</p>
                    <p class="mb-1"><strong>Phone:</strong> {{ $business->phone_number }}</p>
                    <p class="mb-1"><strong>Email:</strong> {{ $business->email }}</p>
                    @if ($business->website)
                        <p class="mb-1"><strong>Website:</strong>
                            <a href="{{ $business->website }}" target="_blank" class="text-frappe-blue hover:underline">
                                {{ $business->website }}
                            </a>
                        </p>
                    @endif
                    @if ($business->logo)
                        <div class="mt-4">
                            <img src="{{ $business->logo }}" alt="Logo" class="max-w-xs rounded shadow">
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
