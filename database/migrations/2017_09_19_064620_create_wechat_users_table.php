<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWechatUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wechat_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('open_id')->unique()->commit('Wechat Unique ID');
            $table->string('nickname',200)->commit('Wechatnickname');
            $table->tinyInteger('gender')->commit('Gender');
            $table->string('city',100)->commit('City');
            $table->integer('integral')->default(0);
            $table->string('province',100)->commit('Province');
            $table->string('avatarUrl',300)->commit('wechatAvatarUrl');
            $table->integer('birthday')->nullable()->commit('userBirthday');
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
        Schema::dropIfExists('wechat_users');
    }
}
