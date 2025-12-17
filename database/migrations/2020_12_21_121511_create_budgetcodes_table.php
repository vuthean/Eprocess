<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBudgetcodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('budgetdetail', function (Blueprint $table) {
            $table->id();
            $table->string('branch_code');
            $table->string('budget_code');
            $table->string('budget_item');
            $table->string('budget_owner');
            $table->string('total');
            $table->string('procerement')->default('');
            $table->string('temp')->nullable();
            $table->string('temp_payment')->nullable();
            $table->string('spent');
            $table->string('remaining');
            $table->string('payment');
            $table->string('payment_remaining');
            $table->string('year');
            $table->string('modify');
            $table->string('modify_by');
            $table->string('modify_date');
            $table->string('budget_after_calculate_pr')->nullable();
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
        Schema::dropIfExists('budgetdetail');
    }
}
