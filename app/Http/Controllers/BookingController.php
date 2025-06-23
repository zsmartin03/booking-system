<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Service;
use App\Models\Employee;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function create(Request $request)
    {
        $businesses = Business::with('services')->get();
        $selectedBusiness = $request->business_id ? Business::find($request->business_id) : null;
        $services = $selectedBusiness ? $selectedBusiness->services()->where('active', true)->get() : collect();
        $selectedService = $request->service_id ? Service::find($request->service_id) : null;
        $employees = $selectedService ? $selectedService->employees()->where('active', true)->get() : collect();
        $selectedEmployee = $request->employee_id ? Employee::find($request->employee_id) : null;

        return view('bookings.create', compact(
            'businesses',
            'selectedBusiness',
            'services',
            'selectedService',
            'employees',
            'selectedEmployee'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'business_id' => 'required|exists:businesses,id',
            'service_id' => 'required|exists:services,id',
            'employee_id' => 'required|exists:employees,id',
            'start_time' => 'required|date_format:Y-m-d\TH:i',
            'notes' => 'nullable|string|max:1000',
        ]);

        $service = Service::findOrFail($validated['service_id']);
        $employee = Employee::findOrFail($validated['employee_id']);

        if (!$employee->canProvideService($service->id)) {
            return back()->withErrors(['employee_id' => 'Selected employee cannot provide this service.'])->withInput();
        }

        $start = Carbon::parse($validated['start_time']);
        $end = $start->copy()->addMinutes($service->duration);

        // Check for overlapping bookings
        $overlap = Booking::where('employee_id', $validated['employee_id'])
            ->where('status', '!=', 'cancelled')
            ->where(function ($q) use ($start, $end) {
                $q->where('start_time', '<', $end)
                    ->where('end_time', '>', $start);
            })
            ->exists();

        if ($overlap) {
            return back()->withErrors(['start_time' => 'This time slot is already booked.'])->withInput();
        }

        // Verify employee is working at this time
        $dayOfWeek = strtolower($start->format('l'));
        $workingHours = $employee->getWorkingHoursForDay($dayOfWeek);

        $isWorking = $workingHours->filter(function ($wh) use ($start, $end) {
            $workStart = Carbon::parse($start->toDateString() . ' ' . $wh->start_time);
            $workEnd = Carbon::parse($start->toDateString() . ' ' . $wh->end_time);
            return $start >= $workStart && $end <= $workEnd;
        })->isNotEmpty();

        if (!$isWorking) {
            return back()->withErrors(['start_time' => 'Selected employee is not working at this time.'])->withInput();
        }

        try {
            $booking = Booking::create([
                'client_id' => Auth::id(),
                'service_id' => $service->id,
                'employee_id' => $validated['employee_id'],
                'start_time' => $start,
                'end_time' => $end,
                'status' => 'pending',
                'notes' => $validated['notes'] ?? '',
                'total_price' => $service->price,
            ]);

            return redirect()->route('bookings.show', $booking->id)
                ->with('success', 'Booking created! Awaiting confirmation.');
        } catch (\Exception $e) {
            return back()->withErrors(['general' => 'Failed to create booking. Please try again.'])->withInput();
        }
    }

    public function availableSlots(Request $request)
    {
        $service = Service::findOrFail($request->service_id);
        $employeeId = $request->employee_id;
        $weekStart = $request->week_start
            ? Carbon::parse($request->week_start)->startOfWeek()
            : now()->startOfWeek();

        // Get employees who can provide this service
        $employees = $employeeId
            ? Employee::where('id', $employeeId)->where('active', true)->get()
            : $service->employees()->where('active', true)->get();

        $slots = [];

        // Use 5-minute intervals for slot generation
        $interval = 5;

        foreach (range(0, 6) as $offset) {
            $date = $weekStart->copy()->addDays($offset);
            $dateString = $date->toDateString();
            $dayOfWeek = strtolower($date->format('l'));

            $timeSlots = [];

            foreach ($employees as $employee) {
                $workingHours = $employee->getWorkingHoursForDay($dayOfWeek);

                $availableExceptions = $employee->availabilityExceptions()
                    ->where('date', $dateString)
                    ->where('type', 'available')
                    ->get();

                foreach ($workingHours as $workingHour) {
                    $startTime = Carbon::parse($date->toDateString() . ' ' . $workingHour->start_time);

                    $minutes = $startTime->minute;
                    $roundedMinutes = ceil($minutes / $interval) * $interval;
                    if ($roundedMinutes == 60) {
                        $startTime->addHour();
                        $roundedMinutes = 0;
                    }
                    $startTime->minute($roundedMinutes);
                    $startTime->second(0);

                    $endTime = Carbon::parse($date->toDateString() . ' ' . $workingHour->end_time);

                    // Generate slots
                    $current = $startTime->copy();
                    while ($current < $endTime) {
                        $timeKey = $current->format('Y-m-d\TH:i');

                        $availabilityException = $employee->availabilityExceptions()
                            ->where('date', $date->toDateString())
                            ->where('start_time', '<=', $current->format('H:i:s'))
                            ->where('end_time', '>', $current->format('H:i:s'))
                            ->first();

                        $isWorkingTime = true;
                        $effectiveEndTime = $endTime;

                        if ($availabilityException) {
                            if ($availabilityException->type === 'unavailable') {
                                // skip this time slot - employee is not available
                                $current->addMinutes($interval);
                                continue;
                            } elseif ($availabilityException->type === 'available') {
                                // override working hours - employee is available
                                $exceptionEndTime = Carbon::parse($date->toDateString() . ' ' . $availabilityException->end_time);
                                $effectiveEndTime = min($effectiveEndTime, $exceptionEndTime);
                            }
                        }

                        $overlap = Booking::where('employee_id', $employee->id)
                            ->where('status', '!=', 'cancelled')
                            ->where(function ($q) use ($current) {
                                $q->where('start_time', '<=', $current)
                                    ->where('end_time', '>', $current);
                            })->exists();

                        // Initialize time slot if it doesn't exist
                        if (!isset($timeSlots[$timeKey])) {
                            $timeSlots[$timeKey] = [
                                'time' => $timeKey,
                                'label' => $current->format('H:i'),
                                'employees' => []
                            ];
                        }

                        // Calculate how much time is available from this slot until employee becomes unavailable
                        $nextBooking = Booking::where('employee_id', $employee->id)
                            ->where('status', '!=', 'cancelled')
                            ->where('start_time', '>', $current)
                            ->orderBy('start_time')
                            ->first();

                        $availableUntil = $effectiveEndTime; // Use effective end time
                        if ($nextBooking) {
                            $availableUntil = min($availableUntil, Carbon::parse($nextBooking->start_time));
                        }

                        // Check for unavailable exceptions that might cut availability short
                        $nextUnavailableException = $employee->availabilityExceptions()
                            ->where('date', $date->toDateString())
                            ->where('type', 'unavailable')
                            ->where('start_time', '>', $current->format('H:i:s'))
                            ->orderBy('start_time')
                            ->first();

                        if ($nextUnavailableException) {
                            $exceptionStartTime = Carbon::parse($date->toDateString() . ' ' . $nextUnavailableException->start_time);
                            $availableUntil = min($availableUntil, $exceptionStartTime);
                        }

                        $availableMinutes = $current->diffInMinutes($availableUntil);
                        $hasFullServiceTime = $availableMinutes >= $service->duration;

                        // Add employee availability to this time slot
                        $timeSlots[$timeKey]['employees'][] = [
                            'employee_id' => $employee->id,
                            'employee_name' => $employee->name,
                            'employee_bio' => $employee->bio ?? '',
                            'available' => !$overlap,
                            'available_minutes' => $availableMinutes,
                            'has_full_service_time' => $hasFullServiceTime,
                            'available_until' => $availableUntil->format('Y-m-d\TH:i')
                        ];

                        $current->addMinutes($interval);
                    }
                }

                // Process 'available' type exceptions (employee available outside regular hours)
                foreach ($availableExceptions as $exception) {
                    $startTime = Carbon::parse($date->toDateString() . ' ' . $exception->start_time);

                    $minutes = $startTime->minute;
                    $roundedMinutes = ceil($minutes / $interval) * $interval;
                    if ($roundedMinutes == 60) {
                        $startTime->addHour();
                        $roundedMinutes = 0;
                    }
                    $startTime->minute($roundedMinutes);
                    $startTime->second(0);

                    $endTime = Carbon::parse($date->toDateString() . ' ' . $exception->end_time);

                    $current = $startTime->copy();
                    while ($current < $endTime) {
                        $timeKey = $current->format('Y-m-d\TH:i');

                        $overlap = Booking::where('employee_id', $employee->id)
                            ->where('status', '!=', 'cancelled')
                            ->where(function ($q) use ($current) {
                                $q->where('start_time', '<=', $current)
                                    ->where('end_time', '>', $current);
                            })->exists();

                        // Initialize time slot if it doesn't exist
                        if (!isset($timeSlots[$timeKey])) {
                            $timeSlots[$timeKey] = [
                                'time' => $timeKey,
                                'label' => $current->format('H:i'),
                                'employees' => []
                            ];
                        }

                        // Calculate availability for exception period
                        $nextBooking = Booking::where('employee_id', $employee->id)
                            ->where('status', '!=', 'cancelled')
                            ->where('start_time', '>', $current)
                            ->orderBy('start_time')
                            ->first();

                        $availableUntil = $endTime; // Default to end of exception period
                        if ($nextBooking) {
                            $availableUntil = min($availableUntil, Carbon::parse($nextBooking->start_time));
                        }

                        $availableMinutes = $current->diffInMinutes($availableUntil);
                        $hasFullServiceTime = $availableMinutes >= $service->duration;

                        // Add employee availability to this time slot
                        $timeSlots[$timeKey]['employees'][] = [
                            'employee_id' => $employee->id,
                            'employee_name' => $employee->name,
                            'employee_bio' => $employee->bio ?? '',
                            'available' => !$overlap,
                            'available_minutes' => $availableMinutes,
                            'has_full_service_time' => $hasFullServiceTime,
                            'available_until' => $availableUntil->format('Y-m-d\TH:i')
                        ];

                        $current->addMinutes($interval);
                    }
                }
            }

            // Convert aggregated time slots to the expected format
            $slots[$dateString] = [];
            foreach ($timeSlots as $timeKey => $timeSlot) {
                // Check if ANY employee is available at this time
                $availableEmployees = array_filter($timeSlot['employees'], function ($emp) {
                    return $emp['available'];
                });

                // Find the best available employee (one with most available time)
                $bestEmployee = null;
                $maxAvailableMinutes = 0;
                $hasAnyFullServiceTime = false;

                foreach ($availableEmployees as $employee) {
                    if ($employee['available_minutes'] > $maxAvailableMinutes) {
                        $maxAvailableMinutes = $employee['available_minutes'];
                        $bestEmployee = $employee;
                    }
                    if ($employee['has_full_service_time']) {
                        $hasAnyFullServiceTime = true;
                    }
                }

                // Create a slot entry for this time if any employee is working
                if (!empty($timeSlot['employees'])) {
                    $slots[$dateString][] = [
                        'time' => $timeSlot['time'],
                        'label' => $timeSlot['label'],
                        'available' => !empty($availableEmployees),
                        'employee_id' => $bestEmployee ? $bestEmployee['employee_id'] : (!empty($availableEmployees) ? array_values($availableEmployees)[0]['employee_id'] : null),
                        'employee_name' => $bestEmployee ? $bestEmployee['employee_name'] : (!empty($availableEmployees) ? array_values($availableEmployees)[0]['employee_name'] : 'All employees booked'),
                        'employee_bio' => $bestEmployee ? $bestEmployee['employee_bio'] : (!empty($availableEmployees) ? array_values($availableEmployees)[0]['employee_bio'] : ''),
                        'service_end_time' => Carbon::parse($timeSlot['time'])->addMinutes($service->duration)->format('Y-m-d\TH:i'),
                        'available_minutes' => $maxAvailableMinutes,
                        'has_full_service_time' => $hasAnyFullServiceTime,
                        'all_employees' => $timeSlot['employees'] // Include all employee data for frontend
                    ];
                }
            }

            // Sort slots by time
            if (isset($slots[$dateString])) {
                usort($slots[$dateString], function ($a, $b) {
                    return strcmp($a['time'], $b['time']);
                });
            }
        }

        return response()->json($slots);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->role === 'admin') {
            $bookings = Booking::with(['service', 'employee', 'client'])->latest()->paginate(20);
        } elseif ($user->role === 'provider') {
            $bookings = Booking::whereHas('service.business', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->with(['service', 'employee', 'client'])->latest()->paginate(20);
        } else {
            $bookings = Booking::where('client_id', $user->id)->with(['service', 'employee', 'client'])->latest()->paginate(20);
        }
        return view('bookings.index', compact('bookings'));
    }

    public function show($id)
    {
        $booking = Booking::with(['service', 'employee', 'client'])->findOrFail($id);
        $user = Auth::user();

        if ($user->role === 'client' && $booking->client_id !== $user->id) {
            abort(403);
        }
        if ($user->role === 'provider' && $booking->service->business->user_id !== $user->id) {
            abort(403);
        }

        return view('bookings.show', compact('booking'));
    }

    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $user = Auth::user();

        if (!in_array($user->role, ['admin', 'provider'])) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled',
        ]);

        $booking->status = $validated['status'];
        $booking->save();

        return redirect()->route('bookings.show', $booking->id)
            ->with('success', 'Booking status updated.');
    }
}
