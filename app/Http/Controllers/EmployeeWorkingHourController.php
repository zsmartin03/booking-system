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

        $workingHours = $employee->workingHours()->get();

        $dayOrder = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $workingHours = $workingHours->sortBy(function ($item) use ($dayOrder) {
            return array_search($item->day_of_week, $dayOrder);
        });

        return view('employee-working-hours.index', compact('employee', 'workingHours'));
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
}
