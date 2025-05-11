<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use DreamTeam\Base\Enums\BaseStatusEnum;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Điểm đón / trả. Mỗi điểm sẽ thuộc vào SG hoặc VT, có thể có điểm đón cha
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('parent_id')->default(0);
            // Tên
            $table->string('name');
            $table->string('from'); // Enum SG |
            $table->text('address')->nullable();
            $table->text('google_map')->nullable();
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
        Schema::dropIfExists('locations');
    }
}
