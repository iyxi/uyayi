<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Seed initial categories
        DB::table('categories')->insert([
            ['name' => 'Bath Essentials'],
            ['name' => 'Diapering Care'],
            ['name' => 'Skin Care'],
            ['name' => 'Health & Hygiene'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};