<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-frappe-lavender leading-tight">
            {{ __('messages.working_hours_for') }} {{ $business->name }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-4">
            <a href="{{ route('business-working-hours.create', ['business_id' => $business->id]) }}"
                class="frosted-button text-white px-4 py-2 rounded-lg hover:transform hover:-translate-y-1 transition-all inline-flex items-center gap-2">
                <x-heroicon-o-plus class="w-5 h-5" /> {{ __('messages.add_working_hour') }}
            </a>
        </div>

        @if (session('success'))
            <div class="mb-4 p-3 bg-frappe-green/20 text-frappe-green rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="frosted-card rounded-xl shadow-lg overflow-hidden">
            <!-- Desktop Table View -->
            <div class="hidden md:block">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-full">
                        <thead>
                            <tr class="border-b border-frappe-surface1/30">
                                <th class="text-left py-3 px-4 font-medium text-frappe-text">{{ __('messages.day') }}
                                </th>
                                <th class="text-left py-3 px-4 font-medium text-frappe-text">{{ __('messages.start') }}
                                </th>
                                <th class="text-left py-3 px-4 font-medium text-frappe-text">{{ __('messages.end') }}
                                </th>
                                <th class="py-3 px-4 font-medium text-frappe-text">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($workingHours as $hour)
                                <tr
                                    class="border-b border-frappe-surface1/20 hover:bg-frappe-surface0/20 transition-colors">
                                    <td class="py-3 px-4 text-frappe-text font-medium">
                                        {{ __('messages.' . $hour->day_of_week) }}
                                    </td>
                                    <td class="py-3 px-4 text-frappe-subtext1">{{ $hour->start_time }}</td>
                                    <td class="py-3 px-4 text-frappe-subtext1">{{ $hour->end_time }}</td>
                                    <td class="py-3 px-4">
                                        <div class="flex gap-2 justify-center">
                                            <a href="{{ route('business-working-hours.edit', $hour->id) }}"
                                                class="edit-button text-white px-6 py-2 rounded-lg flex items-center gap-2 text-sm hover:transform hover:-translate-y-1 transition-all">
                                                <x-heroicon-o-pencil class="w-4 h-4" /> {{ __('messages.edit') }}
                                            </a>
                                            <button
                                                class="delete-button text-white px-6 py-2 rounded-lg flex items-center gap-2 text-sm hover:transform hover:-translate-y-1 transition-all"
                                                onclick="showDeleteModal({{ $hour->id }}, '{{ __('messages.' . $hour->day_of_week) }} {{ $hour->start_time }}-{{ $hour->end_time }}')"
                                                title="{{ __('messages.delete') }}">
                                                <x-heroicon-o-trash class="w-4 h-4" /> {{ __('messages.delete') }}
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-frappe-subtext1">
                                        {{ __('messages.no_working_hours') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mobile Card View -->
            <div class="md:hidden">
                @forelse($workingHours as $hour)
                    <div class="p-4 border-b border-frappe-surface1/20 last:border-b-0">
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <h3 class="font-medium text-frappe-text">{{ __('messages.' . $hour->day_of_week) }}
                                </h3>
                                <span class="text-sm text-frappe-subtext1">{{ $hour->start_time }} -
                                    {{ $hour->end_time }}</span>
                            </div>

                            <div class="flex gap-2 justify-center sm:justify-start">
                                <a href="{{ route('business-working-hours.edit', $hour->id) }}"
                                    class="edit-button text-white px-6 py-2 rounded-lg flex items-center gap-2 text-sm hover:transform hover:-translate-y-1 transition-all">
                                    <x-heroicon-o-pencil class="w-4 h-4" /> {{ __('messages.edit') }}
                                </a>
                                <button
                                    class="delete-button text-white px-6 py-2 rounded-lg flex items-center gap-2 text-sm hover:transform hover:-translate-y-1 transition-all"
                                    onclick="showDeleteModal({{ $hour->id }}, '{{ __('messages.' . $hour->day_of_week) }} {{ $hour->start_time }}-{{ $hour->end_time }}')"
                                    title="{{ __('messages.delete') }}">
                                    <x-heroicon-o-trash class="w-4 h-4" /> {{ __('messages.delete') }}
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-frappe-subtext1">{{ __('messages.no_working_hours') }}</div>
                @endforelse
            </div>
        </div>
    </div>

    <div id="deleteModal"
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm z-50 hidden">
        <div class="frosted-modal p-6 rounded-2xl shadow-2xl w-full max-w-md mx-4">
            <h3 class="text-xl font-semibold mb-4 text-frappe-red">{{ __('messages.delete_working_hour') }}</h3>
            <p class="mb-6 text-frappe-text opacity-90">{{ __('messages.are_you_sure_delete') }} <span
                    id="modalWorkingHour" class="font-bold text-frappe-lavender"></span>?</p>
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
        function showDeleteModal(hourId, hourInfo) {
            document.getElementById('modalWorkingHour').textContent = hourInfo;
            document.getElementById('deleteForm').action = "{{ url('/business-working-hours') }}/" + hourId;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function hideDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
