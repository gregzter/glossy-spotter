<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpotComment extends Model
{
    protected $fillable = [
        'spot_id',
        'user_id',
        'content',
        'is_edited',
        'edited_at'
    ];

    protected $casts = [
        'spot_id' => 'integer',
        'user_id' => 'integer',
        'content' => 'string',
        'is_edited' => 'boolean',
        'edited_at' => 'datetime'
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
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // Méthodes utilitaires
    public function edit(string $newContent): bool
    {
        return $this->update([
            'content' => $newContent,
            'is_edited' => true,
            'edited_at' => now()
        ]);
    }
}
