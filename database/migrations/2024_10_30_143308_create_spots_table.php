<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('outfit_id')->constrained()->onDelete('cascade');
            $table->foreignId('person_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('source_id')->constrained()->onDelete('cascade');
            $table->boolean('validated')->default(false);
            $table->enum('status', ['draft', 'pending', 'published', 'rejected'])->default('draft');
            $table->enum('visibility', ['public', 'member', 'premium', 'private'])->default('public');
            $table->boolean('is_adult_content')->default(false);
            $table->text('rejection_reason')->nullable();
            $table->foreignId('validation_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('validation_date')->nullable();
            $table->timestamps();

            // Index optimisés
            $table->index(['status', 'visibility'], 'idx_spot_status');
            $table->index(['user_id', 'status'], 'idx_user_spots');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spots');
    }
};
