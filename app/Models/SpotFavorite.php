<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpotFavorite extends Model
{
    protected $fillable = [
        'spot_id',
        'user_id'
    ];

    public $timestamps = false;

    protected $casts = [
        'spot_id' => 'integer',
        'user_id' => 'integer',
        'created_at' => 'datetime'
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
}
