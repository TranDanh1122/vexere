<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPrimaryToTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('seos', function (Blueprint $table) {
            $table->primary(['type', 'type_id']);
        });
        Schema::table('language_metas', function (Blueprint $table) {
            $table->primary(['lang_table', 'lang_table_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('seos', function (Blueprint $table) {
            $table->dropPrimary(['type', 'type_id']);
        });
        Schema::table('language_metas', function (Blueprint $table) {
            $table->dropPrimary(['lang_table', 'lang_table_id']);
        });
    }
}
