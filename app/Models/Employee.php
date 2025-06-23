<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = ['business_id', 'user_id', 'name', 'email', 'bio', 'avatar', 'active'];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function workingHours()
    {
        return $this->hasMany(EmployeeWorkingHour::class);
    }

    public function availabilityExceptions()
    {
        return $this->hasMany(AvailabilityException::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_employee');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Check if employee can provide a specific service
     */
    public function canProvideService($serviceId)
    {
        return $this->active && $this->services()->where('service_id', $serviceId)->exists();
    }

    /**
     * Get working hours for a specific day
     */
    public function getWorkingHoursForDay($dayOfWeek)
    {
        return $this->workingHours()->where('day_of_week', $dayOfWeek)->get();
    }

    /**
     * Check if employee is available at a specific date and time
     */
    public function isAvailableAt($date, $time)
    {
        $dayOfWeek = strtolower($date->format('l'));

        // Check for availability exceptions first
        $exception = $this->availabilityExceptions()
            ->where('date', $date->toDateString())
            ->where('start_time', '<=', $time)
            ->where('end_time', '>', $time)
            ->first();

        if ($exception) {
            return $exception->type === 'available';
        }

        // Check regular working hours
        $workingHours = $this->getWorkingHoursForDay($dayOfWeek);

        foreach ($workingHours as $hours) {
            if ($time >= $hours->start_time && $time < $hours->end_time) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get all availability exceptions for a specific date
     */
    public function getAvailabilityExceptionsForDate($date)
    {
        return $this->availabilityExceptions()
            ->where('date', $date->toDateString())
            ->orderBy('start_time')
            ->get();
    }
}
