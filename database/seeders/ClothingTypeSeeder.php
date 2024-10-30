<?php

namespace Database\Seeders;

use App\Models\ClothingCategory;
use App\Models\ClothingType;
use Illuminate\Database\Seeder;

class ClothingTypeSeeder extends Seeder
{
    public function run(): void
    {
        $typesByCategory = [
            'tops' => [
                ['name' => 't_shirt', 'translations' => ['en' => 'T-Shirt', 'fr' => 'T-shirt']],
                ['name' => 'blouse', 'translations' => ['en' => 'Blouse', 'fr' => 'Chemisier']],
                ['name' => 'sleeveless_top', 'translations' => ['en' => 'Sleeveless Top', 'fr' => 'Haut sans manches']],
                ['name' => 'crop_top', 'translations' => ['en' => 'Crop Top', 'fr' => 'Crop top']],
                ['name' => 'tank_top', 'translations' => ['en' => 'Tank Top', 'fr' => 'Débardeur']],
                ['name' => 'bodysuit', 'translations' => ['en' => 'Bodysuit', 'fr' => 'Body']],
                ['name' => 'jacket', 'translations' => ['en' => 'Jacket', 'fr' => 'Veste']]
            ],
            'bottoms' => [
                ['name' => 'pants', 'translations' => ['en' => 'Pants', 'fr' => 'Pantalon']],
                ['name' => 'leggings', 'translations' => ['en' => 'Leggings', 'fr' => 'Legging']],
                ['name' => 'long_skirt', 'translations' => ['en' => 'Long Skirt', 'fr' => 'Jupe longue']],
                ['name' => 'midi_skirt', 'translations' => ['en' => 'Midi Skirt', 'fr' => 'Jupe mi-longue']],
                ['name' => 'short_skirt', 'translations' => ['en' => 'Short Skirt', 'fr' => 'Jupe courte']],
                ['name' => 'mini_skirt', 'translations' => ['en' => 'Mini Skirt', 'fr' => 'Mini jupe']],
                ['name' => 'shorts', 'translations' => ['en' => 'Shorts', 'fr' => 'Short']],
                ['name' => 'capri', 'translations' => ['en' => 'Capri', 'fr' => 'Pantacourt']]
            ],
            'dresses' => [
                ['name' => 'long_dress', 'translations' => ['en' => 'Long Dress', 'fr' => 'Robe longue']],
                ['name' => 'short_dress', 'translations' => ['en' => 'Short Dress', 'fr' => 'Robe courte']],
                ['name' => 'mini_dress', 'translations' => ['en' => 'Mini Dress', 'fr' => 'Mini robe']]
            ],
            'full_outfits' => [
                ['name' => 'jumpsuit', 'translations' => ['en' => 'Jumpsuit', 'fr' => 'Combinaison']],
                ['name' => 'coord_set', 'translations' => ['en' => 'Coordinated Set', 'fr' => 'Ensemble coordonné']],
                ['name' => 'kimono', 'translations' => ['en' => 'Kimono', 'fr' => 'Kimono']],
                ['name' => 'kaftan', 'translations' => ['en' => 'Kaftan', 'fr' => 'Caftan']]
            ]
        ];

        foreach ($typesByCategory as $categoryName => $types) {
            $category = ClothingCategory::where('name', $categoryName)->first();

            if ($category) {
                foreach ($types as $type) {
                    $model = ClothingType::create([
                        'clothing_category_id' => $category->id,
                        'name' => $type['name']
                    ]);

                    $model->setTranslations('name', $type['translations']);
                }
            }
        }
    }
}
