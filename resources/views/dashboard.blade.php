<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-catppuccin-lavender leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-catppuccin-surface0/80 overflow-hidden shadow-sm sm:rounded-lg border border-catppuccin-surface1">
                <div class="p-6 text-catppuccin-text">
                    @if(auth()->user()->role === 'admin')
                        <p class="text-lg">Admin role</p>
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-catppuccin-surface1/50 p-4 rounded-lg border border-catppuccin-surface2">
                                <h3 class="text-catppuccin-blue font-medium">Quick Actions</h3>
                                <ul class="mt-2 space-y-2">
                                    <li><a href="#" class="text-catppuccin-subtext1 hover:text-catppuccin-blue">Manage Users</a></li>
                                    <li><a href="#" class="text-catppuccin-subtext1 hover:text-catppuccin-blue">View Reports</a></li>
                                    <li><a href="#" class="text-catppuccin-subtext1 hover:text-catppuccin-blue">System Settings</a></li>
                                </ul>
                            </div>
                        </div>
                    @elseif(auth()->user()->role === 'provider')
                        <p class="text-lg">Service Provider role</p>
                    @else
                        <p class="text-lg">Client role</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>