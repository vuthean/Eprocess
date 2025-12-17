<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentbodiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paymentbody', function (Blueprint $table) {
            $table->id();
            $table->string('req_recid');
            $table->string('inv_no');
            $table->string('description');
            $table->string('br_dep_code');
            $table->string('budget_code');
            $table->string('alternativebudget_code');
            $table->string('unit');
            $table->string('qty');
            $table->string('unit_price');
            $table->string('total');
            $table->string('ytd_expense');
            $table->string('total_budget');
            $table->string('sub_total');
            $table->string('discount');
            $table->string('vat');
            $table->string('wht');
            $table->string('deposit');
            $table->string('net_payable');
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
        Schema::dropIfExists('paymentbody');
    }
}
