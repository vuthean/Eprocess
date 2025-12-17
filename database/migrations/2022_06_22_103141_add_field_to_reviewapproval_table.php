<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldToReviewapprovalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reviewapprove', function (Blueprint $table) {
            $table->string('co_approver')->nullable();
            $table->string('fourth_reviewer')->nullable();
        });
        Schema::table('flowconfig', function (Blueprint $table) {
            $table->string('request_is_sole_source')->nullable();
        });
    }
}
