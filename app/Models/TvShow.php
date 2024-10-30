<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TvShow extends Model
{
    use HasTranslations;

    protected $fillable = [
        'title',
        'original_title',
        'country_id',
        'start_year',
        'end_year',
        'description',
        'poster_path',
        'status'
    ];

    protected $casts = [
        'title' => 'string',
        'original_title' => 'string',
        'country_id' => 'integer',
        'start_year' => 'integer',
        'end_year' => 'integer',
        'description' => 'string',
        'poster_path' => 'string',
        'status' => 'string'
    ];

    // Relations
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function seasons(): HasMany
    {
        return $this->hasMany(TvSeason::class);
    }

    public function sources(): HasMany
    {
        return $this->hasMany(Source::class);
    }

    // Scopes
    public function scopeOngoing($query)
    {
        return $query->where('status', 'ongoing');
    }

    public function scopeByYear($query, int $year)
    {
        return $query->where('start_year', '<=', $year)
                    ->where(function ($query) use ($year) {
                        $query->where('end_year', '>=', $year)
                              ->orWhereNull('end_year');
                    });
    }

    // Accesseurs
    public function getDisplayTitleAttribute(): string
    {
        return $this->translate('title', app()->getLocale()) ?? $this->original_title;
    }

    public function getYearsRangeAttribute(): string
    {
        if ($this->end_year) {
            return "{$this->start_year}-{$this->end_year}";
        }
        return "{$this->start_year}-present";
    }
}
