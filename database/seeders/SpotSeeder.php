<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Person;
use App\Models\Source;
use App\Models\Outfit;
use App\Models\Spot;
use Illuminate\Database\Seeder;

class SpotSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer les données nécessaires
        $admin = User::where('email', 'admin@example.com')->first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'role' => 'admin'
            ]);
        }

        $person = Person::first();
        $source = Source::first();
        $outfit = Outfit::first();

        if ($admin && $person && $source && $outfit) {
            // Créer un spot validé
            $validatedSpot = Spot::create([
                'user_id' => $admin->id,
                'outfit_id' => $outfit->id,
                'person_id' => $person->id,
                'source_id' => $source->id,
                'validated' => true,
                'status' => 'published',
                'visibility' => 'public',
                'is_adult_content' => false,
                'validation_user_id' => $admin->id,
                'validation_date' => now()
            ]);

            // Créer un spot en attente
            $pendingSpot = Spot::create([
                'user_id' => $admin->id,
                'outfit_id' => $outfit->id,
                'person_id' => $person->id,
                'source_id' => $source->id,
                'validated' => false,
                'status' => 'pending',
                'visibility' => 'public',
                'is_adult_content' => false
            ]);
        }
    }
}
