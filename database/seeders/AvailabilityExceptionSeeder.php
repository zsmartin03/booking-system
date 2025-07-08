<?php

namespace Database\Seeders;

use App\Models\AvailabilityException;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class AvailabilityExceptionSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::all();
        $today = Carbon::now();

        foreach ($employees as $employee) {
            // Only create exceptions for half of the employees to reduce volume
            if (rand(1, 2) === 1) {
                continue; // Skip this employee
            }

            // Create some availability exceptions for each employee
            $exceptions = [];

            // Tomorrow - Employee taking a long lunch
            $exceptions[] = [
                'employee_id' => $employee->id,
                'date' => $today->copy()->addDay()->addWeek()->format('Y-m-d'),
                'start_time' => '12:00:00',
                'end_time' => '14:00:00',
                'type' => 'unavailable',
                'note' => 'Extended lunch break - doctor appointment',
            ];

            // Day after tomorrow - Employee working extended hours
            $exceptions[] = [
                'employee_id' => $employee->id,
                'date' => $today->copy()->addDays(2)->addWeek()->format('Y-m-d'),
                'start_time' => '07:00:00',
                'end_time' => '09:00:00',
                'type' => 'available',
                'note' => 'Early morning availability for special clients',
            ];

            // Next week Monday - Employee unavailable in the morning
            $nextMonday = $today->copy()->addWeek()->next(Carbon::MONDAY);
            $exceptions[] = [
                'employee_id' => $employee->id,
                'date' => $nextMonday->format('Y-m-d'),
                'start_time' => '09:00:00',
                'end_time' => '11:00:00',
                'type' => 'unavailable',
                'note' => 'Training session - not available for appointments',
            ];

            // Next week Wednesday - Employee taking afternoon off
            $nextWednesday = $today->copy()->addWeek()->next(Carbon::WEDNESDAY);
            $exceptions[] = [
                'employee_id' => $employee->id,
                'date' => $nextWednesday->format('Y-m-d'),
                'start_time' => '13:00:00',
                'end_time' => '18:00:00',
                'type' => 'unavailable',
                'note' => 'Personal appointment - afternoon unavailable',
            ];

            // Next week Friday - Employee working late
            $nextFriday = $today->copy()->addWeek()->next(Carbon::FRIDAY);
            $exceptions[] = [
                'employee_id' => $employee->id,
                'date' => $nextFriday->format('Y-m-d'),
                'start_time' => '18:00:00',
                'end_time' => '20:00:00',
                'type' => 'available',
                'note' => 'Extended evening hours for weekend preparation',
            ];

            // Weekend availability for some employees (randomly)
            if (rand(1, 3) === 1) { // 1/3 chance
                $nextSaturday = $today->copy()->addWeek()->next(Carbon::SATURDAY);
                $exceptions[] = [
                    'employee_id' => $employee->id,
                    'date' => $nextSaturday->format('Y-m-d'),
                    'start_time' => '10:00:00',
                    'end_time' => '14:00:00',
                    'type' => 'available',
                    'note' => 'Special weekend availability for urgent appointments',
                ];
            }

            // Holiday/Vacation - full day unavailable (some employees)
            if (rand(1, 4) === 1) { // 1/4 chance
                $vacationDay = $today->copy()->addWeek()->addDays(rand(7, 21));
                $exceptions[] = [
                    'employee_id' => $employee->id,
                    'date' => $vacationDay->format('Y-m-d'),
                    'start_time' => '09:00:00',
                    'end_time' => '18:00:00',
                    'type' => 'unavailable',
                    'note' => 'Vacation day - not available for appointments',
                ];
            }

            // Lunch break exceptions (some days)
            for ($i = 1; $i <= 5; $i++) {
                if (rand(1, 3) === 1) { // 1/3 chance each day
                    $date = $today->copy()->addWeek()->addDays($i);
                    $exceptions[] = [
                        'employee_id' => $employee->id,
                        'date' => $date->format('Y-m-d'),
                        'start_time' => '12:30:00',
                        'end_time' => '13:30:00',
                        'type' => 'unavailable',
                        'note' => 'Lunch break extension',
                    ];
                }
            }

            // Create only a subset of exceptions for each employee (3-5 exceptions)
            $selectedExceptions = array_slice($exceptions, 0, rand(3, 5));

            foreach ($selectedExceptions as $exceptionData) {
                AvailabilityException::create($exceptionData);
            }
        }

        // Add some specific exceptions for demonstration purposes
        $demoEmployee = Employee::first();
        if ($demoEmployee) {
            // Today - short unavailable period
            AvailabilityException::create([
                'employee_id' => $demoEmployee->id,
                'date' => $today->format('Y-m-d'),
                'start_time' => '15:00:00',
                'end_time' => '16:00:00',
                'type' => 'unavailable',
                'note' => 'Team meeting - not available for appointments',
            ]);

            // Tomorrow - extended availability
            AvailabilityException::create([
                'employee_id' => $demoEmployee->id,
                'date' => $today->copy()->addDay()->format('Y-m-d'),
                'start_time' => '19:00:00',
                'end_time' => '21:00:00',
                'type' => 'available',
                'note' => 'Extended evening hours by request',
            ]);
        }
    }
}
