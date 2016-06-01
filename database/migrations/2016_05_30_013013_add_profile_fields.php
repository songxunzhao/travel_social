<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProfileFields extends Migration
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
            $table->integer('age');
            $table->string('location', 256);
            $table->decimal('lat', 10, 4);
            $table->decimal('lng', 10, 4);
            $table->string('job_name', 50);
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
            $table->dropColumn('age');
            $table->dropColumn('location');
            $table->dropColumn('lat');
            $table->dropColumn('lng');
            $table->dropColumn('job_name');
        });
    }
}
