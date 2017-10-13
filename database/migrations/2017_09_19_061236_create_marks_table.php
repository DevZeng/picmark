<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('marks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pic_id');
            $table->unsignedInteger('teacher');
            $table->tinyInteger('score');
            $table->tinyInteger('completion');
            $table->tinyInteger('concept');
            $table->tinyInteger('expression');
            $table->tinyInteger('color');
            $table->tinyInteger('speed');
            $table->string('pic_url',500);
            $table->tinyInteger('issue');
            $table->tinyInteger('redo')->default(1);
            $table->text('detail');
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
        Schema::dropIfExists('marks');
    }
}
