<?php

use App\Models\Spot;
use App\Models\User;
use App\Models\Outfit;
use App\Models\Source;
use App\Models\SourceType;
use App\Models\Material;
use App\Models\ClothingType;
use App\Models\Color;
use App\Models\ClothingCategory;
use Illuminate\Testing\Fluent\AssertableJson;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;
use function Pest\Laravel\delete;
use function Pest\Laravel\actingAs;

beforeEach(function () {
    // Configuration existante
    $this->outfit = Outfit::factory()->create();
    $this->sourceType = SourceType::factory()->create(['name' => 'internet']);
    $this->source = Source::factory()->create([
        'source_type_id' => $this->sourceType->id
    ]);
    $this->user = User::factory()->create(['role' => 'viewer']);
    $this->spotter = User::factory()->create(['role' => 'spotter']);
    $this->spotterPlus = User::factory()->create(['role' => 'spotter_plus']);
    $this->admin = User::factory()->create(['role' => 'admin']);

    // Configurer toutes les requêtes pour accepter JSON
    $this->withHeaders([
        'Accept' => 'application/json'
    ]);

    // Spots pour les tests
    $this->publicSpot = Spot::factory()->create([
        'visibility' => 'public',
        'status' => 'published',
        'outfit_id' => $this->outfit->id,
        'user_id' => $this->user->id,
        'source_id' => $this->source->id,
    ]);

    $this->privateSpot = Spot::factory()->create([
        'visibility' => 'private',
        'status' => 'published',
        'outfit_id' => $this->outfit->id,
        'user_id' => $this->user->id,
        'source_id' => $this->source->id,
    ]);

    $this->pendingSpot = Spot::factory()->create([
        'visibility' => 'public',
        'status' => 'pending',
        'outfit_id' => $this->outfit->id,
        'user_id' => $this->user->id,
        'source_id' => $this->source->id,
    ]);
});

// Tests existants...

// Tests pour la création de spots
test('viewer can create a spot that requires validation', function () {
    actingAs($this->user);

    $response = post('/api/v1/spots', [
        'outfit_id' => $this->outfit->id,
        'source_id' => $this->source->id,
        'visibility' => 'public',
    ]);

    $response->assertStatus(201)
        ->assertJson(fn (AssertableJson $json) =>
            $json->has('id')
                ->where('status', 'pending')
                ->where('user_id', $this->user->id)
                ->etc()
        );
});

// Tests pour la modification
test('user can edit their own spot', function () {
    actingAs($this->user);

    $response = put("/api/v1/spots/{$this->pendingSpot->id}", [
        'visibility' => 'private'
    ]);

    $response->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) =>
            $json->where('visibility', 'private')
                ->etc()
        );
});

test('user cannot edit others spot', function () {
    $otherUser = User::factory()->create();
    actingAs($otherUser);

    $response = put("/api/v1/spots/{$this->pendingSpot->id}", [
        'visibility' => 'private'
    ]);

    $response->assertStatus(403);
});

// Tests pour la validation de spots
test('spotter plus can validate spots', function () {
    actingAs($this->spotterPlus);

    $response = post("/api/v1/spots/{$this->pendingSpot->id}/validate");

    $response->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) =>
            $json->where('status', 'published')
                ->where('validated', true)
                ->etc()
        );
});

test('viewer cannot validate spots', function () {
    actingAs($this->user);

    $response = post("/api/v1/spots/{$this->pendingSpot->id}/validate");

    $response->assertStatus(403);
});

// Tests pour le rejet de spots
test('spotter plus can reject spots with reason', function () {
    actingAs($this->spotterPlus);

    $response = post("/api/v1/spots/{$this->pendingSpot->id}/reject", [
        'reason' => 'Content does not match guidelines'
    ]);

    $response->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) =>
            $json->where('status', 'rejected')
                ->where('rejection_reason', 'Content does not match guidelines')
                ->etc()
        );
});

test('viewer cannot reject spots', function () {
    actingAs($this->user);

    $response = post("/api/v1/spots/{$this->pendingSpot->id}/reject", [
        'reason' => 'Test reason'
    ]);

    $response->assertStatus(403);
});

