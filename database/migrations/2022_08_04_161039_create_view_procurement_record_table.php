<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateViewProcurementRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
        CREATE OR REPLACE VIEW view_procurement_record_table AS
      (
        SELECT
            t.*,
            f.`formname`,
            f.description,
            r.final,
            r.final_group,
            u.fullname,
            rq.subject,

            (
                Case
                When sum(pb.paid='Y') = 0 then 'Pending'
                When sum(pb.paid='N') = 0 then 'Done'
                When sum(pb.paid='Y') != 0 AND sum(pb.paid='N') != 0 then 'Partially Done'
                
                End
            ) AS status
            FROM procurementbody pb
            INNER JOIN tasklist t on t.req_recid =pb.req_recid
            INNER JOIN formname f ON f.id = t.req_type
            INNER JOIN reviewapprove r ON r.req_recid = t.req_recid
            INNER JOIN requester rq ON rq.req_recid = t.req_recid
            INNER JOIN users u ON r.final=u.email
            WHERE t.req_status = '005'
            GROUP BY pb.req_recid
            ORDER BY t.created_at desc
      )
    ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        return <<<SQL

        DROP VIEW IF EXISTS `view_procurement_record_table`;
        SQL;
    }
    
}
