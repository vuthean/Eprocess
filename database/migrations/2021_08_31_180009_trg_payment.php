<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TrgPayment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
        CREATE DEFINER=`admuser`@`%` TRIGGER `trg_payment` BEFORE INSERT ON `payment` FOR EACH ROW BEGIN
          INSERT INTO payment_sequence VALUES (NULL);
          SET NEW.req_recid = CONCAT("RP-",DATE_FORMAT(NOW(),"%Y"),"-",DATE_FORMAT(NOW(),"%m"),"-",LPAD(LAST_INSERT_ID(),5, "0"));
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
        DB::unprepared('DROP TRIGGER `trg_payment`');
    }
}
