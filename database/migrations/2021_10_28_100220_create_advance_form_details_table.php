<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdvanceFormDetailsTable extends Migration
{
    /**
    * Run the migrations.
    *
    * @return void
    */
    public function up()
    {
        Schema::create('advance_form_details', function (Blueprint $table) {
            $table->id();
            $table->string('req_recid');
            $table->text('description')->nullable();
            $table->string('invoice_number')->nullable();
            $table->string('department_code');
            $table->string('unit')->nullable();
            $table->integer('quantity');
            $table->float('exchange_rate_khr')->default(4000);
            $table->float('unit_price_usd', 12, 2)->default(0);
            $table->float('total_amount_usd', 12, 2)->default(0);
            $table->float('unit_price_khr', 50, 2)->default(0);
            $table->float('total_amount_khr', 50, 2)->default(0);

            $table->string('budget_code');
            $table->float('total_budget_amount', 12, 2)->default(0);
            $table->float('total_budget_amount_used', 12, 2)->default(0);
            $table->float('total_budget_ytd_expense_amount', 12, 2)->default(0);

            $table->string('alternative_budget_code')->nullable();
            $table->float('total_alt_budget_amount', 12, 2)->default(0);
            $table->float('total_alt_budget_amount_used', 12, 2)->default(0);
            $table->float('total_alt_budget_ytd_expense_amount', 12, 2)->default(0);

            $table->string('within_budget');
            $table->integer('procurment_body_id')->nullable();
            $table->boolean('is_cleared')->default(false);
            $table->string('used_by_request')->nullable();
            $table->string('used_by_request_bank_voucher')->nullable();

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
        Schema::dropIfExists('advance_form_details');
    }
}
