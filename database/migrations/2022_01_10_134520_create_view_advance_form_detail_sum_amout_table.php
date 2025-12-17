<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateViewAdvanceFormDetailSumAmoutTable extends Migration
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
        CREATE OR REPLACE VIEW view_advance_form_detail_sum_amout AS
        select 
        `advance_form_details`.`req_recid` AS `req_recid`,
        sum(`advance_form_details`.`total_amount_usd`) AS `total`,
        `advance_form_details`.`budget_code` AS `budget_code`,
        max(`advance_form_details`.`used_by_request`) AS `used_by_request` 
        from `advance_form_details` group by `advance_form_details`.`req_recid`,`advance_form_details`.`budget_code`
        SQL;
    }
    private function dropView()
    {
        return <<<SQL

        DROP VIEW IF EXISTS `view_advance_form_detail_sum_amout`;
        SQL;
    }
}
