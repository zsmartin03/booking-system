<x-app-layout>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-frappe-surface0/80 overflow-hidden shadow-sm sm:rounded-lg border border-frappe-surface1">
                <div class="p-6 text-frappe-text">
                    @if(auth()->user()->role === 'admin')
                        <div class="flex items-center mb-6">
                            <svg class="w-6 h-6 text-frappe-blue mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                            </svg>
                            <p class="text-2xl font-medium text-frappe-blue">Admin Dashboard</p>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Quick Actions Card -->
                            <div class="bg-frappe-surface1 p-5 rounded-lg border border-frappe-surface2 hover:border-frappe-blue transition-colors">
                                <div class="flex items-center mb-3">
                                    <svg class="w-5 h-5 text-frappe-blue mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    <h3 class="text-lg font-medium text-frappe-blue">Quick Actions</h3>
                                </div>
                                <ul class="space-y-2">
                                    <li>
                                        <a href="#" class="flex items-center text-frappe-subtext1 hover:text-frappe-blue transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                            Manage Users
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="flex items-center text-frappe-subtext1 hover:text-frappe-blue transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            View Reports
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="flex items-center text-frappe-subtext1 hover:text-frappe-blue transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            System Settings
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <!-- Statistics Card (Empty State) -->
                            <div class="bg-frappe-surface1 p-5 rounded-lg border border-frappe-surface2 hover:border-frappe-blue transition-colors">
                                <div class="flex items-center mb-3">
                                    <svg class="w-5 h-5 text-frappe-blue mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                    <h3 class="text-lg font-medium text-frappe-blue">Statistics</h3>
                                </div>
                                <div class="text-center py-4 text-frappe-subtext1">
                                    <p>No data available</p>
                                </div>
                            </div>

                            <!-- Recent Activity Card (Empty State) -->
                            <div class="bg-frappe-surface1 p-5 rounded-lg border border-frappe-surface2 hover:border-frappe-blue transition-colors">
                                <div class="flex items-center mb-3">
                                    <svg class="w-5 h-5 text-frappe-blue mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <h3 class="text-lg font-medium text-frappe-blue">Recent Activity</h3>
                                </div>
                                <div class="text-center py-4 text-frappe-subtext1">
                                    <p>No recent activity</p>
                                </div>
                            </div>
                        </div>

                    @elseif(auth()->user()->role === 'provider')
                        <div class="flex items-center mb-6">
                            <svg class="w-6 h-6 text-frappe-blue mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <p class="text-2xl font-medium text-frappe-blue">Service Provider Dashboard</p>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Upcoming Appointments (Empty State) -->
                            <div class="bg-frappe-surface1 p-5 rounded-lg border border-frappe-surface2">
                                <h3 class="text-lg font-medium text-frappe-blue mb-4">Upcoming Appointments</h3>
                                <div class="text-center py-4 text-frappe-subtext1">
                                    <p>No upcoming appointments</p>
                                </div>
                            </div>
                            
                            <!-- Business Stats (Empty State) -->
                            <div class="bg-frappe-surface1 p-5 rounded-lg border border-frappe-surface2">
                                <h3 class="text-lg font-medium text-frappe-blue mb-4">Business Overview</h3>
                                <div class="text-center py-4 text-frappe-subtext1">
                                    <p>No business data available</p>
                                </div>
                            </div>
                        </div>

                    @else
                        <div class="flex items-center mb-6">
                            <svg class="w-6 h-6 text-frappe-blue mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <p class="text-2xl font-medium text-frappe-blue">Client Dashboard</p>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Upcoming Bookings (Empty State) -->
                            <div class="bg-frappe-surface1 p-5 rounded-lg border border-frappe-surface2">
                                <h3 class="text-lg font-medium text-frappe-blue mb-4">Your Upcoming Bookings</h3>
                                <div class="text-center py-4 text-frappe-subtext1">
                                    <p>No upcoming bookings</p>
                                </div>
                            </div>
                            
                            <!-- Quick Actions -->
                            <div class="bg-frappe-surface1 p-5 rounded-lg border border-frappe-surface2">
                                <h3 class="text-lg font-medium text-frappe-blue mb-4">Quick Actions</h3>
                                <div class="space-y-3">
                                    <a href="#" class="block p-3 bg-frappe-surface0 rounded-lg hover:bg-frappe-surface2 transition-colors">
                                        <p class="font-medium text-frappe-text">Book New Appointment</p>
                                        <p class="text-sm text-frappe-subtext1">Schedule with your favorite providers</p>
                                    </a>
                                    <a href="#" class="block p-3 bg-frappe-surface0 rounded-lg hover:bg-frappe-surface2 transition-colors">
                                        <p class="font-medium text-frappe-text">View Booking History</p>
                                        <p class="text-sm text-frappe-subtext1">See your past appointments</p>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>