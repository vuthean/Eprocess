<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUsedByRequestCashPaymentToTablePaymentBody extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('paymentbody', function (Blueprint $table) {
            $table->string('used_by_request_cash_payment')->nullable();
        });
        
        Schema::table('clear_advance_form_details', function (Blueprint $table) {
            $table->string('used_by_request_cash_payment')->nullable();
        });
        Schema::table('advance_form_details', function (Blueprint $table) {
            $table->string('used_by_request_cash_payment')->nullable();
        });
        Schema::table('procurementbody', function (Blueprint $table) {
            $table->string('used_by_request_cash_payment')->nullable();
        });
    }

}
