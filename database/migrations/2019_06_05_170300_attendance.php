<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Attendance extends \App\Database\Migrations\AppMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100);
            $table->string('description', 255)->nullable();
            $table->string('location', 255)->nullable();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->boolean('send_notification')->default(false);
            $table->integer('application_id')->unsigned();
            $table->boolean('global')->default(true);
            $table->integer('context_id')->unsigned()->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('application_id')->references('id')->on('application');
            $table->foreign('context_id')->references('id')->on('context');            
        });
        
        Schema::create('attendance_control', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('attendance_id')->unsigned();
            $table->string('type', 100);
            $table->text('parameters');
            
            $table->timestamps();
            
            $table->foreign('attendance_id')->references('id')->on('attendance');
        });

        Schema::create('attendance_user', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('attendance_id')->unsigned();
            $table->integer('user_id')->unsigned()->nullable();
            $table->char('status')->nullable();

            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('user');
            $table->foreign('attendance_id')->references('id')->on('attendance');
        });
        
        $this->addClientVersions(['1.0.%','1.1.%','1.2.%']);
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->addClientVersions(['1.0.%','1.1.%']);
        Schema::dropIfExists('attendance_user');
        Schema::dropIfExists('attendance_control');
        Schema::dropIfExists('attendance');
    }
}
