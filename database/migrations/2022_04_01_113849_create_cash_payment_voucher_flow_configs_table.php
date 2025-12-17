<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashPaymentVoucherFlowConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_payment_voucher_flow_configs', function (Blueprint $table) {
            $table->id();
            $table->float('min_amount',10,2)->default(0);
            $table->integer('step')->default(1);
            $table->string('group_id');
            $table->string('checker');
            $table->blamable();
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
        Schema::dropIfExists('cash_payment_voucher_flow_configs');
    }
}
