<?php

namespace Database\Seeders;

use App\Models\Source;
use App\Models\SourceType;
use App\Models\TvShow;
use App\Models\TvEpisode;
use App\Models\TvProgram;
use Illuminate\Database\Seeder;

class SourceSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer les types de sources
        $showType = SourceType::where('name', 'tv_show')->first();
        $programType = SourceType::where('name', 'tv_program')->first();

        // Récupérer une émission et ses épisodes
        $show = TvShow::with('seasons.episodes')->where('title', 'les_reines_du_shopping')->first();
        if ($show && $showType) {
            // Créer une source pour l'émission
            Source::create([
                'source_type_id' => $showType->id,
                'country_id' => $show->country_id,
                'tv_show_id' => $show->id,
                'title' => $show->title,
                'release_date' => $show->start_year . '-01-01'
            ]);

            // Créer des sources pour quelques épisodes
            if ($show->seasons->isNotEmpty()) {
                $episode = $show->seasons->first()->episodes->first();
                if ($episode) {
                    Source::create([
                        'source_type_id' => $showType->id,
                        'country_id' => $show->country_id,
                        'tv_show_id' => $show->id,
                        'tv_episode_id' => $episode->id,
                        'title' => $episode->title,
                        'release_date' => $episode->air_date
                    ]);
                }
            }
        }

        // Faire de même pour un programme TV
        $program = TvProgram::with('episodes')->where('name', 'journal_20h')->first();
        if ($program && $programType) {
            Source::create([
                'source_type_id' => $programType->id,
                'country_id' => $program->channel->country_id,
                'tv_program_id' => $program->id,
                'tv_channel_id' => $program->channel->id,
                'title' => $program->name,
                'release_date' => $program->start_date
            ]);
        }
    }
}
