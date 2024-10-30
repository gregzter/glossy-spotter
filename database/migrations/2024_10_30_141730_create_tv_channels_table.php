<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tv_channels', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->foreignId('country_id')->constrained();
            $table->string('logo_path')->nullable();
            $table->boolean('active')->default(true);
            $table->enum('type', ['national', 'cable', 'streaming']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tv_channels');
    }
};
