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
        if (!Schema::hasColumn('products', 'deleted_at') || !Schema::hasColumn('products', 'images')) {
            Schema::table('products', function (Blueprint $table) {
                if (!Schema::hasColumn('products', 'deleted_at')) {
                    $table->softDeletes();
                }

                if (!Schema::hasColumn('products', 'images')) {
                    $table->json('images')->nullable()->after('visible');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'deleted_at')) {
                $table->dropSoftDeletes();
            }

            if (Schema::hasColumn('products', 'images')) {
                $table->dropColumn('images');
            }
        });
    }
};
