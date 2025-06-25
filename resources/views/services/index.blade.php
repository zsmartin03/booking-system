<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-frappe-lavender leading-tight">
            {{ __('messages.services') }} - {{ $business->name }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-4">
            <a href="{{ route('services.create', ['business_id' => $business->id]) }}"
                class="frosted-button text-white px-4 py-2 rounded-lg transition-all inline-flex items-center gap-2">
                <x-heroicon-o-plus class="w-5 h-5" /> {{ __('messages.create_service') }}
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
                                <th class="text-left py-3 px-4 font-medium text-frappe-text">
                                    {{ __('messages.service_name') }}</th>
                                <th class="text-left py-3 px-4 font-medium text-frappe-text">
                                    {{ __('messages.duration') }}</th>
                                <th class="text-left py-3 px-4 font-medium text-frappe-text">{{ __('messages.price') }}
                                </th>
                                <th class="text-left py-3 px-4 font-medium text-frappe-text">
                                    {{ __('messages.employees') }}</th>
                                <th class="py-3 px-4 font-medium text-frappe-text">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($services as $service)
                                <tr
                                    class="border-b border-frappe-surface1/20 hover:bg-frappe-surface0/20 transition-colors">
                                    <td class="py-3 px-4 text-frappe-text font-medium">{{ $service->name }}</td>
                                    <td class="py-3 px-4 text-frappe-subtext1">{{ $service->duration }} min</td>
                                    <td class="py-3 px-4 text-frappe-subtext1">
                                        {{ \App\Models\Service::formatPrice($service->price, $businessSettings['currency'] ?? 'USD') }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach ($service->employees as $employee)
                                                <span
                                                    class="inline-block bg-frappe-surface1 px-2 py-1 rounded text-xs text-frappe-text">{{ $employee->name }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex gap-2 justify-center">
                                            <a href="{{ route('services.edit', $service->id) }}"
                                                class="edit-button text-white px-6 py-2 rounded-lg flex items-center gap-2 text-sm transition-all">
                                                <x-heroicon-o-pencil class="w-4 h-4" /> {{ __('messages.edit') }}
                                            </a>
                                            <button
                                                class="delete-button text-white px-6 py-2 rounded-lg flex items-center gap-2 text-sm transition-all"
                                                onclick="showDeleteModal({{ $service->id }}, '{{ addslashes($service->name) }}')"
                                                title="{{ __('messages.delete') }}">
                                                <x-heroicon-o-trash class="w-4 h-4" /> {{ __('messages.delete') }}
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-frappe-subtext1">
                                        {{ __('messages.no_services_set') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mobile/Tablet Card View -->
            <div class="lg:hidden">
                @forelse($services as $service)
                    <div class="p-4 border-b border-frappe-surface1/20 last:border-b-0">
                        <div class="space-y-3">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-medium text-frappe-text">{{ $service->name }}</h3>
                                    <div class="text-sm text-frappe-subtext1 mt-1">
                                        <span class="mr-4">{{ $service->duration }} min</span>
                                        <span
                                            class="font-medium">{{ \App\Models\Service::formatPrice($service->price, $businessSettings['currency'] ?? 'USD') }}</span>
                                    </div>
                                </div>
                            </div>

                            @if ($service->employees->count() > 0)
                                <div>
                                    <div class="text-xs text-frappe-subtext1 mb-1">{{ __('messages.available_with') }}
                                    </div>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($service->employees as $employee)
                                            <span
                                                class="inline-block bg-frappe-surface1 px-2 py-1 rounded text-xs text-frappe-text">{{ $employee->name }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="flex gap-2 pt-2 justify-center sm:justify-start">
                                <a href="{{ route('services.edit', $service->id) }}"
                                    class="edit-button text-white px-6 py-2 rounded-lg flex items-center gap-2 text-sm transition-all">
                                    <x-heroicon-o-pencil class="w-4 h-4" /> {{ __('messages.edit') }}
                                </a>
                                <button
                                    class="delete-button text-white px-6 py-2 rounded-lg flex items-center gap-2 text-sm transition-all"
                                    onclick="showDeleteModal({{ $service->id }}, '{{ addslashes($service->name) }}')"
                                    title="{{ __('messages.delete') }}">
                                    <x-heroicon-o-trash class="w-4 h-4" /> {{ __('messages.delete') }}
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-frappe-subtext1">{{ __('messages.no_services_set') }}</div>
                @endforelse
            </div>
        </div>
    </div>

    <div id="deleteModal"
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm z-50 hidden">
        <div class="frosted-modal p-6 rounded-2xl shadow-2xl w-full max-w-md mx-4">
            <h3 class="text-xl font-semibold mb-4 text-frappe-red">{{ __('messages.delete_service') }}</h3>
            <p class="mb-6 text-frappe-text opacity-90">{{ __('messages.are_you_sure_delete') }} <span
                    id="modalServiceName" class="font-bold text-frappe-lavender"></span>?</p>
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
        function showDeleteModal(serviceId, serviceName) {
            document.getElementById('modalServiceName').textContent = serviceName;
            document.getElementById('deleteForm').action = "{{ url('/services') }}/" + serviceId;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function hideDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
