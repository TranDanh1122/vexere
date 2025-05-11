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
        Schema::table('product_locations', function (Blueprint $table) {
            $table->string('direction')->after('type'); // Enum SGVT | VTSG
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_locations', function (Blueprint $table) {
            $table->dropColumn('direction');
        });
    }
};