// Tests pour la suppression
test('admin can delete any spot', function () {
    actingAs($this->admin);

    $response = delete("/api/v1/spots/{$this->publicSpot->id}");

    $response->assertStatus(204);
});

test('user can delete their own pending spot', function () {
    actingAs($this->user);

    $response = delete("/api/v1/spots/{$this->pendingSpot->id}");

    $response->assertStatus(204);
});

test('user cannot delete others spot', function () {
    $otherUser = User::factory()->create();
    actingAs($otherUser);

    $response = delete("/api/v1/spots/{$this->pendingSpot->id}");

    $response->assertStatus(403);
});

// Test unique pour le spot premium
test('viewer cannot see premium spots', function () {
    $premiumSpot = Spot::factory()->create([
        'visibility' => 'premium',
        'status' => 'published',
        'outfit_id' => $this->outfit->id,
        'source_id' => $this->source->id,
        'user_id' => $this->spotter->id  // Le spot appartient à un spotter
    ]);

    actingAs($this->user); // Le viewer tente d'accéder
    $response = get("/api/v1/spots/{$premiumSpot->id}");

    $response->assertStatus(403);
});


////////////////////////////////////////////////////////////////////////////////////////////////////
// Tests pour la liste de spots
test('spots list is paginated', function () {
    // Créer plus de spots que la limite par page
    Spot::factory(20)->create([
        'visibility' => 'public',
        'status' => 'published',
        'outfit_id' => $this->outfit->id,
        'user_id' => $this->user->id,
        'source_id' => $this->source->id,
    ]);

    $response = get('/api/v1/spots');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => [
                    'id',
                    'visibility',
                    'status'
                ]
            ],
            'first_page_url',
            'from',
            'last_page',
            'per_page',
            'total'
        ])
        ->assertJson(fn (AssertableJson $json) =>
            $json->where('per_page', 15)
                 ->has('data', 15)
                 ->etc()
        );
});

test('can filter spots by visibility', function () {
    // Créer des spots avec différentes visibilités
    Spot::factory(3)->create([
        'visibility' => 'public',
        'status' => 'published',
        'outfit_id' => $this->outfit->id,
        'user_id' => $this->user->id,
        'source_id' => $this->source->id,
    ]);

    Spot::factory(2)->create([
        'visibility' => 'premium',
        'status' => 'published',
        'outfit_id' => $this->outfit->id,
        'user_id' => $this->spotter->id,
        'source_id' => $this->source->id,
    ]);

    actingAs($this->spotter);
    $response = get('/api/v1/spots?visibility=premium');

    $response->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) =>
            $json->has('data', 2)
                 ->where('data.0.visibility', 'premium')
                 ->etc()
        );
});




