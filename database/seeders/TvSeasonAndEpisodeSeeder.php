<?php

namespace Database\Seeders;

use App\Models\TvShow;
use App\Models\TvSeason;
use App\Models\TvEpisode;
use Illuminate\Database\Seeder;

class TvSeasonAndEpisodeSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérons Les Reines du Shopping
        $show = TvShow::where('title', 'les_reines_du_shopping')->first();

        if ($show) {
            // Créons deux saisons
            $seasons = [
                [
                    'season_number' => 1,
                    'release_year' => 2013,
                    'episode_count' => 3,
                    'translations' => [
                        'title' => [
                            'en' => 'First Season',
                            'fr' => 'Première Saison'
                        ],
                        'description' => [
                            'en' => 'The first season of Shopping Queens',
                            'fr' => 'La première saison des Reines du Shopping'
                        ]
                    ],
                    'episodes' => [
                        [
                            'episode_number' => 1,
                            'title' => 'glamorous_cocktail', // Ajout du champ title
                            'air_date' => '2013-07-01',
                            'translations' => [
                                'title' => [
                                    'en' => 'Glamorous for a cocktail',
                                    'fr' => 'Glamour pour un cocktail'
                                ]
                            ]
                        ],
                        [
                            'episode_number' => 2,
                            'title' => 'stylish_leather', // Ajout du champ title
                            'air_date' => '2013-07-02',
                            'translations' => [
                                'title' => [
                                    'en' => 'Stylish in leather',
                                    'fr' => 'Stylée en cuir'
                                ]
                            ]
                        ],
                        [
                            'episode_number' => 3,
                            'title' => 'elegant_black', // Ajout du champ title
                            'air_date' => '2013-07-03',
                            'translations' => [
                                'title' => [
                                    'en' => 'Elegant in black',
                                    'fr' => 'Élégante en noir'
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'season_number' => 2,
                    'release_year' => 2014,
                    'episode_count' => 2,
                    'translations' => [
                        'title' => [
                            'en' => 'Second Season',
                            'fr' => 'Deuxième Saison'
                        ],
                        'description' => [
                            'en' => 'The second season of Shopping Queens',
                            'fr' => 'La deuxième saison des Reines du Shopping'
                        ]
                    ],
                    'episodes' => [
                        [
                            'episode_number' => 1,
                            'title' => 'seductive_red', // Ajout du champ title
                            'air_date' => '2014-01-06',
                            'translations' => [
                                'title' => [
                                    'en' => 'Seductive in red',
                                    'fr' => 'Séduisante en rouge'
                                ]
                            ]
                        ],
                        [
                            'episode_number' => 2,
                            'title' => 'chic_satin', // Ajout du champ title
                            'air_date' => '2014-01-07',
                            'translations' => [
                                'title' => [
                                    'en' => 'Chic in satin',
                                    'fr' => 'Chic en satin'
                                ]
                            ]
                        ]
                    ]
                ]
            ];

            foreach ($seasons as $seasonData) {
                $episodes = $seasonData['episodes'];
                $translations = $seasonData['translations'];
                unset($seasonData['episodes'], $seasonData['translations']);

                // Créer la saison
                $season = $show->seasons()->create($seasonData);

                // Ajouter les traductions de la saison
                foreach ($translations as $field => $trans) {
                    $season->setTranslations($field, $trans);
                }

                // Créer les épisodes
                foreach ($episodes as $episodeData) {
                    $translations = $episodeData['translations'];
                    unset($episodeData['translations']);

                    $episode = $season->episodes()->create($episodeData);

                    foreach ($translations as $field => $trans) {
                        $episode->setTranslations($field, $trans);
                    }
                }
            }
        }
    }
}
