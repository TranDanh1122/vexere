<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 191); // string: tên đăng nhập
            $table->string('slug');
            $table->string('email', 191)->nullable(); // (string) Email đăng nhập
            $table->string('password', 191)->nullable();
            $table->string('position')->nullable();
            $table->string('display_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->date('birthday')->nullable();
            $table->text('avatar')->nullable();
            $table->longtext('infomation')->nullable();
            $table->longtext('social')->nullable();
            $table->longtext('capabilities')->nullable();
            $table->string('remember_token', 191)->nullable();
            $table->longtext('google2fa_secret')->nullable();
            $table->tinyInteger('status')->nullable();
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
        Schema::dropIfExists('admin_users');
    }
}
