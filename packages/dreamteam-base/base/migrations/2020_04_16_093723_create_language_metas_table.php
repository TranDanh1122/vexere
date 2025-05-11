<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLanguageMetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('language_metas', function (Blueprint $table) {
            $table->string('lang_table');
            $table->integer('lang_table_id');
            $table->string('lang_locale', 20);
            $table->string('lang_code');
            // Đánh index cho tên bảng, ngôn ngữ, và và chuỗi ngẫu nhiên
            $table->index([ 'lang_table', 'lang_locale', 'lang_code' ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('language_metas');
    }
}
