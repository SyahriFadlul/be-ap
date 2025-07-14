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
        Schema::create('goods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete(); 
            $table->foreignId('base_unit_id')->constrained('units');    // cth: tablet
            $table->foreignId('medium_unit_id')->nullable()->constrained('units'); // cth: strip
            $table->foreignId('large_unit_id')->nullable()->constrained('units');  // cth: box
            $table->integer('conversion_medium_to_base')->nullable(); // cth: 1 strip = 10 tablet
            $table->integer('conversion_large_to_medium')->nullable(); // cth: 1 box = 10 strip   
            $table->string('shelf_location',3);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods');
    }
};
