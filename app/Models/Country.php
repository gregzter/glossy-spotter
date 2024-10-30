<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasTranslations;

    protected $fillable = [
        'name',
        'code'  // ISO code
    ];

    protected $casts = [
        'name' => 'string',
        'code' => 'string'
    ];

    // Relations
    public function people(): HasMany
    {
        return $this->hasMany(Person::class);
    }

    public function tvShows(): HasMany
    {
        return $this->hasMany(TvShow::class);
    }

    public function tvChannels(): HasMany
    {
        return $this->hasMany(TvChannel::class);
    }

    public function sources(): HasMany
    {
        return $this->hasMany(Source::class);
    }
}
