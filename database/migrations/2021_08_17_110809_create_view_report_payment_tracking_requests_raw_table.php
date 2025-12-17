<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateViewReportPaymentTrackingRequestsRawTable extends Migration
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
        CREATE OR REPLACE VIEW view_report_payment_tracking_requests_raw AS
            select 
            `p`.`req_recid` AS `req_recid`,
            `p`.`req_date` AS `req_date`,
            `p`.`created_at` AS `created_at`,
            `p`.`created_at` AS `payment_at`,
            `t`.`req_name` AS `req_name`,
            `re`.`subject` AS `subject`,
            `re`.`due_expect_date` AS `due_date`,
            `p`.`company` AS `company`,
            `p`.`bank_name` AS `bank_name`,
            `pd`.`total` AS `total`,
            `pd`.`budget_code` AS `budget_code`,
            (select `paymentbody`.`total_budget` from `paymentbody` where (`paymentbody`.`req_recid` = `p`.`req_recid`) 
            limit 1) AS `total_budget`,(select `paymentbody`.`ytd_expense` from `paymentbody` 
            where (`paymentbody`.`req_recid` = `p`.`req_recid`) limit 1) AS `ytd_expense`,(select `paymentbody`.`alternativebudget_code` 
            from `paymentbody` where (`paymentbody`.`req_recid` = `p`.`req_recid`) limit 1) 
            AS `alternativebudget_code`,(select `paymentbody`.`br_dep_code` from `paymentbody` 
            where (`paymentbody`.`req_recid` = `p`.`req_recid`) limit 1) AS `br_dep_code`,concat(if((`r`.`review` is not null),
            coalesce((select `u`.`fullname` from (`reviewapprove` `r1` join `users` `u` on((`r1`.`review` = `u`.`email`))) 
            where (`r1`.`review` = `r`.`review`) limit 1),''),''),if((`r`.`second_review` is not null),coalesce(concat(' | ',
            (select `u`.`fullname` from (`reviewapprove` `r2` join `users` `u` on((`r2`.`second_review` = `u`.`email`))) 
            where (`r2`.`second_review` = `r`.`second_review`) limit 1)),''),''),if((`r`.`third_review` is not null),
            coalesce(concat(' | ',(select `u`.`fullname` 
            from (`reviewapprove` `r3` join `users` `u` on((`r3`.`third_review` = `u`.`email`))) 
            where (`r3`.`third_review` = `r`.`third_review`) limit 1)),''),'')) AS `reviewers`,
            concat(if((`r`.`approve` is not null),
            coalesce((select `u`.`fullname` from (`reviewapprove` `r1` join `users` `u` on((`r1`.`approve` = `u`.`email`))) 
            where (`r1`.`approve` = `r`.`approve`) limit 1),''),''),if((`r`.`final` is not null),
            coalesce(concat(' | ',(select `u`.`fullname` from (`reviewapprove` `r2` join `users` `u` on((`r2`.`final` = `u`.`email`))) 
            where (`r2`.`final` = `r`.`final`) limit 1)),''),'')) AS `approvers`,(select `u`.`department` from `users` `u` 
            where (`u`.`email` = `t`.`req_email`) limit 1) AS `department`,(select `b2`.`budget_item` from `budgetdetail` `b2` 
            where (`b2`.`budget_code` = `pd`.`budget_code`) limit 1) AS `budget_item`,(select `b2`.`payment_remaining` 
            from `budgetdetail` `b2` where (`b2`.`budget_code` = `pd`.`budget_code`) limit 1) AS `payment_remaining`,
            (select `a`.`created_at` from (`auditlog` `a` join `tasklist` `t` on((`t`.`req_recid` = `a`.`req_recid`))) 
            where ((`a`.`activity_form` = 2) and (`t`.`req_status` = '005') and (`t`.`req_recid` = `p`.`req_recid`)) 
            order by `a`.`created_at` desc limit 1) AS `paid_date`,(select `a`.`doer_name` 
            from (`auditlog` `a` join `tasklist` `t` on((`t`.`req_recid` = `a`.`req_recid`))) where ((`a`.`activity_form` = 2) 
            and (`t`.`req_status` = '005') and (`t`.`req_recid` = `p`.`req_recid`)) order by `a`.`created_at` desc limit 1) 
            AS `paid_by`,`recordstatus`.`record_status_description` AS `record_status_description`,(select `a`.`created_at` 
            from (`auditlog` `a` join `tasklist` `t` on((`t`.`req_recid` = `a`.`req_recid`))) where ((`t`.`req_status` = '005') 
            and (`t`.`req_recid` = `p`.`req_recid`)) order by `a`.`id` desc limit 1,1) AS `approval_date1`,(select `a`.`created_at` 
            from (`tasklist` `t` join `view_auditlog_approved` `a` on((`a`.`req_recid` = `t`.`req_recid`))) 
            where ((`t`.`req_status` = '002') and (`t`.`step_number` <> '1') and (`t`.`req_recid` = `p`.`req_recid`) 
            and `a`.`doer_email` in (select `r`.`approve` from (`payment` `p` join `reviewapprove` `r` 
            on((`r`.`req_recid` = `p`.`req_recid`))) where (`r`.`approve` <> ''))) limit 1) AS `approval_date2`,
            (select `a`.`created_at` from (`tasklist` `t` join `view_auditlog_approved` `a` on((`a`.`req_recid` = `t`.`req_recid`))) 
            where ((`t`.`req_status` = '002') and (`t`.`step_number` <> '1') and (`t`.`req_recid` = `p`.`req_recid`) 
            and `a`.`doer_email` in (select `g`.`email` from `groupid` `g` where (`g`.`group_id` = 'GROUP_CEO'))) limit 1) 
            AS `approval_date3`,(select `auditlog`.`created_at` from ((`payment` join `auditlog` 
            on((`auditlog`.`req_recid` = `payment`.`req_recid`))) join `tasklist` on((`tasklist`.`req_recid` = `payment`.`req_recid`))) 
            where (`auditlog`.`doer_email` in (select `groupid`.`email` from `groupid` where (`groupid`.`group_id` = 'GROUP_ACCOUNTING')) 
            and (`tasklist`.`req_recid` = `p`.`req_recid`) and (`tasklist`.`req_status` = '005')) order by `auditlog`.`created_at` 
            desc limit 1,1) AS `accounting_review_date1`,(select `auditlog`.`created_at` from ((`payment` join `auditlog` 
            on((`auditlog`.`req_recid` = `payment`.`req_recid`))) join `tasklist` on((`tasklist`.`req_recid` = `payment`.`req_recid`))) 
            where (`auditlog`.`doer_email` in (select `groupid`.`email` from `groupid` where (`groupid`.`group_id` = 'GROUP_ACCOUNTING')) 
            and (`auditlog`.`req_recid` = `p`.`req_recid`) and (`tasklist`.`req_status` <> '005')) order by `auditlog`.`created_at` 
            desc limit 1) AS `accounting_review_date2`,(select `auditlog`.`created_at` from ((`payment` join `auditlog` 
            on((`auditlog`.`req_recid` = `payment`.`req_recid`))) join `tasklist` on((`tasklist`.`req_recid` = `payment`.`req_recid`))) 
            where (`auditlog`.`doer_email` in (select `reviewapprove`.`review` from `reviewapprove` 
            where (`reviewapprove`.`req_recid` = `p`.`req_recid`)) and (`auditlog`.`req_recid` = `p`.`req_recid`)) 
            order by `auditlog`.`created_at` desc limit 1) AS `line_review_date1`,(select `auditlog`.`created_at` 
            from ((`payment` join `auditlog` on((`auditlog`.`req_recid` = `payment`.`req_recid`))) join `tasklist` 
            on((`tasklist`.`req_recid` = `payment`.`req_recid`))) where (`auditlog`.`doer_email` 
            in (select `reviewapprove`.`second_review` from `reviewapprove` where (`reviewapprove`.`req_recid` = `p`.`req_recid`)) 
            and (`auditlog`.`req_recid` = `p`.`req_recid`)) order by `auditlog`.`created_at` desc limit 1) AS `line_review_date2`,
            (select `auditlog`.`created_at` from ((`payment` join `auditlog` on((`auditlog`.`req_recid` = `payment`.`req_recid`))) 
            join `tasklist` on((`tasklist`.`req_recid` = `payment`.`req_recid`))) 
            where (`auditlog`.`doer_email` in (select `reviewapprove`.`second_review` from `reviewapprove` 
            where (`reviewapprove`.`req_recid` = `p`.`req_recid`)) and (`auditlog`.`req_recid` = `p`.`req_recid`)) 
            order by `auditlog`.`created_at` desc limit 1) AS `line_review_date3` 
            from (((((`payment` `p` join `view_unique_payment_body` `pd` on((`pd`.`req_recid` = `p`.`req_recid`))) 
            join `tasklist` `t` on((`t`.`req_recid` = `p`.`req_recid`))) 
            join `recordstatus` on((`recordstatus`.`record_status_id` = `t`.`req_status`))) 
            join `reviewapprove` `r` on((`r`.`req_recid` = `p`.`req_recid`))) 
            join `requester` `re` on((`re`.`req_recid` = `p`.`req_recid`))) 
        SQL;
    }

    private function dropView()
    {
        return <<<SQL

        DROP VIEW IF EXISTS `view_report_payment_tracking_requests_raw`;
        SQL;
    }
}
