<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClothingCategory extends Model
{
    use HasTranslations, HasFactory;

    protected $fillable = ['name'];

    protected $casts = [
        'name' => 'string'
    ];

    // Relation avec les types de vêtements
    public function clothingTypes(): HasMany
    {
        return $this->hasMany(ClothingType::class);
    }
}
