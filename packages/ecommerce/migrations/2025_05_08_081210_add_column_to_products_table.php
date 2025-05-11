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
        Schema::table('products', function (Blueprint $table) {
            $table->time('start_time_sg_vt')->after('detail')->nullable();
            $table->time('start_time_vt_sg')->after('start_time_sg_vt')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('start_time_sg_vt');
            $table->dropColumn('start_time_vt_sg');
        });
    }
};
