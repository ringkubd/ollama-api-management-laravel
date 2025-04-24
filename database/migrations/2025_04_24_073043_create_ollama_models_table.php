<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ollama_models', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('model_id')->unique();
            $table->text('description')->nullable();
            $table->json('parameters')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('request_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ollama_models');
    }
};
