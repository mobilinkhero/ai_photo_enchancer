<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('app_features', function (Blueprint $table) {
            $table->id();
            $table->string('feature_id')->unique();   // e.g. 'enhance', 'restore', 'colorize'
            $table->string('title');
            $table->string('description')->nullable();
            $table->string('icon')->default('auto_fix_high_rounded');
            $table->string('color')->default('4280391411'); // Flutter Color int
            $table->boolean('is_premium')->default(false);
            $table->boolean('enabled')->default(true);
            $table->integer('coins')->default(1);
            $table->json('benefits')->nullable();        // ["Benefit 1", "Benefit 2"]
            $table->string('before_url')->nullable();
            $table->string('after_url')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_features');
    }
};
