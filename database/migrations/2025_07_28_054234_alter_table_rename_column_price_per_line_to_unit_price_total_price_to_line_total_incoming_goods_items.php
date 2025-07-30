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
        Schema::table('incoming_goods_items', function (Blueprint $table) {
            $table->renameColumn('price_per_line', 'unit_price');
            $table->renameColumn('total_price', 'line_total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incoming_goods_items', function (Blueprint $table) {
            $table->renameColumn('unit_price', 'price_per_line');
            $table->renameColumn('line_total', 'total_price');
        });
    }
};
