<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Material extends Model
{
    use HasTranslations, HasFactory;

    protected $fillable = ['name'];

    protected $casts = [
        'name' => 'string',
    ];

    public function outfitItems(): HasMany
    {
        return $this->hasMany(OutfitItem::class);
    }
}
