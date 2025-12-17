<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ViewReportPaymentAndProcurementTrackingRequests extends Migration
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
        CREATE OR REPLACE VIEW view_report_payment_procurement_tracking_request AS
        select 
        `report`.`req_recid` AS `rp_ref_no`,
        `report`.`subject` AS `subject`,
        `report`.`req_date` AS `req_date`,
        ifnull(date_format(if(`report`.`approval_date1`,`report`.`approval_date1`,
        if(`report`.`approval_date3`,`report`.`approval_date3`,`report`.`approval_date2`)),'%Y-%m-%d %h:%i:%s'),'') AS `approve_date`,
        `report`.`req_name` AS `requester`,
        `report`.`approvers` AS `approvers`,
        `report`.`department` AS `req_department`,
        `report`.department_code AS `department_code`,
        `report`.`budget_code` AS `budget_code`,
        `report`.`alternativebudget_code` AS `alt_code`,
        `report`.`description` AS `description`,
        `report`.`qty` AS `qty`,
        `report`.`unit_price` AS `unit_price`,
        `report`.`total` AS `amount`,
        'USD' AS `ccy`,
        `report`.`company` AS `supplier_name`,
        `report`.`bank_name` AS `payment_method`,
        ifnull(date_format(`report`.`paid_date`,'%Y-%m-%d %h:%i:%s'),'') AS `paid_date`,
        ifnull(`report`.`paid_by`,'') AS `paid_by`,
        `report`.`record_status_description` AS `status`,
        `report`.`ref` AS `ref`,
        `report`.`pro_request_date` AS `pro_request_date`,
        `t`.`updated_at` AS `received_date`,
        `t`.`req_name` AS `pro_requester`,
        `u`.`fullname` AS `procure_by`,
        `report`.`created_at` AS `created_at`,
        (select tl.req_branch from tasklist tl where tl.req_recid = report.ref) as `req_pr_branch`
        from `view_report_payment_procurement_tracking_request_raw` `report` 
        left JOIN `tasklist` `t` ON `t`.req_recid=`report`.`ref`
        left JOIN `reviewapprove` `ra` ON `ra`.`req_recid`=`report`.`ref`
        left JOIN	`usermgt` `u` ON `u`.`email` = `ra`.`final`
        SQL;
    }
    private function dropView()
    {
        return <<<SQL

        DROP VIEW IF EXISTS `view_report_payment_procurement_tracking_request`;
        SQL;
    }
}
