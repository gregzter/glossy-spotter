<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Color;

class ColorSeeder extends Seeder
{
    public function run(): void
    {
        // Basic colors
        $basicColors = [
            ['en' => 'black', 'fr' => 'noir'],
            ['en' => 'white', 'fr' => 'blanc'],
            ['en' => 'red', 'fr' => 'rouge'],
            ['en' => 'blue', 'fr' => 'bleu'],
            ['en' => 'green', 'fr' => 'vert'],
            ['en' => 'yellow', 'fr' => 'jaune'],
            ['en' => 'purple', 'fr' => 'violet'],
            ['en' => 'pink', 'fr' => 'rose'],
            ['en' => 'brown', 'fr' => 'marron'],
            ['en' => 'gray', 'fr' => 'gris']
        ];

        foreach ($basicColors as $color) {
            $colorModel = Color::create([
                'name' => $color['en'],  // Store English in main table
                'is_glossy' => false,
                'category' => 'basic'
            ]);

            // Add French translation
            $colorModel->translations()->create([
                'locale' => 'fr',
                'field' => 'name',
                'value' => $color['fr']
            ]);
        }

        // Glossy colors
        $glossyColors = [
            ['en' => 'silver', 'fr' => 'argenté'],
            ['en' => 'golden', 'fr' => 'doré'],
            ['en' => 'bronze', 'fr' => 'bronze'],
            ['en' => 'chrome', 'fr' => 'chrome'],
            ['en' => 'holographic', 'fr' => 'holographique'],
            ['en' => 'iridescent', 'fr' => 'iridescent'],
            ['en' => 'metallic', 'fr' => 'métallisé'],
            ['en' => 'pearly', 'fr' => 'nacré'],
            ['en' => 'glittery', 'fr' => 'pailleté']
        ];

        foreach ($glossyColors as $color) {
            $colorModel = Color::create([
                'name' => $color['en'],
                'is_glossy' => true,
                'category' => 'glossy'
            ]);

            $colorModel->translations()->create([
                'locale' => 'fr',
                'field' => 'name',
                'value' => $color['fr']
            ]);
        }
    }
}
