<?php

namespace Database\Seeders;

use App\Models\ClothingCategory;
use Illuminate\Database\Seeder;

class ClothingCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'tops',
                'translations' => [
                    'en' => 'Tops',
                    'fr' => 'Hauts'
                ]
            ],
            [
                'name' => 'bottoms',
                'translations' => [
                    'en' => 'Bottoms',
                    'fr' => 'Bas'
                ]
            ],
            [
                'name' => 'dresses',
                'translations' => [
                    'en' => 'Dresses',
                    'fr' => 'Robes'
                ]
            ],
            [
                'name' => 'full_outfits',
                'translations' => [
                    'en' => 'Full Outfits',
                    'fr' => 'Tenues complètes'
                ]
            ]
        ];

        foreach ($categories as $category) {
            $model = ClothingCategory::create([
                'name' => $category['name']
            ]);

            $model->setTranslations('name', $category['translations']);
        }
    }
}
