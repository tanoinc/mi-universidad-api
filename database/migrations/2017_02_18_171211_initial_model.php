<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InitialModel extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('privilege', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->unique();
            $table->string('description', 255);
            $table->string('controller_action', 255)->nullable();
            $table->timestamps();
        });
        Schema::create('application_privilege', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('application_id')->unsigned();
            $table->integer('privilege_id')->unsigned();
            $table->integer('version')->default(1);
            $table->timestamps();
            $table->foreign('application_id')->references('id')->on('application');
            $table->foreign('privilege_id')->references('id')->on('privilege');
        });
        Schema::create('user', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username', 255)->unique();
            $table->string('password', 255);
            $table->string('name', 255);
            $table->string('surname', 255);
            $table->string('email', 255)->unique();
            $table->string('hash_id', 100)->unique();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('user_application', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('application_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->string('external_id', 100);
            $table->integer('granted_privilege_version');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('application_id')->references('id')->on('application');
            $table->foreign('user_id')->references('id')->on('user');
        });
        
        Schema::create('context', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('application_id')->unsigned();            
            $table->string('name', 150);
            $table->string('description', 255);
            $table->timestamps();
            $table->foreign('application_id')->references('id')->on('application');
            $table->unique(array('application_id','name'));
        });        
        Schema::create('context_user_subscription', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('context_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->timestamps();
            $table->foreign('context_id')->references('id')->on('context');
            $table->foreign('user_id')->references('id')->on('user');
            $table->unique(array('context_id','user_id'));
        });
        Schema::create('newsfeed', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 150);
            $table->text('content');
            $table->boolean('send_notification');
            $table->integer('application_id')->unsigned();
            $table->boolean('global')->default(true);
            $table->integer('context_id')->unsigned()->nullable();  
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('application_id')->references('id')->on('application');
            $table->foreign('context_id')->references('id')->on('context');
        });
        Schema::create('newsfeed_user', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('newsfeed_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->timestamps();
            $table->foreign('newsfeed_id')->references('id')->on('newsfeed');
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
        Schema::dropIfExists('newsfeed_application');
        Schema::dropIfExists('newsfeed_user');
        Schema::dropIfExists('newsfeed');
        Schema::dropIfExists('context_user_subscription');
        Schema::dropIfExists('context');
        Schema::dropIfExists('user_application');
        Schema::dropIfExists('user');
        Schema::dropIfExists('application_privilege');
        Schema::dropIfExists('privilege');
    }

}
