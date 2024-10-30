<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Material;

class MaterialSeeder extends Seeder
{
    public function run(): void
    {
        $materials = [
            ['name' => 'satin'],
            ['name' => 'leather'],
            ['name' => 'latex'],
            ['name' => 'vinyl']
        ];

        foreach ($materials as $material) {
            Material::create($material);
        }
    }
}
