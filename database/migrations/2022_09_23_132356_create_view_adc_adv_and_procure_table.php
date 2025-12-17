<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateViewAdcAdvAndProcureTable extends Migration
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
    private function createView(){
        return <<<SQL
        CREATE OR REPLACE VIEW view_adc_adv_and_procure_tracking AS
        SELECT 
        cd.req_recid AS 'adc_ref',
        r.subject AS 'subject',
        c.created_at AS 'req_date',

        (select `a`.`created_at` from (`auditlog` `a` 
        join `tasklist` `task` on((`task`.`req_recid` = `a`.`req_recid`)))
        where ((`a`.`req_recid` = `t`.`req_recid`) and (`task`.`req_status` = '005')) 
        order by `a`.`id` desc limit 1) AS `approved_date`,
        `t`.`req_name` AS `requester`,
        (select `u`.`fullname` from ((`auditlog` `a` 
        join `reviewapprove` `r` on(((`r`.`req_recid` = `a`.`req_recid`) 
        and (`r`.`approve` = `a`.`doer_email`)))) 
        join `users` `u` on((`u`.`email` = `a`.`doer_email`))) 
        where (`a`.`req_recid` = `c`.`req_recid`) order by `a`.`id` desc limit 1) AS `approver`,
        t.req_branch,
        cd.department_code,
        cd.budget_code,
        cd.alternative_budget_code,
        cd.description,
        cd.quantity,
        cd.unit,
        cd.total_amount_usd,

        (select `a`.`created_at` from (`auditlog` `a` join `tasklist` `t` on((`t`.`req_recid` = `a`.`req_recid`))) 
        where ((`a`.`activity_form` = 4) and (`t`.`req_status` = '005') and (`t`.`req_recid` = `c`.`req_recid`)) 
        order by `a`.`created_at` desc limit 1) AS `paid_date`,

        (select `a`.`doer_name` from (`auditlog` `a` join `tasklist` `t` on((`t`.`req_recid` = `a`.`req_recid`))) where ((`a`.`activity_form` = 4) 
        and (`t`.`req_status` = '005') and (`t`.`req_recid` = `c`.`req_recid`)) order by `a`.`created_at` desc limit 1) 
        AS `paid_by`,
        `s`.`record_status_description` AS `record_status_description`,

        'USD' as 'ccy',
        c.company_name as 'supplier_name',
        c.bank_name as 'payment_method',
        c.advance_ref_no,
        (SELECT ts.created_at FROM tasklist ts WHERE ts.req_recid = c.advance_ref_no) AS 'adv_req_date',
        (SELECT t1.req_name FROM tasklist t1 WHERE t1.req_recid = c.advance_ref_no) AS 'adv_requester',

        (select `a`.`created_at` from (`auditlog` `a` join `tasklist` `t` on((`t`.`req_recid` = `a`.`req_recid`))) 
        where ((`a`.`activity_form` = 3) and (`t`.`req_status` = '005') and (`t`.`req_recid` = `c`.`advance_ref_no`)) 
        order by `a`.`created_at` desc limit 1) AS `adv_paid_date`,

        (select `a`.`doer_name` from (`auditlog` `a` join `tasklist` `t` on((`t`.`req_recid` = `a`.`req_recid`))) where ((`a`.`activity_form` = 3) 
        and (`t`.`req_status` = '005') and (`t`.`req_recid` = `c`.`advance_ref_no`)) order by `a`.`created_at` desc limit 1) 
        AS `adv_paid_by`,

        arp.ref AS 'procurement_req',

        (SELECT GROUP_CONCAT(CONCAT(ts.created_at ) SEPARATOR ',')  FROM tasklist ts 
        WHERE arp.ref LIKE CONCAT('%',ts.req_recid,'%')) 
        AS 'procurement_req_date',

        (SELECT GROUP_CONCAT(CONCAT(ts.req_name ) SEPARATOR ',')  FROM tasklist ts 
        WHERE arp.ref LIKE CONCAT('%',ts.req_recid,'%')) 
        AS 'procurement_requester',


        (SELECT GROUP_CONCAT(CONCAT(ts.updated_at ) SEPARATOR ',')  FROM tasklist ts
        WHERE `ts`.`req_status` = '005' AND arp.ref LIKE CONCAT('%',ts.req_recid,'%')) 
        AS 'procurement_paid_date',

        (SELECT GROUP_CONCAT(CONCAT(u.fullname ) SEPARATOR ',')  FROM tasklist ts
        JOIN reviewapprove re ON re.req_recid = ts.req_recid
        JOIN users u ON u.email = re.final
        WHERE `ts`.`req_status` = '005' AND arp.ref LIKE CONCAT('%',re.req_recid,'%')) 
        AS 'procurement_paid_by',

        (select tl.req_branch from tasklist tl where tl.req_recid = arp.ref) as `req_pr_branch`

        FROM clear_advance_form_details cd

        JOIN tasklist t ON cd.req_recid = t.req_recid
        JOIN clear_advance_forms c ON c.req_recid = cd.req_recid
        JOIN requester r ON r.req_recid = cd.req_recid
        JOIN recordstatus s ON  s.record_status_id = t.req_status
        LEFT JOIN view_select_all_reference_of_procurement_table arp on c.advance_ref_no = arp.advance_request
        SQL;
    }
    private function dropView(){ 
        return <<<SQL

        DROP VIEW IF EXISTS `view_adc_adv_and_procure_tracking`;
        SQL;
    }
}
