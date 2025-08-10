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
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('company_phone')->after('company_name');
            $table->string('contact_person_name')->after('company_phone');
            $table->string('contact_person_phone')->after('contact_person_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('company_phone');
            $table->dropColumn('contact_person_name');
            $table->dropColumn('contact_person_phone');
        });
    }
};
