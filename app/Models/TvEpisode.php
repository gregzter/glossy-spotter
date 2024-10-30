<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TvEpisode extends Model
{
    use HasTranslations;

    protected $fillable = [
        'tv_season_id',
        'episode_number',
        'title',
        'air_date',
        'description'
    ];

    protected $casts = [
        'tv_season_id' => 'integer',
        'episode_number' => 'integer',
        'title' => 'string',
        'air_date' => 'date',
        'description' => 'string'
    ];

    // Relations
    public function season(): BelongsTo
    {
        return $this->belongsTo(TvSeason::class, 'tv_season_id');
    }

    public function sources(): HasMany
    {
        return $this->hasMany(Source::class);
    }

    // Accesseurs
    public function getDisplayTitleAttribute(): string
    {
        return $this->translate('title', app()->getLocale()) ?? $this->title;
    }

    public function getEpisodeCodeAttribute(): string
    {
        return sprintf('S%02dE%02d',
            $this->season->season_number,
            $this->episode_number
        );
    }

    // Scopes
    public function scopeAired($query)
    {
        return $query->where('air_date', '<=', now());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('air_date', '>', now());
    }
}
