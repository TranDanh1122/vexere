<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('table_options', function (Blueprint $table) {
            $table->id();
            $table->string('table_type')->index();
            $table->bigInteger('table_id')->index();
            $table->json('value');
            $table->timestamps();
            $table->index(['table_type', 'table_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('table_options');
    }
}
