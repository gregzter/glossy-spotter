<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Spot;
use App\Models\SpotComment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class SpotCommentController extends Controller
{
    public function index(Spot $spot): JsonResponse
    {
        $comments = $spot->comments()
            ->with('user:id,name')
            ->latest()
            ->paginate(15);

        return response()->json($comments);
    }

    public function store(Request $request, Spot $spot): JsonResponse
    {
        try {
            $validated = $request->validate([
                'content' => ['required', 'string', 'min:1', 'max:1000'],
            ]);

            $comment = $spot->comments()->create([
                'user_id' => Auth::id(),
                'content' => $validated['content'],
                'is_edited' => false,
            ]);

            return response()->json($comment->load('user:id,name'), 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function update(Request $request, Spot $spot, SpotComment $comment): JsonResponse
    {
        if ($comment->user_id !==  Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'content' => ['required', 'string', 'min:1', 'max:1000'],
        ]);

        $comment->update([
            'content' => $validated['content'],
            'is_edited' => true,
            'edited_at' => now(),
        ]);

        return response()->json($comment->load('user:id,name'));
    }
}
