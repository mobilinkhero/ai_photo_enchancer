<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('api_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('request_id')->index();     // Groups all logs for one photo job

            // What came IN from the client (Flutter app)
            $table->string('client_ip')->nullable();
            $table->string('client_endpoint')->nullable();   // /api/enhance
            $table->string('client_method')->default('POST');
            $table->json('client_headers')->nullable();
            $table->json('client_payload')->nullable();      // What the app sent

            // What we sent OUT to AI provider
            $table->string('ai_provider')->nullable();       // replicate | openai | gemini
            $table->string('ai_endpoint')->nullable();       // Full URL called
            $table->json('ai_request_payload')->nullable();  // Body we sent to AI
            $table->integer('ai_request_size_bytes')->nullable();

            // What the AI sent BACK
            $table->integer('ai_response_status')->nullable();
            $table->json('ai_response_body')->nullable();
            $table->float('ai_response_time_ms')->nullable();  // milliseconds
            $table->string('ai_model')->nullable();
            $table->string('ai_output_url')->nullable();        // Result URL if done

            // Final outcome
            $table->string('status')->default('pending');  // pending | success | error | timeout
            $table->text('error_message')->nullable();
            $table->float('total_time_ms')->nullable();    // End-to-end

            // Relations
            $table->string('user_uid')->nullable()->index();
            $table->unsignedBigInteger('photo_id')->nullable()->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_logs');
    }
};
