<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ViewReportPaymentAndProcurementTrackingRequestsRaw extends Migration
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
        CREATE OR REPLACE VIEW view_report_payment_procurement_tracking_request_raw AS
        select 
            `p`.`req_recid` AS `req_recid`,
            `re`.`subject` AS `subject`,
            `p`.`created_at` AS `req_date`,
            `p`.`created_at` AS `payment_at`,
            `p`.`created_at` AS `created_at`,
            `t`.`req_name` AS `req_name`,
            concat(if((`r`.`approve` is not null),
            coalesce((select `u`.`fullname` from (`reviewapprove` `r1` join `users` `u` on((`r1`.`approve` = `u`.`email`))) 
            where (`r1`.`approve` = `r`.`approve`) limit 1),''),''),if((`r`.`final` is not null),
            coalesce(concat(' | ',(select `u`.`fullname` from (`reviewapprove` `r2` join `users` `u` on((`r2`.`final` = `u`.`email`))) 
            where (`r2`.`final` = `r`.`final`) limit 1)),''),'')) AS `approvers`,
            (select `u`.`department` from `users` `u` 
            where (`u`.`email` = `t`.`req_email`) limit 1) AS `department`,
            `pd`.`br_dep_code` AS `department_code`,
            `pd`.`budget_code` AS `budget_code`,
            (select `paymentbody`.`alternativebudget_code` 
            from `paymentbody` where (`paymentbody`.`req_recid` = `p`.`req_recid`) limit 1) 
            AS `alternativebudget_code`,
            `pd`.`description` AS `description`,
            `pd`.`qty` AS `qty`,
            `pd`.`unit_price` AS `unit_price`,
            `pd`.`total` AS `total`,
            `p`.`company` AS `company`,
            `p`.`bank_name` AS `bank_name`,
            (select `a`.`created_at` from (`auditlog` `a` join `tasklist` `t` on((`t`.`req_recid` = `a`.`req_recid`))) 
            where ((`a`.`activity_form` = 2) and (`t`.`req_status` = '005') and (`t`.`req_recid` = `p`.`req_recid`)) 
            order by `a`.`created_at` desc limit 1) AS `paid_date`,
            (select `a`.`doer_name` from (`auditlog` `a` join `tasklist` `t` on((`t`.`req_recid` = `a`.`req_recid`))) where ((`a`.`activity_form` = 2) 
            and (`t`.`req_status` = '005') and (`t`.`req_recid` = `p`.`req_recid`)) order by `a`.`created_at` desc limit 1) 
            AS `paid_by`,
            `recordstatus`.`record_status_description` AS `record_status_description`,
            (select `a`.`created_at` from (`auditlog` `a` join `tasklist` `t` on((`t`.`req_recid` = `a`.`req_recid`))) where ((`t`.`req_status` = '005') and (`t`.`req_recid` = `p`.`req_recid`)) order by `a`.`id` desc limit 1,1) AS `approval_date1`,
            (select `a`.`created_at` 
            from (`tasklist` `t` join `view_auditlog_approved` `a` on((`a`.`req_recid` = `t`.`req_recid`))) 
            where ((`t`.`req_status` = '002') and (`t`.`step_number` <> '1') and (`t`.`req_recid` = `p`.`req_recid`) 
            and `a`.`doer_email` in (select `r`.`approve` from (`payment` `p` join `reviewapprove` `r` 
            on((`r`.`req_recid` = `p`.`req_recid`))) where (`r`.`approve` <> ''))) limit 1) AS `approval_date2`,
            (select `a`.`created_at` from (`tasklist` `t` join `view_auditlog_approved` `a` on((`a`.`req_recid` = `t`.`req_recid`))) 
            where ((`t`.`req_status` = '002') and (`t`.`step_number` <> '1') and (`t`.`req_recid` = `p`.`req_recid`) 
            and `a`.`doer_email` in (select `g`.`email` from `groupid` `g` where (`g`.`group_id` = 'GROUP_CEO'))) limit 1) 
            AS `approval_date3`,
            `re`.`ref` AS `ref`,
            `pro`.`created_at` AS `pro_request_date`
            FROM ((((((`payment` `p` join `paymentbody` `pd` on((`pd`.`req_recid` = `p`.`req_recid`))) 
            join `tasklist` `t` on((`t`.`req_recid` = `p`.`req_recid`))) 
            join `recordstatus` on((`recordstatus`.`record_status_id` = `t`.`req_status`))) 
            join `reviewapprove` `r` on((`r`.`req_recid` = `p`.`req_recid`))) 
            join `requester` `re` on((`re`.`req_recid` = `p`.`req_recid`))) 
            left join `procurement` `pro` on((`pro`.`req_recid` = `re`.`ref`))) 
        SQL;
    }
    private function dropView()
    {
        return <<<SQL

        DROP VIEW IF EXISTS `view_report_payment_procurement_tracking_request_raw`;
        SQL;
    }
}
