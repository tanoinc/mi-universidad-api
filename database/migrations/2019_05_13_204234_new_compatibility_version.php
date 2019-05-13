<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Database\Migrations\AppMigration;

class NewCompatibilityVersion extends AppMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->addClientVersions(['1.0.%','1.1.%']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	$this->addClientVersions(['1.0.%','0.1.%']);
    }
}
