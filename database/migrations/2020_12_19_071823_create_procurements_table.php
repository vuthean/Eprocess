<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcurementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('procurement', function (Blueprint $table) {
            $table->id();
            $table->string('req_recid');
            $table->string('req_email');
            $table->string('req_date');             
            $table->string('purpose');           
            $table->string('bid'); 
            $table->string('justification');           
            $table->string('comment_by_pr');           
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
        Schema::dropIfExists('procurement');
    }
}
