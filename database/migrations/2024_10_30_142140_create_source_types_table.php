<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('source_types', function (Blueprint $table) {
            $table->id();
            $table->enum('name', [
                'movie',
                'tv_show',
                'tv_program',
                'event',
                'internet'
            ]);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('source_types');
    }
};
