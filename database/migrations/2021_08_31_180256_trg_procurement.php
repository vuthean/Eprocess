<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TrgProcurement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
        CREATE DEFINER=`admuser`@`%` TRIGGER `trg_procurement` BEFORE INSERT ON `procurement` FOR EACH ROW BEGIN
        INSERT INTO pr_sequence VALUES (NULL);
        SET NEW.req_recid = CONCAT("PR-",DATE_FORMAT(NOW(),"%Y"),"-",DATE_FORMAT(NOW(),"%m"),"-",LPAD(LAST_INSERT_ID(),5, "0"));
    END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER `trg_procurement`');
    }
}
