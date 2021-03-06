<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApplicationsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('application', function (Blueprint $table) {
            $table->increments('id');
            $table->string('api_key', 100)->unique();
            $table->string('name', 50)->unique();
            $table->string('description', 255);
            $table->string('api_secret', 100);
            $table->integer('privilege_version')->default(1);
            $table->boolean('auth_required')->default(false);
            $table->string('auth_callback_url', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('application');
    }

}
