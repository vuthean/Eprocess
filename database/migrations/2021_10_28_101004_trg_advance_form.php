<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TrgAdvanceForm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
        CREATE DEFINER=`admuser`@`%` TRIGGER `trg_advance_form` BEFORE INSERT ON `advance_forms` FOR EACH ROW BEGIN
        INSERT INTO advance_form_sequence VALUES (NULL);
        SET NEW.req_recid = CONCAT("ADV-",DATE_FORMAT(NOW(),"%Y"),"-",DATE_FORMAT(NOW(),"%m"),"-",LPAD(LAST_INSERT_ID(),5, "0"));
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
        DB::unprepared('DROP TRIGGER `trg_advance_form`');
    }
}
