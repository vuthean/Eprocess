<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldOldAmoutAfterAssignBankToTasklistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasklist', function (Blueprint $table) {
            $table->string('old_amout_after_assign_bank')->nullable();
        });
    }
}
