<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdvanceFormSequence extends Migration
{
    /**
      * Run the migrations.
      *
      * @return void
      */
    public function up()
    {
        Schema::create('advance_form_sequence', function (Blueprint $table) {
            $table->id();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('advance_form_sequence');
    }
}
