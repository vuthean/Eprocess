<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcurementbottomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('procurementbottom', function (Blueprint $table) {
            $table->id();
            $table->string('req_recid');
            $table->string('general');
            $table->string('loan_general');
            $table->string('mortage');
            $table->string('busines');
            $table->string('personal');
            $table->string('card_general');
            $table->string('debit_card');
            $table->string('credit_card');
            $table->string('trade_general');
            $table->string('bank_guarantee');
            $table->string('letter_of_credit');
            $table->string('deposit_general');
            $table->string('casa_individual');
            $table->string('td_individual');
            $table->string('casa_corporate');
            $table->string('td_corporate');
            $table->string('sagement_general');
            $table->string('sagement_bfs');
            $table->string('sagement_rfs');
            $table->string('sagement_pb');
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
        Schema::dropIfExists('procurementbottom');
    }
}
