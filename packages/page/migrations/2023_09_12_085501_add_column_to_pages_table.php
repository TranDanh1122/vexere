<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->tinyInteger('hide_title')->after('detail')->default(0);
            $table->tinyInteger('hide_sidebar')->after('hide_title')->default(0);
            $table->tinyInteger('hide_breadcrumb')->after('hide_sidebar')->default(0);
            $table->tinyInteger('hide_toc')->after('hide_breadcrumb')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('hide_sidebar');
            $table->dropColumn('hide_breadcrumb');
            $table->dropColumn('hide_title');
            $table->dropColumn('hide_toc');
        });
    }
}
