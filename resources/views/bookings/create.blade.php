<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-frappe-lavender leading-tight">
            {{ __('messages.book_service') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Business Status Messages -->
        @if ($selectedBusiness && $businessSettings)
            @if ($businessSettings['holiday_mode'])
                <div class="mb-6 p-4 bg-orange-500/20 border border-orange-400/30 text-orange-300 rounded-lg">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
                        <span class="font-semibold">{{ __('messages.holiday_mode') }}</span>
                    </div>
                    <p class="mt-1 text-sm">{{ __('messages.this_business_not_accepting_bookings') }}</p>
                </div>
            @endif

            @if ($businessSettings['maintenance_mode'])
                <div class="mb-6 p-4 bg-red-500/20 border border-red-400/30 text-red-300 rounded-lg">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-wrench-screwdriver class="w-5 h-5" />
                        <span class="font-semibold">{{ __('messages.maintenance_mode') }}</span>
                    </div>
                    <p class="mt-1 text-sm">
                        {{ __('messages.business_under_maintenance') }}</p>
                </div>
            @endif

            @if ($businessSettings['booking_confirmation_required'])
                <div class="mb-6 p-4 bg-blue-500/20 border border-blue-400/30 text-blue-300 rounded-lg">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-clock class="w-5 h-5" />
                        <span class="font-semibold">{{ __('messages.booking_confirmation_required') }}</span>
                    </div>
                    <p class="mt-1 text-sm">{{ __('messages.booking_pending_confirmation') }}
                    </p>
                </div>
            @endif

            @if ($businessSettings['booking_advance_hours'] > 0 || $businessSettings['booking_advance_days'] > 0)
                <div class="mb-6 p-4 bg-frappe-blue/20 border border-frappe-blue/30 text-frappe-blue rounded-lg">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-information-circle class="w-5 h-5" />
                        <span class="font-semibold">{{ __('messages.booking_restrictions') }}</span>
                    </div>
                    <p class="mt-1 text-sm">
                        {{ __('messages.booking_advance_restrictions', [
                            'hours' => $businessSettings['booking_advance_hours'],
                            'days' => $businessSettings['booking_advance_days'],
                        ]) }}
                    </p>
                </div>
            @endif
        @endif

        <!-- Only show service selection and booking form if business is not in holiday or maintenance mode -->
        @if (!$selectedBusiness || (!$businessSettings['holiday_mode'] && !$businessSettings['maintenance_mode']))
            <!-- Service Selection -->
            <div class="mb-6 p-4 sm:p-6 frosted-card rounded-xl shadow-lg">
                <form method="GET" action="{{ route('bookings.create') }}" class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1 min-w-0">
                        <x-input-label for="business_id" :value="__('messages.business')" />
                        <select name="business_id" id="business_id"
                            class="block w-full mt-1 bg-frappe-surface0/50 border-frappe-surface1/30 text-frappe-text rounded-md shadow-sm backdrop-blur-sm focus:border-frappe-blue focus:ring-frappe-blue/50"
                            onchange="this.form.submit()">
                            <option value="">{{ __('messages.select_business') }}</option>
                            @foreach ($businesses as $business)
                                <option value="{{ $business->id }}" @selected(request('business_id') == $business->id)>{{ $business->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if ($selectedBusiness)
                        <div class="flex-1 min-w-0">
                            <x-input-label for="service_id" :value="__('messages.service')" />
                            <select name="service_id" id="service_id"
                                class="block w-full mt-1 bg-frappe-surface0/50 border-frappe-surface1/30 text-frappe-text rounded-md shadow-sm backdrop-blur-sm focus:border-frappe-blue focus:ring-frappe-blue/50"
                                onchange="this.form.submit()">
                                <option value="">{{ __('messages.select_service') }}</option>
                                @foreach ($services as $service)
                                    <option value="{{ $service->id }}" @selected(request('service_id') == $service->id)>
                                        {{ $service->name }} ({{ $service->duration }}min -
                                        {{ \App\Models\Service::formatPrice($service->price, $businessSettings['currency'] ?? 'USD') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </form>
            </div>

            @if ($selectedService)
                <!-- Service Info -->
                <div class="mb-6 p-4 sm:p-6 frosted-card rounded-xl shadow-lg">
                    <h3 class="text-lg font-semibold text-frappe-text mb-2">{{ $selectedService->name }}</h3>
                    <div class="flex flex-col sm:flex-row sm:gap-8 text-frappe-subtext1">
                        <p>{{ __('messages.duration') }}: {{ $selectedService->duration }}
                            {{ __('messages.minutes') }}</p>
                        <p>{{ __('messages.price') }}:
                            {{ \App\Models\Service::formatPrice($selectedService->price, $businessSettings['currency'] ?? 'USD') }}
                        </p>
                    </div>
                </div>

                <!-- Interval-based Timetable -->
                <div class="frosted-card rounded-xl shadow-lg p-4 sm:p-6" x-data="intervalTimetable()"
                    x-init="loadSchedule()">
                    <div class="flex flex-col sm:flex-row justify-between items-center mb-4 gap-4">
                        <button @click="previousWeek()"
                            class="frosted-button px-4 py-2 text-white rounded-lg hover:transform hover:-translate-y-1 transition-all w-full sm:w-auto">
                            ← {{ __('messages.previous_week') }}
                        </button>
                        <h3 class="text-lg font-semibold text-frappe-text text-center" x-text="weekLabel"></h3>
                        <button @click="nextWeek()"
                            class="frosted-button px-4 py-2 text-white rounded-lg hover:transform hover:-translate-y-1 transition-all w-full sm:w-auto">
                            {{ __('messages.next_week') }} →
                        </button>
                    </div>

                    <!-- Legend -->
                    <div class="mb-4 grid grid-cols-2 sm:flex sm:gap-4 gap-2 text-sm">
                        <div class="flex items-center gap-1">
                            <div class="w-4 h-4 bg-green-500 rounded"></div>
                            <span>{{ __('messages.available') }}</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <div class="w-4 h-4 bg-blue-500 rounded"></div>
                            <span>{{ __('messages.selected') }}</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <div class="w-4 h-4 bg-red-500 rounded"></div>
                            <span>{{ __('messages.booked') }}</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <div class="w-4 h-4 bg-frappe-surface1 border border-frappe-surface2 rounded"></div>
                            <span>{{ __('messages.working_time') }}</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <div class="w-4 h-4 bg-frappe-surface0 border border-frappe-surface1 rounded"></div>
                            <span>{{ __('messages.not_available') }}</span>
                        </div>
                    </div>

                    <!-- Days Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-2 mb-6">
                        <template x-for="(day, dayIndex) in days" :key="day.dateString">
                            <div>
                                <!-- Day Header -->
                                <div
                                    class="text-center font-medium text-frappe-text p-2 bg-frappe-surface1 rounded mb-2">
                                    <div x-text="day.name" class="text-sm font-semibold"></div>
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

                                    <!-- 5-minute interval slots -->
                                    <div class="space-y-0">
                                        <template x-for="(slot, slotIndex) in getDaySlots(day.dateString)"
                                            :key="`${day.dateString}-${slotIndex}`">
                                            <div class="cursor-pointer transition-all relative"
                                                :class="getSlotClasses(slot, day.dateString, slotIndex)"
                                                :style="`height: ${slotHeight}px`"
                                                @click="handleSlotClick(slot, day.dateString, slotIndex)"
                                                @mouseenter="handleSlotHover(slot, day.dateString, slotIndex, true)"
                                                @mouseleave="handleSlotHover(slot, day.dateString, slotIndex, false)"
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
                        <div class="text-frappe-subtext1">{{ __('messages.loading_schedule') }}</div>
                    </div>

                    <div x-show="selectedSlot"
                        class="mt-6 p-4 sm:p-6 bg-frappe-blue/20 border border-frappe-blue/30 rounded-lg backdrop-blur-sm">
                        <h4 class="font-semibold text-frappe-text mb-3">{{ __('messages.confirm_your_booking') }}</h4>
                        <div class="mb-4">
                            <span class="text-frappe-subtext1">{{ __('messages.time') }}: </span>
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
                            <span class="text-frappe-subtext1">{{ __('messages.choose_employee') }}: </span>
                            <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <template x-for="employee in availableEmployees" :key="employee.id">
                                    <div class="p-3 border rounded-lg cursor-pointer transition-all relative backdrop-blur-sm"
                                        :class="selectedEmployeeId === employee.id ?
                                            'bg-frappe-blue/30 border-frappe-blue shadow-lg' :
                                            'bg-frappe-surface0/50 border-frappe-surface1/30 hover:bg-frappe-surface1/50'"
                                        @click="selectEmployee(employee.id)">
                                        <div class="font-medium text-frappe-text" x-text="employee.name"></div>
                                        <div class="text-sm text-frappe-subtext1"
                                            x-text="employee.bio || '{{ __('messages.no_bio_available') }}'"></div>
                                        <div x-show="employee.available === false"
                                            class="absolute top-2 right-2 bg-red-500 text-white text-xs px-2 py-1 rounded">
                                            {{ __('messages.unavailable') }}
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('bookings.store') }}" class="space-y-4"
                            x-ref="bookingForm">
                            @csrf
                            <input type="hidden" name="business_id" value="{{ $selectedBusiness->id }}">
                            <input type="hidden" name="service_id" value="{{ $selectedService->id }}">
                            <!-- Fix: Use proper binding for form submission -->
                            <input type="hidden" name="employee_id" x-bind:value="selectedEmployeeId || ''">
                            <input type="hidden" name="start_time"
                                x-bind:value="selectedSlot ? selectedSlot.datetime : ''">

                            <div class="space-y-4">
                                <div>
                                    <x-input-label for="notes" :value="__('messages.notes_optional')" />
                                    <textarea name="notes" id="notes" rows="3"
                                        class="block w-full mt-1 bg-frappe-surface0/50 border-frappe-surface1/30 text-frappe-text rounded-md shadow-sm backdrop-blur-sm focus:border-frappe-blue focus:ring-frappe-blue/50"
                                        placeholder="{{ __('messages.any_special_requests') }}">{{ old('notes') }}</textarea>
                                </div>

                                <div class="flex flex-col sm:flex-row gap-2">
                                    <button type="button" @click="submitBooking()"
                                        class="frosted-button px-6 py-3 text-white rounded-lg hover:transform hover:-translate-y-1 transition-all w-full sm:w-auto"
                                        :disabled="!selectedEmployeeId">
                                        {{ __('messages.confirm_booking') }}
                                    </button>
                                    <button type="button" @click="clearSelection()"
                                        class="px-6 py-3 bg-frappe-surface0/50 border border-frappe-surface1/30 text-frappe-text rounded-lg hover:bg-frappe-surface0/70 transition-all backdrop-blur-sm w-full sm:w-auto">
                                        {{ __('messages.cancel') }}
                                    </button>
                                </div>
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
                            @if ($businessSettings)
                                bookingAdvanceHours: {{ $businessSettings['booking_advance_hours'] }},
                                bookingAdvanceDays: {{ $businessSettings['booking_advance_days'] }},
                            @else
                                bookingAdvanceHours: 0,
                                bookingAdvanceDays: 365,
                            @endif

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

                            isSlotWithinBookingWindow(dateString, hour, minute) {
                                const now = new Date();
                                const slotDateTime = new Date(dateString + 'T' + String(hour).padStart(2, '0') + ':' + String(minute)
                                    .padStart(2, '0'));

                                // Check minimum advance hours
                                const minAdvanceMs = this.bookingAdvanceHours * 60 * 60 * 1000;
                                const timeDiffMs = slotDateTime.getTime() - now.getTime();

                                if (timeDiffMs < minAdvanceMs) {
                                    return false; // Too soon
                                }

                                // Check maximum advance days
                                const maxAdvanceMs = this.bookingAdvanceDays * 24 * 60 * 60 * 1000;

                                if (timeDiffMs > maxAdvanceMs) {
                                    return false; // Too far in advance
                                }

                                return true;
                            },

                            getRestrictionReason(dateString, hour, minute) {
                                const now = new Date();
                                const slotDateTime = new Date(dateString + 'T' + String(hour).padStart(2, '0') + ':' + String(minute)
                                    .padStart(2, '0'));

                                const minAdvanceMs = this.bookingAdvanceHours * 60 * 60 * 1000;
                                const timeDiffMs = slotDateTime.getTime() - now.getTime();

                                if (timeDiffMs < minAdvanceMs) {
                                    return `{{ __('messages.minimum_advance_notice', ['hours' => '']) }}${this.bookingAdvanceHours}`;
                                }

                                const maxAdvanceMs = this.bookingAdvanceDays * 24 * 60 * 60 * 1000;
                                if (timeDiffMs > maxAdvanceMs) {
                                    return `{{ __('messages.cannot_book_advance', ['days' => '']) }}${this.bookingAdvanceDays}`;
                                }

                                return '{{ __('messages.time_slot_not_available') }}';
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
                                const dayNames = [
                                    '{{ __('messages.monday') }}',
                                    '{{ __('messages.tuesday') }}',
                                    '{{ __('messages.wednesday') }}',
                                    '{{ __('messages.thursday') }}',
                                    '{{ __('messages.friday') }}',
                                    '{{ __('messages.saturday') }}',
                                    '{{ __('messages.sunday') }}'
                                ];

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
                                        // Group slots by time
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
                                                timeSlots[timeKey] = slot;
                                            }
                                        });

                                        // Determine working hours range
                                        const workingTimes = new Set();
                                        Object.values(timeSlots).forEach(slot => {
                                            const slotTime = new Date(slot.time);
                                            const slotIndex = this.getSlotIndex(slotTime.getHours(), slotTime.getMinutes());
                                            if (slotIndex >= 0 && slotIndex < daySlots.length) {
                                                workingTimes.add(slotIndex);
                                            }
                                        });

                                        // Mark working hours
                                        workingTimes.forEach(index => {
                                            if (daySlots[index].type === 'not_available') {
                                                daySlots[index].type = 'working';
                                            }
                                        });

                                        // Process time slots for availability
                                        Object.entries(timeSlots).forEach(([timeKey, slot]) => {
                                            const slotTime = new Date(timeKey);
                                            const slotIndex = this.getSlotIndex(slotTime.getHours(), slotTime.getMinutes());

                                            if (slotIndex >= 0 && slotIndex < daySlots.length) {
                                                const currentSlot = daySlots[slotIndex];

                                                if (slot.available) {
                                                    const slotTime = new Date(timeKey);
                                                    const isWithinBookingWindow = this.isSlotWithinBookingWindow(
                                                        dateString,
                                                        slotTime.getHours(),
                                                        slotTime.getMinutes()
                                                    );

                                                    if (!isWithinBookingWindow) {
                                                        // Slot is outside booking advance window - mark as not available
                                                        currentSlot.type = 'not_available';
                                                        currentSlot.datetime = timeKey;
                                                        currentSlot.available = false;
                                                        currentSlot.restrictionReason = this.getRestrictionReason(dateString,
                                                            slotTime.getHours(), slotTime.getMinutes());
                                                    } else {
                                                        // Employee is available at this time and within booking window
                                                        currentSlot.type = 'available';
                                                        currentSlot.datetime = timeKey;
                                                        currentSlot.available = true;
                                                        currentSlot.hasFullServiceTime = slot.has_full_service_time;
                                                        currentSlot.availableMinutes = slot.available_minutes;
                                                    }

                                                    // Handle all employees data if available
                                                    if (slot.all_employees && isWithinBookingWindow) {
                                                        // Filter employees who are available AND have enough time for full service
                                                        const availableEmployees = slot.all_employees.filter(emp => emp
                                                            .available && emp.has_full_service_time);
                                                        availableEmployees.forEach(employee => {
                                                            if (!currentSlot.employeeIds.includes(employee
                                                                    .employee_id)) {
                                                                currentSlot.employeeIds.push(employee.employee_id);
                                                                currentSlot.employeeData[employee.employee_id] = {
                                                                    id: employee.employee_id,
                                                                    name: employee.employee_name,
                                                                    bio: employee.employee_bio || '',
                                                                    availableMinutes: employee.available_minutes,
                                                                    hasFullServiceTime: employee
                                                                        .has_full_service_time
                                                                };
                                                            }
                                                        });
                                                    } else {
                                                        // Fallback to single employee data - only if they have full service time
                                                        if (slot.employee_id && slot.has_full_service_time && !currentSlot
                                                            .employeeIds.includes(slot
                                                                .employee_id)) {
                                                            currentSlot.employeeIds.push(slot.employee_id);
                                                            currentSlot.employeeData[slot.employee_id] = {
                                                                id: slot.employee_id,
                                                                name: slot.employee_name,
                                                                bio: slot.employee_bio || '',
                                                                availableMinutes: slot.available_minutes,
                                                                hasFullServiceTime: slot.has_full_service_time
                                                            };
                                                        }
                                                    }
                                                } else {
                                                    // All employees are busy - mark as booked
                                                    currentSlot.type = 'booked';
                                                    currentSlot.available = false;
                                                    currentSlot.datetime = timeKey;
                                                    currentSlot.bookedEmployees = slot.employee_name || 'All employees booked';
                                                }
                                            }
                                        });
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
                                        // All available slots show as green, regardless of full service time
                                        classes.push('bg-green-400', 'text-white');
                                        break;
                                    case 'selected':
                                        classes.push('bg-blue-500', 'text-white');
                                        break;
                                    case 'booked':
                                        classes.push('bg-red-400', 'text-white');
                                        break;
                                    case 'hover_preview':
                                        classes.push('bg-blue-300', 'text-white', 'opacity-70');
                                        break;
                                    case 'hover_conflict':
                                        classes.push('bg-red-600', 'text-white');
                                        break;
                                    case 'working':
                                        classes.push('bg-frappe-surface1', 'text-frappe-subtext1');
                                        break;
                                    default:
                                        classes.push('bg-frappe-surface0', 'text-frappe-subtext1');
                                        // Add cursor-not-allowed for slots with restriction reasons (booking advance violations)
                                        if (slot.restrictionReason) {
                                            classes.push('cursor-not-allowed');
                                        }
                                }

                                return classes.join(' ');
                            },

                            getSlotTooltip(slot, dateString, slotIndex) {
                                if (!slot.time) return '';

                                if (slot.type === 'available') {
                                    const employeeCount = Object.keys(slot.employeeData).length;
                                    let tooltip = `Available at ${slot.time}`;

                                    if (employeeCount > 0) {
                                        tooltip += ` - ${employeeCount} employees available`;

                                        const employeeNames = Object.values(slot.employeeData).map(emp => emp.name).join(', ');
                                        if (employeeNames) {
                                            tooltip += `: ${employeeNames}`;
                                        }
                                    }
                                    return tooltip;
                                } else if (slot.type === 'booked') {
                                    return `Booked at ${slot.time}${slot.bookedEmployees ? ' by ' + slot.bookedEmployees : ''}`;
                                } else if (slot.restrictionReason) {
                                    // Slots that don't meet booking advance requirements
                                    return `${slot.time} - ${slot.restrictionReason}`;
                                } else if (slot.type === 'selected') {
                                    return `Selected: ${slot.time}`;
                                } else if (slot.type === 'working') {
                                    return `Working hours: ${slot.time}`;
                                }
                                return `${slot.time}`;
                            },

                            handleSlotClick(slot, dateString, slotIndex) {

                                // Clear hover effects first to get the real slot type
                                this.clearHoverEffects();

                                // Get the actual slot after clearing hover effects
                                const daySlots = this.getDaySlots(dateString);
                                const actualSlot = daySlots[slotIndex];

                                // Allow clicking on available slots, hover previews, and selected slots
                                const clickableTypes = ['available', 'hover_preview', 'selected'];
                                const originalType = slot.originalType || slot.type;

                                // Don't allow clicking on slots that would cause conflicts, are restricted, or have booking restrictions
                                if (slot.type === 'hover_conflict' || originalType === 'hover_conflict' ||
                                    slot.restrictionReason || actualSlot.restrictionReason) {
                                    return;
                                }

                                if (!clickableTypes.includes(slot.type) && !clickableTypes.includes(originalType)) {
                                    return;
                                }

                                // Additional check: verify the full service duration doesn't conflict
                                const slotsNeeded = this.serviceSlotCount;
                                for (let i = 0; i < slotsNeeded; i++) {
                                    const checkIndex = slotIndex + i;
                                    if (checkIndex >= daySlots.length) {
                                        return;
                                    }

                                    const checkSlot = daySlots[checkIndex];
                                    if (checkSlot.type === 'booked' || checkSlot.type === 'not_available') {
                                        return;
                                    }
                                }

                                // Use the actual slot data after hover clearing
                                const targetSlot = actualSlot.type === 'available' ? actualSlot : slot;

                                // Check if slot has employee data
                                if (!targetSlot.employeeData || Object.keys(targetSlot.employeeData).length === 0) {
                                    return;
                                }

                                this.clearSelection();

                                // Mark the clicked slot as selected
                                // slotsNeeded already declared above

                                // Store original types for restoration
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

                                this.selectedSlot = targetSlot;
                                this.selectedSlotPosition = {
                                    dateString,
                                    slotIndex,
                                    originalSlotTypes
                                };

                                this.availableEmployees = Object.values(targetSlot.employeeData);
                                this.selectedEmployeeId = null;

                            },

                            handleSlotHover(slot, dateString, slotIndex, isEntering) {
                                // Clear any previous hover effects
                                this.clearHoverEffects();

                                if (!isEntering) return;

                                // Don't allow hover effects on restricted slots
                                if (slot.type === 'restricted') return;

                                const slotsNeeded = this.serviceSlotCount;
                                const daySlots = this.getDaySlots(dateString);

                                // Check if we can fit the full service duration
                                let canFitService = true;
                                let hasConflict = false;

                                for (let i = 0; i < slotsNeeded; i++) {
                                    const checkIndex = slotIndex + i;
                                    if (checkIndex >= daySlots.length) {
                                        canFitService = false;
                                        break;
                                    }

                                    const checkSlot = daySlots[checkIndex];
                                    // Allow hovering over selected slots, but check for conflicts with booked/unavailable/restricted
                                    if (checkSlot.type === 'booked' || checkSlot.type === 'not_available' || checkSlot.type ===
                                        'restricted') {
                                        hasConflict = true;
                                    }
                                }

                                if (canFitService) {
                                    // Apply hover effects to show service duration
                                    for (let i = 0; i < slotsNeeded; i++) {
                                        const hoverIndex = slotIndex + i;
                                        if (hoverIndex < daySlots.length) {
                                            const hoverSlot = daySlots[hoverIndex];

                                            // Store original type for restoration
                                            if (!hoverSlot.originalType) {
                                                hoverSlot.originalType = hoverSlot.type;
                                            }

                                            // Apply hover effect based on conflict
                                            if (hasConflict) {
                                                hoverSlot.type = 'hover_conflict';
                                            } else {
                                                hoverSlot.type = 'hover_preview';
                                            }
                                        }
                                    }
                                }
                            },

                            clearHoverEffects() {
                                // Restore original types for all slots that have hover effects
                                Object.values(this.schedule).forEach(daySlots => {
                                    daySlots.forEach(slot => {
                                        if (slot.originalType && (slot.type === 'hover_preview' || slot.type ===
                                                'hover_conflict')) {
                                            slot.type = slot.originalType;
                                            delete slot.originalType;
                                        }
                                    });
                                });
                            },

                            selectEmployee(employeeId) {
                                this.selectedEmployeeId = employeeId;
                            },

                            clearSelection() {
                                // Clear hover effects first
                                this.clearHoverEffects();

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
                                @if ($businessSettings && ($businessSettings['holiday_mode'] || $businessSettings['maintenance_mode']))
                                    @if ($businessSettings['holiday_mode'])
                                        alert('{{ __('messages.this_business_not_accepting_bookings') }}');
                                    @elseif ($businessSettings['maintenance_mode'])
                                        alert('{{ __('messages.business_under_maintenance') }}');
                                    @endif
                                    return;
                                @endif

                                if (!this.selectedEmployeeId || !this.selectedSlot) {
                                    alert('{{ __('messages.please_select_employee_and_time') }}');
                                    return;
                                }

                                let formattedDate = '';
                                if (this.selectedSlot.datetime) {
                                    const date = new Date(this.selectedSlot.datetime);

                                    if (isNaN(date.getTime())) {
                                        alert('{{ __('messages.invalid_date_selected') }}');
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


                                this.$refs.bookingForm.submit();
                            }
                        }
                    }
                </script>
            @endif
        @endif
    </div>
</x-app-layout>
