<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('events', function(Blueprint $table){
            $table->increments('id');
            $table->string('img', 200)->nullable();
            $table->string('title', 200);
            $table->dateTime('from');
            $table->dateTime('to')->nullable();
            $table->string('description', 1000)->nullable();
            $table->string('venue')->nullable();
            $table->integer('creator_id')->unsigned();
            $table->foreign('creator_id')
                ->references('id')
                ->on('users')
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
        //
        Schema::drop('events');
    }
}
