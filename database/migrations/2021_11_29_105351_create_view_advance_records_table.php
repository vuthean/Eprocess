<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateViewAdvanceRecordsTable extends Migration
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
        CREATE OR REPLACE VIEW view_advance_records AS 
            select 
                t.created_at as request_date,
                (select a.created_at  from auditlog a where a.req_recid = t.req_recid order by id desc limit 1) as approval_date,
                t.req_recid,
                t.req_name as requester,
                t.req_branch as department,
                t.req_from,
                af.currency,
                af.total_amount_khr as request_amount_khr,
                af.total_amount_usd as request_amount_usd,
                (select a.doer_name from auditlog a where a.activity_code in ('A010','A011','A012') and a.req_recid = t.req_recid  order by a.id desc limit 1) as paid_by,
                (select 
                if(a.activity_code = 'A010','YES',if(a.activity_code = 'A011','NO','CANCEL')) as activityCode
                from auditlog a 
                where a.activity_code in ('A010','A011','A012') 
                and a.req_recid = t.req_recid
                order by a.id 
                desc limit 1 ) COLLATE utf8mb4_unicode_ci  as paid,
                (select a.activity_datetime from auditlog a where a.activity_code in ('A010','A011','A012') and a.req_recid = t.req_recid  order by a.id desc limit 1) as paid_date,
                if((select count(afd.req_recid) from advance_form_details afd where  afd.req_recid = t.req_recid and is_cleared is null) = 0 ,'YES','NO') as cleared,
                f.description as form_url
            from tasklist t 
            join advance_forms af on af.req_recid = t.req_recid 
            join formname f on f.id = t.req_from 
            where t.req_from = (select id from formname f2 where f2.formname = 'AdvanceFormRequest')
            and t.req_status = (select r.record_status_id from recordstatus r where r.record_status_description = 'Approved')
            union all 
            select 
                t.created_at as request_date,
                (select a.created_at  from auditlog a where a.req_recid = t.req_recid order by id desc limit 1) as approval_date,
                t.req_recid,
                t.req_name as requester,
                t.req_branch as department,
                t.req_from,
                caf.currency,
                caf.total_amount_khr as request_amount_khr,
                caf.total_amount_usd as request_amount_usd,
                (select a.doer_name from auditlog a where a.activity_code in ('A010','A011','A012') and a.req_recid = t.req_recid  order by a.id desc limit 1) as paid_by,
                (select 
                if(a.activity_code = 'A010','YES',if(a.activity_code = 'A011','NO','CANCEL')) as activityCode
                from auditlog a 
                where a.activity_code in ('A010','A011','A012') 
                and a.req_recid = t.req_recid
                order by a.id 
                desc limit 1 ) as paid,
                (select a.activity_datetime from auditlog a where a.activity_code in ('A010','A011','A012') and a.req_recid = t.req_recid  order by a.id desc limit 1) as paid_date,
                'NO' as cleared,
                f.description as form_url
            from tasklist t 
            join clear_advance_forms caf on caf.req_recid = t.req_recid 
            join formname f on f.id = t.req_from 
            where t.req_from = (select id from formname f2 where f2.formname = 'ClearAdvanceFormRequest')
            and t.req_status = (select r.record_status_id from recordstatus r where r.record_status_description = 'Approved')
        SQL;
    }

    private function dropView()
    {
        return <<<SQL

        DROP VIEW IF EXISTS `view_advance_records`;
        SQL;
    }
}
