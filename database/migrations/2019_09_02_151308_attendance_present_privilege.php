<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AttendancePresentPrivilege extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        App\Privilege::create([
            'name' => 'attendance:get_present',
            'description' => 'Retrieve the list of user IDs whose status  to the attendance session is present.',
            'controller_action' => 'App\\Http\\Controllers\\AttendanceController@getPresent',
            'level' => 'application',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        App\Privilege::removeFromModel('attendance:get_present');
    }
}
