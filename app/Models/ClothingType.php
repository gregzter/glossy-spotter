<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClothingType extends Model
{
    use HasTranslations, HasFactory;

    protected $fillable = [
        'clothing_category_id',
        'name'
    ];

    protected $casts = [
        'name' => 'string',
        'clothing_category_id' => 'integer'
    ];

    // Relation avec la catégorie
    public function category(): BelongsTo
    {
        return $this->belongsTo(ClothingCategory::class, 'clothing_category_id');
    }

    // Relation avec les éléments de tenue
    public function outfitItems(): HasMany
    {
        return $this->hasMany(OutfitItem::class);
    }
}
