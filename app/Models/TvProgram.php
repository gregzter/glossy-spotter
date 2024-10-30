<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TvProgram extends Model
{
    use HasTranslations;

    protected $fillable = [
        'tv_channel_id',
        'name',
        'description',
        'start_date',
        'end_date',
        'still_running',
        'frequency',
        'type'
    ];

    protected $casts = [
        'tv_channel_id' => 'integer',
        'name' => 'string',
        'description' => 'string',
        'start_date' => 'date',
        'end_date' => 'date',
        'still_running' => 'boolean',
        'frequency' => 'string',
        'type' => 'string'
    ];

    // Relations
    public function channel(): BelongsTo
    {
        return $this->belongsTo(TvChannel::class, 'tv_channel_id');
    }

    public function episodes(): HasMany
    {
        return $this->hasMany(TvProgramEpisode::class);
    }

    public function sources(): HasMany
    {
        return $this->hasMany(Source::class);
    }

    // Scopes
    public function scopeRunning($query)
    {
        return $query->where('still_running', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByFrequency($query, string $frequency)
    {
        return $query->where('frequency', $frequency);
    }

    // Accesseurs
    public function getDisplayNameAttribute(): string
    {
        return $this->translate('name', app()->getLocale()) ?? $this->name;
    }
}
