<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\OutfitItem;

class Material extends Model
{
    protected $fillable = ['name'];

    protected $casts = [
        'name' => 'string',
    ];

    // Relation avec OutfitItem
    public function outfitItems(): HasMany
    {
        return $this->hasMany(OutfitItem::class);
    }
}
