<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TvChannel extends Model
{
    use HasTranslations;

    protected $fillable = [
        'name',
        'country_id',
        'logo_path',
        'active',
        'type'
    ];

    protected $casts = [
        'name' => 'string',
        'country_id' => 'integer',
        'logo_path' => 'string',
        'active' => 'boolean',
        'type' => 'string'
    ];

    // Relations
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function tvPrograms(): HasMany
    {
        return $this->hasMany(TvProgram::class);
    }

    public function sources(): HasMany
    {
        return $this->hasMany(Source::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
