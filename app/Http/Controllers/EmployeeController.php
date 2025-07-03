<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $businessId = $request->query('business_id');
        $business = Business::where('id', $businessId)
            ->when($user->role !== 'admin', fn($q) => $q->where('user_id', $user->id))
            ->firstOrFail();

        $employees = $business->employees()->get();

        return view('employees.index', compact('business', 'employees'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $businessId = $request->query('business_id');
        $business = Business::where('id', $businessId)
            ->when($user->role !== 'admin', fn($q) => $q->where('user_id', $user->id))
            ->firstOrFail();

        return view('employees.create', compact('business'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $business = Business::where('id', $request->business_id)
            ->when($user->role !== 'admin', fn($q) => $q->where('user_id', $user->id))
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'bio' => 'nullable|string',
            'avatar' => 'nullable|image|max:2048',
            'active' => 'boolean',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $validated['business_id'] = $business->id;

        $employeeUser = \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => 'employee',
        ]);

        event(new Registered($employeeUser)); // This sends the verification email

        $validated['user_id'] = $employeeUser->id;

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        Employee::create($validated);

        return redirect()->route('employees.index', ['business_id' => $business->id])
            ->with('success', 'Employee created.');
    }

    public function edit($id)
    {
        $user = Auth::user();
        $employee = Employee::findOrFail($id);
        $business = Business::where('id', $employee->business_id)
            ->when($user->role !== 'admin', fn($q) => $q->where('user_id', $user->id))
            ->firstOrFail();

        return view('employees.edit', compact('business', 'employee'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $employee = Employee::findOrFail($id);
        $business = Business::where('id', $employee->business_id)
            ->when($user->role !== 'admin', fn($q) => $q->where('user_id', $user->id))
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'bio' => 'nullable|string',
            'avatar' => 'nullable|image|max:2048',
            'active' => 'boolean',
        ]);

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $employee->update($validated);

        return redirect()->route('employees.index', ['business_id' => $business->id])
            ->with('success', 'Employee updated.');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $employee = Employee::findOrFail($id);
        $business = Business::where('id', $employee->business_id)
            ->when($user->role !== 'admin', fn($q) => $q->where('user_id', $user->id))
            ->firstOrFail();

        $employee->delete();

        return redirect()->route('employees.index', ['business_id' => $business->id])
            ->with('success', 'Employee deleted.');
    }
}
