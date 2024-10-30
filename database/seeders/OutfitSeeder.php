<?php

namespace Database\Seeders;

use App\Models\Outfit;
use App\Models\Material;
use App\Models\Color;
use App\Models\ClothingType;
use Illuminate\Database\Seeder;

class OutfitSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer quelques données de référence
        $satin = Material::where('name', 'satin')->first();
        $leather = Material::where('name', 'leather')->first();

        $black = Color::where('name', 'black')->first();
        $red = Color::where('name', 'red')->first();

        $dress = ClothingType::whereHas('category', function($query) {
            $query->where('name', 'dresses');
        })->first();

        $jacket = ClothingType::whereHas('category', function($query) {
            $query->where('name', 'tops');
        })->where('name', 'jacket')->first();

        if ($satin && $leather && $black && $red && $dress && $jacket) {
            // Créer une tenue avec robe
            $outfit1 = Outfit::create([
                'description' => 'evening_dress'  // Description de base
            ]);

            // Ajouter les traductions
            $outfit1->setTranslations('description', [
                'en' => 'Elegant evening outfit with a satin dress',
                'fr' => 'Tenue de soirée élégante avec une robe en satin'
            ]);

            $outfit1->addItem([
                'clothing_type_id' => $dress->id,
                'material_id' => $satin->id,
                'color_id' => $black->id,
                'shine_level' => 'very_shiny'
            ]);

            // Créer une tenue avec veste
            $outfit2 = Outfit::create([
                'description' => 'leather_jacket'  // Description de base
            ]);

            // Ajouter les traductions
            $outfit2->setTranslations('description', [
                'en' => 'Rock style outfit with a leather jacket',
                'fr' => 'Tenue rock avec une veste en cuir'
            ]);

            $outfit2->addItem([
                'clothing_type_id' => $jacket->id,
                'material_id' => $leather->id,
                'color_id' => $red->id,
                'shine_level' => 'shiny'
            ]);
        }
    }
}
