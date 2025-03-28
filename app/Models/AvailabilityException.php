<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AvailabilityException extends Model
{
    protected $fillable = ['employee_id', 'date', 'start_time', 'end_time', 'type', 'note'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
