<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateViewUniquePaymentBodyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement($this->createView());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement($this->dropView());
    }

    private function createView()
    {
        return <<<SQL
        CREATE OR REPLACE VIEW view_unique_payment_body AS
            select 
            `paymentbody`.`req_recid` AS `req_recid`,sum(`paymentbody`.`total`) AS `total`,`paymentbody`.`budget_code` AS `budget_code` 
            from `paymentbody` 
            group by `paymentbody`.`req_recid`,`paymentbody`.`budget_code`
        SQL;
    }

    private function dropView()
    {
        return <<<SQL

        DROP VIEW IF EXISTS `view_unique_payment_body`;
        SQL;
    }
}
