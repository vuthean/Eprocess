<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFlowconfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flowconfig', function (Blueprint $table) {
            $table->id();
            $table->string('req_name');
            $table->string('step_number');
            $table->string('checker');
            $table->string('notification_type');
            // $table->string('send_type');
            $table->string('step_description');            
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
        Schema::dropIfExists('flowconfig');
    }
}
