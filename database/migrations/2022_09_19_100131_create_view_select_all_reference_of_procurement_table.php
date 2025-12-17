<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateViewSelectAllReferenceOfProcurementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
        CREATE OR REPLACE VIEW view_select_all_reference_of_procurement_table AS
      (
        SELECT 
        a.req_recid AS 'advance_request',
        a.ref,
        ca.req_recid AS 'clear_request',
        a.created_at AS 'date_of_adv',
        ca.created_at AS 'date_of_adc'
        FROM advance_forms a 
        JOIN clear_advance_forms ca ON a.req_recid=ca.advance_ref_no
        WHERE ref !='' and ref !='N/A'
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

        DROP VIEW IF EXISTS `view_select_all_reference_of_procurement_table`;
        SQL;
    }
}
