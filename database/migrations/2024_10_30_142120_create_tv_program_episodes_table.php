<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tv_program_episodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tv_program_id')->constrained()->onDelete('cascade');
            $table->string('title', 191);
            $table->date('air_date');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tv_program_episodes');
    }
};
