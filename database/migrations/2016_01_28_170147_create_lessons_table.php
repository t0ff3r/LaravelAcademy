<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lessons', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('title', 100);
            $table->text('description');
            $table->dateTime('start');
            $table->dateTime('end');

            $table->integer('teacher_id')->unsigned();
            $table->foreign('teacher_id')
                  ->references('id')
                  ->on('teachers')
                  ->onDelete('cascade');

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
        Schema::drop('lessons');
    }
}
