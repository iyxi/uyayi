<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersPaymentsTables extends Migration
{
    public function up()
    {
        // Create orders table first (without payment_id foreign key)
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('order_number')->unique();
            $table->enum('status',['pending','processing','shipped','completed','cancelled'])->default('pending');
            $table->decimal('total_amount',10,2)->default(0);
            $table->text('shipping_address')->nullable();
            $table->timestamps();
        });

        // Create order_items table
        Schema::create('order_items', function (Blueprint $table){
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->integer('quantity');
            $table->decimal('unit_price',10,2);
            $table->decimal('subtotal',10,2);
            $table->timestamps();
        });

        // Create payments table (referencing orders)
        Schema::create('payments', function (Blueprint $table){
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('method',['gcash','card','cod']);
            $table->decimal('amount',10,2);
            $table->enum('status',['pending','paid','failed','refunded'])->default('pending');
            $table->string('txn_reference')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
}
