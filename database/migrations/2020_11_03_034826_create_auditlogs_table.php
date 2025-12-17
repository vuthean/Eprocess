<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditlogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auditlog', function (Blueprint $table) {
            $table->id();
            $table->string('req_recid');
            $table->string('doer_email');
            $table->string('doer_name');
            $table->string('doer_branch')->nullable();
            $table->string('doer_position')->nullable();            
            $table->string('activity_code');
            $table->string('activity_description');
            $table->string('activity_form');
            $table->string('activity_datetime');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('auditlog');
    }
}
