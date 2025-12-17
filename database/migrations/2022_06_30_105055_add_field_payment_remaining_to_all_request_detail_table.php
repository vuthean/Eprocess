<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldPaymentRemainingToAllRequestDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('advance_form_details', function (Blueprint $table) {
            $table->string('old_payment_remaining')->nullable();
        });
        Schema::table('clear_advance_form_details', function (Blueprint $table) {
            $table->string('old_payment_remaining')->nullable();
        });
        Schema::table('paymentbody', function (Blueprint $table) {
            $table->string('old_payment_remaining')->nullable();
        });
    }
}
