<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnSeoToPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->integer('seo_point')->after('detail')->default(0);
            $table->string('primary_keyword')->after('seo_point')->nullable();
            $table->text('secondary_keyword')->after('primary_keyword')->nullable();
            $table->string('google_index')->after('secondary_keyword')->default('na');
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
            $table->dropColumn('seo_point');
            $table->dropColumn('primary_keyword');
            $table->dropColumn('secondary_keyword');
            $table->dropColumn('google_index');
        });
    }
}
