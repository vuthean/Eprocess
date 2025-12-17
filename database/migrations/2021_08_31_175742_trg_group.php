<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class TrgGroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
        CREATE DEFINER=`admuser`@`%` TRIGGER `trg_group` BEFORE INSERT ON `groupdescription` FOR EACH ROW BEGIN
        INSERT INTO group_sequence VALUES (NULL);
            SET NEW.group_id = CONCAT("GROUP_", LPAD(LAST_INSERT_ID(), 3, "0"));
        END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER `trg_group`');
    }
}
