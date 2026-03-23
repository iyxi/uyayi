<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddStockToProductsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('products', 'stock')) {
            Schema::table('products', function (Blueprint $table) {
                $table->integer('stock')->default(0)->after('price');
            });
        }

        if (Schema::hasTable('inventory')) {
            DB::statement('
                UPDATE products p
                LEFT JOIN inventory i ON i.product_id = p.id
                SET p.stock = COALESCE(i.stock, p.stock, 0)
            ');
        }

        DB::table('products')->whereNull('stock')->update(['stock' => 0]);

        Schema::dropIfExists('restocks');
        Schema::dropIfExists('inventory');
    }

    public function down()
    {
        // Recreate inventory table
        if (!Schema::hasTable('inventory')) {
            Schema::create('inventory', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->integer('stock')->default(0);
                $table->timestamps();
            });
        }

        // Recreate restocks table
        if (!Schema::hasTable('restocks')) {
            Schema::create('restocks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->integer('added_quantity');
                $table->timestamp('restock_date')->useCurrent();
                $table->string('note')->nullable();
            });
        }

        // Migrate stock back to inventory
        DB::statement('INSERT INTO inventory (product_id, stock, created_at, updated_at) SELECT id, stock, NOW(), NOW() FROM products');

        // Remove stock column from products
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('stock');
        });
    }
}
