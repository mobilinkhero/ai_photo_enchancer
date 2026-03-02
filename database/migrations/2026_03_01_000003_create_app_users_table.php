<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('app_users', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->unique();         // Firebase/App user UID
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('platform')->default('android'); // android, ios
            $table->string('subscription')->default('free'); // free, pro, premium
            $table->integer('credits')->default(0);
            $table->integer('photos_enhanced')->default(0);
            $table->boolean('is_banned')->default(false);
            $table->timestamp('subscription_expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_users');
    }
};
