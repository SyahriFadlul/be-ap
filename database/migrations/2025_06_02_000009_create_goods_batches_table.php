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
        Schema::create('goods_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incoming_goods_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('goods_id')->constrained()->onDelete('cascade');
            $table->string('batch_number',20)->unique();
            $table->date('expiry_date')->nullable();
            $table->integer('selling_price');
            $table->integer('qty')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_batches');
    }
};
