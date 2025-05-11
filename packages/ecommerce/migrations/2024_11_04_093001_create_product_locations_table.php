<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use DreamTeam\Base\Enums\BaseStatusEnum;

class CreateProductLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Điểm đón / trả. Mỗi điểm sẽ thuộc vào SG hoặc VT, có thể có điểm đón cha
        Schema::create('product_locations', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // Enum PICKUP | DROPOFF
            $table->unsignedInteger('product_id')->default(0);
            $table->unsignedInteger('location_id')->default(0);
            $table->time('time')->nullable();
            $table->integer('order')->default(99999);
            // Trạng thái (-1 Xóa | 0 Không hoạt động | 1 Hoạt động)
            $table->tinyInteger('status')->default(BaseStatusEnum::ACTIVE);
            // Ngày đăng/cập nhật
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_locations');
    }
}