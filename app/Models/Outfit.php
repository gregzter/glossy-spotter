<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Outfit extends Model
{
    use HasTranslations;

    protected $fillable = [
        'description'
    ];

    protected $casts = [
        'description' => 'string'
    ];

    // Relations
    public function outfitItems(): HasMany
    {
        return $this->hasMany(OutfitItem::class);
    }

    public function spots(): HasMany
    {
        return $this->hasMany(Spot::class);
    }

    // Méthodes utilitaires
    public function addItem(array $data): OutfitItem
    {
        return $this->outfitItems()->create($data);
    }

    // Scope pour la recherche par type de matériau
    public function scopeWithMaterial($query, int $materialId)
    {
        return $query->whereHas('outfitItems', function($query) use ($materialId) {
            $query->where('material_id', $materialId);
        });
    }

    // Scope pour la recherche par couleur
    public function scopeWithColor($query, int $colorId)
    {
        return $query->whereHas('outfitItems', function($query) use ($colorId) {
            $query->where('color_id', $colorId);
        });
    }

    // Scope pour la recherche par type de vêtement
    public function scopeWithClothingType($query, int $clothingTypeId)
    {
        return $query->whereHas('outfitItems', function($query) use ($clothingTypeId) {
            $query->where('clothing_type_id', $clothingTypeId);
        });
    }
}
