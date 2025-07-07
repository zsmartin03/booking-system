<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Review extends Model
{
    protected $fillable = [
        'business_id',
        'user_id',
        'rating',
        'comment',
        'has_booking'
    ];

    protected $casts = [
        'has_booking' => 'boolean',
        'rating' => 'integer'
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function response(): HasOne
    {
        return $this->hasOne(ReviewResponse::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(ReviewVote::class);
    }

    public function upvotes(): HasMany
    {
        return $this->hasMany(ReviewVote::class)->where('is_upvote', true);
    }

    public function downvotes(): HasMany
    {
        return $this->hasMany(ReviewVote::class)->where('is_upvote', false);
    }

    public function getUpvotesCountAttribute(): int
    {
        return $this->upvotes()->count();
    }

    public function getDownvotesCountAttribute(): int
    {
        return $this->downvotes()->count();
    }

    public function getNetVotesAttribute(): int
    {
        return $this->upvotes_count - $this->downvotes_count;
    }

    public function userHasVoted($userId): bool
    {
        return $this->votes()->where('user_id', $userId)->exists();
    }

    public function getUserVoteType($userId): ?bool
    {
        $vote = $this->votes()->where('user_id', $userId)->first();
        return $vote ? $vote->is_upvote : null;
    }
}
