<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('first_name_eng');
            $table->string('last_name_eng');
            $table->string('first_name_kh');
            $table->string('last_name_kh');
            $table->string('full_name_eng');
            $table->string('full_name_kh');
            $table->string('gender');
            $table->date('date_of_birth')->nullable();
            $table->string('race')->nullable();
            $table->string('nationality')->nullable();
            $table->string('id_card_number')->nullable();
            $table->string('passport_number')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('type')->nullable();
            $table->string('acct_name')->nullable();
            $table->string('acct_number')->nullable();
            $table->string('acct_currency')->nullable();
            $table->string('pay_to_bank')->nullable();

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
        Schema::dropIfExists('suppliers');
    }
}
