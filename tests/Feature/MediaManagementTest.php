<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\Spot;
use App\Models\User;
use App\Models\Outfit;
use App\Models\Source;
use App\Models\SourceType;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\post;
use function Pest\Laravel\delete;
use function Pest\Laravel\actingAs;
use Illuminate\Support\Facades\Log;

beforeEach(function () {
    Storage::fake('s3');

    $this->outfit = Outfit::factory()->create();
    $this->sourceType = SourceType::factory()->create(['name' => 'internet']);
    $this->source = Source::factory()->create([
        'source_type_id' => $this->sourceType->id
    ]);

    $this->user = User::factory()->create(['role' => 'viewer']);
    $this->spotter = User::factory()->create(['role' => 'spotter']);

    $this->spot = Spot::factory()->create([
        'visibility' => 'public',
        'status' => 'published',
        'outfit_id' => $this->outfit->id,
        'user_id' => $this->user->id,
        'source_id' => $this->source->id,
    ]);
});

test('can upload image to spot', function () {
    actingAs($this->user);

    $file = UploadedFile::fake()->image('outfit.jpg', 1200, 800);

    $response = post("/api/v1/spots/{$this->spot->id}/media", [
        'file' => $file,
        'type' => 'image'
    ]);

    $response->assertStatus(201)
        ->assertJson(fn ($json) =>
            $json->has('id')
                ->where('type', 'image')
                ->where('status', 'ready')
                ->where('mime_type', 'image/jpeg')
                ->etc()
        );

    // Vérifier que les fichiers ont été créés
    expect(Storage::disk('s3')->exists("images/original/{$this->spot->id}/" . $file->hashName()))->toBeTrue();
    expect(Storage::disk('s3')->exists("images/thumbnails/{$this->spot->id}/" . $file->hashName()))->toBeTrue();
});

test('validates image dimensions and size', function () {
    actingAs($this->user);

    // 1. Image trop petite en largeur
    $smallWidthFile = UploadedFile::fake()->image('small_width.jpg', 700, 800);
    $response = post("/api/v1/spots/{$this->spot->id}/media", [
        'file' => $smallWidthFile,
        'type' => 'image'
    ]);
    expect($response->getStatusCode())->toBe(422);
    expect($response->json('errors.file'))->toContain('The file field has invalid image dimensions.');

    // 2. Image trop petite en hauteur
    $smallHeightFile = UploadedFile::fake()->image('small_height.jpg', 800, 500);
    $response = post("/api/v1/spots/{$this->spot->id}/media", [
        'file' => $smallHeightFile,
        'type' => 'image'
    ]);
    expect($response->getStatusCode())->toBe(422);
    expect($response->json('errors.file'))->toContain('The file field has invalid image dimensions.');

    // 3. Image trop grande en largeur
    $largeWidthFile = UploadedFile::fake()->image('large_width.jpg', 4200, 1000);
    $response = post("/api/v1/spots/{$this->spot->id}/media", [
        'file' => $largeWidthFile,
        'type' => 'image'
    ]);
    expect($response->getStatusCode())->toBe(422);
    expect($response->json('errors.file'))->toContain('The file field has invalid image dimensions.');

    // 4. Image trop grande en hauteur
    $largeHeightFile = UploadedFile::fake()->image('large_height.jpg', 1000, 4200);
    $response = post("/api/v1/spots/{$this->spot->id}/media", [
        'file' => $largeHeightFile,
        'type' => 'image'
    ]);
    expect($response->getStatusCode())->toBe(422);
    expect($response->json('errors.file'))->toContain('The file field has invalid image dimensions.');

    // 5. Fichier trop lourd (>10MB)
    $tooBigFile = UploadedFile::fake()->create('big.jpg', 11 * 1024); // 11MB
    $response = post("/api/v1/spots/{$this->spot->id}/media", [
        'file' => $tooBigFile,
        'type' => 'image'
    ]);
    expect($response->getStatusCode())->toBe(422);
    expect($response->json('errors.file'))->toContain('The file field must not be greater than 10240 kilobytes.');

    // 6. Type de fichier non autorisé
    $invalidTypeFile = UploadedFile::fake()->create('document.pdf', 100);
    $response = post("/api/v1/spots/{$this->spot->id}/media", [
        'file' => $invalidTypeFile,
        'type' => 'image'
    ]);
    expect($response->getStatusCode())->toBe(422);
    expect($response->json('errors.file'))->toContain('The file field must be an image.');

    // 7. Sans fichier
    $response = post("/api/v1/spots/{$this->spot->id}/media", [
        'type' => 'image'
    ]);
    expect($response->getStatusCode())->toBe(422);
    expect($response->json('errors.file'))->toContain('The file field is required.');

    // 8. Avec un type invalide
    $validFile = UploadedFile::fake()->image('valid.jpg', 1000, 800);
    $response = post("/api/v1/spots/{$this->spot->id}/media", [
        'file' => $validFile,
        'type' => 'invalid'
    ]);
    expect($response->getStatusCode())->toBe(422);
    expect($response->json('errors.type'))->toContain('The selected type is invalid.');

    // 9. Sans type
    $response = post("/api/v1/spots/{$this->spot->id}/media", [
        'file' => $validFile
    ]);
    expect($response->getStatusCode())->toBe(422);
    expect($response->json('errors.type'))->toContain('The type field is required.');

    // 10. Fichier aux dimensions minimales exactes (doit passer)
    $minDimensionsFile = UploadedFile::fake()->image('min.jpg', 800, 600);
    $response = post("/api/v1/spots/{$this->spot->id}/media", [
        'file' => $minDimensionsFile,
        'type' => 'image'
    ]);
    expect($response->getStatusCode())->toBe(201);

    // 11. Fichier aux dimensions maximales exactes (doit passer)
    $maxDimensionsFile = UploadedFile::fake()->image('max.jpg', 4096, 4096);
    $response = post("/api/v1/spots/{$this->spot->id}/media", [
        'file' => $maxDimensionsFile,
        'type' => 'image'
    ]);
    expect($response->getStatusCode())->toBe(201);
});

