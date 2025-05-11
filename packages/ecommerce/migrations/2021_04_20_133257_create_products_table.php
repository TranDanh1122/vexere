<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use DreamTeam\Base\Enums\BaseStatusEnum;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            // ID Sản phẩm
            $table->id();
            // ID thương hiệu
            $table->integer('brand_id')->nullable();
            // Tên sản phẩm
            $table->string('name', 255);
            // Ảnh sản phẩm
            $table->text('image')->nullable();
            $table->text('slide')->nullable();
            // Giá bán
            $table->bigInteger('price')->nullable();
            // Giá thị trường
            $table->bigInteger('price_old')->nullable();
            // mô tả dưới giá sp
            $table->longtext('description')->nullable();
            // Nội dung giới thiệu sản phẩm (Editor hoặc cấu hình json)
            $table->longtext('detail')->nullable();

            $table->tinyInteger('status')->default(BaseStatusEnum::ACTIVE);
            // Ngày đăng/cập nhật
            $table->timestamps();
            $table->index(['brand_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
