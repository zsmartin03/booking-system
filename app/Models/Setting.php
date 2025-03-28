<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['business_id', 'key', 'value'];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
