<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CalendarEventExtraFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('calendar_event', function (Blueprint $table) {
            $table->string('event_location', 150)->nullable();
            $table->string('event_description', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('calendar_event', function (Blueprint $table) {
            $table->dropColumn('location');
            $table->dropColumn('event_description');
        });
    }
}
