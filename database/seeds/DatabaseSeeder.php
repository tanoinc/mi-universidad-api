<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call('PrivilegeTableSeeder');
        $this->call('ApplicationTableSeeder');
        $this->call('UserTableSeeder');
    }

}