////////////////////////////////////////////////////////////////////////////////////////////////////
test('cannot create spot with missing data', function () {
    actingAs($this->user);

    $response = post('/api/v1/spots', [], [
        'Accept' => 'application/json'
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['outfit_id', 'source_id', 'visibility']);
});

test('cannot create spot with invalid visibility value', function () {
    actingAs($this->user);

    $response = post('/api/v1/spots', [
        'outfit_id' => $this->outfit->id,
        'source_id' => $this->source->id,
        'visibility' => 'invalid_value'
    ], [
        'Accept' => 'application/json'
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['visibility']);
});

test('cannot create spot with non-existent outfit_id', function () {
    actingAs($this->user);

    $response = post('/api/v1/spots', [
        'outfit_id' => 99999,
        'source_id' => $this->source->id,
        'visibility' => 'public'
    ], [
        'Accept' => 'application/json'
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['outfit_id']);
});

test('cannot update spot with invalid visibility', function () {
    $spot = Spot::factory()->create([
        'user_id' => $this->user->id,
        'outfit_id' => $this->outfit->id,
        'source_id' => $this->source->id,
        'visibility' => 'public'
    ]);

    actingAs($this->user);

    $response = $this->putJson("/api/v1/spots/{$spot->id}", [
        'visibility' => 'invalid_value'
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['visibility']);
});




////////////////////////////////////////////////////////////////////////////////////////////////////
test('spot details include related data', function () {
    $spot = Spot::factory()->create([
        'visibility' => 'public',
        'status' => 'published',
        'outfit_id' => $this->outfit->id,
        'user_id' => $this->user->id,
        'source_id' => $this->source->id,
    ]);

    actingAs($this->user);  // Authentifier l'utilisateur
    $response = get("/api/v1/spots/{$spot->id}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'id',
            'visibility',
            'status',
            'user' => [
                'id',
                'name',
                'role'
            ],
            'outfit' => [
                'id',
                'description'
            ],
            'source' => [
                'id',
                'title',
                'source_type' => [
                    'id',
                    'name'
                ]
            ]
        ]);
});

test('spot details show validation information when applicable', function () {
    $spot = Spot::factory()->create([
        'visibility' => 'public',
        'status' => 'published',
        'outfit_id' => $this->outfit->id,
        'user_id' => $this->user->id,
        'source_id' => $this->source->id,
        'validated' => true,
        'validation_user_id' => $this->spotterPlus->id,
        'validation_date' => now(),
    ]);

    actingAs($this->user);  // Authentifier l'utilisateur
    $response = get("/api/v1/spots/{$spot->id}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'id',
            'validated',
            'validation_date',
            'validation_user' => [
                'id',
                'name',
                'role'
            ]
        ]);
});

test('rejected spot includes rejection information', function () {
    $spot = Spot::factory()->create([
        'visibility' => 'public',
        'status' => 'rejected',
        'outfit_id' => $this->outfit->id,
        'user_id' => $this->user->id,
        'source_id' => $this->source->id,
        'validated' => false,
        'validation_user_id' => $this->spotterPlus->id,
        'validation_date' => now(),
        'rejection_reason' => 'Content does not meet guidelines'
    ]);

    // Le propriétaire du spot peut voir son spot rejeté
    actingAs($this->user);
    $response = get("/api/v1/spots/{$spot->id}");

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'rejected',
            'rejection_reason' => 'Content does not meet guidelines'
        ]);

    // Un spotter+ peut aussi voir le spot rejeté
    actingAs($this->spotterPlus);
    $response = get("/api/v1/spots/{$spot->id}");

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'rejected',
            'rejection_reason' => 'Content does not meet guidelines'
        ]);

    // Un autre utilisateur ne peut pas voir le spot rejeté
    $otherUser = User::factory()->create(['role' => 'viewer']);
    actingAs($otherUser);
    $response = get("/api/v1/spots/{$spot->id}");

    $response->assertStatus(403);
});






////////////////////////////////////////////////////////////////////////////////////////////////////
// Nouveaux tests à ajouter dans SpotControllerTest.php

test('can search spots by source title', function () {
    $source1 = Source::factory()->create([
        'title' => 'Unique Movie Title',
        'source_type_id' => $this->sourceType->id
    ]);
    $source2 = Source::factory()->create([
        'title' => 'Different Show Title',
        'source_type_id' => $this->sourceType->id
    ]);

    Spot::factory()->create([
        'visibility' => 'public',
        'status' => 'published',
        'outfit_id' => $this->outfit->id,
        'source_id' => $source1->id,
        'user_id' => $this->user->id
    ]);

    Spot::factory()->create([
        'visibility' => 'public',
        'status' => 'published',
        'outfit_id' => $this->outfit->id,
        'source_id' => $source2->id,
        'user_id' => $this->user->id
    ]);

    $response = get('/api/v1/spots?search=Unique');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJson(fn (AssertableJson $json) =>
            $json->has('data.0.source.title')
                 ->where('data.0.source.title', 'Unique Movie Title')
                 ->etc()
        );
});

