<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\User;
use App\Models\Service;
use App\Models\Employee;
use App\Models\AvailabilityException;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $clients = User::where('role', 'client')->get();
        $employees = Employee::all();
        $today = Carbon::now();

        $bookings = [];

        foreach ($employees as $employee) {
            $services = $employee->services;
            if ($services->isEmpty()) continue;

            $todayBookings = $this->createBookingsForDay($employee, $services, $clients, $today, 2);
            $bookings = array_merge($bookings, $todayBookings);

            $tomorrowBookings = $this->createBookingsForDay($employee, $services, $clients, $today->copy()->addDay(), 3);
            $bookings = array_merge($bookings, $tomorrowBookings);

            $dayAfterBookings = $this->createBookingsForDay($employee, $services, $clients, $today->copy()->addDays(2), 2);
            $bookings = array_merge($bookings, $dayAfterBookings);

            for ($i = 7; $i <= 10; $i++) {
                $futureBookings = $this->createBookingsForDay($employee, $services, $clients, $today->copy()->addDays($i), 1);
                $bookings = array_merge($bookings, $futureBookings);
            }
        }

        $this->createOverlappingBookings($employees, $clients, $today);

        foreach ($bookings as $booking) {
            Booking::create($booking);
        }
    }

    private function createBookingsForDay($employee, $services, $clients, $date, $maxBookings)
    {
        $bookings = [];
        $numBookings = rand(0, $maxBookings);

        $dayOfWeek = strtolower($date->format('l'));
        $workingHour = $employee->workingHours()->where('day_of_week', $dayOfWeek)->first();

        if (!$workingHour) {
            return $bookings;
        }

        $startHour = Carbon::parse($workingHour->start_time)->hour;
        $endHour = Carbon::parse($workingHour->end_time)->hour;

        $usedTimeSlots = [];

        for ($i = 0; $i < $numBookings; $i++) {
            $service = $services->random();
            $client = $clients->random();

            $attempts = 0;
            do {
                $startTime = $date->copy()->setHour(rand($startHour, $endHour - 2))->setMinute(rand(0, 1) * 30);
                $endTime = $startTime->copy()->addMinutes($service->duration);

                $timeSlot = $startTime->format('H:i') . '-' . $endTime->format('H:i');
                $isAvailable = $this->isEmployeeAvailable($employee, $startTime, $endTime);
                $attempts++;
            } while ((in_array($timeSlot, $usedTimeSlots) || !$isAvailable) && $attempts < 20);

            if ($attempts >= 20) continue;

            $usedTimeSlots[] = $timeSlot;

            $bookings[] = [
                'client_id' => $client->id,
                'service_id' => $service->id,
                'employee_id' => $employee->id,
                'start_time' => $startTime->format('Y-m-d H:i:s'),
                'end_time' => $endTime->format('Y-m-d H:i:s'),
                'status' => $this->getRandomStatus($date),
                'notes' => $this->getRandomNotes(),
                'total_price' => $service->price,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        return $bookings;
    }

    private function isEmployeeAvailable($employee, $startTime, $endTime)
    {
        $date = $startTime->format('Y-m-d');
        $startTimeOnly = $startTime->format('H:i:s');
        $endTimeOnly = $endTime->format('H:i:s');

        $exceptions = AvailabilityException::where('employee_id', $employee->id)
            ->where('date', $date)
            ->get();

        foreach ($exceptions as $exception) {
            $exceptionStart = $exception->start_time;
            $exceptionEnd = $exception->end_time;

            $hasOverlap = ($startTimeOnly < $exceptionEnd && $endTimeOnly > $exceptionStart);

            if ($hasOverlap) {
                if ($exception->type === 'unavailable') {
                    return false;
                }
            }
        }

        $existingBooking = Booking::where('employee_id', $employee->id)
            ->whereDate('start_time', $date)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<', $endTime)
                        ->where('end_time', '>', $startTime);
                });
            })
            ->exists();

        return !$existingBooking;
    }

    private function createOverlappingBookings($employees, $clients, $today)
    {
        $employee = $employees->first();
        $services = $employee->services;

        if ($services->isEmpty()) return;

        $service = $services->first();
        $tomorrow = $today->copy()->addDay();

        $attempts = 0;
        do {
            $startTime1 = $tomorrow->copy()->setHour(rand(10, 15))->setMinute(0);
            $endTime1 = $startTime1->copy()->addMinutes($service->duration);
            $isAvailable = $this->isEmployeeAvailable($employee, $startTime1, $endTime1);
            $attempts++;
        } while (!$isAvailable && $attempts < 10);

        if ($attempts < 10) {
            Booking::create([
                'client_id' => $clients->random()->id,
                'service_id' => $service->id,
                'employee_id' => $employee->id,
                'start_time' => $startTime1->format('Y-m-d H:i:s'),
                'end_time' => $endTime1->format('Y-m-d H:i:s'),
                'status' => 'confirmed',
                'notes' => 'First appointment - will create conflict',
                'total_price' => $service->price,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $startTime2 = $startTime1->copy()->addMinutes(30);
            $endTime2 = $startTime2->copy()->addMinutes($service->duration);

            Booking::create([
                'client_id' => $clients->random()->id,
                'service_id' => $service->id,
                'employee_id' => $employee->id,
                'start_time' => $startTime2->format('Y-m-d H:i:s'),
                'end_time' => $endTime2->format('Y-m-d H:i:s'),
                'status' => 'pending',
                'notes' => 'Overlapping appointment - demonstrates conflict',
                'total_price' => $service->price,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if ($employees->count() > 1) {
            $employee2 = $employees->skip(1)->first();
            $services2 = $employee2->services;

            if (!$services2->isEmpty()) {
                $service2 = $services2->first();
                $dayAfterTomorrow = $today->copy()->addDays(2);

                $attempts2 = 0;
                do {
                    $startTime3 = $dayAfterTomorrow->copy()->setHour(rand(10, 15))->setMinute(0);
                    $endTime3 = $startTime3->copy()->addMinutes($service2->duration);
                    $isAvailable2 = $this->isEmployeeAvailable($employee2, $startTime3, $endTime3);
                    $attempts2++;
                } while (!$isAvailable2 && $attempts2 < 10);

                if ($attempts2 < 10) {
                    Booking::create([
                        'client_id' => $clients->random()->id,
                        'service_id' => $service2->id,
                        'employee_id' => $employee2->id,
                        'start_time' => $startTime3->format('Y-m-d H:i:s'),
                        'end_time' => $endTime3->format('Y-m-d H:i:s'),
                        'status' => 'confirmed',
                        'notes' => 'Regular appointment',
                        'total_price' => $service2->price,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $startTime4 = $startTime3->copy()->addMinutes(15);
                    $endTime4 = $startTime4->copy()->addMinutes($service2->duration);

                    Booking::create([
                        'client_id' => $clients->random()->id,
                        'service_id' => $service2->id,
                        'employee_id' => $employee2->id,
                        'start_time' => $startTime4->format('Y-m-d H:i:s'),
                        'end_time' => $endTime4->format('Y-m-d H:i:s'),
                        'status' => 'pending',
                        'notes' => 'Another overlapping appointment example',
                        'total_price' => $service2->price,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    private function getRandomStatus($date)
    {
        $today = Carbon::now();

        if ($date->isPast()) {
            return collect(['completed', 'cancelled'])->random();
        } elseif ($date->isToday()) {
            return collect(['confirmed', 'pending'])->random();
        } else {
            return collect(['confirmed', 'pending'])->random();
        }
    }

    private function getRandomNotes()
    {
        $notes = [
            'Regular appointment',
            'First time client',
            'Prefers morning appointments',
            'Has allergies - check products',
            'Needs extra time',
            'Special request for style',
            'Follow-up appointment',
            'Consultation required',
            'Bring own materials',
            'Client running late',
            null,
            null,
            null,
        ];

        return collect($notes)->random();
    }
}
