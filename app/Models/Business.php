<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Business extends Model
{
    protected $fillable = ['user_id', 'name', 'description', 'address', 'phone_number', 'email', 'website', 'logo', 'latitude', 'longitude'];

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

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getAverageRatingAttribute(): float
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function getReviewsCountAttribute(): int
    {
        return $this->reviews()->count();
    }

    public function getLogoUrlAttribute(): ?string
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }
        return null;
    }
}
