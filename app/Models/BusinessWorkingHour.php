<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessWorkingHour extends Model
{
    protected $fillable = ['business_id', 'day_of_week', 'start_time', 'end_time'];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
