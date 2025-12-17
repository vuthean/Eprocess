<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class TrgClearAdvanceForm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
        CREATE DEFINER=`admuser`@`%` TRIGGER `trg_clear_advance_form` BEFORE INSERT ON `clear_advance_forms` FOR EACH ROW BEGIN
        INSERT INTO clear_advance_form_sequences VALUES (NULL);
        SET NEW.req_recid = CONCAT("ADC-",DATE_FORMAT(NOW(),"%Y"),"-",DATE_FORMAT(NOW(),"%m"),"-",LPAD(LAST_INSERT_ID(),5, "0"));
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
        DB::unprepared('DROP TRIGGER `trg_clear_advance_form`');
    }
}
