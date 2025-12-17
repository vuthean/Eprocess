<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentupload extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documentupload', function (Blueprint $table) {
            $table->id();
            $table->string('req_recid');
            $table->string('filename');
            $table->string('filepath');
            $table->string('doer_email');
            $table->string('doer_name');                        
            $table->string('activity_form');
            $table->string('activity_datetime');
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
        Schema::dropIfExists('documentupload');
    }
}
