<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Spot;
use App\Models\SpotFavorite;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class SpotFavoriteController extends Controller
{
    public function index(Spot $spot): JsonResponse
    {
        $users = $spot->favoritedBy()
            ->select('users.id', 'users.name')
            ->paginate(15);

        return response()->json($users);
    }

    public function store(Spot $spot): JsonResponse
    {
        $favorite = $spot->favorites()->firstOrCreate([
            'user_id' => Auth::id()
        ]);

        if ($favorite->wasRecentlyCreated) {
            return response()->json(['message' => 'Spot added to favorites'], 201);
        }

        return response()->json(['message' => 'Spot already in favorites']);
    }

    public function destroy(Spot $spot): JsonResponse
    {
        $spot->favorites()
            ->where('user_id', Auth::id())
            ->delete();

        return response()->json(['message' => 'Spot removed from favorites']);
    }
}
