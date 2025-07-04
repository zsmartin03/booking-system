<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    protected $fillable = ['user_id', 'name', 'description', 'address', 'phone_number', 'email', 'website', 'logo'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function workingHours()
    {
        return $this->hasMany(BusinessWorkingHour::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function settings()
    {
        return $this->hasMany(Setting::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
}
