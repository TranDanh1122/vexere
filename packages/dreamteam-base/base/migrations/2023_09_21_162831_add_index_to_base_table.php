<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexToBaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('language_metas', function (Blueprint $table) {
            $table->index('lang_table');
            $table->index('lang_table_id');
        });
        Schema::table('settings', function (Blueprint $table) {
            $table->index('key');
            $table->index('locale');
        });
        Schema::table('system_logs', function (Blueprint $table) {
            $table->index('type');
            $table->index('type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('language_metas', function (Blueprint $table) {
            $table->dropIndex('language_metas_lang_table_index');
            $table->dropIndex('language_metas_lang_table_id_index');
        });
        Schema::table('settings', function (Blueprint $table) {
            $table->dropIndex('settings_key_index');
            $table->dropIndex('settings_locale_index');
        });
        Schema::table('system_logs', function (Blueprint $table) {
            $table->dropIndex('system_logs_type_index');
            $table->dropIndex('system_logs_type_id_index');
        });
    }
}
