<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use DreamTeam\Base\Enums\BaseStatusEnum;

class CreateMediasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medias', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id');
            $table->string('name'); // Tên file
            $table->bigInteger('size')->nullable(); // Kích cỡ
            $table->string('type')->nullable(); // Phân Loại file: image | file
            $table->string('title')->nullable(); // Tiêu đề
            $table->string('caption')->nullable(); // Mô tả
            $table->string('extention')->nullable(); // Đuôi file
            $table->text('url')->nullable(); // url file
            $table->bigInteger('parent_id')->default(0);
            $table->tinyInteger('status')->default(BaseStatusEnum::ACTIVE); // Trạng thái
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
        Schema::dropIfExists('medias');
    }
}
