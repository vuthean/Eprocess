<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateViewProcurementReportTrackingsTable extends Migration
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
        CREATE OR REPLACE VIEW view_procurement_report_trackings AS
        SELECT 
        `p2`.`id` AS `id`,
        `t`.`req_recid` AS `req_recid`,
        `t`.`created_at` AS `req_date`,
        `t`.`created_at` AS `requested_date`,
        `t`.`req_branch` AS `req_branch`,
        (select `a`.`created_at` from (`auditlog` `a` 
        join `tasklist` `task` on((`task`.`req_recid` = `a`.`req_recid`)))
        where ((`a`.`req_recid` = `t`.`req_recid`) and (`task`.`req_status` = '005')) 
        order by `a`.`id` desc limit 1) AS `approved_date`,`t`.`req_name` AS `requester`,

        (select `u`.`fullname` from ((`auditlog` `a` 
        join `reviewapprove` `r` on(((`r`.`req_recid` = `a`.`req_recid`) 
        and (`r`.`review` = `a`.`doer_email`)))) 
        join `users` `u` on((`u`.`email` = `a`.`doer_email`))) 
        where (`a`.`req_recid` = `t`.`req_recid`) order by `a`.`id` desc limit 1) AS `reviewer`,

        (select `u`.`fullname` from ((`auditlog` `a` 
        join `reviewapprove` `r` on(((`r`.`req_recid` = `a`.`req_recid`) 
        and (`r`.`second_review` = `a`.`doer_email`)))) 
        join `users` `u` on((`u`.`email` = `a`.`doer_email`))) 
        where (`a`.`req_recid` = `t`.`req_recid`) order by `a`.`id` desc limit 1) AS `second_review`,

        (select `u`.`fullname` from ((`auditlog` `a` 
        join `reviewapprove` `r` on(((`r`.`req_recid` = `a`.`req_recid`) 
        and (`r`.`third_review` = `a`.`doer_email`)))) 
        join `users` `u` on((`u`.`email` = `a`.`doer_email`))) 
        where (`a`.`req_recid` = `t`.`req_recid`) order by `a`.`id` desc limit 1) AS `third_review`,

        (select `u`.`fullname` from ((`auditlog` `a` 
        join `reviewapprove` `r` on(((`r`.`req_recid` = `a`.`req_recid`) 
        and (`r`.`fourth_reviewer` = `a`.`doer_email`)))) 
        join `users` `u` on((`u`.`email` = `a`.`doer_email`))) 
        where (`a`.`req_recid` = `t`.`req_recid`) order by `a`.`id` desc limit 1) AS `fourth_reviewer`,

        (select `u`.`fullname` from ((`auditlog` `a` 
        join `reviewapprove` `r` on(((`r`.`req_recid` = `a`.`req_recid`) 
        and (`r`.`co_approver` = `a`.`doer_email`)))) 
        join `users` `u` on((`u`.`email` = `a`.`doer_email`))) 
        where (`a`.`req_recid` = `t`.`req_recid`) order by `a`.`id` desc limit 1) AS `co_approver`,


        (select `u`.`fullname` from ((`auditlog` `a` 
        join `reviewapprove` `r` on(((`r`.`req_recid` = `a`.`req_recid`) 
        and (`r`.`approve` = `a`.`doer_email`)))) 
        join `users` `u` on((`u`.`email` = `a`.`doer_email`))) 
        where (`a`.`req_recid` = `t`.`req_recid`) order by `a`.`id` desc limit 1) AS `approver`,
        `r2`.`subject` AS `subject`,
        `p2`.`budget_code` AS `budget_code`,
        `p2`.`br_dep_code` AS `br_dep_code`,
        `p2`.`alternativebudget_code` AS `alternativebudget_code`,
        `p2`.`description` AS `description`,
        `r2`.`ccy` AS `currency`,
        `p2`.`qty` AS `quantity`,
        `p2`.`unit` AS `unit`,
        `p2`.`unit_price` AS `unit_price`,
        `p2`.`total` AS `total_usd`,
        `p2`.`total_khr` AS `total_khr`,
        `p2`.`used_by_request` as `used_by_request`,
        (SELECT t.created_at FROM `tasklist` t WHERE t.req_recid = p2.used_by_request) AS `date_of_adv1`,
        `ca`.req_recid AS `clear_request1`,
        (SELECT t.created_at FROM `tasklist` t WHERE t.req_recid = ca.req_recid) AS `date_of_adc1`,
        if((`p2`.`vat` IS NULL OR `p2`.`vat`='0'),'not_vat','vat') AS `vat`,

        if((`p2`.`paid` = 'y'),'YES','NO') AS `paid`,
        (select `u`.`fullname` 
        from ((`auditlog` `a` 
        join `reviewapprove` `r` on(((`r`.`req_recid` = `a`.`req_recid`) 
        and (`r`.`final` = `a`.`doer_email`)))) 
        join `users` `u` on((`u`.`email` = `a`.`doer_email`))) 
        where (`a`.`req_recid` = `t`.`req_recid`) order by `a`.`id` desc limit 1) AS `procured_by`,
        if((`p2`.`paid` = 'Y'),(select `payment_ref`.`created_at` from `view_procurement_references` `payment_ref` where (`payment_ref`.`req_recid` = `t`.`req_recid`) limit 1),'') AS `payment_date`,
        if((`p2`.`paid` = 'Y'),(select `payment_ref`.`payment_ref_no` from `view_procurement_references` `payment_ref` where (`payment_ref`.`req_recid` = `t`.`req_recid`) limit 1),'') AS `payment_ref_no`,
        `p`.`bid`
        FROM (((((`tasklist` `t` 
        join `requester` `r2` on((`r2`.`req_recid` = `t`.`req_recid`))) 
        join `procurement` `p` on((`p`.`req_recid` = `t`.`req_recid`))) 
        join `procurementbody` `p2` on((`p2`.`req_recid` = `t`.`req_recid`))) 
        left join `clear_advance_forms` `ca` ON((`p2`.`used_by_request` = `ca`.`advance_ref_no`))))
        SQL;
    }
}
