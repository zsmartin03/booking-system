<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = ['client_id', 'service_id', 'employee_id', 'start_time', 'end_time', 'status', 'notes', 'total_price'];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'total_price' => 'decimal:2',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
