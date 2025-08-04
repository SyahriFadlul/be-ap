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
        Schema::table('outgoing_goods_items', function (Blueprint $table) {
            $table->integer('final_qty')->after('initial_qty')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('outgoing_goods_items', function (Blueprint $table) {
            $table->dropColumn('final_qty');
        });
    }
};
