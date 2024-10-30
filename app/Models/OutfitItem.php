<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OutfitItem extends Model
{
    protected $fillable = [
        'outfit_id',
        'clothing_type_id',
        'material_id',
        'color_id',
        'shine_level'
    ];

    protected $casts = [
        'outfit_id' => 'integer',
        'clothing_type_id' => 'integer',
        'material_id' => 'integer',
        'color_id' => 'integer',
        'shine_level' => 'string'
    ];

    // Relations
    public function outfit(): BelongsTo
    {
        return $this->belongsTo(Outfit::class);
    }

    public function clothingType(): BelongsTo
    {
        return $this->belongsTo(ClothingType::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }

    // Scopes
    public function scopeByShineLevel($query, string $level)
    {
        return $query->where('shine_level', $level);
    }

    // Accesseur pour description complète
    public function getFullDescriptionAttribute(): string
    {
        $locale = app()->getLocale();

        return sprintf(
            '%s en %s %s (%s)',
            $this->clothingType->translate('name', $locale),
            $this->color->translate('name', $locale),
            $this->material->translate('name', $locale),
            match($this->shine_level) {
                'very_shiny' => __('Très brillant'),
                'shiny' => __('Brillant'),
                'slightly_shiny' => __('Légèrement brillant'),
                default => $this->shine_level
            }
        );
    }
}
