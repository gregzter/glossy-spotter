<?php

namespace Database\Seeders;

use App\Models\Spot;
use App\Models\User;
use App\Models\SpotComment;
use App\Models\SpotRating;
use App\Models\SpotFavorite;
use Illuminate\Database\Seeder;

class SpotInteractionsSeeder extends Seeder
{
    public function run(): void
    {
        $spot = Spot::where('status', 'published')->first();
        $users = User::all();

        if ($spot && $users->isNotEmpty()) {
            foreach ($users as $user) {
                // Ajouter un commentaire
                SpotComment::create([
                    'spot_id' => $spot->id,
                    'user_id' => $user->id,
                    'content' => "Commentaire de test par {$user->name}",
                    'is_edited' => false
                ]);

                // Ajouter une note
                SpotRating::create([
                    'spot_id' => $spot->id,
                    'user_id' => $user->id,
                    'rating' => rand(3, 5)
                ]);

                // Ajouter aux favoris
                SpotFavorite::create([
                    'spot_id' => $spot->id,
                    'user_id' => $user->id,
                    'created_at' => now()
                ]);
            }
        }
    }
}
