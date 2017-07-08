<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PrivilegeLevel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('privilege', function (Blueprint $table) {
            $table->enum('level', ['user', 'application'])->default('user');
        });
        Schema::table('application', function (Blueprint $table) {
            $table->integer('privilege_version')->default(1)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('privilege', function (Blueprint $table) {
            $table->dropColumn('level');
        });
        Schema::table('application', function (Blueprint $table) {
            $table->integer('privilege_version')->default(1)->change();
        });           
    }
}
