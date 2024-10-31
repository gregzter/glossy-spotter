<?php

use App\Models\Spot;
use App\Models\User;
use App\Models\SpotComment;
use App\Models\SpotFavorite;
use App\Models\Outfit;
use App\Models\Source;
use App\Models\SourceType;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Auth\AuthenticationException;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\delete;
use function Pest\Laravel\put;
use function Pest\Laravel\actingAs;

// tests/Feature/Api/SpotInteractionsTest.php

beforeEach(function () {
    // Créer les dépendances nécessaires
    $this->outfit = Outfit::factory()->create();
    $this->sourceType = SourceType::factory()->create(['name' => 'internet']);
    $this->source = Source::factory()->create([
        'source_type_id' => $this->sourceType->id
    ]);

    // Créer les utilisateurs
    $this->user = User::factory()->create(['role' => 'viewer']);
    $this->otherUser = User::factory()->create(['role' => 'viewer']);

    // Créer le spot avec toutes les relations requises
    $this->spot = Spot::factory()->create([
        'visibility' => 'public',
        'status' => 'published',
        'user_id' => $this->user->id,
        'outfit_id' => $this->outfit->id,
        'source_id' => $this->source->id
    ]);
});

// Tests pour les commentaires
test('user can comment on public spot', function () {
    actingAs($this->user);

    $response = post("/api/v1/spots/{$this->spot->id}/comments", [
        'content' => 'This is a test comment'
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'content' => 'This is a test comment',
            'user_id' => $this->user->id
        ]);
});

test('cannot comment without authentication', function () {
    // Désactive la gestion des exceptions
    $this->withoutExceptionHandling();

    // Vérifie que le code suivant lance une exception d'authentification
    $this->expectException(AuthenticationException::class);
    $this->expectExceptionMessage('Unauthenticated.');

    // Fait la requête
    $this->withHeaders([
        'Accept' => 'application/json'
    ])->post("/api/v1/spots/{$this->spot->id}/comments", [
        'content' => 'This is a test comment'
    ]);
});

test('cannot create empty comment', function () {
    actingAs($this->user);

    $response = post("/api/v1/spots/{$this->spot->id}/comments", [
        'content' => ''
    ], [
        'Accept' => 'application/json'
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['content']);
});

test('user can edit their own comment', function () {
    actingAs($this->user);

    $comment = SpotComment::factory()->create([
        'spot_id' => $this->spot->id,
        'user_id' => $this->user->id,
        'content' => 'Original content'
    ]);

    $response = put("/api/v1/spots/{$this->spot->id}/comments/{$comment->id}", [
        'content' => 'Updated content'
    ]);

    $response->assertOk()
        ->assertJson([
            'content' => 'Updated content',
            'is_edited' => true
        ]);
});

test('user cannot edit others comments', function () {
    actingAs($this->otherUser);

    $comment = SpotComment::factory()->create([
        'spot_id' => $this->spot->id,
        'user_id' => $this->user->id,
        'content' => 'Original content'
    ]);

    $response = put("/api/v1/spots/{$this->spot->id}/comments/{$comment->id}", [
        'content' => 'Updated content'
    ]);

    $response->assertStatus(403);
});

// Tests pour les favoris
test('user can favorite a spot', function () {
    actingAs($this->user);

    $response = post("/api/v1/spots/{$this->spot->id}/favorite");

    $response->assertStatus(201)
        ->assertJson([
            'message' => 'Spot added to favorites'
        ]);

    $this->assertDatabaseHas('spot_favorites', [
        'spot_id' => $this->spot->id,
        'user_id' => $this->user->id
    ]);
});

test('user can unfavorite a spot', function () {
    actingAs($this->user);

    SpotFavorite::factory()->create([
        'spot_id' => $this->spot->id,
        'user_id' => $this->user->id
    ]);

    $response = delete("/api/v1/spots/{$this->spot->id}/favorite");

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Spot removed from favorites'
        ]);

    $this->assertDatabaseMissing('spot_favorites', [
        'spot_id' => $this->spot->id,
        'user_id' => $this->user->id
    ]);
});

test('can get spot comments with pagination', function () {
    SpotComment::factory(3)->create([
        'spot_id' => $this->spot->id,
        'user_id' => $this->user->id
    ]);

    $response = get("/api/v1/spots/{$this->spot->id}/comments");

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'content',
                    'user' => [
                        'id',
                        'name'
                    ],
                    'created_at'
                ]
            ],
        ]);
});

test('can get users who favorited a spot', function () {
    $users = User::factory(3)->create();

    foreach ($users as $user) {
        SpotFavorite::create([
            'spot_id' => $this->spot->id,
            'user_id' => $user->id
        ]);
    }

    $response = get("/api/v1/spots/{$this->spot->id}/favorites");

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name'
                ]
            ]
        ]);
});
