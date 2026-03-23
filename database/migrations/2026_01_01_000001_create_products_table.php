<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->foreignId('parent_id')->nullable()->constrained('products')->cascadeOnDelete();
                $table->string('sku', 64)->unique();
                $table->string('name', 200);
                $table->text('description')->nullable();
                $table->decimal('price', 10, 2)->default(0);
                $table->integer('stock')->default(0);
                $table->boolean('visible')->default(true);
                $table->string('image')->nullable();
                $table->json('images')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('inventory')) {
            Schema::create('inventory', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->integer('stock')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('restocks')) {
            Schema::create('restocks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->integer('added_quantity');
                $table->timestamp('restock_date')->useCurrent();
                $table->string('note')->nullable();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('restocks');
        Schema::dropIfExists('inventory');
        Schema::dropIfExists('products');
    }
}
