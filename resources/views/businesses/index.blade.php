<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-frappe-lavender leading-tight">
            {{ __('messages.businesses') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('businesses.create') }}"
                    class="frosted-button text-white px-6 py-3 rounded-lg font-medium inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    {{ __('messages.create_business') }}
                </a>
            </div>

            @if ($businesses->count() > 0)
                <div class="frosted-card rounded-xl shadow-lg overflow-hidden">
                    <div class="divide-y divide-frappe-surface2/30">
                        @foreach ($businesses as $business)
                            <div class="p-4 sm:p-6 hover:bg-frappe-surface0/20 transition-all duration-200">
                                <!-- Main business info -->
                                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex flex-col sm:flex-row sm:items-start gap-4">
                                            <div class="flex-1">
                                                <a href="{{ route('businesses.show', $business->id) }}"
                                                    class="text-frappe-blue hover:text-frappe-sapphire text-xl font-semibold block mb-1 transition-colors">
                                                    {{ $business->name }}
                                                </a>
                                                <p class="text-frappe-subtext1 text-sm opacity-80 mb-1">
                                                    {{ $business->address }}</p>
                                                @if ($business->description)
                                                    <p
                                                        class="text-frappe-subtext0 text-sm opacity-70 line-clamp-2 mb-2">
                                                        {{ $business->description }}</p>
                                                @endif
                                            </div>
                                            <div class="text-left sm:text-right text-sm text-frappe-subtext1">
                                                <div class="opacity-60">{{ $business->phone_number }}</div>
                                                <div class="opacity-60">{{ $business->email }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action buttons -->
                                <div class="mt-4 flex flex-wrap gap-2 justify-center sm:justify-start">
                                    <a href="{{ route('business-working-hours.index', ['business_id' => $business->id]) }}"
                                        class="inline-flex items-center gap-2 bg-gradient-to-r from-green-500/20 to-teal-500/20 backdrop-blur-sm border border-green-400/30 text-green-300 px-4 py-2 rounded-lg text-sm hover:from-green-500/30 hover:to-teal-500/30 hover:transform hover:-translate-y-1 transition-all"
                                        title="{{ __('messages.working_hours') }}">
                                        <x-heroicon-o-clock class="w-4 h-4" />
                                        <span class="hidden sm:inline">{{ __('messages.working_hours') }}</span>
                                        <span class="sm:hidden">{{ __('messages.working_hours') }}</span>
                                    </a>
                                    <a href="{{ route('services.index', ['business_id' => $business->id]) }}"
                                        class="inline-flex items-center gap-2 bg-gradient-to-r from-yellow-500/20 to-orange-500/20 backdrop-blur-sm border border-yellow-400/30 text-yellow-300 px-4 py-2 rounded-lg text-sm hover:from-yellow-500/30 hover:to-orange-500/30 hover:transform hover:-translate-y-1 transition-all"
                                        title="{{ __('messages.services') }}">
                                        <x-heroicon-o-briefcase class="w-4 h-4" />
                                        {{ __('messages.services') }}
                                    </a>
                                    <a href="{{ route('employees.index', ['business_id' => $business->id]) }}"
                                        class="inline-flex items-center gap-2 bg-gradient-to-r from-purple-500/20 to-indigo-500/20 backdrop-blur-sm border border-purple-400/30 text-purple-300 px-4 py-2 rounded-lg hover:from-purple-500/30 hover:to-indigo-500/30 hover:transform hover:-translate-y-1 transition-all"
                                        title="{{ __('messages.employees') }}">
                                        <x-heroicon-o-users class="w-4 h-4" />
                                        {{ __('messages.employees') }}
                                    </a>
                                    <a href="{{ route('settings.index', ['business_id' => $business->id]) }}"
                                        class="inline-flex items-center gap-2 bg-gradient-to-r from-cyan-500/20 to-blue-500/20 backdrop-blur-sm border border-cyan-400/30 text-cyan-300 px-4 py-2 rounded-lg text-sm hover:from-cyan-500/30 hover:to-blue-500/30 hover:transform hover:-translate-y-1 transition-all"
                                        title="{{ __('messages.settings') }}">
                                        <x-heroicon-o-cog-6-tooth class="w-4 h-4" />
                                        {{ __('messages.settings') }}
                                    </a>
                                    <a href="{{ route('businesses.edit', $business->id) }}"
                                        class="edit-button text-white px-4 py-2 rounded-lg inline-flex items-center gap-2 text-sm hover:transform hover:-translate-y-1 transition-all"
                                        title="{{ __('messages.edit') }}">
                                        <x-heroicon-o-pencil class="w-4 h-4" />
                                        <span class="hidden sm:inline">{{ __('messages.edit') }}</span>
                                    </a>
                                    <button
                                        class="delete-button text-white px-4 py-2 rounded-lg inline-flex items-center gap-2 text-sm hover:transform hover:-translate-y-1 transition-all"
                                        onclick="showDeleteModal({{ $business->id }}, '{{ addslashes($business->name) }}')"
                                        title="{{ __('messages.delete') }}">
                                        <x-heroicon-o-trash class="w-4 h-4" />
                                        <span class="hidden sm:inline">{{ __('messages.delete') }}</span>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="frosted-card overflow-hidden shadow-lg sm:rounded-xl">
                    <div class="p-6 text-center">
                        <p class="text-frappe-subtext1 opacity-80">{{ __('messages.no_data') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div id="deleteModal"
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm z-50 hidden">
        <div class="frosted-modal p-6 rounded-2xl shadow-2xl w-full max-w-md mx-4">
            <h3 class="text-xl font-semibold mb-4 text-frappe-red">{{ __('messages.delete_business') }}</h3>
            <p class="mb-6 text-frappe-text opacity-90">{{ __('messages.are_you_sure_delete') }} <span
                    id="modalBusinessName" class="font-bold text-frappe-lavender"></span>?</p>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="hideDeleteModal()"
                        class="px-6 py-2 bg-gradient-to-r from-gray-500/20 to-gray-600/20 backdrop-blur-sm border border-gray-400/30 text-gray-300 rounded-lg hover:from-gray-500/30 hover:to-gray-600/30 transition-all">
                        {{ __('messages.cancel') }}
                    </button>
                    <button type="submit"
                        class="px-6 py-2 bg-gradient-to-r from-red-500/30 to-pink-500/30 backdrop-blur-sm border border-red-400/40 text-red-300 rounded-lg hover:from-red-500/40 hover:to-pink-500/40 transition-all">
                        {{ __('messages.delete') }}
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
