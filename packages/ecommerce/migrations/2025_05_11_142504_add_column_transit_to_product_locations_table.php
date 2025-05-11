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
            $table->boolean('transit')->default(0)->after('status')->comment('Trạng thái transit');
        });
        Schema::table('order_details', function (Blueprint $table) {
            $table->foreignId('return_location_product_id')->after('location_product_id')->nullable();
            $table->text('location_pickup_detail')->after('location_product_id')->nullable();
            $table->text('location_return_detail')->after('return_location_product_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_locations', function (Blueprint $table) {
            $table->dropColumn('transit');
        });
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn('return_location_product_id');
            $table->dropColumn('location_pickup_detail');
            $table->dropColumn('location_return_detail');
        });
    }
};
