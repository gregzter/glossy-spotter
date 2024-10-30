<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\TvShow;
use Illuminate\Database\Seeder;

class TvShowSeeder extends Seeder
{
    public function run(): void
    {
        $france = Country::where('code', 'FR')->first();
        $usa = Country::where('code', 'US')->first();
        $uk = Country::where('code', 'GB')->first();

        $shows = [
            [
                'title' => 'les_reines_du_shopping',
                'country_id' => $france->id,
                'start_year' => 2013,
                'status' => 'ongoing',
                'translations' => [
                    'title' => [
                        'en' => 'Shopping Queens',
                        'fr' => 'Les Reines du Shopping'
                    ],
                    'description' => [
                        'en' => 'French fashion reality show where contestants compete to be the best shopper',
                        'fr' => 'Émission de téléréalité française où des candidates s\'affrontent pour être la meilleure shoppeuse'
                    ]
                ]
            ],
            [
                'title' => 'project_runway',
                'country_id' => $usa->id,
                'start_year' => 2004,
                'end_year' => 2023,
                'status' => 'ended',
                'translations' => [
                    'title' => [
                        'en' => 'Project Runway',
                        'fr' => 'Project Runway'
                    ],
                    'description' => [
                        'en' => 'Reality competition show focused on fashion design',
                        'fr' => 'Émission de téléréalité centrée sur le design de mode'
                    ]
                ]
            ],
            [
                'title' => 'next_in_fashion',
                'country_id' => $uk->id,
                'start_year' => 2020,
                'status' => 'ongoing',
                'translations' => [
                    'title' => [
                        'en' => 'Next in Fashion',
                        'fr' => 'Next in Fashion'
                    ],
                    'description' => [
                        'en' => 'Fashion design competition series featuring up-and-coming designers',
                        'fr' => 'Série de compétition de mode mettant en vedette des créateurs émergents'
                    ]
                ]
            ]
        ];

        foreach ($shows as $show) {
            $translations = $show['translations'];
            unset($show['translations']);

            $model = TvShow::create($show);

            foreach ($translations as $field => $trans) {
                $model->setTranslations($field, $trans);
            }
        }
    }
}
