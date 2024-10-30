<?php

namespace Database\Seeders;

use App\Models\SourceType;
use Illuminate\Database\Seeder;

class SourceTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name' => 'movie',
                'translations' => [
                    'name' => [
                        'en' => 'Movie',
                        'fr' => 'Film'
                    ]
                ]
            ],
            [
                'name' => 'tv_show',
                'translations' => [
                    'name' => [
                        'en' => 'TV Show',
                        'fr' => 'Série TV'
                    ]
                ]
            ],
            [
                'name' => 'tv_program',
                'translations' => [
                    'name' => [
                        'en' => 'TV Program',
                        'fr' => 'Programme TV'
                    ]
                ]
            ],
            [
                'name' => 'event',
                'translations' => [
                    'name' => [
                        'en' => 'Event',
                        'fr' => 'Événement'
                    ]
                ]
            ],
            [
                'name' => 'internet',
                'translations' => [
                    'name' => [
                        'en' => 'Internet',
                        'fr' => 'Internet'
                    ]
                ]
            ]
        ];

        foreach ($types as $type) {
            $translations = $type['translations'];
            unset($type['translations']);

            $model = SourceType::create($type);
            $model->setTranslations('name', $translations['name']);
        }
    }
}
