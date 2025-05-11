<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            // ID đơn hàng
            $table->integer('order_id');
            // ID sản phẩm
            $table->integer('product_id');
            $table->unsignedInteger('location_product_id');
            // giá bán
            $table->integer('price')->nullable();
            // Số lượng
            $table->integer('quantity')->default(1);

            $table->text('product_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_details');
    }
}
