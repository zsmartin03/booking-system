<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-frappe-lavender leading-tight">
            {{ __('Book a Service') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-6xl mx-auto">
        <!-- Service Selection -->
        <div class="mb-6 p-4 bg-frappe-surface0 rounded shadow">
            <form method="GET" action="{{ route('bookings.create') }}" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[200px]">
                    <x-input-label for="business_id" :value="__('Business')" />
                    <select name="business_id" id="business_id"
                        class="block w-full mt-1 bg-frappe-surface0 border-frappe-surface1 text-frappe-text rounded"
                        onchange="this.form.submit()">
                        <option value="">{{ __('Select Business') }}</option>
                        @foreach ($businesses as $business)
                            <option value="{{ $business->id }}" @selected(request('business_id') == $business->id)>{{ $business->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if ($selectedBusiness)
                    <div class="flex-1 min-w-[200px]">
                        <x-input-label for="service_id" :value="__('Service')" />
                        <select name="service_id" id="service_id"
                            class="block w-full mt-1 bg-frappe-surface0 border-frappe-surface1 text-frappe-text rounded"
                            onchange="this.form.submit()">
                            <option value="">{{ __('Select Service') }}</option>
                            @foreach ($services as $service)
                                <option value="{{ $service->id }}" @selected(request('service_id') == $service->id)>
                                    {{ $service->name }} ({{ $service->duration }}min - ${{ $service->price }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </form>
        </div>

        @if ($selectedService)
            <!-- Service Info -->
            <div class="mb-6 p-4 bg-frappe-surface0 rounded shadow">
                <h3 class="text-lg font-semibold text-frappe-text mb-2">{{ $selectedService->name }}</h3>
                <p class="text-frappe-subtext1">Duration: {{ $selectedService->duration }} minutes</p>
                <p class="text-frappe-subtext1">Price: ${{ number_format($selectedService->price / 100, 2) }}</p>
            </div>

            <!-- Interval-based Timetable -->
            <div class="bg-frappe-surface0 rounded shadow p-4" x-data="intervalTimetable()" x-init="loadSchedule()">
                <div class="flex justify-between items-center mb-4">
                    <button @click="previousWeek()"
                        class="px-4 py-2 bg-frappe-blue text-white rounded hover:bg-frappe-sapphire transition">
                        ← Previous Week
                    </button>
                    <h3 class="text-lg font-semibold text-frappe-text" x-text="weekLabel"></h3>
                    <button @click="nextWeek()"
                        class="px-4 py-2 bg-frappe-blue text-white rounded hover:bg-frappe-sapphire transition">
                        Next Week →
                    </button>
                </div>

                <!-- Legend -->
                <div class="mb-4 flex gap-4 text-sm">
                    <div class="flex items-center gap-1">
                        <div class="w-4 h-4 bg-green-500 rounded"></div>
                        <span>Available</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <div class="w-4 h-4 bg-blue-500 rounded"></div>
                        <span>Selected</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <div class="w-4 h-4 bg-red-500 rounded"></div>
                        <span>Booked</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <div class="w-4 h-4 bg-frappe-surface1 border border-frappe-surface2 rounded"></div>
                        <span>Working Time</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <div class="w-4 h-4 bg-frappe-surface0 border border-frappe-surface1 rounded"></div>
                        <span>Not Available</span>
                    </div>
                </div>

                <!-- Days Grid -->
                <div class="grid grid-cols-7 gap-2 mb-6">
                    <template x-for="(day, dayIndex) in days" :key="day.dateString">
                        <div>
                            <!-- Day Header -->
                            <div class="text-center font-medium text-frappe-text p-2 bg-frappe-surface1 rounded mb-2">
                                <div x-text="day.name" class="text-sm"></div>
                                <div class="text-xs text-frappe-subtext1" x-text="day.date"></div>
                            </div>

                            <!-- Time Grid for this day -->
                            <div class="relative">
                                <!-- Time labels (only show on first column) -->
                                <template x-if="dayIndex === 0">
                                    <div class="absolute -left-12 top-0 h-full">
                                        <template x-for="hour in timeLabels" :key="hour">
                                            <div class="absolute text-xs text-frappe-subtext1 -ml-2 w-10 text-right"
                                                :style="`top: ${(hour - startHour) * slotsPerHour * slotHeight}px`"
                                                x-text="hour + ':00'">
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <!-- 10-minute interval slots -->
                                <div class="space-y-0">
                                    <template x-for="(slot, slotIndex) in getDaySlots(day.dateString)"
                                        :key="`${day.dateString}-${slotIndex}`">
                                        <div class="cursor-pointer transition-all hover:opacity-80"
                                            :class="getSlotClasses(slot, day.dateString, slotIndex)"
                                            :style="`height: ${slotHeight}px`"
                                            @click="handleSlotClick(slot, day.dateString, slotIndex)"
                                            :title="getSlotTooltip(slot, day.dateString, slotIndex)">


                                            <!-- Show booking info for booked/selected slots -->
                                            <template
                                                x-if="slot.type === 'booked' && slotIndex % Math.ceil(serviceDuration / intervalMinutes) === 0">
                                                <div class="text-xs text-white p-1 truncate">
                                                    <div x-text="slot.employeeName"></div>
                                                </div>
                                            </template>

                                            <template
                                                x-if="slot.type === 'selected' && slotIndex % Math.ceil(serviceDuration / intervalMinutes) === 0">
                                                <div class="text-xs text-white p-1 truncate">
                                                    <div x-text="slot.employeeName"></div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Loading State -->
                <div x-show="loading" class="text-center py-8">
                    <div class="text-frappe-subtext1">Loading schedule...</div>
                </div>

                <div x-show="selectedSlot" class="mt-6 p-4 bg-frappe-blue/10 border border-frappe-blue rounded">
                    <h4 class="font-semibold text-frappe-text mb-3">Confirm Your Booking</h4>
                    <div class="mb-4">
                        <span class="text-frappe-subtext1">Selected Time: </span>
                        <span class="font-medium text-frappe-text" x-text="formatSelectedTime()"></span>
                    </div>

                    <!-- Employee Selection (new) -->
                    <div class="mb-4">
                        <span class="text-frappe-subtext1">Choose Employee: </span>
                        <div class="mt-2 space-y-2">
                            <template x-for="employee in availableEmployees" :key="employee.id">
                                <div class="p-3 border rounded cursor-pointer transition-colors"
                                    :class="selectedEmployeeId === employee.id ? 'bg-frappe-blue/20 border-frappe-blue' :
                                        'bg-frappe-surface0 border-frappe-surface1 hover:bg-frappe-surface1'"
                                    @click="selectEmployee(employee.id)">
                                    <div class="font-medium text-frappe-text" x-text="employee.name"></div>
                                    <div class="text-sm text-frappe-subtext1"
                                        x-text="employee.bio || 'No bio available'"></div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('bookings.store') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="business_id" value="{{ $selectedBusiness->id }}">
                        <input type="hidden" name="service_id" value="{{ $selectedService->id }}">
                        <input type="hidden" name="employee_id" :value="selectedEmployeeId">
                        <input type="hidden" name="start_time" :value="selectedSlot?.datetime">

                        <div>
                            <x-input-label for="notes" :value="__('Notes (optional)')" />
                            <textarea name="notes" id="notes" rows="3"
                                class="block w-full mt-1 bg-frappe-surface0 border-frappe-surface1 text-frappe-text rounded"
                                placeholder="Any special requests or notes..."></textarea>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit"
                                class="px-6 py-2 bg-frappe-green text-white rounded hover:bg-frappe-teal transition"
                                :disabled="!selectedEmployeeId">
                                Confirm Booking
                            </button>
                            <button type="button" @click="clearSelection()"
                                class="px-6 py-2 bg-frappe-surface2 text-frappe-text rounded hover:bg-frappe-surface1 transition">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <script>
                function intervalTimetable() {
                    return {
                        schedule: {},
                        days: [],
                        weekStart: null,
                        weekLabel: '',
                        selectedSlot: null,
                        selectedSlotPosition: null,
                        availableEmployees: [], // New array to hold available employees
                        selectedEmployeeId: null, // Track selected employee
                        loading: false,
                        startHour: 8,
                        endHour: 18,
                        intervalMinutes: 1,
                        slotHeight: 1, // pixels per 10-minute slot
                        serviceDuration: {{ $selectedService->duration }},

                        get slotsPerHour() {
                            return 60 / this.intervalMinutes; // 6 slots per hour
                        },

                        get totalSlots() {
                            return (this.endHour - this.startHour) * this.slotsPerHour;
                        },

                        get serviceSlotCount() {
                            return Math.ceil(this.serviceDuration / this.intervalMinutes);
                        },

                        init() {
                            const today = new Date();
                            const monday = new Date(today);
                            monday.setDate(today.getDate() - today.getDay() + 1);
                            this.weekStart = monday;
                            this.generateDays();
                        },

                        generateDays() {
                            this.days = [];
                            const dayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

                            for (let i = 0; i < 7; i++) {
                                const date = new Date(this.weekStart);
                                date.setDate(this.weekStart.getDate() + i);

                                this.days.push({
                                    name: dayNames[i],
                                    date: date.toLocaleDateString('en-GB', {
                                        day: '2-digit',
                                        month: '2-digit'
                                    }),
                                    dateString: date.toISOString().split('T')[0]
                                });
                            }

                            this.weekLabel = this.weekStart.toLocaleDateString('en-GB', {
                                    day: 'numeric',
                                    month: 'short'
                                }) + ' - ' +
                                new Date(this.weekStart.getTime() + 6 * 24 * 60 * 60 * 1000).toLocaleDateString('en-GB', {
                                    day: 'numeric',
                                    month: 'short',
                                    year: 'numeric'
                                });
                        },

                        get timeLabels() {
                            const labels = [];
                            for (let hour = this.startHour; hour <= this.endHour; hour++) {
                                labels.push(hour);
                            }
                            return labels;
                        },

                        async loadSchedule() {
                            this.loading = true;
                            this.clearSelection();

                            try {
                                const weekStartString = this.weekStart.toISOString().split('T')[0];
                                const url =
                                    `{{ route('booking-slots') }}?service_id={{ $selectedService->id }}&week_start=${weekStartString}`;

                                const response = await fetch(url);
                                const data = await response.json();

                                this.schedule = this.transformToIntervals(data);
                            } catch (error) {
                                console.error('Error loading schedule:', error);
                                this.schedule = {};
                            } finally {
                                this.loading = false;
                            }
                        },

                        transformToIntervals(data) {
                            const schedule = {};

                            for (const [dateString, slots] of Object.entries(data)) {
                                // Create array of all 10-minute intervals for this day
                                const daySlots = [];

                                for (let i = 0; i < this.totalSlots; i++) {
                                    const slotHour = this.startHour + Math.floor(i / this.slotsPerHour);
                                    const slotMinute = (i % this.slotsPerHour) * this.intervalMinutes;

                                    daySlots.push({
                                        time: `${slotHour.toString().padStart(2, '0')}:${slotMinute.toString().padStart(2, '0')}`,
                                        type: 'not_available', // default
                                        employeeIds: [], // Changed to array for multiple employees
                                        employeeData: {}, // Store employee details keyed by ID
                                        datetime: null,
                                        available: false
                                    });
                                }

                                // Mark working periods and availability
                                if (slots && slots.length > 0) {
                                    // Group slots by time instead of employee
                                    const timeSlots = {};
                                    slots.forEach(slot => {
                                        const timeKey = new Date(slot.time).toISOString();
                                        if (!timeSlots[timeKey]) {
                                            timeSlots[timeKey] = [];
                                        }
                                        timeSlots[timeKey].push(slot);
                                    });

                                    // Process each time slot
                                    Object.entries(timeSlots).forEach(([timeKey, timeSlotList]) => {
                                        const slotTime = new Date(timeKey);
                                        const slotIndex = this.getSlotIndex(slotTime.getHours(), slotTime.getMinutes());

                                        if (slotIndex >= 0 && slotIndex < daySlots.length) {
                                            const slotsNeeded = this.serviceSlotCount;

                                            // Check if all slots are available for the duration
                                            let allAvailable = true;
                                            for (let i = 0; i < slotsNeeded && (slotIndex + i) < daySlots.length; i++) {
                                                if (daySlots[slotIndex + i].type === 'booked') {
                                                    allAvailable = false;
                                                    break;
                                                }
                                            }

                                            if (allAvailable) {
                                                // Collect available employees for this slot
                                                const availableEmployees = timeSlotList.filter(slot => slot.available);

                                                if (availableEmployees.length > 0) {
                                                    // Mark slots as available with multiple employees
                                                    for (let i = 0; i < slotsNeeded && (slotIndex + i) < daySlots
                                                        .length; i++) {
                                                        const currentSlot = daySlots[slotIndex + i];
                                                        currentSlot.type = 'available';
                                                        currentSlot.datetime = timeSlotList[0].time;
                                                        currentSlot.available = true;

                                                        // Store all employee data
                                                        availableEmployees.forEach(employee => {
                                                            if (!currentSlot.employeeIds.includes(employee
                                                                    .employee_id)) {
                                                                currentSlot.employeeIds.push(employee.employee_id);
                                                                currentSlot.employeeData[employee.employee_id] = {
                                                                    id: employee.employee_id,
                                                                    name: employee.employee_name,
                                                                    bio: employee.employee_bio || ''
                                                                };
                                                            }
                                                        });
                                                    }
                                                }
                                            } else {
                                                // Mark as booked
                                                for (let i = 0; i < slotsNeeded && (slotIndex + i) < daySlots.length; i++) {
                                                    daySlots[slotIndex + i].type = 'booked';
                                                    daySlots[slotIndex + i].available = false;
                                                }
                                            }
                                        }
                                    });

                                    // Mark working hours background (where no specific slot data exists)
                                    const firstSlot = slots[0];
                                    const lastSlot = slots[slots.length - 1];
                                    if (firstSlot && lastSlot) {
                                        const startTime = new Date(firstSlot.time);
                                        const endTime = new Date(lastSlot.time);
                                        endTime.setMinutes(endTime.getMinutes() + this.serviceDuration);

                                        const startIndex = this.getSlotIndex(startTime.getHours(), startTime.getMinutes());
                                        const endIndex = this.getSlotIndex(endTime.getHours(), endTime.getMinutes());

                                        for (let i = startIndex; i <= endIndex && i < daySlots.length; i++) {
                                            if (daySlots[i].type === 'not_available') {
                                                daySlots[i].type = 'working';
                                            }
                                        }
                                    }
                                }

                                schedule[dateString] = daySlots;
                            }

                            return schedule;
                        },

                        getSlotIndex(hour, minute) {
                            const hourOffset = hour - this.startHour;
                            const slotInHour = Math.floor(minute / this.intervalMinutes);
                            return (hourOffset * this.slotsPerHour) + slotInHour;
                        },

                        getDaySlots(dateString) {
                            return this.schedule[dateString] || [];
                        },

                        getSlotClasses(slot, dateString, slotIndex) {
                            const classes = [];

                            switch (slot.type) {
                                case 'available':
                                    classes.push('bg-green-400', 'text-white');
                                    break;
                                case 'selected':
                                    classes.push('bg-blue-400', 'text-white');
                                    break;
                                case 'booked':
                                    classes.push('bg-red-400', 'text-white');
                                    break;
                                case 'working':
                                    classes.push('bg-frappe-surface1', 'text-frappe-subtext1');
                                    break;
                                default:
                                    classes.push('bg-frappe-surface0', 'text-frappe-subtext1');
                            }

                            return classes.join(' ');
                        },

                        getSlotTooltip(slot, dateString, slotIndex) {
                            if (slot.type === 'available') {
                                return `Available at ${slot.time}`;
                            } else if (slot.type === 'booked') {
                                return `Booked at ${slot.time}`;
                            } else if (slot.type === 'selected') {
                                return `Selected at ${slot.time}`;
                            }
                            return `${slot.time} - ${slot.type}`;
                        },

                        handleSlotClick(slot, dateString, slotIndex) {
                            if (slot.type !== 'available') return;

                            // Check if we can fit the full service duration
                            const slotsNeeded = this.serviceSlotCount;
                            const daySlots = this.getDaySlots(dateString);

                            // Check if all required slots are available
                            for (let i = 0; i < slotsNeeded; i++) {
                                const checkIndex = slotIndex + i;
                                if (checkIndex >= daySlots.length || daySlots[checkIndex].type !== 'available') {
                                    return; // Can't book here
                                }
                            }

                            this.clearSelection();

                            // Mark slots as selected
                            for (let i = 0; i < slotsNeeded; i++) {
                                const selectIndex = slotIndex + i;
                                if (selectIndex < daySlots.length) {
                                    daySlots[selectIndex].type = 'selected';
                                }
                            }

                            this.selectedSlot = slot;
                            this.selectedSlotPosition = {
                                dateString,
                                slotIndex
                            };

                            // Populate available employees from the first slot
                            this.availableEmployees = Object.values(slot.employeeData);
                            this.selectedEmployeeId = null; // Reset selection
                        },

                        // New method to select an employee
                        selectEmployee(employeeId) {
                            this.selectedEmployeeId = employeeId;
                        },

                        clearSelection() {
                            if (this.selectedSlotPosition) {
                                const {
                                    dateString,
                                    slotIndex
                                } = this.selectedSlotPosition;
                                const daySlots = this.getDaySlots(dateString);
                                const slotsNeeded = this.serviceSlotCount;

                                // Restore original state
                                for (let i = 0; i < slotsNeeded; i++) {
                                    const restoreIndex = slotIndex + i;
                                    if (restoreIndex < daySlots.length && daySlots[restoreIndex].type === 'selected') {
                                        daySlots[restoreIndex].type = 'available';
                                    }
                                }
                            }

                            this.selectedSlot = null;
                            this.selectedSlotPosition = null;
                            this.availableEmployees = [];
                            this.selectedEmployeeId = null;
                        },

                        formatSelectedTime() {
                            if (!this.selectedSlot) return '';
                            const date = new Date(this.selectedSlot.datetime);
                            return date.toLocaleDateString('en-GB', {
                                weekday: 'long',
                                day: 'numeric',
                                month: 'long',
                                year: 'numeric'
                            }) + ' at ' + this.selectedSlot.time;
                        },

                        previousWeek() {
                            this.weekStart.setDate(this.weekStart.getDate() - 7);
                            this.generateDays();
                            this.loadSchedule();
                        },

                        nextWeek() {
                            this.weekStart.setDate(this.weekStart.getDate() + 7);
                            this.generateDays();
                            this.loadSchedule();
                        }
                    }
                }
            </script>
        @endif
    </div>
</x-app-layout>
