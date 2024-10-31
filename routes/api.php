<?php

use App\Http\Controllers\Api\SpotController;
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
});
