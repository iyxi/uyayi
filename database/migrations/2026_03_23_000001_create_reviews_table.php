<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->string('reviewable_type');
            $table->unsignedBigInteger('reviewable_id');
            $table->unsignedTinyInteger('rating');
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'reviewable_type', 'reviewable_id'], 'reviews_unique_user_target');
            $table->index(['reviewable_type', 'reviewable_id']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
