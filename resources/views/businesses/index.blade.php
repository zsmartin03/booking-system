<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-frappe-lavender leading-tight">
            {{ __('My Businesses') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4">
                <a href="{{ route('businesses.create') }}"
                    class="bg-frappe-blue text-white px-4 py-2 rounded hover:bg-frappe-sapphire">
                    {{ __('Add New Business') }}
                </a>
            </div>
            <div class="bg-frappe-surface0 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <ul>
                        @forelse($businesses as $business)
                            <li class="mb-4 flex justify-between items-center">
                                <div>
                                    <a href="{{ route('businesses.show', $business->id) }}"
                                        class="text-frappe-blue hover:underline text-lg font-semibold">
                                        {{ $business->name }}
                                    </a>
                                    <div class="text-frappe-subtext1">{{ $business->address }}</div>
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ route('businesses.edit', $business->id) }}"
                                        class="inline-flex items-center gap-1 bg-frappe-blue text-white px-3 py-1.5 rounded hover:bg-frappe-sapphire transition"
                                        title="{{ __('Edit') }}">
                                        <x-heroicon-o-pencil class="w-4 h-4" />
                                        {{ __('Edit') }}
                                    </a>
                                    <a href="{{ route('business-working-hours.index', ['business_id' => $business->id]) }}"
                                        class="inline-flex items-center gap-1 bg-frappe-green text-white px-3 py-1.5 rounded hover:bg-frappe-teal transition"
                                        title="{{ __('Working Hours') }}">
                                        <x-heroicon-o-clock class="w-4 h-4" />
                                        {{ __('Working Hours') }}
                                    </a>
                                    <button
                                        class="inline-flex items-center gap-1 bg-frappe-red text-white px-3 py-1.5 rounded hover:bg-frappe-maroon transition"
                                        onclick="showDeleteModal({{ $business->id }}, '{{ addslashes($business->name) }}')"
                                        title="{{ __('Delete') }}">
                                        <x-heroicon-o-trash class="w-4 h-4" />
                                        {{ __('Delete') }}
                                    </button>
                                </div>
                            </li>
                        @empty
                            <li>{{ __('No businesses found.') }}</li>
                        @endforelse
                    </ul>
                </div>
            </div>
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
