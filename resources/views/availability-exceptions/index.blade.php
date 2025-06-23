<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-frappe-lavender leading-tight">
            {{ __('Availability Exceptions for') }} {{ $employee->name }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-4">
            <a href="{{ route('availability-exceptions.create', ['employee_id' => $employee->id]) }}"
                class="action-button text-white px-4 py-2 rounded-lg inline-flex items-center gap-2">
                <x-heroicon-o-plus class="w-5 h-5" /> {{ __('Add Exception') }}
            </a>
        </div>

        @if (session('success'))
            <div class="mb-4 p-3 bg-frappe-green/20 text-frappe-green rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="frosted-card rounded-xl shadow-lg overflow-hidden">
            <!-- Desktop Table View -->
            <div class="hidden lg:block">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-full">
                        <thead>
                            <tr class="border-b border-frappe-surface1/30">
                                <th class="text-left py-3 px-4 font-medium text-frappe-text">{{ __('Date') }}</th>
                                <th class="text-left py-3 px-4 font-medium text-frappe-text">{{ __('Time') }}</th>
                                <th class="text-left py-3 px-4 font-medium text-frappe-text">{{ __('Type') }}</th>
                                <th class="text-left py-3 px-4 font-medium text-frappe-text">{{ __('Note') }}</th>
                                <th class="py-3 px-4 font-medium text-frappe-text">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($exceptions as $exception)
                                <tr
                                    class="border-b border-frappe-surface1/20 hover:bg-frappe-surface0/20 transition-colors">
                                    <td class="py-3 px-4">
                                        <div class="font-medium text-frappe-text">
                                            {{ \Carbon\Carbon::parse($exception->date)->format('M d, Y') }}
                                        </div>
                                        <div class="text-sm text-frappe-subtext1">
                                            {{ \Carbon\Carbon::parse($exception->date)->format('l') }}
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-frappe-text">
                                        {{ \Carbon\Carbon::parse($exception->start_time)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($exception->end_time)->format('H:i') }}
                                    </td>
                                    <td class="py-3 px-4">
                                        @if ($exception->type === 'available')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-frappe-green/20 text-frappe-green">
                                                <x-heroicon-o-check-circle class="w-3 h-3 mr-1" />
                                                {{ __('Available') }}
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-frappe-red/20 text-frappe-red">
                                                <x-heroicon-o-x-circle class="w-3 h-3 mr-1" />
                                                {{ __('Unavailable') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-frappe-text max-w-xs truncate">
                                        {{ $exception->note ?: '—' }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex gap-2">
                                            <a href="{{ route('availability-exceptions.edit', $exception->id) }}"
                                                class="edit-button text-white px-3 py-1 rounded-lg flex items-center gap-1 text-sm">
                                                <x-heroicon-o-pencil class="w-4 h-4" /> {{ __('Edit') }}
                                            </a>
                                            <button
                                                class="delete-button text-white px-3 py-1 rounded-lg flex items-center gap-1 text-sm"
                                                onclick="showDeleteModal({{ $exception->id }}, '{{ \Carbon\Carbon::parse($exception->date)->format('M d, Y') }} {{ \Carbon\Carbon::parse($exception->start_time)->format('H:i') }}-{{ \Carbon\Carbon::parse($exception->end_time)->format('H:i') }}')"
                                                title="{{ __('Delete') }}">
                                                <x-heroicon-o-trash class="w-4 h-4" /> {{ __('Delete') }}
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-frappe-subtext1">
                                        <div class="flex flex-col items-center gap-2">
                                            <x-heroicon-o-calendar-days class="w-12 h-12 opacity-50" />
                                            <div>{{ __('No availability exceptions set.') }}</div>
                                            <div class="text-sm">
                                                {{ __('Add exceptions to override regular working hours.') }}</div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mobile Card View -->
            <div class="lg:hidden">
                @forelse($exceptions as $exception)
                    <div class="p-4 border-b border-frappe-surface1/20 last:border-b-0">
                        <div class="space-y-3">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-medium text-frappe-text">
                                        {{ \Carbon\Carbon::parse($exception->date)->format('M d, Y') }}
                                    </h3>
                                    <p class="text-sm text-frappe-subtext1">
                                        {{ \Carbon\Carbon::parse($exception->date)->format('l') }}
                                    </p>
                                </div>
                                @if ($exception->type === 'available')
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-frappe-green/20 text-frappe-green">
                                        <x-heroicon-o-check-circle class="w-3 h-3 mr-1" />
                                        {{ __('Available') }}
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-frappe-red/20 text-frappe-red">
                                        <x-heroicon-o-x-circle class="w-3 h-3 mr-1" />
                                        {{ __('Unavailable') }}
                                    </span>
                                @endif
                            </div>

                            <div class="text-sm text-frappe-subtext1">
                                <div><strong>{{ __('Time:') }}</strong>
                                    {{ \Carbon\Carbon::parse($exception->start_time)->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($exception->end_time)->format('H:i') }}</div>
                                @if ($exception->note)
                                    <div class="mt-1"><strong>{{ __('Note:') }}</strong> {{ $exception->note }}
                                    </div>
                                @endif
                            </div>

                            <div class="flex gap-2 pt-2">
                                <a href="{{ route('availability-exceptions.edit', $exception->id) }}"
                                    class="edit-button text-white px-4 py-2 rounded-lg flex items-center gap-1 text-sm">
                                    <x-heroicon-o-pencil class="w-4 h-4" /> {{ __('Edit') }}
                                </a>
                                <button
                                    class="delete-button text-white px-4 py-2 rounded-lg flex items-center gap-1 text-sm"
                                    onclick="showDeleteModal({{ $exception->id }}, '{{ \Carbon\Carbon::parse($exception->date)->format('M d, Y') }} {{ \Carbon\Carbon::parse($exception->start_time)->format('H:i') }}-{{ \Carbon\Carbon::parse($exception->end_time)->format('H:i') }}')"
                                    title="{{ __('Delete') }}">
                                    <x-heroicon-o-trash class="w-4 h-4" /> {{ __('Delete') }}
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-frappe-subtext1">
                        <div class="flex flex-col items-center gap-2">
                            <x-heroicon-o-calendar-days class="w-12 h-12 opacity-50" />
                            <div>{{ __('No availability exceptions set.') }}</div>
                            <div class="text-sm">{{ __('Add exceptions to override regular working hours.') }}</div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteModal"
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm z-50 hidden">
        <div class="frosted-modal p-6 rounded-2xl shadow-2xl w-full max-w-md mx-4">
            <h3 class="text-xl font-semibold mb-4 text-frappe-red">{{ __('Delete Availability Exception') }}</h3>
            <p class="mb-6 text-frappe-text opacity-90">{{ __('Are you sure you want to delete') }} <span
                    id="modalExceptionInfo" class="font-bold text-frappe-lavender"></span>?</p>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="hideDeleteModal()"
                        class="px-6 py-2 bg-gradient-to-r from-gray-500/20 to-gray-600/20 backdrop-blur-sm border border-gray-400/30 text-gray-300 rounded-lg hover:from-gray-500/30 hover:to-gray-600/30 transition-all">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit"
                        class="px-6 py-2 bg-gradient-to-r from-red-500/30 to-pink-500/30 backdrop-blur-sm border border-red-400/40 text-red-300 rounded-lg hover:from-red-500/40 hover:to-pink-500/40 transition-all">
                        {{ __('Delete') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showDeleteModal(exceptionId, exceptionInfo) {
            document.getElementById('modalExceptionInfo').textContent = exceptionInfo;
            document.getElementById('deleteForm').action = "{{ url('/availability-exceptions') }}/" + exceptionId;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function hideDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
