<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requester', function (Blueprint $table) {
            $table->id();
            $table->string('req_recid');
            $table->string('req_email');
            $table->string('req_name');
            $table->string('req_branch')->nullable();
            $table->string('req_position')->nullable();
            $table->string('req_from');
            $table->string('req_date'); 
            $table->string('due_expect_date'); 
            $table->string('subject'); 
            $table->string('ccy');                 
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
        Schema::dropIfExists('requester');
    }
}
