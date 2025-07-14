<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'role',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function businesses()
    {
        return $this->hasMany(Business::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function employee()
    {
        return $this->hasOne(\App\Models\Employee::class);
    }

    public function clientBookings()
    {
        return $this->hasMany(Booking::class, 'client_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function reviewVotes()
    {
        return $this->hasMany(ReviewVote::class);
    }

    public function reviewResponses()
    {
        return $this->hasMany(ReviewResponse::class);
    }

    public function hasBookingWithBusiness($businessId): bool
    {
        return $this->clientBookings()
            ->whereHas('service', function ($query) use ($businessId) {
                $query->where('business_id', $businessId);
            })
            ->where('status', '!=', 'cancelled')
            ->exists();
    }

    /**
     * Get the user's avatar URL
     */
    public function getAvatarUrl(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }

        return null;
    }

    /**
     * Get the user's avatar or initials fallback
     */
    public function getAvatarOrInitials(): array
    {
        return [
            'avatar' => $this->getAvatarUrl(),
            'initials' => substr($this->name, 0, 1),
            'name' => $this->name
        ];
    }

    /**
     * Override email verification notification to skip example.com addresses
     */
    public function sendEmailVerificationNotification()
    {
        if (str_ends_with($this->email, 'example.com')) {
            return;
        }

        parent::sendEmailVerificationNotification();
    }
}
