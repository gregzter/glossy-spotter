<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Créer un admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        // Créer un spotter
        User::create([
            'name' => 'Spotter',
            'email' => 'spotter@example.com',
            'password' => bcrypt('password'),
            'role' => 'spotter'
        ]);

        // Créer un viewer
        User::create([
            'name' => 'Viewer',
            'email' => 'viewer@example.com',
            'password' => bcrypt('password'),
            'role' => 'viewer'
        ]);
    }
}
