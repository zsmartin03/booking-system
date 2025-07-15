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

    // Statistics methods
    public function bookings()
    {
        return $this->hasManyThrough(Booking::class, Service::class);
    }

    public function getTotalBookingsAttribute(): int
    {
        return $this->bookings()->count();
    }

    public function getTotalRevenueAttribute(): float
    {
        return $this->bookings()->where('status', 'completed')->sum('total_price');
    }

    public function getTotalCustomersAttribute(): int
    {
        return $this->bookings()->distinct('client_id')->count('client_id');
    }

    public function getMostBookedServicesAttribute()
    {
        return $this->services()
            ->withCount('bookings')
            ->orderBy('bookings_count', 'desc')
            ->take(5)
            ->get();
    }

    public function getBookingsPerPeriod(string $period = 'month', int $limit = 12)
    {
        $query = $this->bookings()
            ->groupBy('period')
            ->orderBy('period', 'desc')
            ->limit($limit);

        // Check database driver for compatibility
        $driver = config('database.default');
        $connection = config("database.connections.{$driver}.driver");

        switch ($period) {
            case 'week':
                if ($connection === 'sqlite') {
                    $query->selectRaw("strftime('%Y-W%W', start_time) as period, COUNT(*) as count");
                } else {
                    $query->selectRaw("CONCAT(YEAR(start_time), '-W', LPAD(WEEK(start_time), 2, '0')) as period, COUNT(*) as count");
                }
                break;
            case 'day':
                if ($connection === 'sqlite') {
                    $query->selectRaw("date(start_time) as period, COUNT(*) as count");
                } else {
                    $query->selectRaw("DATE(start_time) as period, COUNT(*) as count");
                }
                break;
            default: // month
                if ($connection === 'sqlite') {
                    $query->selectRaw("strftime('%Y-%m', start_time) as period, COUNT(*) as count");
                } else {
                    $query->selectRaw("DATE_FORMAT(start_time, '%Y-%m') as period, COUNT(*) as count");
                }
                break;
        }

        return $query->get();
    }

    public function getRevenuePerPeriod(string $period = 'month', int $limit = 12)
    {
        $query = $this->bookings()
            ->where('status', 'completed')
            ->groupBy('period')
            ->orderBy('period', 'desc')
            ->limit($limit);

        // Check database driver for compatibility
        $driver = config('database.default');
        $connection = config("database.connections.{$driver}.driver");

        switch ($period) {
            case 'week':
                if ($connection === 'sqlite') {
                    $query->selectRaw("strftime('%Y-W%W', start_time) as period, SUM(total_price) as revenue");
                } else {
                    $query->selectRaw("CONCAT(YEAR(start_time), '-W', LPAD(WEEK(start_time), 2, '0')) as period, SUM(total_price) as revenue");
                }
                break;
            case 'day':
                if ($connection === 'sqlite') {
                    $query->selectRaw("date(start_time) as period, SUM(total_price) as revenue");
                } else {
                    $query->selectRaw("DATE(start_time) as period, SUM(total_price) as revenue");
                }
                break;
            default: // month
                if ($connection === 'sqlite') {
                    $query->selectRaw("strftime('%Y-%m', start_time) as period, SUM(total_price) as revenue");
                } else {
                    $query->selectRaw("DATE_FORMAT(start_time, '%Y-%m') as period, SUM(total_price) as revenue");
                }
                break;
        }

        return $query->get();
    }
}
