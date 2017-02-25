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
        Schema::table('application', function(Blueprint $table) {
            $table->string('application_hash_id', 50)->unique()->after('id');
            $table->integer('privilege_version')->default(1);
        });
        Schema::create('privilege', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->unique();
            $table->string('description', 255);
            $table->boolean('granted');
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
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('application_id')->references('id')->on('application');
            $table->foreign('user_id')->references('id')->on('user');
        });
        Schema::create('newsfeed', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 150);
            $table->text('content');
            $table->boolean('send_notification');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('newsfeed_user', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('newsfeed_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->timestamps();
            $table->foreign('newsfeed_id')->references('id')->on('newsfeed');
            $table->foreign('user_id')->references('id')->on('user');
        });
        Schema::create('newsfeed_application', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('newsfeed_id')->unsigned();
            $table->integer('application_id')->unsigned();
            $table->timestamps();
            $table->foreign('newsfeed_id')->references('id')->on('newsfeed');
            $table->foreign('application_id')->references('id')->on('application');
        });        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('application', function (Blueprint $table) {
            $table->dropColumn('application_hash_id');
            $table->dropColumn('privilege_version');
        });
        Schema::dropIfExists('newsfeed_application');
        Schema::dropIfExists('newsfeed_user');
        Schema::dropIfExists('newsfeed');
        Schema::dropIfExists('user_application');
        Schema::dropIfExists('user');
        Schema::dropIfExists('application_privilege');
        Schema::dropIfExists('privilege');
    }

}
