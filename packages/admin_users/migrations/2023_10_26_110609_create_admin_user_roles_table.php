<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminUserRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_user_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('team', 255)->nullable();
            $table->text('note')->nullable();
            $table->json('permisions');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
        Schema::table('admin_users', function (Blueprint $table) {
            $table->foreignId('admin_user_role_id')->after('id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_user_roles');
        Schema::table('admin_users', function (Blueprint $table) {
            $table->dropColumn('admin_user_role_id');
        });
    }
}
