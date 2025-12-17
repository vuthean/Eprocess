<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdvanceFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advance_forms', function (Blueprint $table) {
            $table->id();
            $table->string('req_recid');
            $table->string('currency');
            $table->string('department');
            $table->date('request_date')->nullable();
            $table->date('due_date')->nullable();
            $table->string('category');
            $table->string('subject');
            $table->string('ref')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_address')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('company_name')->nullable();
            $table->string('id_number')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('address')->nullable();
            $table->text('additional_remark')->nullable();
            $table->text('additional_remark_product_segment')->nullable();
            $table->float('total_amount_usd', 12, 2)->default(0);
            $table->float('total_amount_khr', 50, 2)->default(0);
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
        Schema::dropIfExists('advance_forms');
    }
}
