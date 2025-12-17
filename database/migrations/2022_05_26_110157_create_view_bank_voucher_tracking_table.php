<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateViewBankVoucherTrackingTable extends Migration
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
        CREATE OR REPLACE VIEW view_bank_voucher_tracking AS
        select 
            bpv.req_recid,
            t.req_date,
            t.created_at as requested_date,
            (select a.created_at  from auditlog a 
            join reviewapprove r on r.req_recid = a.req_recid and a.doer_email = r.review 
            where a.req_recid = t.req_recid 
            order by a.created_at desc 
            limit 1) as reviewed_date,
            (select a.created_at  from auditlog a 
            join reviewapprove r on r.req_recid = a.req_recid and a.doer_email = r.approve 
            where a.req_recid = t.req_recid 
            order by a.created_at desc 
            limit 1) as approved_date,
            t.req_name as requester,
            (select u.fullname  from auditlog a 
            join reviewapprove r on r.req_recid = a.req_recid and a.doer_email = r.review 
            join users u on r.review = u.email 
            where a.req_recid = t.req_recid 
            order by a.created_at desc 
            limit 1) as reviewer,
            (select u.fullname  from auditlog a 
            join reviewapprove r on r.req_recid = a.req_recid and a.doer_email = r.approve 
            join users u on r.approve = u.email 
            where a.req_recid = t.req_recid 
            order by a.created_at desc 
            limit 1) as approver,
            (select a.doer_name from tasklist
            join auditlog a on a.req_recid = tasklist.req_recid 
            where tasklist.req_recid = t.req_recid
            and tasklist.req_status = '005'
            and a.activity_code = 'A002'
            order by a.id desc 
            limit 1) as paid_by,
            (select a.created_at from tasklist
            join auditlog a on a.req_recid = tasklist.req_recid 
            where tasklist.req_recid = t.req_recid
            and tasklist.req_status = '005'
            and a.activity_code = 'A002'
            order by a.id desc 
            limit 1) as paid_date,
            r.record_status_description,
            'USD' as ccy,
            bpv.total_for_approval_usd as total_amount,
            bpv.exported_at
            from tasklist t 
            join bank_vouchers bpv on bpv.req_recid = t.req_recid 
            join recordstatus r on r.record_status_id = t.req_status 
            where t.req_from = '10' and t.req_status <> '001'
        SQL;
    }
}
