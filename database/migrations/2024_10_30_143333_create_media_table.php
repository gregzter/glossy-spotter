<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spot_id')->constrained()->onDelete('cascade');
            $table->string('original_path');
            $table->string('thumbnail_path');
            $table->string('upscaled_path')->nullable();
            $table->enum('type', ['image', 'video']);
            $table->enum('status', ['processing', 'ready', 'error'])->default('processing');
            $table->string('mime_type', 100);
            $table->integer('file_size');
            $table->string('dimensions', 20);
            $table->timestamps();

            $table->index(['spot_id', 'type'], 'idx_spot_media');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
