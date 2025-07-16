<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['user_id', 'booking_id', 'title', 'content', 'is_read', 'sent_at', 'read_at'];

    protected $casts = [
        'sent_at' => 'datetime',
        'read_at' => 'datetime',
        'is_read' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the translated title for the notification
     */
    public function getTranslatedTitleAttribute()
    {
        return __($this->title);
    }

    /**
     * Get the translated content for the notification
     */
    public function getTranslatedContentAttribute()
    {
        if (str_contains($this->content, '|')) {
            [$translationKey, $parametersJson] = explode('|', $this->content, 2);
            $parameters = json_decode($parametersJson, true) ?? [];

            if (isset($parameters['previous_status'])) {
                $parameters['previous_status'] = __('messages.' . $parameters['previous_status']);
            }
            if (isset($parameters['current_status'])) {
                $parameters['current_status'] = __('messages.' . $parameters['current_status']);
            }

            return __($translationKey, $parameters);
        }

        return $this->content;
    }
}
