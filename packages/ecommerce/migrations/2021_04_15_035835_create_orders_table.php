<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            // ID khách hàng
            $table->integer('customer_id');
            // Mã đơn hàng
            $table->string('code')->nullable();
            // Tổng giá của đơn
            $table->integer('total_price')->default(0);
            // Trạng thái đơn (1 Đơn hàng mới | 2 Đã tiếp nhận | 3 Chờ xử lý | 4 Huỷ | 5 Thành công)
            $table->tinyInteger('status')->default(1);
            // Ghi chú tại đơn
            $table->text('note')->nullable();
            // Ngày tạo, cập nhật
            $table->timestamps();
            $table->index(['customer_id']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
