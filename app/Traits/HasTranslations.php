<?php

namespace App\Traits;

use App\Models\Translation;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasTranslations
{
    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    public function translate(string $field, string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();

        return $this->translations()
            ->where('locale', $locale)
            ->where('field', $field)
            ->first()?->value ?? $this->{$field};
    }

    public function setTranslation(string $field, string $locale, string $value): void
    {
        $this->translations()->updateOrCreate(
            [
                'field' => $field,
                'locale' => $locale,
            ],
            [
                'value' => $value,
            ]
        );
    }

    public function setTranslations(string $field, array $translations): void
    {
        foreach ($translations as $locale => $value) {
            $this->setTranslation($field, $locale, $value);
        }
    }

    public function deleteTranslations(string $field = null, string $locale = null): void
    {
        $query = $this->translations();

        if ($field) {
            $query->where('field', $field);
        }

        if ($locale) {
            $query->where('locale', $locale);
        }

        $query->delete();
    }
}
