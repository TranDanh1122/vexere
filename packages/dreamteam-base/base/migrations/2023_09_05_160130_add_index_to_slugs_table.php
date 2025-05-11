<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexToSlugsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('slugs', function (Blueprint $table) {
            $table->index('table');
            $table->index('table_id');
            $table->index('slug');
            $table->index(['table', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('slugs', function (Blueprint $table) {
            $table->dropIndex('slugs_table_index');
            $table->dropIndex('slugs_table_id_index');
            $table->dropIndex('slugs_slug_index');
            $table->dropIndex('slugs_table_slug_index');
        });
    }
}
