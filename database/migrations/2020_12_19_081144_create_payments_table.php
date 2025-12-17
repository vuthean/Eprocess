<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment', function (Blueprint $table) {
            $table->id();
            $table->string('req_recid'); 
            $table->string('req_email')->nullable();           
            $table->string('type'); 
            $table->string('category'); 
            $table->string('account_name');
            $table->string('account_number');
            $table->string('bank_name');
            $table->string('bank_address');
            $table->string('tel');
            $table->string('company');
            $table->string('id_no');
            $table->string('contact_no');
            $table->string('address');
            $table->string('ref');
            $table->text('remarkable');
            $table->string('within_budget_code');   
            $table->string('req_date')->nullable();   
            $table->string('due_date')->nullable();      
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
        Schema::dropIfExists('payment');
    }
}
