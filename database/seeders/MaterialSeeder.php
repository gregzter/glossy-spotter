<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Material;

class MaterialSeeder extends Seeder
{
    public function run(): void
    {
        $materials = [
            ['en' => 'satin', 'fr' => 'satin'],
            ['en' => 'leather', 'fr' => 'cuir'],
            ['en' => 'latex', 'fr' => 'latex'],
            ['en' => 'vinyl', 'fr' => 'vinyle']
        ];

        foreach ($materials as $material) {
            $model = Material::create([
                'name' => $material['en']
            ]);

            $model->setTranslations('name', [
                'en' => $material['en'],
                'fr' => $material['fr']
            ]);
        }
    }
}
