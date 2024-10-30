<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            [
                'code' => 'FR',
                'name' => 'france',
                'translations' => [
                    'en' => 'France',
                    'fr' => 'France'
                ]
            ],
            [
                'code' => 'US',
                'name' => 'united_states',
                'translations' => [
                    'en' => 'United States',
                    'fr' => 'États-Unis'
                ]
            ],
            [
                'code' => 'GB',
                'name' => 'united_kingdom',
                'translations' => [
                    'en' => 'United Kingdom',
                    'fr' => 'Royaume-Uni'
                ]
            ],
            [
                'code' => 'DE',
                'name' => 'germany',
                'translations' => [
                    'en' => 'Germany',
                    'fr' => 'Allemagne'
                ]
            ],
            [
                'code' => 'IT',
                'name' => 'italy',
                'translations' => [
                    'en' => 'Italy',
                    'fr' => 'Italie'
                ]
            ],
            [
                'code' => 'ES',
                'name' => 'spain',
                'translations' => [
                    'en' => 'Spain',
                    'fr' => 'Espagne'
                ]
            ],
            [
                'code' => 'JP',
                'name' => 'japan',
                'translations' => [
                    'en' => 'Japan',
                    'fr' => 'Japon'
                ]
            ],
            [
                'code' => 'KR',
                'name' => 'south_korea',
                'translations' => [
                    'en' => 'South Korea',
                    'fr' => 'Corée du Sud'
                ]
            ],
            [
                'code' => 'CA',
                'name' => 'canada',
                'translations' => [
                    'en' => 'Canada',
                    'fr' => 'Canada'
                ]
            ],
            [
                'code' => 'AU',
                'name' => 'australia',
                'translations' => [
                    'en' => 'Australia',
                    'fr' => 'Australie'
                ]
            ]
        ];

        foreach ($countries as $country) {
            $model = Country::create([
                'name' => $country['name'],
                'code' => $country['code']
            ]);

            $model->setTranslations('name', $country['translations']);
        }
    }
}
