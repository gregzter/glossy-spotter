<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table des notes
        Schema::create('spot_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spot_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('rating')->check('rating between 1 and 5');
            $table->timestamps();

            $table->unique(['user_id', 'spot_id'], 'idx_user_spot_rating');
        });

        // Table des favoris
        Schema::create('spot_favorites', function (Blueprint $table) {
            $table->foreignId('spot_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('created_at')->nullable();

            $table->primary(['spot_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spot_ratings');
        Schema::dropIfExists('spot_favorites');
    }
};
