<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TvProgramEpisode extends Model
{
    use HasTranslations;

    protected $fillable = [
        'tv_program_id',
        'title',
        'air_date',
        'description'
    ];

    protected $casts = [
        'tv_program_id' => 'integer',
        'title' => 'string',
        'air_date' => 'date',
        'description' => 'string'
    ];

    // Relations
    public function program(): BelongsTo
    {
        return $this->belongsTo(TvProgram::class, 'tv_program_id');
    }

    public function sources(): HasMany
    {
        return $this->hasMany(Source::class);
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

    public function scopeByDate($query, string $date)
    {
        return $query->whereDate('air_date', $date);
    }

    // Accesseurs
    public function getDisplayTitleAttribute(): string
    {
        return $this->translate('title', app()->getLocale()) ?? $this->title;
    }
}
