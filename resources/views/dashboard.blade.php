<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-frappe-surface0 rounded-lg p-6 text-center">
                <p class="text-frappe-text text-lg">
                    Role:
                    <span class="font-bold text-frappe-blue">
                        {{ ucfirst(auth()->user()->role) }}
                    </span>
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
