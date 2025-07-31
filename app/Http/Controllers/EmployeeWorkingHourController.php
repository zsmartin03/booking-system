<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeWorkingHour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeWorkingHourController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $employeeId = $request->query('employee_id');
        $employee = Employee::where('id', $employeeId)
            ->when($user->role !== 'admin', fn($q) => $q->whereHas('business', fn($q2) => $q2->where('user_id', $user->id)))
            ->firstOrFail();

        $businessWorkingHours = $employee->business->workingHours->keyBy('day_of_week');
        $employeeWorkingHours = $employee->workingHours->keyBy('day_of_week');
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $businessHoursArr = [];
        $employeeHoursArr = [];
        foreach ($days as $day) {
            $bwh = $businessWorkingHours[$day] ?? null;
            $businessHoursArr[$day] = [
                'enabled' => $bwh !== null,
                'start_time' => $bwh->start_time ?? '',
                'end_time' => $bwh->end_time ?? '',
            ];
            $ewh = $employeeWorkingHours[$day] ?? null;
            $employeeHoursArr[$day] = [
                'enabled' => $ewh !== null,
                'start_time' => $ewh->start_time ?? '',
                'end_time' => $ewh->end_time ?? '',
            ];
        }

        if ($request->expectsJson()) {
            return response()->json([
                'employee' => $employee,
                'businessWorkingHours' => $businessHoursArr,
                'employeeWorkingHours' => $employeeHoursArr,
            ]);
        }

        return view('employee-working-hours.index', [
            'employee' => $employee,
            'businessWorkingHours' => $businessHoursArr,
            'employeeWorkingHours' => $employeeHoursArr,
        ]);
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $employeeId = $request->query('employee_id');
        $employee = Employee::where('id', $employeeId)
            ->when($user->role !== 'admin', fn($q) => $q->whereHas('business', fn($q2) => $q2->where('user_id', $user->id)))
            ->firstOrFail();

        return view('employee-working-hours.create', compact('employee'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $employee = Employee::where('id', $request->employee_id)
            ->when($user->role !== 'admin', fn($q) => $q->whereHas('business', fn($q2) => $q2->where('user_id', $user->id)))
            ->firstOrFail();

        $validated = $request->validate([
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $validated['employee_id'] = $employee->id;

        EmployeeWorkingHour::create($validated);

        return redirect()->route('employee-working-hours.index', ['employee_id' => $employee->id])
            ->with('success', 'Working hour added.');
    }

    public function edit($id)
    {
        $user = Auth::user();
        $workingHour = EmployeeWorkingHour::findOrFail($id);
        $employee = Employee::where('id', $workingHour->employee_id)
            ->when($user->role !== 'admin', fn($q) => $q->whereHas('business', fn($q2) => $q2->where('user_id', $user->id)))
            ->firstOrFail();

        if (request()->expectsJson()) {
            return response()->json([
                'employee' => $employee,
                'workingHour' => $workingHour,
            ]);
        }

        return view('employee-working-hours.edit', compact('employee', 'workingHour'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $workingHour = EmployeeWorkingHour::findOrFail($id);
        $employee = Employee::where('id', $workingHour->employee_id)
            ->when($user->role !== 'admin', fn($q) => $q->whereHas('business', fn($q2) => $q2->where('user_id', $user->id)))
            ->firstOrFail();

        $validated = $request->validate([
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $workingHour->update($validated);

        return redirect()->route('employee-working-hours.index', ['employee_id' => $employee->id])
            ->with('success', 'Working hour updated.');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $workingHour = EmployeeWorkingHour::findOrFail($id);
        $employee = Employee::where('id', $workingHour->employee_id)
            ->when($user->role !== 'admin', fn($q) => $q->whereHas('business', fn($q2) => $q2->where('user_id', $user->id)))
            ->firstOrFail();

        $workingHour->delete();

        return redirect()->route('employee-working-hours.index', ['employee_id' => $employee->id])
            ->with('success', 'Working hour delfeted.');
    }

    public function bulkUpdate(Request $request)
    {
        $user = Auth::user();
        $employee = Employee::where('id', $request->employee_id)
            ->when($user->role !== 'admin', fn($q) => $q->whereHas('business', fn($q2) => $q2->where('user_id', $user->id)))
            ->firstOrFail();
        $businessWorkingHours = $employee->business->workingHours->keyBy('day_of_week');
        $input = $request->input('working_hours', []);
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $errors = [];
        foreach ($days as $day) {
            if (empty($input[$day]['enabled'])) {
                continue;
            }
            $start = $input[$day]['start_time'] ?? null;
            $end = $input[$day]['end_time'] ?? null;
            $bwh = $businessWorkingHours[$day] ?? null;
            // Accept both H:i and H:i:s
            $timePattern = '/^\d{2}:\d{2}(:\d{2})?$/';
            if (!$start || !preg_match($timePattern, $start)) {
                $errors[] = __('validation.date_format', ['attribute' => "working hours.$day.start time", 'format' => 'H:i']);
            }
            if (!$end || !preg_match($timePattern, $end)) {
                $errors[] = __('validation.date_format', ['attribute' => "working hours.$day.end time", 'format' => 'H:i']);
            }
            // Normalize to H:i:s for DB
            if ($start && strlen($start) === 5) $start .= ':00';
            if ($end && strlen($end) === 5) $end .= ':00';
            if ($start && $end && $start >= $end) {
                $errors[] = __('validation.after', ['attribute' => "working hours.$day.end time", 'date' => "working hours.$day.start time"]);
            }
            if (!$bwh) {
                $errors[] = __('messages.business_closed_on_day', ['day' => __("messages.$day")]);
            } elseif ($start && $end && ($start < $bwh->start_time || $end > $bwh->end_time)) {
                $errors[] = __('messages.employee_hours_outside_business', [
                    'day' => __("messages.$day"),
                    'business_start' => $bwh->start_time,
                    'business_end' => $bwh->end_time,
                ]);
            }
            $input[$day]['start_time'] = $start;
            $input[$day]['end_time'] = $end;
        }
        if ($errors) {
            return back()->withErrors($errors)->withInput();
        }

        foreach ($days as $day) {
            if (empty($input[$day]['enabled'])) {
                $ewh = $employee->workingHours()->where('day_of_week', $day)->first();
                if ($ewh) {
                    $ewh->delete();
                }
                continue;
            }
            $start = $input[$day]['start_time'] ?? null;
            $end = $input[$day]['end_time'] ?? null;
            $ewh = $employee->workingHours()->where('day_of_week', $day)->first();
            if ($start && $end) {
                if ($ewh) {
                    $ewh->update(['start_time' => $start, 'end_time' => $end]);
                } else {
                    $employee->workingHours()->create([
                        'day_of_week' => $day,
                        'start_time' => $start,
                        'end_time' => $end,
                    ]);
                }
            }
        }
        return redirect()->route('employee-working-hours.index', ['employee_id' => $employee->id])
            ->with('success', __('messages.working_hours_updated'));
    }
}
