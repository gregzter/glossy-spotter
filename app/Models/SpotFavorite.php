<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpotFavorite extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'spot_id',
        'user_id'
    ];

    public function spot(): BelongsTo
    {
        return $this->belongsTo(Spot::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
