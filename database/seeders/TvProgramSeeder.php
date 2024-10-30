<?php

namespace Database\Seeders;

use App\Models\TvChannel;
use App\Models\TvProgram;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TvProgramSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer TF1
        $tf1 = TvChannel::where('name', 'tf1')->first();
        // Récupérer M6
        $m6 = TvChannel::where('name', 'm6')->first();

        if ($tf1 && $m6) {
            $programs = [
                [
                    'tv_channel_id' => $tf1->id,
                    'name' => 'journal_20h',
                    'start_date' => '2000-01-01',
                    'still_running' => true,
                    'frequency' => 'daily',
                    'type' => 'news',
                    'translations' => [
                        'name' => [
                            'en' => '8PM News',
                            'fr' => 'Journal de 20h'
                        ],
                        'description' => [
                            'en' => 'Daily evening news program',
                            'fr' => 'Journal télévisé quotidien du soir'
                        ]
                    ],
                    'episodes' => [
                        [
                            'title' => 'evening_news',  // Ajout du champ title
                            'air_date' => now()->subDays(1),
                            'translations' => [
                                'title' => [
                                    'en' => 'Evening News',
                                    'fr' => 'Journal du Soir'
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'tv_channel_id' => $m6->id,
                    'name' => 'top_chef',
                    'start_date' => '2010-02-22',
                    'still_running' => true,
                    'frequency' => 'weekly',
                    'type' => 'reality',
                    'translations' => [
                        'name' => [
                            'en' => 'Top Chef',
                            'fr' => 'Top Chef'
                        ],
                        'description' => [
                            'en' => 'Cooking competition show',
                            'fr' => 'Concours culinaire'
                        ]
                    ],
                    'episodes' => [
                        [
                            'title' => 'season_opening',  // Ajout du champ title
                            'air_date' => '2024-01-15',
                            'translations' => [
                                'title' => [
                                    'en' => 'Season Opening',
                                    'fr' => 'Ouverture de la Saison'
                                ],
                                'description' => [
                                    'en' => 'The new season begins with challenging tasks',
                                    'fr' => 'La nouvelle saison commence avec des défis de taille'
                                ]
                            ]
                        ]
                    ]
                ]
            ];

            foreach ($programs as $programData) {
                $episodes = $programData['episodes'];
                $translations = $programData['translations'];
                unset($programData['episodes'], $programData['translations']);

                // Créer le programme
                $program = TvProgram::create($programData);

                // Ajouter les traductions
                foreach ($translations as $field => $trans) {
                    $program->setTranslations($field, $trans);
                }

                // Créer les épisodes
                foreach ($episodes as $episodeData) {
                    $translations = $episodeData['translations'];
                    unset($episodeData['translations']);

                    $episode = $program->episodes()->create($episodeData);

                    foreach ($translations as $field => $trans) {
                        $episode->setTranslations($field, $trans);
                    }
                }
            }
        }
    }
}
