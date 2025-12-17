<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddSwiftCodeToPaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment', function (Blueprint $table) {
            $table->string('swift_code')->nullable();
        });
        Schema::table('paymentbody', function (Blueprint $table) {
            $table->string('vat_item')->nullable();
            $table->string('vat_item_khr')->nullable();
        });
        Schema::table('advance_form_details', function (Blueprint $table) {
            $table->string('vat_item')->nullable();
            $table->string('vat_item_khr')->nullable();
        });
        Schema::table('clear_advance_form_details', function (Blueprint $table) {
            $table->string('vat_item')->nullable();
            $table->string('vat_item_khr')->nullable();
        });
    }
}