test('user can only upload to their own spots', function () {
    $otherUser = User::factory()->create(['role' => 'viewer']);
    actingAs($otherUser);

    $file = UploadedFile::fake()->image('outfit.jpg', 1200, 800);

    $response = post("/api/v1/spots/{$this->spot->id}/media", [
        'file' => $file,
        'type' => 'image'
    ]);

    $response->assertStatus(403);
});

test('can delete media from spot', function () {
    actingAs($this->user);

    // Upload first
    $file = UploadedFile::fake()->image('outfit.jpg', 1200, 800);
    $uploadResponse = post("/api/v1/spots/{$this->spot->id}/media", [
        'file' => $file,
        'type' => 'image'
    ]);

    expect($uploadResponse->status())->toBe(201);

    $mediaId = $uploadResponse->json('id');
    $media = Media::find($mediaId);
    expect($media)->not->toBeNull();

    // Then delete
    $response = delete("/api/v1/spots/{$this->spot->id}/media/{$media->id}");

    $response->assertStatus(204);
});

test('can request image upscaling', function () {
    actingAs($this->user);

    // Upload original image
    $file = UploadedFile::fake()->image('outfit.jpg', 1200, 800);
    $uploadResponse = post("/api/v1/spots/{$this->spot->id}/media", [
        'file' => $file,
        'type' => 'image'
    ]);

    $mediaId = $uploadResponse->json('id');

    // Request upscaling
    $response = post("/api/v1/media/{$mediaId}/upscale");

    $response->assertStatus(202)
        ->assertJson(['message' => 'Upscaling process started']);

    $media = Media::find($mediaId);
    expect($media->status)->toBe('processing');
});

test('prevents duplicate media uploads', function () {
    actingAs($this->user);

    // Premier upload avec une image valide
    $validFile = UploadedFile::fake()->image('original.jpg', 1000, 800);

    $response = post("/api/v1/spots/{$this->spot->id}/media", [
        'file' => $validFile,
        'type' => 'image'
    ]);

    expect($response->getStatusCode())->toBe(201);

    // Deuxième upload avec la même image
    $response = post("/api/v1/spots/{$this->spot->id}/media", [
        'file' => $validFile,
        'type' => 'image'
    ]);

    expect($response->getStatusCode())->toBe(422);
    expect($response->json('errors.file'))->toContain('This file has already been uploaded to this spot.');
});


test('media storage and paths', function () {
    actingAs($this->user);

    $file = UploadedFile::fake()->image('test.jpg', 1000, 800);
    $response = post("/api/v1/spots/{$this->spot->id}/media", [
        'file' => $file,
        'type' => 'image'
    ]);

    expect($response->getStatusCode())->toBe(201);

    // Vérifier que les chemins sont corrects
    $media = Media::find($response->json('id'));
    expect($media->original_path)->toContain("images/original/{$this->spot->id}/");
    expect($media->thumbnail_path)->toContain("images/thumbnails/{$this->spot->id}/");

    // Vérifier que les fichiers existent
    expect(Storage::disk('s3')->exists($media->original_path))->toBeTrue();
    expect(Storage::disk('s3')->exists($media->thumbnail_path))->toBeTrue();
});

test('media deletion cleans up files', function () {
    actingAs($this->user);

    // Upload a file first
    $file = UploadedFile::fake()->image('test.jpg', 1000, 800);
    $response = post("/api/v1/spots/{$this->spot->id}/media", [
        'file' => $file,
        'type' => 'image'
    ]);

    $mediaId = $response->json('id');
    $media = Media::find($mediaId);
    $originalPath = $media->original_path;
    $thumbnailPath = $media->thumbnail_path;

    // Delete the media
    $response = delete("/api/v1/spots/{$this->spot->id}/media/{$mediaId}");
    expect($response->getStatusCode())->toBe(204);

    // Verify files are deleted
    expect(Storage::disk('s3')->exists($originalPath))->toBeFalse();
    expect(Storage::disk('s3')->exists($thumbnailPath))->toBeFalse();

    // Verify database record is deleted
    expect(Media::find($mediaId))->toBeNull();
});
