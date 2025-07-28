<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReviewVote extends Model
{
    use HasFactory;
    protected $fillable = [
        'review_id',
        'user_id',
        'is_upvote'
    ];

    protected $casts = [
        'is_upvote' => 'boolean'
    ];

    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
