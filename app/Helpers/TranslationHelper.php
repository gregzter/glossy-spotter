<?php

namespace App\Helpers;

class TranslationHelper
{
    public static function getAvailableLocales(): array
    {
        return ['en', 'fr']; // Vous pourrez ajouter d'autres langues ici
    }

    public static function getDefaultLocale(): string
    {
        return 'en';
    }

    public static function getFallbackLocale(): string
    {
        return 'en';
    }
}
