<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-frappe-text leading-tight">
            {{ __('notifications.notifications') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Success Message -->
            @if (session('success'))
                <div class="mb-6 bg-frappe-green/20 border border-frappe-green text-frappe-green px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="frosted-card overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 text-frappe-text">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-frappe-lavender">
                            {{ __('notifications.all_notifications') }}
                        </h3>
                        @if ($notifications->count() > 0)
                            <form method="POST" action="{{ route('notifications.clear') }}" class="inline">
                                @csrf
                                <button type="submit"
                                    class="text-frappe-red hover:text-frappe-maroon transition-colors duration-200">
                                    {{ __('notifications.clear_all') }}
                                </button>
                            </form>
                        @endif
                    </div>

                    @if ($notifications->count() > 0)
                        <div class="notification-container space-y-4">
                            @foreach ($notifications as $notification)
                                <div
                                    class="notification-item border border-frappe-surface1 rounded-lg p-4 {{ $notification->is_read ? 'bg-frappe-surface0/50 opacity-75' : 'bg-frappe-blue/10' }}">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-frappe-blue mb-2">
                                                {{ $notification->translated_title }}
                                            </h4>
                                            <p class="text-frappe-text mb-2">
                                                {{ $notification->translated_content }}
                                            </p>
                                            <div class="flex items-center space-x-4 text-sm text-frappe-subtext1">
                                                <span>
                                                    <x-heroicon-o-clock class="w-4 h-4 inline mr-1" />
                                                    {{ $notification->sent_at ? $notification->sent_at->format('M d, Y H:i') : __('notifications.no_date') }}
                                                </span>
                                                @if ($notification->is_read && $notification->read_at)
                                                    <span>
                                                        <x-heroicon-o-eye class="w-4 h-4 inline mr-1" />
                                                        {{ __('notifications.read_at') }}:
                                                        {{ $notification->read_at->format('M d, Y H:i') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            @if ($notification->is_read)
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-frappe-surface1 text-frappe-subtext1">
                                                    {{ __('notifications.read') }}
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-frappe-blue text-frappe-crust">
                                                    {{ __('notifications.unread') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $notifications->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <x-heroicon-o-bell-slash class="w-16 h-16 text-frappe-subtext1 mx-auto mb-4" />
                            <h3 class="text-lg font-medium text-frappe-subtext1 mb-2">
                                {{ __('notifications.no_notifications') }}
                            </h3>
                            <p class="text-frappe-subtext1">
                                {{ __('notifications.no_notifications_message') }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const clearAllForm = document.querySelector('form[action="{{ route('notifications.clear') }}"]');
                if (clearAllForm) {
                    clearAllForm.addEventListener('submit', function(e) {
                        e.preventDefault();

                        fetch("{{ route('notifications.clear') }}", {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Network response was not ok');
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.success) {
                                    const notificationItems = document.querySelectorAll(
                                        '.notification-item');
                                    notificationItems.forEach(item => {
                                        item.style.display = 'none';
                                    });

                                    const notificationContainer = document.querySelector(
                                        '.notification-container');
                                    if (notificationContainer) {
                                        notificationContainer.innerHTML = `
                                        <div class="text-center py-12">
                                            <svg class="w-16 h-16 text-frappe-subtext1 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                                            </svg>
                                            <h3 class="text-lg font-medium text-frappe-subtext1 mb-2">
                                                {{ __('notifications.no_notifications') }}
                                            </h3>
                                            <p class="text-frappe-subtext1">
                                                {{ __('notifications.no_notifications_message') }}
                                            </p>
                                        </div>
                                    `;
                                    }

                                    const existingMessage = document.querySelector('.bg-frappe-green\\/20');
                                    if (!existingMessage) {
                                        const successMessage = document.createElement('div');
                                        successMessage.className =
                                            'mb-6 bg-frappe-green/20 border border-frappe-green text-frappe-green px-4 py-3 rounded-lg';
                                        successMessage.textContent =
                                            '{{ __('notifications.notifications_cleared') }}';

                                        const container = document.querySelector('.max-w-7xl > div');
                                        if (container) {
                                            container.insertBefore(successMessage, container.firstChild);
                                        }
                                    }
                                }
                            })
                            .catch(error => {
                                console.error('Error clearing notifications:', error);
                                clearAllForm.submit();
                            });
                    });
                }
            });
        </script>
    @endpush
</x-app-layout>
