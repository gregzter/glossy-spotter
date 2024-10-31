<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Spot;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SpotController extends Controller
{
    protected $perPage = 15;

    public function index(Request $request): JsonResponse
    {
        $query = Spot::query()
        ->with(['user:id,name,role', 'outfit:id,description', 'source.sourceType'])
        ->where('status', 'published');

        // Filtre par visibilité
        if ($request->has('visibility')) {
            $visibility = $request->input('visibility');
            $query->where('visibility', $visibility);

            // Si le filtre est sur premium, vérifions les permissions
            if ($visibility === 'premium' && !Auth::check()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        } else {
            // Par défaut, montrer uniquement les spots publics
            $query->where('visibility', 'public');
        }

        // Ajout d'autres filtres potentiels ici...

        // Tri par date de création décroissante
        $query->latest();

        return response()->json(
            $query->paginate($this->perPage)
        );
    }

    public function show(Spot $spot): JsonResponse
    {
        if (! Gate::allows('view-spot', $spot)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $spot->load([
            'user:id,name,role',
            'outfit:id,description',
            'source.sourceType',
            'validationUser:id,name,role'
        ]);

        return response()->json($spot);
    }

    public function store(Request $request): JsonResponse
    {
        if (! Gate::allows('create-spot')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $validated = $request->validate([
                'outfit_id' => ['required', 'exists:outfits,id'],
                'source_id' => ['required', 'exists:sources,id'],
                'visibility' => ['required', Rule::in(['public', 'private', 'premium'])],
                'is_adult_content' => ['boolean'],
            ]);

            $spot = new Spot($validated);
            $spot->user_id = Auth::id();
            $spot->status = 'pending';
            $spot->save();

            return response()->json($spot, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function update(Request $request, Spot $spot): JsonResponse
    {
        if (! Gate::allows('edit-spot', $spot)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $validated = $request->validate([
                'visibility' => ['sometimes', Rule::in(['public', 'private', 'premium'])],
                'is_adult_content' => ['sometimes', 'boolean'],
            ]);

            $spot->update($validated);

            return response()->json($spot);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function destroy(Spot $spot): JsonResponse
    {
        if (! Gate::allows('delete-spot', $spot)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $spot->delete();

        return response()->json(null, 204);
    }

    public function validate(Spot $spot): JsonResponse
    {
        if (! Gate::allows('validate-spot')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $spot->update([
            'status' => 'published',
            'validated' => true,
            'validation_user_id' => Auth::id(),
            'validation_date' => now()
        ]);

        return response()->json($spot);
    }

    public function reject(Request $request, Spot $spot): JsonResponse
    {
        if (! Gate::allows('validate-spot')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:255'
        ]);

        $spot->update([
            'status' => 'rejected',
            'validated' => false,
            'validation_user_id' => Auth::id(),
            'validation_date' => now(),
            'rejection_reason' => $validated['reason']
        ]);

        return response()->json($spot);
    }
}
