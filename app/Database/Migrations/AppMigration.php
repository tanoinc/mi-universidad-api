<?php

namespace App\Database\Migrations;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Description of AppMigration
 *
 * @author lucianoc
 */
abstract class AppMigration extends Migration
{
    protected function addClientVersions($versions, $truncate = true)
    {
        if ($truncate) {
            DB::table('client_compatibility')->truncate();
        }
        
        if (empty($versions)) {
            return ;
        }
        
        $versionsToInsert = [];
        foreach ($versions as $version) {
            $versionsToInsert[] = [
                'client_version' => $version,
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
            ];
        }
        
        DB::table('client_compatibility')->insert($versionsToInsert);
    }
}
