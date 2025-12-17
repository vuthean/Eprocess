<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateViewClearAdvanceFormDetailSumAmout extends Migration
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
        CREATE OR REPLACE VIEW view_clear_advance_form_detail_sum_amout AS
        select 
        `clear_advance_form_details`.`req_recid` AS `req_recid`,
        sum(`clear_advance_form_details`.`total_amount_usd`) AS `total`,`clear_advance_form_details`.`budget_code` AS `budget_code` 
        from `clear_advance_form_details` 
        group by `clear_advance_form_details`.`req_recid`,`clear_advance_form_details`.`budget_code`
        SQL;
    }
    private function dropView()
    {
        return <<<SQL

        DROP VIEW IF EXISTS `view_clear_advance_form_detail_sum_amout`;
        SQL;
    }
}
