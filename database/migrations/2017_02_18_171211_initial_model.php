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
        Schema::create('newsfeed', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 150);
            $table->text('content');
            $table->boolean('send_notification');
            $table->integer('application_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('application_id')->references('id')->on('application');
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
        Schema::table('application', function (Blueprint $table) {
            $table->dropColumn('application_hash_id');
        });
        Schema::dropIfExists('newsfeed');
        Schema::dropIfExists('user');
        Schema::dropIfExists('application_privilege');
        Schema::dropIfExists('privilege');
    }

}
