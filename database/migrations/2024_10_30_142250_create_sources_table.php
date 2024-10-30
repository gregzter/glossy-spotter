<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_type_id')->constrained();
            $table->foreignId('country_id')->nullable()->constrained();
            $table->foreignId('tv_show_id')->nullable()->constrained();
            $table->foreignId('tv_episode_id')->nullable()->constrained();
            $table->foreignId('tv_channel_id')->nullable()->constrained();
            $table->foreignId('tv_program_id')->nullable()->constrained();
            $table->string('title', 191);
            $table->string('original_title', 191)->nullable();
            $table->date('release_date')->nullable();
            $table->string('url', 255)->nullable();
            $table->timestamps();

            // Index avec un nom plus court
            $table->index(['source_type_id', 'tv_show_id'], 'idx_source_show');
            $table->index(['tv_episode_id', 'tv_program_id'], 'idx_source_episode');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sources');
    }
};
