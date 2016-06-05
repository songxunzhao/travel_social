<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserRankField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if(!Schema::hasTable('users')) {
            return;
        }
        Schema::table('users', function($table) {
            $table->integer('score');
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
        if(!Schema::hasTable('users')) {
            return;
        }
        Schema::table('users', function($table) {
            $table->dropColumn('score');
        });
    }
}
