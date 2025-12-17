<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateViewCashPaymentVoucherReferencesTable extends Migration
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
    private function createView()
    {
        return <<<SQL
        CREATE OR REPLACE VIEW view_cash_payment_voucher_references AS
        select t.req_recid from tasklist t 
        join paymentbody p on p.req_recid = t.req_recid 
        where (`t`.`req_status` = '005' OR `t`.`req_status` = '002' OR `t`.`req_status` = '003')
        and p.used_by_request_cash_payment is null
        group by t.req_recid 
        union 
        select t.req_recid from tasklist t 
        join advance_form_details afd on afd.req_recid = t.req_recid 
        where (`t`.`req_status` = '005' OR `t`.`req_status` = '002' OR `t`.`req_status` = '003')
        and afd.used_by_request_cash_payment is null
        group by t.req_recid
        union 
        select t.req_recid from tasklist t 
        join clear_advance_form_details cafd on cafd.req_recid = t.req_recid 
        where (`t`.`req_status` = '005' OR `t`.`req_status` = '002' OR `t`.`req_status` = '003')
        and cafd.used_by_request_cash_payment is null
        group by t.req_recid 
        SQL;
    }
}
