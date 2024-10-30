<?php

namespace Database\Seeders;

use App\Models\Spot;
use App\Models\Media;
use Illuminate\Database\Seeder;

class MediaSeeder extends Seeder
{
    public function run(): void
    {
        $spot = Spot::where('status', 'published')->first();

        if ($spot) {
            // Ajouter une image
            Media::create([
                'spot_id' => $spot->id,
                'original_path' => 'spots/original/example.jpg',
                'thumbnail_path' => 'spots/thumbnails/example.jpg',
                'type' => 'image',
                'status' => 'ready',
                'mime_type' => 'image/jpeg',
                'file_size' => 1024 * 1024, // 1MB
                'dimensions' => '1920x1080'
            ]);

            // Ajouter une image en cours d'upscaling
            Media::create([
                'spot_id' => $spot->id,
                'original_path' => 'spots/original/processing.jpg',
                'thumbnail_path' => 'spots/thumbnails/processing.jpg',
                'type' => 'image',
                'status' => 'processing',
                'mime_type' => 'image/jpeg',
                'file_size' => 2048 * 1024, // 2MB
                'dimensions' => '1280x720'
            ]);
        }
    }
}
