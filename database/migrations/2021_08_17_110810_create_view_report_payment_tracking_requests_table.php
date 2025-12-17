<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateViewReportPaymentTrackingRequestsTable extends Migration
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
        CREATE OR REPLACE VIEW view_report_payment_tracking_requests AS
            select 
                report.req_recid                                as 'rp_ref_no',
                report.req_date                                 as 'req_date',
                IFNULL(DATE_FORMAT(if(approval_date1,approval_date1,if(approval_date3,approval_date3,approval_date2)),'%d-%m-%Y %h:%i'),'') as 'approve_date',
                ifnull(date_format(IF(report.line_review_date3,report.line_review_date3,if(report.line_review_date2,report.line_review_date2,line_review_date1)),'%d-%m-%Y %h:%i'),'') as 'line_review_date',
                IFNULL(DATE_FORMAT(if(report.accounting_review_date2,report.accounting_review_date2,report.accounting_review_date1),'%d-%m-%Y %h:%i'),'') as 'accounting_review_date',
                report.req_name                                 as 'requester',
                report.reviewers                                as 'reviewers',
                report.approvers                                as 'approvers',
                report.department                               as 'req_department',
                report.subject                                  as 'subject',
                report.due_date                                 as 'due_date',
                'USD'                                           as 'ccy',
                report.total                                    as 'amount',
                report.company                                  as 'supplier_name',
                report.bank_name                                as 'payment_method',
                report.budget_code                              as 'budget_code',
                report.alternativebudget_code                   as 'alt_code',
                report.budget_item                              as 'budget_items',
                report.total_budget                             as 'total_budget',
                report.ytd_expense                              as 'ytd_expense',
                report.payment_remaining                        as 'total_budget_remaining',
                IFNULL(DATE_FORMAT(report.paid_date,'%d-%m-%Y %h:%i'),'')   as 'paid_date',
                IFNULL(report.paid_by,'')                       as 'paid_by',
                report.record_status_description                as 'status',
                report.created_at                               as 'created_at'
                from view_report_payment_tracking_requests_raw as report
        SQL;
    }

    private function dropView()
    {
        return <<<SQL

        DROP VIEW IF EXISTS `view_report_payment_tracking_requests`;
        SQL;
    }
}
