<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Ramsey\Uuid\Type\Integer;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('outgoing_goods_items', function (Blueprint $table) {
            $table->renameColumn('qty', 'initial_qty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('outgoing_goods_items', function (Blueprint $table) {
            $table->renameColumn('initial_qty', 'qty');
        });
    }
};
