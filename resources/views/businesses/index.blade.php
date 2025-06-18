<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-frappe-lavender leading-tight">
            {{ __('My Businesses') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('businesses.create') }}"
                    class="bg-frappe-blue text-white px-4 py-2 rounded hover:bg-frappe-sapphire">
                    {{ __('Add New Business') }}
                </a>
            </div>

            @forelse($businesses as $business)
                @if ($loop->first)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @endif

                <div
                    class="bg-frappe-surface0 overflow-hidden shadow-sm sm:rounded-lg border border-frappe-surface2 hover:shadow-lg transition-shadow">
                    <div class="p-6">
                        <div class="mb-4">
                            <a href="{{ route('businesses.show', $business->id) }}"
                                class="text-frappe-blue hover:underline text-xl font-semibold block mb-2">
                                {{ $business->name }}
                            </a>
                            <p class="text-frappe-subtext1 text-sm">{{ $business->address }}</p>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('business-working-hours.index', ['business_id' => $business->id]) }}"
                                class="inline-flex items-center gap-1 bg-frappe-green text-white px-3 py-1.5 rounded text-sm hover:bg-frappe-teal transition"
                                title="{{ __('Working Hours') }}">
                                <x-heroicon-o-clock class="w-4 h-4" />
                                {{ __('Working Hours') }}
                            </a>
                            <a href="{{ route('services.index', ['business_id' => $business->id]) }}"
                                class="inline-flex items-center gap-1 bg-frappe-yellow text-white px-3 py-1.5 rounded text-sm hover:bg-frappe-peach transition"
                                title="{{ __('Services') }}">
                                <x-heroicon-o-briefcase class="w-4 h-4" />
                                {{ __('Services') }}
                            </a>
                            <a href="{{ route('businesses.edit', $business->id) }}"
                                class="inline-flex items-center gap-1 bg-frappe-blue text-white px-3 py-1.5 rounded text-sm hover:bg-frappe-sapphire transition"
                                title="{{ __('Edit') }}">
                                <x-heroicon-o-pencil class="w-4 h-4" />
                                {{ __('Edit') }}
                            </a>
                            <button
                                class="inline-flex items-center gap-1 bg-frappe-red text-white px-3 py-1.5 rounded text-sm hover:bg-frappe-maroon transition"
                                onclick="showDeleteModal({{ $business->id }}, '{{ addslashes($business->name) }}')"
                                title="{{ __('Delete') }}">
                                <x-heroicon-o-trash class="w-4 h-4" />
                                {{ __('Delete') }}
                            </button>
                        </div>
                    </div>
                </div>

                @if ($loop->last)
        </div>
        @endif
    @empty
        <div class="bg-frappe-surface0 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-center">
                <p class="text-frappe-subtext1">{{ __('No businesses found.') }}</p>
            </div>
        </div>
        @endforelse
    </div>
    </div>

    <div id="deleteModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50 hidden">
        <div class="bg-frappe-surface1 border border-frappe-surface2 p-4 rounded-lg shadow-xl w-full max-w-xs">
            <h3 class="text-lg font-semibold mb-3 text-frappe-red">{{ __('Delete Business') }}</h3>
            <p class="mb-4 text-frappe-text">{{ __('Are you sure you want to delete') }} <span id="modalBusinessName"
                    class="font-bold"></span>?</p>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="hideDeleteModal()"
                        class="px-4 py-2 bg-frappe-blue text-white rounded hover:bg-frappe-sapphire transition">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-frappe-red text-white rounded hover:bg-frappe-maroon transition">
                        {{ __('Delete') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showDeleteModal(businessId, businessName) {
            document.getElementById('modalBusinessName').textContent = businessName;
            document.getElementById('deleteForm').action = "{{ url('/manage/businesses') }}/" + businessId;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function hideDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
