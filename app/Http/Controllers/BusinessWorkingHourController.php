<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\BusinessWorkingHour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BusinessWorkingHourController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $businessId = $request->query('business_id');
        $business = Business::where('id', $businessId)
            ->when($user->role !== 'admin', fn($q) => $q->where('user_id', $user->id))
            ->firstOrFail();

        $workingHours = $business->workingHours()->orderBy('day_of_week')->get();

        return view('business-working-hours.index', compact('business', 'workingHours'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $businessId = $request->query('business_id');
        $business = Business::where('id', $businessId)
            ->when($user->role !== 'admin', fn($q) => $q->where('user_id', $user->id))
            ->firstOrFail();

        return view('business-working-hours.create', compact('business'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $business = Business::where('id', $request->business_id)
            ->when($user->role !== 'admin', fn($q) => $q->where('user_id', $user->id))
            ->firstOrFail();

        $validated = $request->validate([
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $validated['business_id'] = $business->id;

        BusinessWorkingHour::create($validated);

        return redirect()->route('business-working-hours.index', ['business_id' => $business->id])
            ->with('success', 'Working hour added.');
    }

    public function edit($id)
    {
        $user = Auth::user();
        $workingHour = BusinessWorkingHour::findOrFail($id);
        $business = Business::where('id', $workingHour->business_id)
            ->when($user->role !== 'admin', fn($q) => $q->where('user_id', $user->id))
            ->firstOrFail();

        return view('business-working-hours.edit', compact('business', 'workingHour'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $workingHour = BusinessWorkingHour::findOrFail($id);
        $business = Business::where('id', $workingHour->business_id)
            ->when($user->role !== 'admin', fn($q) => $q->where('user_id', $user->id))
            ->firstOrFail();

        $validated = $request->validate([
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $workingHour->update($validated);

        return redirect()->route('business-working-hours.index', ['business_id' => $business->id])
            ->with('success', 'Working hour updated.');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $workingHour = BusinessWorkingHour::findOrFail($id);
        $business = Business::where('id', $workingHour->business_id)
            ->when($user->role !== 'admin', fn($q) => $q->where('user_id', $user->id))
            ->firstOrFail();

        $workingHour->delete();

        return redirect()->route('business-working-hours.index', ['business_id' => $business->id])
            ->with('success', 'Working hour deleted.');
    }

    public function bulkUpdate(Request $request)
    {
        $user = Auth::user();
        $business = Business::where('id', $request->business_id)
            ->when($user->role !== 'admin', fn($q) => $q->where('user_id', $user->id))
            ->firstOrFail();
        $input = $request->input('working_hours', []);
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $errors = [];
        $newBusinessHours = [];
        foreach ($days as $day) {
            $enabled = !empty($input[$day]['enabled']);
            $start = $input[$day]['start_time'] ?? null;
            $end = $input[$day]['end_time'] ?? null;
            $timePattern = '/^\d{2}:\d{2}(:\d{2})?$/';
            if ($enabled) {
                if ($start && strlen($start) === 5) $start .= ':00';
                if ($end && strlen($end) === 5) $end .= ':00';
                if (!$start || !preg_match($timePattern, $start)) {
                    $errors[] = __('validation.date_format', ['attribute' => "business hours.$day.start time", 'format' => 'H:i']);
                }
                if (!$end || !preg_match($timePattern, $end)) {
                    $errors[] = __('validation.date_format', ['attribute' => "business hours.$day.end time", 'format' => 'H:i']);
                }

                if ($start && $end && $start >= $end) {
                    $errors[] = __('validation.after', ['attribute' => "business hours.$day.end time", 'date' => "business hours.$day.start time"]);
                }
            }
            $newBusinessHours[$day] = [
                'enabled' => $enabled,
                'start_time' => $start,
                'end_time' => $end,
            ];
        }
        if ($errors) {
            return back()->withErrors($errors)->withInput();
        }

        $conflicts = [];
        foreach ($business->employees as $employee) {
            foreach ($days as $day) {
                $empHour = $employee->workingHours()->where('day_of_week', $day)->first();
                if ($empHour) {
                    $bwh = $newBusinessHours[$day];
                    if (!$bwh['enabled'] || $empHour->start_time < $bwh['start_time'] || $empHour->end_time > $bwh['end_time']) {
                        $conflicts[$day][] = [
                            'employee' => $employee,
                            'start_time' => $empHour->start_time,
                            'end_time' => $empHour->end_time,
                        ];
                    }
                }
            }
        }
        $confirm = $request->input('confirm_delete_conflicts');
        if ($conflicts && !$confirm) {
            return back()->withInput()->with(['conflicts' => $conflicts]);
        }

        if ($conflicts && $confirm) {
            foreach ($conflicts as $day => $list) {
                foreach ($list as $conflict) {
                    $conflict['employee']->workingHours()->where('day_of_week', $day)->delete();
                }
            }
        }
        foreach ($days as $day) {
            $bwh = $business->workingHours()->where('day_of_week', $day)->first();
            $enabled = $newBusinessHours[$day]['enabled'];
            $start = $newBusinessHours[$day]['start_time'];
            $end = $newBusinessHours[$day]['end_time'];
            if ($enabled && $start && $end) {
                if ($bwh) {
                    $bwh->update(['start_time' => $start, 'end_time' => $end]);
                } else {
                    $business->workingHours()->create([
                        'day_of_week' => $day,
                        'start_time' => $start,
                        'end_time' => $end,
                    ]);
                }
            } else {
                if ($bwh) {
                    $bwh->delete();
                }
            }
        }
        return redirect()->route('business-working-hours.index', ['business_id' => $business->id])
            ->with('success', __('messages.working_hours_updated'));
    }
}
