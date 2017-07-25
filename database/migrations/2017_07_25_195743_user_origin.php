<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserOrigin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user', function (Blueprint $table) {
            $table->enum('origin', ['mobile', 'facebook'])->default('mobile');
            $table->dropUnique('user_username_unique');
            $table->dropUnique('user_email_unique');
            $table->unique(array('origin','username'));
            $table->unique(array('origin','email'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user', function (Blueprint $table) {
            $table->dropColumn('origin');
        });
    }
}
