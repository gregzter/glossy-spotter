<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tv_episodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tv_season_id')->constrained()->onDelete('cascade');
            $table->integer('episode_number');
            $table->string('title', 191);
            $table->date('air_date');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['tv_season_id', 'episode_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tv_episodes');
    }
};
