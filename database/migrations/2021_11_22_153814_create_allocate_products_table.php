<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllocateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('allocate_products', function (Blueprint $table) {
            $table->id();
            $table->string('req_recid');
            $table->float('general', 5)->default(0);
            $table->float('loan_general', 5)->default(0);
            $table->float('mortgage', 5)->default(0);
            $table->float('business', 5)->default(0);
            $table->float('personal', 5)->default(0);
            $table->float('card_general', 5)->default(0);
            $table->float('debit_card', 5)->default(0);
            $table->float('credit_card', 5)->default(0);
            $table->float('trade_general', 5)->default(0);
            $table->float('bank_general', 5)->default(0);
            $table->float('letter_of_credit', 5)->default(0);
            $table->float('deposit_general', 5)->default(0);
            $table->float('casa_individual', 5)->default(0);
            $table->float('td_individual', 5)->default(0);
            $table->float('casa_corporate', 5)->default(0);
            $table->float('td_corporate', 5)->default(0);

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
        Schema::dropIfExists('allocate_products');
    }
}
