<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserConfirmationAndRecovery extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user', function (Blueprint $table) {
            $table->boolean('confirmed')->default(true);
            $table->dateTime('last_password_recovery')->nullable();
            $table->smallInteger('attempts_login')->default(0);
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
            $table->dropColumn('confirmed');
            $table->dropColumn('last_password_recovery');
            $table->dropColumn('attempts_login');
        });
    }
}
