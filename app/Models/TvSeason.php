<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TvSeason extends Model
{
    use HasTranslations;

    protected $fillable = [
        'tv_show_id',
        'season_number',
        'release_year',
        'title',
        'description',
        'episode_count'
    ];

    protected $casts = [
        'tv_show_id' => 'integer',
        'season_number' => 'integer',
        'release_year' => 'integer',
        'title' => 'string',
        'description' => 'string',
        'episode_count' => 'integer'
    ];

    // Relations
    public function show(): BelongsTo
    {
        return $this->belongsTo(TvShow::class, 'tv_show_id');
    }

    public function episodes(): HasMany
    {
        return $this->hasMany(TvEpisode::class);
    }

    // Accesseurs
    public function getDisplayTitleAttribute(): string
    {
        if ($this->title) {
            return $this->translate('title', app()->getLocale()) ?? $this->title;
        }
        return sprintf('Season %d', $this->season_number);
    }

    // Scopes
    public function scopeByNumber($query, int $number)
    {
        return $query->where('season_number', $number);
    }
}
