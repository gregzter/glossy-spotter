<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clothing_categories', function (Blueprint $table) {
            $table->id();
            $table->enum('name', ['tops', 'bottoms', 'dresses', 'full_outfits']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clothing_categories');
    }
};
