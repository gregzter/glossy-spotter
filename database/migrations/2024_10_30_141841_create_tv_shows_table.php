<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tv_shows', function (Blueprint $table) {
            $table->id();
            $table->string('title', 191);
            $table->string('original_title', 191)->nullable();
            $table->foreignId('country_id')->constrained();
            $table->year('start_year');
            $table->year('end_year')->nullable();
            $table->text('description')->nullable();
            $table->string('poster_path')->nullable();
            $table->enum('status', ['ongoing', 'ended', 'cancelled']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tv_shows');
    }
};
