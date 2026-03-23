<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('products', 'category_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unsignedBigInteger('category_id')->nullable()->after('id');
                $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('products', 'category_id')) {
            return;
        }

        try {
            DB::statement('ALTER TABLE products DROP FOREIGN KEY products_category_id_foreign');
        } catch (\Throwable $e) {
            // The column can exist without the foreign key in older/local schemas.
        }

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('category_id');
        });
    }
};
