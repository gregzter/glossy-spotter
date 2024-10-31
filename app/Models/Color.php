<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Color extends Model
{
    use HasTranslations, HasFactory;

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

    public function outfitItems(): HasMany
    {
        return $this->hasMany(OutfitItem::class);
    }
}
