<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Calendar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calendar_event', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('event_name', 150);
            $table->dateTime('event_date');
            $table->time('event_duration')->nullable();
            $table->boolean('send_notification');
            $table->integer('application_id')->unsigned();
            $table->boolean('global')->default(true);
            $table->integer('context_id')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('application_id')->references('id')->on('application');
            $table->foreign('context_id')->references('id')->on('context');
        });
        Schema::create('calendar_event_user', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('calendar_event_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->timestamps();
            $table->foreign('calendar_event_id')->references('id')->on('calendar_event');
            $table->foreign('user_id')->references('id')->on('user');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('calendar_event_user');
        Schema::dropIfExists('calendar_event');
    }
}