test('can filter spots by date range', function () {
    // Nettoyer les spots existants
    Spot::query()->delete();

    // Créer un spot hier
    $yesterdaySpot = Spot::factory()->create([
        'visibility' => 'public',
        'status' => 'published',
        'outfit_id' => $this->outfit->id,
        'source_id' => $this->source->id,
        'user_id' => $this->user->id,
        'created_at' => now()->subDay()->startOfDay()
    ]);

    // Créer un spot aujourd'hui
    $todaySpot = Spot::factory()->create([
        'visibility' => 'public',
        'status' => 'published',
        'outfit_id' => $this->outfit->id,
        'source_id' => $this->source->id,
        'user_id' => $this->user->id,
        'created_at' => now()->startOfDay()
    ]);

    $response = get('/api/v1/spots?from=' . now()->startOfDay()->format('Y-m-d'));

    $response->assertOk()
        ->assertJson(fn (AssertableJson $json) =>
            $json->has('data', 1)
                ->has('data.0', fn ($json) =>
                    $json->where('id', $todaySpot->id)
                         ->etc()
                )
                ->etc()
        );
});

test('can filter spots by material type', function () {
    $material1 = Material::factory()->create(['name' => 'latex']);
    $material2 = Material::factory()->create(['name' => 'leather']);

    $clothingCategory = ClothingCategory::factory()->create();
    $clothingType = ClothingType::factory()->create(['clothing_category_id' => $clothingCategory->id]);

    $outfit1 = Outfit::factory()->create();
    $outfit2 = Outfit::factory()->create();

    $outfit1->outfitItems()->create([
        'clothing_type_id' => $clothingType->id,
        'material_id' => $material1->id,
        'color_id' => Color::factory()->create()->id,
        'shine_level' => 'shiny'
    ]);

    $outfit2->outfitItems()->create([
        'clothing_type_id' => $clothingType->id,
        'material_id' => $material2->id,
        'color_id' => Color::factory()->create()->id,
        'shine_level' => 'shiny'
    ]);

    Spot::factory()->create([
        'visibility' => 'public',
        'status' => 'published',
        'outfit_id' => $outfit1->id,
        'source_id' => $this->source->id,
        'user_id' => $this->user->id
    ]);

    Spot::factory()->create([
        'visibility' => 'public',
        'status' => 'published',
        'outfit_id' => $outfit2->id,
        'source_id' => $this->source->id,
        'user_id' => $this->user->id
    ]);

    $response = get('/api/v1/spots?material=latex');

    $response->assertOk()
        ->assertJson(fn (AssertableJson $json) =>
            $json->has('data', 1)
                ->has('data.0.outfit.outfit_items.0', fn ($json) =>
                    $json->where('material.name', 'latex')
                         ->etc()
                )
                ->etc()
        );
});

test('can combine multiple filters', function () {
    $material = Material::factory()->create(['name' => 'latex']);
    $outfit = Outfit::factory()->create();
    $outfit->outfitItems()->create([
        'clothing_type_id' => ClothingType::factory()->create()->id,
        'material_id' => $material->id,
        'color_id' => Color::factory()->create()->id,
    ]);

    // Spot qui correspond aux critères
    Spot::factory()->create([
        'visibility' => 'public',
        'status' => 'published',
        'outfit_id' => $outfit->id,
        'source_id' => $this->source->id,
        'user_id' => $this->user->id,
        'created_at' => now()
    ]);

    // Spot qui ne correspond pas (date différente)
    Spot::factory()->create([
        'visibility' => 'public',
        'status' => 'published',
        'outfit_id' => $outfit->id,
        'source_id' => $this->source->id,
        'user_id' => $this->user->id,
        'created_at' => now()->subDays(5)
    ]);

    $response = get('/api/v1/spots?material=latex&from=' . now()->subDay()->toDateString());

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data');
});

test('returns empty result for no matches', function () {
    Spot::factory(3)->create([
        'visibility' => 'public',
        'status' => 'published',
        'outfit_id' => $this->outfit->id,
        'source_id' => $this->source->id,
        'user_id' => $this->user->id
    ]);

    $response = get('/api/v1/spots?search=NoMatchingTitle');

    $response->assertStatus(200)
        ->assertJsonCount(0, 'data');
});
