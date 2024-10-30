<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('translatable_type');  // Model type (Color, Material, etc.)
            $table->unsignedBigInteger('translatable_id');  // ID of the model
            $table->string('locale', 5);  // fr, en, etc.
            $table->string('field');  // field being translated
            $table->text('value');  // translated value
            $table->timestamps();

            $table->unique(['translatable_type', 'translatable_id', 'locale', 'field'], 'unique_translation');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
