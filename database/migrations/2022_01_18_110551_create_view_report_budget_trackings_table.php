<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateViewReportBudgetTrackingsTable extends Migration
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

    private function createView()
    {
        return <<<SQL
        CREATE OR REPLACE VIEW view_report_budget_trackings AS
        select 
            b.budget_code,
            b.budget_item,
            b.budget_owner,
            b.total,
            round(b.total - b.payment_remaining,2) as ytd_payment,
            b.payment_remaining as remaining_payment,
            round(b.total - b.temp, 2) as ytd_procurement,
            b.temp as remaining_procurement,
            b.`year`,
            b.created_at,
            u.fullname 
        from budgetdetail b 
        left join users u on u.email = b.budget_owner 
        SQL;
    }
}
