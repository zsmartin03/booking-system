<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h1 class="text-3xl font-bold text-frappe-text">{{ __('messages.business_statistics') }}</h1>
                        <p class="text-frappe-subtext1 mt-2">{{ $business->name }}</p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                        <!-- Business Selector -->
                        <div class="flex items-center gap-2">
                            <label for="business" class="text-frappe-text font-medium">{{ __('messages.business') }}:</label>
                            <select id="business" 
                                    class="frosted-input rounded-lg border-frappe-surface1 bg-frappe-base/50 text-frappe-text focus:border-frappe-lavender focus:ring-frappe-lavender min-w-[200px]">
                                @foreach($businesses as $biz)
                                    <option value="{{ $biz->id }}" {{ $biz->id == $selectedBusinessId ? 'selected' : '' }}>
                                        {{ $biz->name }}
                                        @if(auth()->user()->role === 'admin' && $biz->user)
                                            ({{ $biz->user->name }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Period Selector -->
                        <div class="flex items-center gap-2">
                            <label for="period" class="text-frappe-text font-medium">{{ __('messages.period') }}:</label>
                            <select id="period" 
                                    class="frosted-input rounded-lg border-frappe-surface1 bg-frappe-base/50 text-frappe-text focus:border-frappe-lavender focus:ring-frappe-lavender">
                                <option value="day" {{ $period === 'day' ? 'selected' : '' }}>{{ __('messages.daily') }}</option>
                                <option value="week" {{ $period === 'week' ? 'selected' : '' }}>{{ __('messages.weekly') }}</option>
                                <option value="month" {{ $period === 'month' ? 'selected' : '' }}>{{ __('messages.monthly') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Bookings -->
                <div class="frosted-card rounded-xl p-6 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-frappe-subtext1 text-sm font-medium">{{ __('messages.total_bookings') }}</p>
                            <p class="text-3xl font-bold text-frappe-blue">{{ number_format($totalBookings) }}</p>
                        </div>
                        <div class="p-3 bg-blue-500/20 rounded-full">
                            <x-heroicon-o-calendar class="w-8 h-8 text-frappe-blue" />
                        </div>
                    </div>
                </div>

                <!-- Total Revenue -->
                <div class="frosted-card rounded-xl p-6 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-frappe-subtext1 text-sm font-medium">{{ __('messages.total_revenue') }}</p>
                            <p class="text-3xl font-bold text-frappe-green">${{ number_format($totalRevenue, 2) }}</p>
                        </div>
                        <div class="p-3 bg-green-500/20 rounded-full">
                            <x-heroicon-o-currency-dollar class="w-8 h-8 text-frappe-green" />
                        </div>
                    </div>
                </div>

                <!-- Total Customers -->
                <div class="frosted-card rounded-xl p-6 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-frappe-subtext1 text-sm font-medium">{{ __('messages.total_customers') }}</p>
                            <p class="text-3xl font-bold text-frappe-peach">{{ number_format($totalCustomers) }}</p>
                        </div>
                        <div class="p-3 bg-orange-500/20 rounded-full">
                            <x-heroicon-o-users class="w-8 h-8 text-frappe-peach" />
                        </div>
                    </div>
                </div>

                <!-- Average Revenue per Booking -->
                <div class="frosted-card rounded-xl p-6 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-frappe-subtext1 text-sm font-medium">{{ __('messages.avg_revenue_per_booking') }}</p>
                            <p class="text-3xl font-bold text-frappe-lavender">
                                ${{ $totalBookings > 0 ? number_format($totalRevenue / $totalBookings, 2) : '0.00' }}
                            </p>
                        </div>
                        <div class="p-3 bg-purple-500/20 rounded-full">
                            <x-heroicon-o-chart-bar class="w-8 h-8 text-frappe-lavender" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Bookings Chart -->
                <div class="frosted-card rounded-xl p-6 shadow-lg">
                    <h3 class="text-xl font-semibold text-frappe-text mb-4">{{ __('messages.bookings_over_time') }}</h3>
                    <div class="h-80">
                        <canvas id="bookingsChart"></canvas>
                    </div>
                </div>

                <!-- Revenue Chart -->
                <div class="frosted-card rounded-xl p-6 shadow-lg">
                    <h3 class="text-xl font-semibold text-frappe-text mb-4">{{ __('messages.revenue_over_time') }}</h3>
                    <div class="h-80">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Most Booked Services -->
            <div class="frosted-card rounded-xl p-6 shadow-lg">
                <h3 class="text-xl font-semibold text-frappe-text mb-6">{{ __('messages.most_booked_services') }}</h3>
                @if($mostBookedServices->count() > 0)
                    <div class="space-y-4">
                        @foreach($mostBookedServices as $service)
                            <div class="flex items-center justify-between p-4 bg-frappe-surface0/50 rounded-lg">
                                <div class="flex-1">
                                    <h4 class="font-medium text-frappe-text">{{ $service->name }}</h4>
                                    <p class="text-sm text-frappe-subtext1">{{ $service->description }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-bold text-frappe-blue">{{ $service->bookings_count }}</p>
                                    <p class="text-sm text-frappe-subtext1">{{ __('messages.bookings') }}</p>
                                </div>
                                <div class="ml-4 text-right">
                                    <p class="font-medium text-frappe-green">${{ number_format($service->price, 2) }}</p>
                                    <p class="text-sm text-frappe-subtext1">{{ __('messages.per_service') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <x-heroicon-o-chart-bar class="w-16 h-16 text-frappe-overlay0 mx-auto mb-4" />
                        <p class="text-frappe-subtext1">{{ __('messages.no_services_booked') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Chart configuration
        const chartColors = {
            primary: 'rgba(139, 57, 239, 0.8)',
            primaryBorder: 'rgba(139, 57, 239, 1)',
            secondary: 'rgba(34, 197, 94, 0.8)',
            secondaryBorder: 'rgba(34, 197, 94, 1)',
            background: 'rgba(49, 50, 68, 0.1)',
            text: '#cdd6f4'
        };

        Chart.defaults.color = chartColors.text;
        Chart.defaults.borderColor = 'rgba(127, 132, 156, 0.2)';

        // Initial chart data
        let chartData = @json($chartData);

        // Bookings Chart
        const bookingsCtx = document.getElementById('bookingsChart').getContext('2d');
        const bookingsChart = new Chart(bookingsCtx, {
            type: 'line',
            data: {
                labels: chartData.bookings.labels,
                datasets: [{
                    label: 'Bookings',
                    data: chartData.bookings.data,
                    borderColor: chartColors.primaryBorder,
                    backgroundColor: chartColors.primary,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: chartData.revenue.labels,
                datasets: [{
                    label: 'Revenue',
                    data: chartData.revenue.data,
                    borderColor: chartColors.secondaryBorder,
                    backgroundColor: chartColors.secondary,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toFixed(2);
                            }
                        }
                    }
                }
            }
        });

        // Period selector change handler
        function updateCharts() {
            const period = document.getElementById('period').value;
            const businessId = document.getElementById('business').value;
            
            // Show loading state
            bookingsChart.data.labels = ['Loading...'];
            bookingsChart.data.datasets[0].data = [0];
            revenueChart.data.labels = ['Loading...'];
            revenueChart.data.datasets[0].data = [0];
            bookingsChart.update();
            revenueChart.update();

            // Fetch new data
            fetch(`{{ route('statistics.data') }}?period=${period}&business_id=${businessId}`)
                .then(response => response.json())
                .then(data => {
                    // Update bookings chart
                    bookingsChart.data.labels = data.bookings.labels;
                    bookingsChart.data.datasets[0].data = data.bookings.data;
                    bookingsChart.update();

                    // Update revenue chart
                    revenueChart.data.labels = data.revenue.labels;
                    revenueChart.data.datasets[0].data = data.revenue.data;
                    revenueChart.update();
                })
                .catch(error => {
                    console.error('Error fetching chart data:', error);
                });
        }

        // Attach event listeners
        document.getElementById('period').addEventListener('change', updateCharts);
        document.getElementById('business').addEventListener('change', function() {
            // When business changes, reload the page to update all statistics
            const businessId = this.value;
            const period = document.getElementById('period').value;
            window.location.href = `{{ route('statistics.index') }}?business_id=${businessId}&period=${period}`;
        });
    </script>
</x-app-layout>
