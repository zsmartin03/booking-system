<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['business_id', 'name', 'description', 'price', 'duration', 'active'];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'service_employee');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
