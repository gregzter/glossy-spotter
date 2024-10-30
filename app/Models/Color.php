<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Color extends Model {
    protected $fillable = [
        'name',
        'is_glossy',
        'category'
    ];

    protected $casts = [
        'name' => 'string',
        'is_glossy' => 'boolean',
        'category' => 'string'
    ];

    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    public function getTranslatedName(string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return $this->translations()
            ->where('locale', $locale)
            ->where('field', 'name')
            ->first()?->value ?? $this->name;
    }

    public function outfitItems(): HasMany
    {
        return $this->hasMany(OutfitItem::class);
    }
}
