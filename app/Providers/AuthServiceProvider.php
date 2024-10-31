<?php

namespace App\Providers;

use App\Models\Spot;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Visualisation des spots
        Gate::define('view-spot', function(User $user, Spot $spot) {
            // Admin voit tout
            if ($user->role->value === 'admin') {
                return true;
            }

            // Public accessible à tous
            if ($spot->visibility === 'public') {
                return true;
            }

            // Private uniquement pour le propriétaire
            if ($spot->visibility === 'private') {
                return $spot->user_id === $user->id;
            }

            return false;
        });

        // Création de spots
        Gate::define('create-spot', function(User $user) {
            return in_array($user->role->value, ['admin', 'spotter_plus', 'spotter', 'viewer']);
        });

        // Modification de spots
        Gate::define('edit-spot', function(User $user, Spot $spot) {
            switch ($user->role->value) {
                case 'admin':
                case 'spotter_plus':
                    return true; // Peuvent modifier tous les spots

                case 'spotter':
                    return $spot->user_id === $user->id; // Uniquement leurs spots

                case 'viewer':
                    // Uniquement leurs spots non publiés
                    return $spot->user_id === $user->id && $spot->status !== 'published';

                default:
                    return false;
            }
        });

        // Validation de spots
        Gate::define('validate-spot', function(User $user) {
            return in_array($user->role->value, ['admin', 'spotter_plus']);
        });
    }
}
