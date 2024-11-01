<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\Spot;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class MediaController extends Controller
{
    protected $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    public function store(Request $request, Spot $spot): JsonResponse
    {
        if (!Gate::allows('edit-spot', $spot)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $validated = $request->validate([
                'file' => [
                    'required',
                    'file',
                    'image',
                    'max:10240', // 10MB
                    'dimensions:min_width=800,min_height=600,max_width=4096,max_height=4096',
                    function ($attribute, $value, $fail) use ($spot) {
                        if (!$value->isValid()) {
                            $fail('The file is invalid.');
                            return;
                        }

                        $hash = md5_file($value->path());
                        if (Media::where('spot_id', $spot->id)
                            ->where('file_hash', $hash)
                            ->exists()) {
                            $fail('This file has already been uploaded to this spot.');
                        }
                    }
                ],
                'type' => ['required', 'in:image']
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }


        try {
            $file = $request->file('file');
            $filename = $file->hashName();
            $fileHash = md5_file($file->path());

            // Créer les chemins de stockage
            $originalPath = "images/original/{$spot->id}/{$filename}";
            $thumbnailPath = "images/thumbnails/{$spot->id}/{$filename}";

            // Stocker l'image originale
            if (!Storage::disk('s3')->putFileAs(
                "images/original/{$spot->id}",
                $file,
                $filename
            )) {
                throw new \Exception('Failed to store original file');
            }

            // Créer et stocker la miniature
            $image = $this->manager->read($file);
            $thumbnail = $image->cover(300, 300);

            if (!Storage::disk('s3')->put(
                $thumbnailPath,
                $thumbnail->toJpeg()
            )) {
                // Si la création de la miniature échoue, supprimer l'original
                Storage::disk('s3')->delete($originalPath);
                throw new \Exception('Failed to create thumbnail');
            }

            // Créer l'enregistrement dans la base de données
            $media = new Media([
                'spot_id' => $spot->id,
                'original_path' => $originalPath,
                'thumbnail_path' => $thumbnailPath,
                'type' => 'image',
                'status' => 'ready',
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'dimensions' => $image->width() . 'x' . $image->height(),
                'file_hash' => $fileHash
            ]);

            $media->save();

            return response()->json($media, 201);

        } catch (\Exception $e) {
            // Nettoyage en cas d'erreur
            if (isset($originalPath)) {
                Storage::disk('s3')->delete($originalPath);
            }
            if (isset($thumbnailPath)) {
                Storage::disk('s3')->delete($thumbnailPath);
            }

            return response()->json([
                'message' => 'Failed to process media file',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Spot $spot, Media $media): JsonResponse
    {
        if (!Gate::allows('edit-spot', $spot) || $media->spot_id !== $spot->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $paths = array_filter([
                $media->original_path ?? null,
                $media->thumbnail_path ?? null,
                $media->upscaled_path ?? null
            ]);

            if (!empty($paths)) {
                Storage::disk('s3')->delete($paths);
            }

            $media->delete();

            return response()->json(null, 204);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete media',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function requestUpscale(Media $media): JsonResponse
    {
        if (!Gate::allows('edit-spot', $media->spot)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($media->type !== 'image') {
            return response()->json([
                'message' => 'Only images can be upscaled'
            ], 422);
        }

        if ($media->status === 'processing') {
            return response()->json([
                'message' => 'Media is already being processed'
            ], 422);
        }

        $media->update([
            'status' => 'processing'
        ]);

        // Ici, vous pourriez dispatcher un job pour traiter l'upscaling
        // UpscaleImage::dispatch($media);

        return response()->json([
            'message' => 'Upscaling process started'
        ], 202);
    }
}
