<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldToTreasuryVoucherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bank_vouchers', function (Blueprint $table) {
            $table->string('thb_exchange_rate')->nullable();
            $table->string('eur_exchange_rate')->nullable();
        });
    }
}
