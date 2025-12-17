<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcurementbodiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('procurementbody', function (Blueprint $table) {
            $table->id();
            $table->string('req_recid');
            $table->string('description');
            $table->string('br_dep_code');
            $table->string('budget_code');
            $table->string('alternativebudget_code');
            $table->string('unit');
            $table->string('qty');
            $table->string('unit_price');
            $table->string('total_estimate');
            $table->string('delivery_date');            
            $table->string('total');
            $table->string('unit_price_khr')->nullable();
            $table->string('total_estimate_khr')->nullable();
            $table->string('total_khr')->nullable();
            $table->string('budget_use')->nullable();
            $table->string('within_budget_code');  
            $table->string('alternative_use')->nullable();
            $table->string('paid')->default('N');
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
        Schema::dropIfExists('procurementbody');
    }
}
