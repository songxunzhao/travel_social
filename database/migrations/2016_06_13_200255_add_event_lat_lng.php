<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEventLatLng extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if(!Schema::hasTable('events')) {
            return;
        }
        Schema::table('events', function($table) {
            $table->decimal('lat', 10, 4);
            $table->decimal('lng', 10, 4);
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
        if(!Schema::hasTable('events')) {
            return;
        }
        Schema::table('events', function($table) {
            $table->dropColumn('lat');
            $table->dropColumn('lng');
        });
    }
}
