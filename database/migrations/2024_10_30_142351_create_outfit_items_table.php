<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outfit_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outfit_id')->constrained()->onDelete('cascade');
            $table->foreignId('clothing_type_id')->constrained();
            $table->foreignId('material_id')->constrained();
            $table->foreignId('color_id')->constrained();
            $table->enum('shine_level', ['very_shiny', 'shiny', 'slightly_shiny']);
            $table->timestamps();

            // Index pour optimiser les recherches
            $table->index(['outfit_id', 'clothing_type_id', 'material_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outfit_items');
    }
};
