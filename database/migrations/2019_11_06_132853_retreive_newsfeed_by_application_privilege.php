<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RetreiveNewsfeedByApplicationPrivilege extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        App\Privilege::create([
            'name' => 'newsfeed:retrieve',
            'description' => 'Retrieve the list of news from the application\'s newsfeed.',
            'controller_action' => 'App\\Http\\Controllers\\NewsfeedController@retreieveFromAuthenticatedApplication',
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
        App\Privilege::removeFromModel('newsfeed:retrieve');
    }
}
