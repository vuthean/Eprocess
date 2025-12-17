<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldIsCfoToGroupIdTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('groupid', function (Blueprint $table) {
            $table->string('is_cfo')->default('0')->nullable();
        });
    }

    
}
