<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SourceType extends Model
{
    use HasTranslations;

    protected $fillable = ['name'];

    protected $casts = [
        'name' => 'string'
    ];

    // Relations
    public function sources(): HasMany
    {
        return $this->hasMany(Source::class);
    }
}
