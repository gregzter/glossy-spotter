<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpotRating extends Model
{
    protected $fillable = [
        'spot_id',
        'user_id',
        'rating'
    ];

    protected $casts = [
        'spot_id' => 'integer',
        'user_id' => 'integer',
        'rating' => 'integer'
    ];

    // Relations
    public function spot(): BelongsTo
    {
        return $this->belongsTo(Spot::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeHighRated($query)
    {
        return $query->where('rating', '>=', 4);
    }
}
