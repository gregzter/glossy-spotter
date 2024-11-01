<?php

use App\Http\Controllers\Api\SpotController;
use App\Http\Controllers\Api\SpotCommentController;
use App\Http\Controllers\Api\SpotFavoriteController;
use App\Http\Controllers\Api\MediaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route existante pour l'utilisateur
Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// Routes pour les spots
Route::prefix('v1')->group(function () {
    // Routes publiques
    Route::get('spots', [SpotController::class, 'index']);
    Route::get('spots/{spot}', [SpotController::class, 'show']);

    // Routes authentifiées
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('spots', [SpotController::class, 'store']);
        Route::put('spots/{spot}', [SpotController::class, 'update']);
        Route::delete('spots/{spot}', [SpotController::class, 'destroy']);

        // Routes de validation
        Route::post('spots/{spot}/validate', [SpotController::class, 'validate']);
        Route::post('spots/{spot}/reject', [SpotController::class, 'reject']);
    });



    // Routes publiques pour les commentaires et favoris
    Route::get('spots/{spot}/comments', [SpotCommentController::class, 'index']);
    Route::get('spots/{spot}/favorites', [SpotFavoriteController::class, 'index']);

    // Routes qui nécessitent une authentification
    Route::middleware('auth:sanctum')->group(function () {
        // Commentaires
        Route::post('spots/{spot}/comments', [SpotCommentController::class, 'store']);
        Route::put('spots/{spot}/comments/{comment}', [SpotCommentController::class, 'update']);

        // Favoris
        Route::post('spots/{spot}/favorite', [SpotFavoriteController::class, 'store']);
        Route::delete('spots/{spot}/favorite', [SpotFavoriteController::class, 'destroy']);
    });


    Route::middleware('auth:sanctum')->group(function () {
        // Routes pour les médias
        Route::post('spots/{spot}/media', [MediaController::class, 'store']);
        Route::delete('spots/{spot}/media/{media}', [MediaController::class, 'destroy']);
        Route::post('media/{media}/upscale', [MediaController::class, 'requestUpscale']);
    });
});
