<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->string('user_uid')->nullable();
            $table->string('original_path');
            $table->string('enhanced_path')->nullable();
            $table->string('provider')->nullable();  // replicate, openai, gemini
            $table->string('model')->nullable();
            $table->string('status')->default('pending'); // pending, processing, done, failed
            $table->text('error_message')->nullable();
            $table->float('processing_time')->nullable(); // seconds
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('photos');
    }
};
