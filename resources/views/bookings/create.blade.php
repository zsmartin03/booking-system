<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-frappe-lavender leading-tight">
            {{ __('Book a Service') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-6xl mx-auto">
        <!-- Service Selection -->
        <div class="mb-6 p-4 frosted-card rounded-xl shadow-lg">
            <form method="GET" action="{{ route('bookings.create') }}" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[200px]">
                    <x-input-label for="business_id" :value="__('Business')" />
                    <select name="business_id" id="business_id"
                        class="block w-full mt-1 bg-frappe-surface0/50 border-frappe-surface1/30 text-frappe-text rounded-md shadow-sm backdrop-blur-sm focus:border-frappe-blue focus:ring-frappe-blue/50"
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
                            class="block w-full mt-1 bg-frappe-surface0/50 border-frappe-surface1/30 text-frappe-text rounded-md shadow-sm backdrop-blur-sm focus:border-frappe-blue focus:ring-frappe-blue/50"
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
            <div class="mb-6 p-4 frosted-card rounded-xl shadow-lg">
                <h3 class="text-lg font-semibold text-frappe-text mb-2">{{ $selectedService->name }}</h3>
                <p class="text-frappe-subtext1">Duration: {{ $selectedService->duration }} minutes</p>
                <p class="text-frappe-subtext1">Price: ${{ number_format($selectedService->price / 100, 2) }}</p>
            </div>

            <!-- Interval-based Timetable -->
            <div class="frosted-card rounded-xl shadow-lg p-4" x-data="intervalTimetable()" x-init="loadSchedule()">
                <div class="flex justify-between items-center mb-4">
                    <button @click="previousWeek()"
                        class="frosted-button px-4 py-2 text-white rounded-lg hover:transform hover:-translate-y-1 transition-all">
                        ← Previous Week
                    </button>
                    <h3 class="text-lg font-semibold text-frappe-text" x-text="weekLabel"></h3>
                    <button @click="nextWeek()"
                        class="frosted-button px-4 py-2 text-white rounded-lg hover:transform hover:-translate-y-1 transition-all">
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

                <div x-show="selectedSlot"
                    class="mt-6 p-4 bg-frappe-blue/20 border border-frappe-blue/30 rounded-lg backdrop-blur-sm">
                    <h4 class="font-semibold text-frappe-text mb-3">Confirm Your Booking</h4>
                    <div class="mb-4">
                        <span class="text-frappe-subtext1">Time: </span>
                        <span class="font-medium text-frappe-text" x-text="formatSelectedTime()"></span>
                    </div>

                    <!-- Error Messages -->
                    @if ($errors->any())
                        <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Employee Selection (new) -->
                    <div class="mb-4">
                        <span class="text-frappe-subtext1">Choose Employee: </span>
                        <div class="mt-2 space-y-2">
                            <template x-for="employee in availableEmployees" :key="employee.id">
                                <div class="p-3 border rounded-lg cursor-pointer transition-all relative backdrop-blur-sm"
                                    :class="selectedEmployeeId === employee.id ?
                                        'bg-frappe-blue/30 border-frappe-blue shadow-lg' :
                                        'bg-frappe-surface0/50 border-frappe-surface1/30 hover:bg-frappe-surface1/50'"
                                    @click="selectEmployee(employee.id)">
                                    <div class="font-medium text-frappe-text" x-text="employee.name"></div>
                                    <div class="text-sm text-frappe-subtext1"
                                        x-text="employee.bio || 'No bio available'"></div>
                                    <div x-show="employee.available === false"
                                        class="absolute top-2 right-2 bg-red-500 text-white text-xs px-2 py-1 rounded">
                                        Unavailable
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('bookings.store') }}" class="space-y-4" x-ref="bookingForm">
                        @csrf
                        <input type="hidden" name="business_id" value="{{ $selectedBusiness->id }}">
                        <input type="hidden" name="service_id" value="{{ $selectedService->id }}">
                        <!-- Fix: Use proper binding for form submission -->
                        <input type="hidden" name="employee_id" x-bind:value="selectedEmployeeId || ''">
                        <input type="hidden" name="start_time"
                            x-bind:value="selectedSlot ? selectedSlot.datetime : ''">

                        <div>
                            <x-input-label for="notes" :value="__('Notes (optional)')" />
                            <textarea name="notes" id="notes" rows="3"
                                class="block w-full mt-1 bg-frappe-surface0/50 border-frappe-surface1/30 text-frappe-text rounded-md shadow-sm backdrop-blur-sm focus:border-frappe-blue focus:ring-frappe-blue/50"
                                placeholder="Any special requests or notes...">{{ old('notes') }}</textarea>
                        </div>

                        <div class="flex gap-2">
                            <button type="button" @click="submitBooking()"
                                class="frosted-button px-6 py-3 text-white rounded-lg hover:transform hover:-translate-y-1 transition-all"
                                :disabled="!selectedEmployeeId">
                                Confirm Booking
                            </button>
                            <button type="button" @click="clearSelection()"
                                class="px-6 py-3 bg-frappe-surface0/50 border border-frappe-surface1/30 text-frappe-text rounded-lg hover:bg-frappe-surface0/70 transition-all backdrop-blur-sm">
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
                        availableEmployees: [],
                        selectedEmployeeId: null,
                        loading: false,
                        startHour: 8,
                        endHour: 18,
                        intervalMinutes: 5,
                        slotHeight: 5,
                        serviceDuration: {{ $selectedService->duration }},

                        get slotsPerHour() {
                            return 60 / this.intervalMinutes;
                        },

                        get totalSlots() {
                            return (this.endHour - this.startHour) * this.slotsPerHour;
                        },

                        get serviceSlotCount() {
                            return Math.ceil(this.serviceDuration / this.intervalMinutes);
                        },

                        getSlotIndex(hour, minute) {
                            // Round to nearest 5 minutes
                            const roundedMinute = Math.round(minute / this.intervalMinutes) * this.intervalMinutes;
                            const adjustedHour = hour + (roundedMinute >= 60 ? 1 : 0);
                            const adjustedMinute = roundedMinute % 60;

                            const hourOffset = adjustedHour - this.startHour;
                            const slotInHour = Math.floor(adjustedMinute / this.intervalMinutes);
                            return (hourOffset * this.slotsPerHour) + slotInHour;
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
                                const daySlots = [];

                                for (let i = 0; i < this.totalSlots; i++) {
                                    const slotHour = this.startHour + Math.floor(i / this.slotsPerHour);
                                    const slotMinute = (i % this.slotsPerHour) * this.intervalMinutes;

                                    daySlots.push({
                                        time: `${slotHour.toString().padStart(2, '0')}:${slotMinute.toString().padStart(2, '0')}`,
                                        type: 'not_available',
                                        employeeIds: [],
                                        employeeData: {},
                                        datetime: null,
                                        available: false
                                    });
                                }

                                if (slots && slots.length > 0) {
                                    const timeSlots = {};
                                    slots.forEach(slot => {
                                        const slotDate = new Date(slot.time);
                                        const minutes = slotDate.getMinutes();
                                        const roundedMinutes = Math.round(minutes / 5) * 5;
                                        slotDate.setMinutes(roundedMinutes === 60 ? 0 : roundedMinutes);
                                        slotDate.setSeconds(0);
                                        if (roundedMinutes === 60) {
                                            slotDate.setHours(slotDate.getHours() + 1);
                                        }
                                        const timeKey = slotDate.toISOString();

                                        if (!timeSlots[timeKey]) {
                                            timeSlots[timeKey] = [];
                                        }
                                        timeSlots[timeKey].push({
                                            ...slot,
                                            time: slotDate.toISOString()
                                        });
                                    });

                                    Object.entries(timeSlots).forEach(([timeKey, timeSlotList]) => {
                                        const slotTime = new Date(timeKey);
                                        const slotIndex = this.getSlotIndex(slotTime.getHours(), slotTime.getMinutes());

                                        if (slotIndex >= 0 && slotIndex < daySlots.length) {
                                            const slotsNeeded = this.serviceSlotCount;

                                            const availableEmployees = timeSlotList.filter(slot => slot.available);

                                            if (availableEmployees.length > 0) {
                                                for (let i = 0; i < slotsNeeded && (slotIndex + i) < daySlots.length; i++) {
                                                    const currentSlot = daySlots[slotIndex + i];
                                                    currentSlot.type = 'available';
                                                    currentSlot.datetime = timeKey;
                                                    currentSlot.available = true;

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
                                            } else {
                                                const allEmployees = timeSlotList;
                                                for (let i = 0; i < slotsNeeded && (slotIndex + i) < daySlots.length; i++) {
                                                    daySlots[slotIndex + i].type = 'booked';
                                                    daySlots[slotIndex + i].available = false;
                                                    daySlots[slotIndex + i].datetime = timeKey;
                                                    daySlots[slotIndex + i].bookedEmployees = allEmployees.map(e => e
                                                        .employee_name).join(', ');
                                                }
                                            }
                                        }
                                    });

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
                            if (!slot.time) return '';

                            if (slot.type === 'available') {
                                const employeeCount = Object.keys(slot.employeeData).length;
                                let tooltip = `Available at ${slot.time}`;
                                if (employeeCount > 0) {
                                    tooltip += ` (${employeeCount} employees available)`;

                                    const employeeNames = Object.values(slot.employeeData).map(emp => emp.name).join(', ');
                                    if (employeeNames) {
                                        tooltip += `: ${employeeNames}`;
                                    }
                                }
                                return tooltip;
                            } else if (slot.type === 'booked') {
                                return `Booked at ${slot.time}${slot.bookedEmployees ? ' by ' + slot.bookedEmployees : ''}`;
                            } else if (slot.type === 'selected') {
                                return `Selected: ${slot.time}`;
                            } else if (slot.type === 'working') {
                                return `Working hours: ${slot.time}`;
                            }
                            return `${slot.time}`;
                        },

                        handleSlotClick(slot, dateString, slotIndex) {
                            if (slot.type !== 'available') return;

                            const slotsNeeded = this.serviceSlotCount;
                            const daySlots = this.getDaySlots(dateString);

                            for (let i = 0; i < slotsNeeded; i++) {
                                const checkIndex = slotIndex + i;
                                if (checkIndex >= daySlots.length || daySlots[checkIndex].type !== 'available') {
                                    return;
                                }
                            }

                            this.clearSelection();

                            const originalSlotTypes = [];
                            for (let i = 0; i < slotsNeeded; i++) {
                                const selectIndex = slotIndex + i;
                                if (selectIndex < daySlots.length) {
                                    originalSlotTypes.push({
                                        index: selectIndex,
                                        type: daySlots[selectIndex].type
                                    });
                                    daySlots[selectIndex].type = 'selected';
                                }
                            }

                            this.selectedSlot = slot;
                            this.selectedSlotPosition = {
                                dateString,
                                slotIndex,
                                originalSlotTypes
                            };

                            this.availableEmployees = Object.values(slot.employeeData);
                            this.selectedEmployeeId = null;
                        },

                        selectEmployee(employeeId) {
                            this.selectedEmployeeId = employeeId;
                        },

                        clearSelection() {
                            if (this.selectedSlotPosition) {
                                const {
                                    dateString,
                                    slotIndex,
                                    originalSlotTypes
                                } = this.selectedSlotPosition;
                                const daySlots = this.getDaySlots(dateString);

                                if (originalSlotTypes && originalSlotTypes.length > 0) {
                                    originalSlotTypes.forEach((item, index) => {
                                        const restoreIndex = slotIndex + index;
                                        if (restoreIndex < daySlots.length) {
                                            daySlots[restoreIndex].type = item.type;
                                        }
                                    });
                                } else {
                                    const slotsNeeded = this.serviceSlotCount;
                                    for (let i = 0; i < slotsNeeded; i++) {
                                        const restoreIndex = slotIndex + i;
                                        if (restoreIndex < daySlots.length && daySlots[restoreIndex].type === 'selected') {
                                            daySlots[restoreIndex].type = 'available';
                                        }
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

                            const endDate = new Date(date);
                            endDate.setMinutes(endDate.getMinutes() + this.serviceDuration);

                            const formattedDate = date.toLocaleDateString('en-GB', {
                                weekday: 'long',
                                day: 'numeric',
                                month: 'long',
                                year: 'numeric'
                            });

                            const startTime = date.toLocaleTimeString('en-GB', {
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: false
                            });

                            const endTime = endDate.toLocaleTimeString('en-GB', {
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: false
                            });

                            return `${formattedDate} from ${startTime} to ${endTime}`;
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
                        },

                        submitBooking() {
                            if (!this.selectedEmployeeId || !this.selectedSlot) {
                                alert('Please select an employee and time slot first.');
                                return;
                            }

                            let formattedDate = '';
                            if (this.selectedSlot.datetime) {
                                const date = new Date(this.selectedSlot.datetime);

                                if (isNaN(date.getTime())) {
                                    alert('Invalid date selected. Please try again.');
                                    return;
                                }

                                formattedDate = date.getFullYear() +
                                    '-' + String(date.getMonth() + 1).padStart(2, '0') +
                                    '-' + String(date.getDate()).padStart(2, '0') +
                                    'T' + String(date.getHours()).padStart(2, '0') +
                                    ':' + String(date.getMinutes()).padStart(2, '0');
                            }

                            const employeeInput = this.$refs.bookingForm.querySelector('input[name="employee_id"]');
                            const startTimeInput = this.$refs.bookingForm.querySelector('input[name="start_time"]');

                            if (employeeInput) employeeInput.value = this.selectedEmployeeId;
                            if (startTimeInput) startTimeInput.value = formattedDate;

                            console.log('Submitting booking with:');
                            console.log('Employee ID:', this.selectedEmployeeId);
                            console.log('Start Time:', formattedDate);
                            console.log('Selected Slot:', this.selectedSlot);

                            this.$refs.bookingForm.submit();
                        }
                    }
                }
            </script>
        @endif
    </div>
</x-app-layout>
