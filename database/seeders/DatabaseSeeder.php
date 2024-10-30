<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CountrySeeder::class,
            MaterialSeeder::class,
            ColorSeeder::class,
            ClothingCategorySeeder::class,
            ClothingTypeSeeder::class,
            PersonSeeder::class,
            TvChannelSeeder::class,
            TvShowSeeder::class,
            TvSeasonAndEpisodeSeeder::class,
            TvProgramSeeder::class,
            SourceTypeSeeder::class,
            SourceSeeder::class,
            OutfitSeeder::class,
            SpotSeeder::class,
            SpotInteractionsSeeder::class,
            MediaSeeder::class,
        ]);
    }
}
