<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\TvChannel;
use Illuminate\Database\Seeder;

class TvChannelSeeder extends Seeder
{
    public function run(): void
    {
        $france = Country::where('code', 'FR')->first();
        $usa = Country::where('code', 'US')->first();
        $uk = Country::where('code', 'GB')->first();

        $channels = [
            // Chaînes françaises
            [
                'name' => 'tf1',
                'country_id' => $france->id,
                'type' => 'national',
                'active' => true,
                'translations' => [
                    'en' => 'TF1',
                    'fr' => 'TF1'
                ]
            ],
            [
                'name' => 'france_2',
                'country_id' => $france->id,
                'type' => 'national',
                'active' => true,
                'translations' => [
                    'en' => 'France 2',
                    'fr' => 'France 2'
                ]
            ],
            [
                'name' => 'm6',
                'country_id' => $france->id,
                'type' => 'national',
                'active' => true,
                'translations' => [
                    'en' => 'M6',
                    'fr' => 'M6'
                ]
            ],
            [
                'name' => 'canal_plus',
                'country_id' => $france->id,
                'type' => 'cable',
                'active' => true,
                'translations' => [
                    'en' => 'Canal+',
                    'fr' => 'Canal+'
                ]
            ],
            // Chaînes américaines
            [
                'name' => 'nbc',
                'country_id' => $usa->id,
                'type' => 'national',
                'active' => true,
                'translations' => [
                    'en' => 'NBC',
                    'fr' => 'NBC'
                ]
            ],
            [
                'name' => 'cbs',
                'country_id' => $usa->id,
                'type' => 'national',
                'active' => true,
                'translations' => [
                    'en' => 'CBS',
                    'fr' => 'CBS'
                ]
            ],
            // Services de streaming
            [
                'name' => 'netflix_fr',
                'country_id' => $france->id,
                'type' => 'streaming',
                'active' => true,
                'translations' => [
                    'en' => 'Netflix France',
                    'fr' => 'Netflix France'
                ]
            ],
            [
                'name' => 'amazon_prime_video_fr',
                'country_id' => $france->id,
                'type' => 'streaming',
                'active' => true,
                'translations' => [
                    'en' => 'Amazon Prime Video France',
                    'fr' => 'Amazon Prime Video France'
                ]
            ],
            // Chaînes britanniques
            [
                'name' => 'bbc_one',
                'country_id' => $uk->id,
                'type' => 'national',
                'active' => true,
                'translations' => [
                    'en' => 'BBC One',
                    'fr' => 'BBC One'
                ]
            ],
            [
                'name' => 'itv',
                'country_id' => $uk->id,
                'type' => 'national',
                'active' => true,
                'translations' => [
                    'en' => 'ITV',
                    'fr' => 'ITV'
                ]
            ]
        ];

        foreach ($channels as $channel) {
            $translations = $channel['translations'];
            unset($channel['translations']);

            $model = TvChannel::create($channel);

            $model->setTranslations('name', $translations);
        }
    }
}
