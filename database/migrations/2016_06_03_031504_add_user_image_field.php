<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserImageField extends Migration
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
            $table->string('profile_img', 50);
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
            $table->dropColumn('profile_img');
        });
    }
}
