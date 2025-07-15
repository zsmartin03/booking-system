<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[
            ['text' => __('messages.all_businesses'), 'url' => route('businesses.public.index')],
            ['text' => $selectedBusiness->name, 'url' => route('businesses.show', $selectedBusiness->id)],
            ['text' => __('messages.book_service'), 'url' => null],
        ]" />
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Business Status Messages (only critical messages - holiday/maintenance mode) -->
        @php
            $criticalMessages = [];
            if ($businessSettings['holiday_mode']) {
                $criticalMessages[] = [
                    'icon' => 'exclamation-triangle',
                    'color' => 'orange-400',
                    'bg' => 'bg-orange-500/20',
                    'border' => 'border-orange-400/30',
                    'text' => 'text-orange-300',
                    'title' => __('messages.holiday_mode'),
                    'desc' => __('messages.this_business_not_accepting_bookings'),
                ];
            }
            if ($businessSettings['maintenance_mode']) {
                $criticalMessages[] = [
                    'icon' => 'wrench-screwdriver',
                    'color' => 'red-400',
                    'bg' => 'bg-red-500/20',
                    'border' => 'border-red-400/30',
                    'text' => 'text-red-300',
                    'title' => __('messages.maintenance_mode'),
                    'desc' => __('messages.business_under_maintenance'),
                ];
            }
        @endphp
        @if (count($criticalMessages) > 0)
            <div class="mb-6">
                <div class="space-y-3">
                    @foreach ($criticalMessages as $msg)
                        <div
                            class="frosted-card flex items-start gap-3 p-4 rounded-lg border {{ $msg['border'] }} {{ $msg['text'] }} shadow-sm">
                            <x-dynamic-component :component="'heroicon-o-' . $msg['icon']" class="w-6 h-6 text-{{ $msg['color'] }} mt-1" />
                            <div>
                                <div class="font-semibold">{{ $msg['title'] }}</div>
                                <div class="text-sm opacity-80">{{ $msg['desc'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Only show service selection and booking form if business is not in holiday or maintenance mode -->
        @if (!$businessSettings['holiday_mode'] && !$businessSettings['maintenance_mode'])
            <!-- Service Selection and Booking Restrictions -->
            <div class="mb-6 p-4 sm:p-6 frosted-card rounded-xl shadow-lg">
                <div class="flex flex-col lg:flex-row gap-6">
                    <!-- Service Selection (Left Side) -->
                    <div class="flex-1">
                        <form method="GET" action="{{ route('bookings.create', $selectedBusiness->id) }}">
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
                        </form>
                    </div>

                    <!-- Booking Restrictions (Right Side) -->
                    @php
                        $bookingInfo = [];
                        if ($businessSettings['booking_confirmation_required']) {
                            $bookingInfo[] = [
                                'icon' => 'clock',
                                'color' => 'blue-400',
                                'text' => 'text-blue-300',
                                'title' => __('messages.booking_confirmation_required'),
                                'desc' => __('messages.booking_pending_confirmation'),
                            ];
                        }
                        if ($businessSettings['booking_advance_hours'] > 0 || $businessSettings['booking_advance_days'] > 0) {
                            $bookingInfo[] = [
                                'icon' => 'information-circle',
                                'color' => 'frappe-blue',
                                'text' => 'text-frappe-blue',
                                'title' => __('messages.booking_restrictions'),
                                'desc' => __('messages.booking_advance_restrictions', [
                                    'hours' => $businessSettings['booking_advance_hours'],
                                    'days' => $businessSettings['booking_advance_days'],
                                ]),
                            ];
                        }
                    @endphp
                    @if (count($bookingInfo) > 0)
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-frappe-text mb-3">{{ __('messages.booking_information') }}</h4>
                            <div class="space-y-2">
                                @foreach ($bookingInfo as $info)
                                    <div class="flex items-start gap-2 p-2 rounded-lg border border-{{ str_replace('text-', '', $info['text']) }}/30 {{ $info['text'] }} bg-{{ str_replace('text-', '', $info['text']) }}/10">
                                        <x-dynamic-component :component="'heroicon-o-' . $info['icon']" class="w-4 h-4 mt-0.5 flex-shrink-0" />
                                        <div class="text-xs">
                                            <div class="font-medium">{{ $info['title'] }}</div>
                                            <div class="opacity-80">{{ $info['desc'] }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Service Info (only if selected) -->
            @if ($selectedService)
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
            @endif

            <!-- Timetable: always visible, but only interactive if service is selected -->
            <div class="frosted-card rounded-xl shadow-lg p-4 sm:p-6 min-h-[800px]" x-data="intervalTimetable()"
                x-init="init();
                loadSchedule()">
                <div class="flex flex-col sm:flex-row justify-between items-center mb-4 gap-4">
                    <button @click="previousWeek()"
                        class="frosted-button px-4 py-2 text-white rounded-lg transition-all w-full sm:w-auto">
                        ← {{ __('messages.previous_week') }}
                    </button>
                    <h3 class="text-lg font-semibold text-frappe-text text-center" x-text="weekLabel"></h3>
                    <button @click="nextWeek()"
                        class="frosted-button px-4 py-2 text-white rounded-lg transition-all w-full sm:w-auto">
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
                            <div class="text-center font-medium text-frappe-text p-2 bg-frappe-surface1 rounded mb-2">
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
                                            @click="canSelectSlots ? handleSlotClick(slot, day.dateString, slotIndex) : null"
                                            @mouseenter="canSelectSlots ? handleSlotHover(slot, day.dateString, slotIndex, true) : null"
                                            @mouseleave="canSelectSlots ? handleSlotHover(slot, day.dateString, slotIndex, false) : null"
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
                                <!-- 30-minute interval lines -->
                                <template x-for="interval in Math.floor((endHour - startHour) * 2)"
                                    :key="interval">
                                    <div class="absolute left-0 w-full border-t border-gray-300/60 pointer-events-none"
                                        :style="`top: ${(interval * 6) * slotHeight}px; z-index: 10;`">
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Loading State -->
                <div x-show="loading" class="text-center py-8">
                    <div class="text-frappe-subtext1">{{ __('messages.loading_schedule') }}</div>
                </div>

                <!-- Only show booking form if service is selected and slot is selected -->
                <div x-show="selectedSlot && canSelectSlots"
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
                    <!-- Employee Selection -->
                    <div class="mb-4">
                        <span class="text-frappe-subtext1">{{ __('messages.choose_employee') }}: </span>
                        <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <template x-for="employee in availableEmployees" :key="employee.id">
                                <div class="employee-card p-3 transition-all relative"
                                    :class="{
                                        'selected': selectedEmployeeId === employee.id,
                                        'unavailable': employee.available === false
                                    }"
                                    @click="selectEmployee(employee.id)">
                                    <div class="font-medium" x-text="employee.name"></div>
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
                        <input type="hidden" name="service_id"
                            value="{{ $selectedService ? $selectedService->id : '' }}">
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
                                    class="frosted-button px-6 py-3 text-white rounded-lg transition-all w-full sm:w-auto"
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
                        serviceDuration: {{ $selectedService ? $selectedService->duration : 5 }},
                        canSelectSlots: {{ $selectedService ? 'true' : 'false' }},
                        bookingAdvanceHours: {{ $businessSettings['booking_advance_hours'] }},
                        bookingAdvanceDays: {{ $businessSettings['booking_advance_days'] }},

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

                            const minAdvanceMs = this.bookingAdvanceHours * 60 * 60 * 1000;
                            const timeDiffMs = slotDateTime.getTime() - now.getTime();

                            if (timeDiffMs < minAdvanceMs) {
                                return false;
                            }

                            const maxAdvanceMs = this.bookingAdvanceDays * 24 * 60 * 60 * 1000;

                            if (timeDiffMs > maxAdvanceMs) {
                                return false;
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
                        },                        async loadSchedule() {
                            this.loading = true;
                            this.clearSelection();

                            try {
                                const weekStartString = this.weekStart.toISOString().split('T')[0];
                                let url = null;
                                
                                if (this.canSelectSlots && {{ $selectedService ? 'true' : 'false' }}) {
                                    // Service selected: get full availability data
                                    url = `{{ route('booking-slots') }}?service_id={{ $selectedService ? $selectedService->id : '' }}&week_start=${weekStartString}`;
                                } else {
                                    // No service selected: get basic working hours and booked slots
                                    url = `{{ route('business-schedule') }}?business_id={{ $selectedBusiness->id }}&week_start=${weekStartString}`;
                                }

                                if (url) {
                                    const response = await fetch(url);
                                    const data = await response.json();

                                    this.schedule = this.transformToIntervals(data);
                                } else {
                                    // Fallback: empty schedule
                                    this.schedule = {};
                                }
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

                                    const workingTimes = new Set();
                                    Object.values(timeSlots).forEach(slot => {
                                        const slotTime = new Date(slot.time);
                                        const slotIndex = this.getSlotIndex(slotTime.getHours(), slotTime.getMinutes());
                                        if (slotIndex >= 0 && slotIndex < daySlots.length) {
                                            workingTimes.add(slotIndex);
                                        }
                                    });

                                    workingTimes.forEach(index => {
                                        if (daySlots[index].type === 'not_available') {
                                            daySlots[index].type = 'working';
                                        }
                                    });

                                    Object.entries(timeSlots).forEach(([timeKey, slot]) => {
                                        const slotTime = new Date(timeKey);
                                        const slotIndex = this.getSlotIndex(slotTime.getHours(), slotTime.getMinutes());

                                        if (slotIndex >= 0 && slotIndex < daySlots.length) {
                                            const currentSlot = daySlots[slotIndex];

                                            if (slot.available) {
                                                const slotTime = new Date(timeKey);

                                                // Check if this is service-specific data or basic business schedule
                                                const hasServiceData = slot.hasOwnProperty('has_full_service_time');

                                                if (hasServiceData) {
                                                    // Service-specific logic with booking window validation
                                                    const isWithinBookingWindow = this.isSlotWithinBookingWindow(
                                                        dateString,
                                                        slotTime.getHours(),
                                                        slotTime.getMinutes()
                                                    );

                                                    if (!isWithinBookingWindow) {
                                                        currentSlot.type = 'not_available';
                                                        currentSlot.datetime = timeKey;
                                                        currentSlot.available = false;
                                                        currentSlot.restrictionReason = this.getRestrictionReason(dateString,
                                                            slotTime.getHours(), slotTime.getMinutes());
                                                    } else {
                                                        currentSlot.type = 'available';
                                                        currentSlot.datetime = timeKey;
                                                        currentSlot.available = true;
                                                        currentSlot.hasFullServiceTime = slot.has_full_service_time;
                                                        currentSlot.availableMinutes = slot.available_minutes;
                                                    }

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
                                                    }                                            } else {
                                                // Basic business schedule (no service selected) - just show as working
                                                currentSlot.type = 'working';
                                                currentSlot.datetime = timeKey;
                                                currentSlot.available = false; // Not clickable when no service
                                            }
                                        } else {
                                            // For basic business schedule, we don't show booked slots
                                            const hasServiceData = slot.hasOwnProperty('has_full_service_time');

                                            if (hasServiceData) {
                                                    // Service-specific logic
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
                                                            slotTime.getHours(), slotTime.getMinutes());                                                } else {
                                                    // All employees are busy - mark as booked
                                                    currentSlot.type = 'booked';
                                                    currentSlot.available = false;
                                                    currentSlot.datetime = timeKey;
                                                    currentSlot.bookedEmployees = slot.employee_name ||
                                                        'All employees booked';
                                                }
                                            } else {
                                                // Basic business schedule - skip non-available slots (don't show bookings)
                                                // These slots are already marked as 'working' from the available=true case above
                                            }
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
                                    if (!this.canSelectSlots) {
                                        classes.push('cursor-default');
                                    }
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
                                    if (!this.canSelectSlots) {
                                        classes.push('cursor-default');
                                    }
                                    break;
                                default:
                                    classes.push('bg-frappe-surface0', 'text-frappe-subtext1');
                                    // Add cursor-not-allowed for slots with restriction reasons (booking advance violations)
                                    if (slot.restrictionReason) {
                                        classes.push('cursor-not-allowed');
                                    } else if (!this.canSelectSlots) {
                                        classes.push('cursor-default');
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
                                return `Booked at ${slot.time}${slot.bookedEmployees ? ' - ' + slot.bookedEmployees : ''}`;
                            } else if (slot.restrictionReason) {
                                // Slots that don't meet booking advance requirements
                                return `${slot.time} - ${slot.restrictionReason}`;
                            } else if (slot.type === 'selected') {
                                return `Selected: ${slot.time}`;
                            } else if (slot.type === 'working') {
                                if (!this.canSelectSlots) {
                                    return `Working hours: ${slot.time} - Select a service to book`;
                                }
                                return `Working hours: ${slot.time}`;
                            }
                            return `${slot.time}`;
                        },

                        handleSlotClick(slot, dateString, slotIndex) {
                            // Don't allow slot clicking if no service is selected
                            if (!this.canSelectSlots) {
                                return;
                            }

                            this.clearHoverEffects();

                            const daySlots = this.getDaySlots(dateString);
                            const actualSlot = daySlots[slotIndex];

                            const clickableTypes = ['available', 'hover_preview', 'selected'];
                            const originalType = slot.originalType || slot.type;

                            if (slot.type === 'hover_conflict' || originalType === 'hover_conflict' ||
                                slot.restrictionReason || actualSlot.restrictionReason) {
                                return;
                            }

                            if (!clickableTypes.includes(slot.type) && !clickableTypes.includes(originalType)) {
                                return;
                            }

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

                            const targetSlot = actualSlot.type === 'available' ? actualSlot : slot;

                            if (!targetSlot.employeeData || Object.keys(targetSlot.employeeData).length === 0) {
                                return;
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
                            // Don't show hover effects if no service is selected
                            if (!this.canSelectSlots) {
                                return;
                            }

                            this.clearHoverEffects();

                            if (!isEntering) return;

                            if (slot.type === 'restricted') return;

                            const slotsNeeded = this.serviceSlotCount;
                            const daySlots = this.getDaySlots(dateString);

                            let canFitService = true;
                            let hasConflict = false;

                            for (let i = 0; i < slotsNeeded; i++) {
                                const checkIndex = slotIndex + i;
                                if (checkIndex >= daySlots.length) {
                                    canFitService = false;
                                    break;
                                }

                                const checkSlot = daySlots[checkIndex];
                                if (checkSlot.type === 'booked' || checkSlot.type === 'not_available' || checkSlot.type ===
                                    'restricted') {
                                    hasConflict = true;
                                }
                            }

                            if (canFitService) {
                                for (let i = 0; i < slotsNeeded; i++) {
                                    const hoverIndex = slotIndex + i;
                                    if (hoverIndex < daySlots.length) {
                                        const hoverSlot = daySlots[hoverIndex];

                                        if (!hoverSlot.originalType) {
                                            hoverSlot.originalType = hoverSlot.type;
                                        }

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
                            @if ($businessSettings['holiday_mode'] || $businessSettings['maintenance_mode'])
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
    </div>
</x-app-layout>
