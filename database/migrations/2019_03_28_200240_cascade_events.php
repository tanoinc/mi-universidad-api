<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CascadeEvents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('calendar_event_user', function (Blueprint $table) {
            $table->dropForeign(['calendar_event_id']);
            $table->foreign('calendar_event_id')->references('id')->on('calendar_event')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('calendar_event_user', function (Blueprint $table) {
            $table->dropForeign(['calendar_event_id']);
            $table->foreign('calendar_event_id')->references('id')->on('calendar_event');
        });
    }
}
