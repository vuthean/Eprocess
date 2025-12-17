<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateViewSelectApprovalUsersTable extends Migration
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
        CREATE OR REPLACE VIEW view_select_approval_users AS
            select 
            `a`.`id` AS `id`,
            `a`.`req_recid` AS `req_recid`,
            `a`.`doer_email` AS `doer_email`,
            `a`.`doer_name` AS `doer_name`,
            `a`.`doer_branch` AS `doer_branch`,
            `a`.`doer_position` AS `doer_position`,
            `a`.`activity_code` AS `activity_code`,
            `a`.`activity_description` AS `activity_description`,
            `a`.`activity_form` AS `activity_form`,
            `a`.`activity_datetime` AS `activity_datetime`,
            `a`.`created_at` AS `created_at`,
            `a`.`updated_at` AS `updated_at` 
            from `auditlog` `a` 
            where (`a`.`id` in (select max(`a1`.`id`) 
            from `auditlog` `a1` group by `a1`.`req_recid`) 
            and (`a`.`activity_code` = 'A002'))
        SQL;
    }
    private function dropView()
    {
        return <<<SQL

        DROP VIEW IF EXISTS `view_select_approval_users`;
        SQL;
    }
}
