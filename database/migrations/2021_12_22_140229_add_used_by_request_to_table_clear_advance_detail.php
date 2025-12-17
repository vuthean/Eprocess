<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUsedByRequestToTableClearAdvanceDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clear_advance_form_details', function (Blueprint $table) {
            $table->string('used_by_request')->nullable();
        });
    }
}
