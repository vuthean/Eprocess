<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUsedByRequestToTablePaymentBody extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('paymentbody', function (Blueprint $table) {
            $table->string('used_by_request')->nullable();
        });
    }
}
