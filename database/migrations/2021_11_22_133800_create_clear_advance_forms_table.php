<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClearAdvanceFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clear_advance_forms', function (Blueprint $table) {
            $table->id();
            $table->string('req_recid');
            $table->string('department');
            $table->date('request_date')->nullable();
            $table->date('due_date')->nullable();
            $table->string('currency');
            $table->string('category');
            $table->string('advance_ref_no')->nullable();
            $table->string('subject');

            /**Paid To */
            $table->string('bank_name')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('bank_address')->nullable();
            $table->string('phone_number')->nullable();

            /** For */
            $table->string('company_name')->nullable();
            $table->string('id_number')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('address')->nullable();
            $table->text('additional_remark')->nullable();
            $table->text('additional_remark_product_segment')->nullable();

            /** catche database */
            $table->float('total_amount_usd', 20, 2)->default(0);
            $table->float('total_amount_khr', 60, 2)->default(0);

            $table->float('discount_amount_usd', 20, 2)->default(0);
            $table->float('discount_amount_khr', 60, 2)->default(0);

            $table->float('vat_amount_usd', 20, 2)->default(0);
            $table->float('vat_amount_khr', 60, 2)->default(0);

            $table->float('wht_amount_usd', 20, 2)->default(0);
            $table->float('wht_amount_khr', 60, 2)->default(0);

            $table->float('total_advance_amount_usd', 20, 2)->default(0);
            $table->float('total_advance_amount_khr', 60, 2)->default(0);

            $table->float('net_payable_amount_usd', 20, 2)->default(0);
            $table->float('net_payable_amount_khr', 60, 2)->default(0);

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
        Schema::dropIfExists('clear_advance_forms');
    }
}
