<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Content extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 40);
            $table->string('description', 255); 
            $table->string('icon_name', 50); 
            $table->integer('order')->default(100); 
            $table->integer('application_id')->unsigned();
            $table->integer('contained_id');
            $table->string('contained_type');
            $table->timestamps();
            $table->foreign('application_id')->references('id')->on('application');
        });
        Schema::create('content_google_map', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('description', 255);
            $table->string('url', 255);
            $table->boolean('cache')->default(false);
            $table->dateTime('cache_expiration')->nullable();
            $table->boolean('send_user_info')->default(false);
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('content_google_map');
        Schema::dropIfExists('content');
    }
}
