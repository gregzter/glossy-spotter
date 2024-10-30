<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tv_seasons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tv_show_id')->constrained()->onDelete('cascade');
            $table->integer('season_number');
            $table->year('release_year');
            $table->string('title', 191)->nullable();
            $table->text('description')->nullable();
            $table->integer('episode_count');
            $table->timestamps();

            $table->unique(['tv_show_id', 'season_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tv_seasons');
    }
};
