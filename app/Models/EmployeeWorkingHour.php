<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeWorkingHour extends Model
{
    protected $fillable = ['employee_id', 'day_of_week', 'start_time', 'end_time'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
