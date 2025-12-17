<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateViewJournalVoucherTrackingTable extends Migration
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
        Schema::dropIfExists('view_journal_voucher_tracking');
    }
    

    private function createView()
    {
        return <<<SQL
        CREATE OR REPLACE VIEW view_journal_voucher_tracking AS
        select 
            bpv.req_recid,
            bpv.ref_no,
            IF(SUBSTRING_INDEX(bpv.ref_no , '-', 1) = 'RP','PAYMENT',
            IF(SUBSTRING_INDEX(bpv.ref_no , '-', 1) = 'PR','PROCUREMENT',
            IF(SUBSTRING_INDEX(bpv.ref_no , '-', 1) = 'ADV','ADVANCE',
            IF(SUBSTRING_INDEX(bpv.ref_no , '-', 1) = 'ADC','CLEAR ADVANCE',
            IF(SUBSTRING_INDEX(bpv.ref_no , '-', 1) = 'BP','BANK PAYMENT VOUCHER',
            IF(SUBSTRING_INDEX(bpv.ref_no , '-', 1) = 'JV','JOURNAL VOUCHER','')))))) as ref_type,
            bpv.payment_method_code,
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
            bpv.account_name,
            bpv.account_number,
            bpv.exported_at
            from tasklist t 
            join journal_vouchers bpv on bpv.req_recid = t.req_recid 
            join recordstatus r on r.record_status_id = t.req_status 
            where t.req_from = '6' and t.req_status <> '001'
        SQL;
    }
}