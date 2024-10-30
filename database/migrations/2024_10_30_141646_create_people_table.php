<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->string('firstname', 50);
            $table->string('lastname', 50);
            $table->string('nickname', 50)->nullable();
            $table->date('birth_date')->nullable();
            $table->foreignId('country_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('profession', ['actress', 'presenter', 'singer', 'other']);
            $table->text('biography')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
