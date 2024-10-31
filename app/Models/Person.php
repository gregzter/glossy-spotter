<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Person extends Model
{
    use HasTranslations;
    use HasFactory;

    protected $fillable = [
        'firstname',
        'lastname',
        'nickname',
        'birth_date',
        'country_id',
        'profession',
        'biography',
        'image_path'
    ];

    protected $casts = [
        'firstname' => 'string',
        'lastname' => 'string',
        'nickname' => 'string',
        'birth_date' => 'date',
        'country_id' => 'integer',
        'profession' => 'string',
        'biography' => 'string',
        'image_path' => 'string'
    ];

    // Relations
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function spots(): HasMany
    {
        return $this->hasMany(Spot::class);
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return trim("{$this->firstname} {$this->lastname}");
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->nickname ?? $this->full_name;
    }

    // Scope pour la recherche
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($query) use ($search) {
            $query->where('firstname', 'like', "%{$search}%")
                  ->orWhere('lastname', 'like', "%{$search}%")
                  ->orWhere('nickname', 'like', "%{$search}%");
        });
    }
}
