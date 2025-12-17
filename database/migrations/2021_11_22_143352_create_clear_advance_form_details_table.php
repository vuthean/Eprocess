<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClearAdvanceFormDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clear_advance_form_details', function (Blueprint $table) {
            $table->id(); 
            $table->string('req_recid');
            $table->text('description')->nullable();
            $table->string('invoice_number')->nullable();
            $table->string('department_code');
            $table->string('budget_code');
            $table->string('alternative_budget_code')->nullable();
            $table->float('exchange_rate_khr')->default(4000);

            $table->string('unit')->nullable();
            $table->integer('quantity');
            $table->float('unit_price_usd', 12, 2)->default(0);
            $table->float('unit_price_khr', 60, 2)->default(0);

            $table->float('total_amount_usd', 12, 2)->default(0);
            $table->float('total_amount_khr', 60, 2)->default(0);

            
            $table->float('total_budget_amount', 12, 2)->default(0);
            $table->float('total_budget_amount_used', 12, 2)->default(0);
            $table->float('total_budget_ytd_expense_amount', 12, 2)->default(0);

            $table->float('total_alt_budget_amount', 12, 2)->default(0);
            $table->float('total_alt_budget_amount_used', 12, 2)->default(0);
            $table->float('total_alt_budget_ytd_expense_amount', 12, 2)->default(0);

            $table->string('within_budget');
            $table->integer('advance_form_detail_id')->nullable();

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
        Schema::dropIfExists('clear_advance_form_details');
    }
}
