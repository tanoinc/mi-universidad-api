<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Database\Migrations\AppMigration;

class ApiVersionControl extends AppMigration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_compatibility', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('client_version', 50);
            $table->timestamps();
            $table->unique('client_version');
        });
        $this->addClientVersions(['1.0.%','0.1.%']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_compatibility');
    }

}
