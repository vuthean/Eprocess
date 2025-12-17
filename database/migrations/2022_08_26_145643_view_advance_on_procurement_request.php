<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ViewAdvanceOnProcurementRequest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
        CREATE OR REPLACE VIEW view_advance_on_procurement_request AS
      (
        SELECT a.req_recid AS 'advance_req_recid',
        p.req_recid AS 'procurement_req_recid',
        (case
            when t.req_status = '005' then 'Done'
            ELSE 'Pending'
        END) AS 'status_advance_forms',
        p.id AS 'id_procurementbody'
            FROM advance_forms a
            JOIN procurementbody p ON a.ref
            LIKE CONCAT( '%', p.req_recid,'%' )
        JOIN tasklist t ON t.req_recid=a.req_recid 
        group BY a.req_recid,p.req_recid
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

        DROP VIEW IF EXISTS `view_advance_on_procurement_request`;
        SQL;
    }
}
