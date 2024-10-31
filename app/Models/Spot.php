<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Spot extends Model
{
    use HasTranslations;
    use HasFactory;

    protected $fillable = [
        'user_id',
        'outfit_id',
        'person_id',
        'source_id',
        'validated',
        'status',
        'visibility',
        'is_adult_content',
        'rejection_reason',
        'validation_user_id',
        'validation_date'
    ];

    protected $casts = [
        'user_id' => 'integer',
        'outfit_id' => 'integer',
        'person_id' => 'integer',
        'source_id' => 'integer',
        'validated' => 'boolean',
        'status' => 'string',
        'visibility' => 'string',
        'is_adult_content' => 'boolean',
        'rejection_reason' => 'string',
        'validation_user_id' => 'integer',
        'validation_date' => 'datetime'
    ];

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function outfit(): BelongsTo
    {
        return $this->belongsTo(Outfit::class);
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    public function validationUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validation_user_id');
    }

    public function media(): HasMany
    {
        return $this->hasMany(Media::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(SpotComment::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(SpotRating::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(SpotFavorite::class);
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'spot_favorites')
                    ->withTimestamps(['created_at'])  // Spécifie uniquement created_at
                    ->withPivot('created_at');  // Spécifie les colonnes pivot à inclure
    }

    // Scopes pour la recherche et le filtrage
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopePublicOnly(Builder $query): Builder
    {
        return $query->where('visibility', 'public');
    }

    public function scopeByMaterial(Builder $query, int $materialId): Builder
    {
        return $query->whereHas('outfit.outfitItems', function($query) use ($materialId) {
            $query->where('material_id', $materialId);
        });
    }

    public function scopeByColor(Builder $query, int $colorId): Builder
    {
        return $query->whereHas('outfit.outfitItems', function($query) use ($colorId) {
            $query->where('color_id', $colorId);
        });
    }

    public function scopeByPerson(Builder $query, int $personId): Builder
    {
        return $query->where('person_id', $personId);
    }

    // Méthodes utilitaires
    public function validate(User $user): bool
    {
        return $this->update([
            'validated' => true,
            'status' => 'published',
            'validation_user_id' => $user->id,
            'validation_date' => now()
        ]);
    }

    public function reject(User $user, string $reason): bool
    {
        return $this->update([
            'validated' => false,
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'validation_user_id' => $user->id,
            'validation_date' => now()
        ]);
    }

    // Accesseur pour la moyenne des notes
    public function getAverageRatingAttribute(): ?float
    {
        if ($this->ratings()->count() === 0) {
            return null;
        }
        return round($this->ratings()->avg('rating'), 1);
    }

    // Méthode pour vérifier si un utilisateur peut voir ce spot
    public function canBeSeenBy(User $user): bool
    {
        return match($this->visibility) {
            'public' => true,
            'member' => !is_null($user),
            'premium' => $user && in_array($user->role, ['spotter', 'spotter_plus', 'admin']),
            'private' => $user && ($user->id === $this->user_id || $user->role === 'admin'),
            default => false
        };
    }

    public function isFavoritedBy(User $user): bool
    {
        return $this->favorites()->where('user_id', $user->id)->exists();
    }
}
