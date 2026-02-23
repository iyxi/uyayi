<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price',10,2);
            $table->boolean('visible')->default(true);
            $table->timestamps();
        });

        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('stock')->default(0);
            $table->timestamps();
        });

        Schema::create('restocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('added_quantity');
            $table->timestamp('restock_date')->useCurrent();
            $table->string('note')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('restocks');
        Schema::dropIfExists('inventory');
        Schema::dropIfExists('products');
    }
}
