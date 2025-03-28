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
}
