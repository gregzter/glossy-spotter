<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Material extends Model
{
    use HasTranslations;

    protected $fillable = ['name'];

    protected $casts = [
        'name' => 'string',
    ];

    public function outfitItems(): HasMany
    {
        return $this->hasMany(OutfitItem::class);
    }
}
