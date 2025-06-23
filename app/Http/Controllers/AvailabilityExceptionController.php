<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\AvailabilityException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AvailabilityExceptionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $employeeId = $request->query('employee_id');
        $employee = Employee::where('id', $employeeId)
            ->when($user->role !== 'admin', fn($q) => $q->whereHas('business', fn($q2) => $q2->where('user_id', $user->id)))
            ->firstOrFail();

        $exceptions = $employee->availabilityExceptions()
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'asc')
            ->get();

        return view('availability-exceptions.index', compact('employee', 'exceptions'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $employeeId = $request->query('employee_id');
        $employee = Employee::where('id', $employeeId)
            ->when($user->role !== 'admin', fn($q) => $q->whereHas('business', fn($q2) => $q2->where('user_id', $user->id)))
            ->firstOrFail();

        return view('availability-exceptions.create', compact('employee'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $employee = Employee::where('id', $request->employee_id)
            ->when($user->role !== 'admin', fn($q) => $q->whereHas('business', fn($q2) => $q2->where('user_id', $user->id)))
            ->firstOrFail();

        $validated = $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'type' => 'required|in:available,unavailable',
            'note' => 'nullable|string|max:500',
        ]);

        $validated['employee_id'] = $employee->id;

        // Check for overlapping exceptions
        $overlap = AvailabilityException::where('employee_id', $employee->id)
            ->where('date', $validated['date'])
            ->where(function ($q) use ($validated) {
                $q->where(function ($subQ) use ($validated) {
                    $subQ->where('start_time', '<=', $validated['start_time'])
                        ->where('end_time', '>', $validated['start_time']);
                })->orWhere(function ($subQ) use ($validated) {
                    $subQ->where('start_time', '<', $validated['end_time'])
                        ->where('end_time', '>=', $validated['end_time']);
                });
            })
            ->exists();

        if ($overlap) {
            return back()->withErrors(['time' => 'This time period overlaps with an existing exception.'])->withInput();
        }

        AvailabilityException::create($validated);

        return redirect()->route('availability-exceptions.index', ['employee_id' => $employee->id])
            ->with('success', 'Availability exception created successfully.');
    }

    public function edit($id)
    {
        $user = Auth::user();
        $exception = AvailabilityException::findOrFail($id);
        $employee = Employee::where('id', $exception->employee_id)
            ->when($user->role !== 'admin', fn($q) => $q->whereHas('business', fn($q2) => $q2->where('user_id', $user->id)))
            ->firstOrFail();

        return view('availability-exceptions.edit', compact('employee', 'exception'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $exception = AvailabilityException::findOrFail($id);
        $employee = Employee::where('id', $exception->employee_id)
            ->when($user->role !== 'admin', fn($q) => $q->whereHas('business', fn($q2) => $q2->where('user_id', $user->id)))
            ->firstOrFail();

        $validated = $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'type' => 'required|in:available,unavailable',
            'note' => 'nullable|string|max:500',
        ]);

        // Check for overlapping exceptions (excluding current exception)
        $overlap = AvailabilityException::where('employee_id', $employee->id)
            ->where('date', $validated['date'])
            ->where('id', '!=', $exception->id)
            ->where(function ($q) use ($validated) {
                $q->where(function ($subQ) use ($validated) {
                    $subQ->where('start_time', '<=', $validated['start_time'])
                        ->where('end_time', '>', $validated['start_time']);
                })->orWhere(function ($subQ) use ($validated) {
                    $subQ->where('start_time', '<', $validated['end_time'])
                        ->where('end_time', '>=', $validated['end_time']);
                });
            })
            ->exists();

        if ($overlap) {
            return back()->withErrors(['time' => 'This time period overlaps with an existing exception.'])->withInput();
        }

        $exception->update($validated);

        return redirect()->route('availability-exceptions.index', ['employee_id' => $employee->id])
            ->with('success', 'Availability exception updated successfully.');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $exception = AvailabilityException::findOrFail($id);
        $employee = Employee::where('id', $exception->employee_id)
            ->when($user->role !== 'admin', fn($q) => $q->whereHas('business', fn($q2) => $q2->where('user_id', $user->id)))
            ->firstOrFail();

        $exception->delete();

        return redirect()->route('availability-exceptions.index', ['employee_id' => $employee->id])
            ->with('success', 'Availability exception deleted successfully.');
    }
}
