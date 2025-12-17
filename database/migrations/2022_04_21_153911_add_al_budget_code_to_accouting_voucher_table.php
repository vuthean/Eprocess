<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAlBudgetCodeToAccoutingVoucherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bank_payment_voucher_details', function (Blueprint $table) {
            $table->string('al_budget_code')->nullable();
        });
        
        Schema::table('journal_voucher_details', function (Blueprint $table) {
            $table->string('al_budget_code')->nullable();
        });
        Schema::table('bank_receipt_voucher_details', function (Blueprint $table) {
            $table->string('al_budget_code')->nullable();
        });
    }

}
