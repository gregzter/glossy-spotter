<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Source extends Model
{
    use HasTranslations;

    protected $fillable = [
        'source_type_id',
        'country_id',
        'tv_show_id',
        'tv_episode_id',
        'tv_channel_id',
        'tv_program_id',
        'title',
        'original_title',
        'release_date',
        'url'
    ];

    protected $casts = [
        'source_type_id' => 'integer',
        'country_id' => 'integer',
        'tv_show_id' => 'integer',
        'tv_episode_id' => 'integer',
        'tv_channel_id' => 'integer',
        'tv_program_id' => 'integer',
        'title' => 'string',
        'original_title' => 'string',
        'release_date' => 'date',
        'url' => 'string'
    ];

    // Relations
    public function sourceType(): BelongsTo
    {
        return $this->belongsTo(SourceType::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function tvShow(): BelongsTo
    {
        return $this->belongsTo(TvShow::class);
    }

    public function tvEpisode(): BelongsTo
    {
        return $this->belongsTo(TvEpisode::class);
    }

    public function tvChannel(): BelongsTo
    {
        return $this->belongsTo(TvChannel::class);
    }

    public function tvProgram(): BelongsTo
    {
        return $this->belongsTo(TvProgram::class);
    }

    public function spots(): HasMany
    {
        return $this->hasMany(Spot::class);
    }

    // Accesseurs
    public function getDisplayTitleAttribute(): string
    {
        return $this->translate('title', app()->getLocale()) ?? $this->original_title ?? $this->title;
    }

    // Scopes
    public function scopeOfType($query, string $type)
    {
        return $query->whereHas('sourceType', function($query) use ($type) {
            $query->where('name', $type);
        });
    }
}
