<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldProcurementBodyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('procurementbody', function (Blueprint $table) {
            $table->string('vat')->nullable();
            $table->string('grand_total')->nullable();
        });
    }

    
}
