<?php

namespace App\Enums;

enum UserRole: string
{
    case VIEWER = 'viewer';
    case SPOTTER = 'spotter';
    case SPOTTER_PLUS = 'spotter_plus';
    case ADMIN = 'admin';

    public function permissions(): array
    {
        return match($this) {
            self::VIEWER => [
                'spot.view',
                'spot.create',
                'comment.create',
                'rating.create',
                'favorite.toggle'
            ],
            self::SPOTTER => [
                'spot.view',
                'spot.create',
                'spot.edit-own',
                'spot.delete-own',
                'comment.create',
                'comment.edit-own',
                'comment.delete-own',
                'rating.create',
                'favorite.toggle',
                'premium.access'
            ],
            self::SPOTTER_PLUS => [
                'spot.view',
                'spot.create',
                'spot.edit-any',
                'spot.validate',
                'comment.create',
                'comment.edit-own',
                'comment.delete-own',
                'rating.create',
                'favorite.toggle',
                'premium.access'
            ],
            self::ADMIN => [
                '*' // Tous les droits
            ]
        };
    }

    public function label(): string
    {
        return match($this) {
            self::VIEWER => 'Viewer',
            self::SPOTTER => 'Spotter',
            self::SPOTTER_PLUS => 'Spotter+',
            self::ADMIN => 'Administrator'
        };
    }

    public static function fromRole(string $role): self
    {
        return match($role) {
            'viewer' => self::VIEWER,
            'spotter' => self::SPOTTER,
            'spotter_plus' => self::SPOTTER_PLUS,
            'admin' => self::ADMIN,
            default => throw new \ValueError("Invalid role: {$role}")
        };
    }
}
