<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Service;
use App\Models\Employee;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        // Verify employee can provide this service
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
            $slots[$dateString] = [];

            foreach ($employees as $employee) {
                $dayOfWeek = strtolower($date->format('l'));
                $workingHours = $employee->getWorkingHoursForDay($dayOfWeek);

                foreach ($workingHours as $workingHour) {
                    $startTime = Carbon::parse($date->toDateString() . ' ' . $workingHour->start_time);
                    // Round to nearest 5 minutes
                    $minutes = $startTime->minute;
                    $roundedMinutes = ceil($minutes / $interval) * $interval;
                    if ($roundedMinutes == 60) {
                        $startTime->addHour();
                        $roundedMinutes = 0;
                    }
                    $startTime->minute($roundedMinutes);
                    $startTime->second(0);

                    $endTime = Carbon::parse($date->toDateString() . ' ' . $workingHour->end_time);

                    // Generate slots every 5 minutes
                    $current = $startTime->copy();
                    while ($current->copy()->addMinutes($service->duration) <= $endTime) {
                        $slotEnd = $current->copy()->addMinutes($service->duration);

                        // Check for overlapping bookings
                        $overlap = Booking::where('employee_id', $employee->id)
                            ->where('status', '!=', 'cancelled')
                            ->where(function ($q) use ($current, $slotEnd) {
                                $q->where('start_time', '<', $slotEnd)
                                    ->where('end_time', '>', $current);
                            })->exists();

                        $slots[$dateString][] = [
                            'time' => $current->format('Y-m-d\TH:i'),
                            'label' => $current->format('H:i'),
                            'available' => !$overlap,
                            'employee_id' => $employee->id,
                            'employee_name' => $employee->name,
                            'employee_bio' => $employee->bio ?? '',
                        ];

                        $current->addMinutes($interval);
                    }
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
