<?php

namespace App\Providers;

use App\Models\Spot;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Visualisation des spots
        Gate::define('view-spot', function(?User $user, Spot $spot) {
            // Spots publics et publiés sont visibles par tous
            if ($spot->visibility === 'public' && $spot->status === 'published') {
                return true;
            }

            // Le reste nécessite une authentification
            if (!$user) {
                return false;
            }

            // Admin voit tout
            if ($user->role === UserRole::ADMIN) {
                return true;
            }

            // Les utilisateurs peuvent voir leurs propres spots quel que soit le statut
            if ($spot->user_id === $user->id) {
                return true;
            }

            // Les Spotter+ peuvent voir tous les spots
            if ($user->role === UserRole::SPOTTER_PLUS) {
                return true;
            }

            // Premium accessible aux spotters
            if ($spot->visibility === 'premium' && $spot->status === 'published') {
                return in_array($user->role, [UserRole::SPOTTER, UserRole::SPOTTER_PLUS, UserRole::ADMIN]);
            }

            return false;
        });

        // Création de spots
        Gate::define('create-spot', function(User $user) {
            return true; // Tout utilisateur authentifié peut créer
        });

        // Modification de spots
        Gate::define('edit-spot', function(User $user, Spot $spot) {
            if ($user->role === UserRole::ADMIN || $user->role === UserRole::SPOTTER_PLUS) {
                return true;
            }

            if ($user->role === UserRole::SPOTTER) {
                return $spot->user_id === $user->id;
            }

            if ($user->role === UserRole::VIEWER) {
                return $spot->user_id === $user->id && $spot->status !== 'published';
            }

            return false;
        });

        // Suppression de spots
        Gate::define('delete-spot', function(User $user, Spot $spot) {
            if ($user->role === UserRole::ADMIN) {
                return true;
            }

            return $spot->user_id === $user->id && $spot->status === 'pending';
        });

        // Validation de spots
        Gate::define('validate-spot', function(User $user) {
            return in_array($user->role, [UserRole::ADMIN, UserRole::SPOTTER_PLUS]);
        });
    }
}
