<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tv_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tv_channel_id')->constrained()->onDelete('cascade');
            $table->string('name', 191);
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('still_running')->default(true);
            $table->enum('frequency', ['daily', 'weekly', 'monthly', 'special']);
            $table->enum('type', [
                'news',
                'entertainment',
                'reality',
                'talk_show',
                'game_show',
                'other'
            ]);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tv_programs');
    }
};
