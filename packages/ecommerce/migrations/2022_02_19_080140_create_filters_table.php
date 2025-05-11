<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFiltersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('filters', function (Blueprint $table) {
            // ID
            $table->id();
            // Tên
            $table->string('name');
            // Sắp xếp
            $table->integer('order')->default(99999);
            // Trạng thái (-1 Xóa | 0 Không hoạt động | 1 Hoạt động)
            $table->tinyInteger('status')->default(1);
            // Ngày đăng/cập nhật
            $table->timestamps();
            $table->index(['id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('filters');
    }
}
