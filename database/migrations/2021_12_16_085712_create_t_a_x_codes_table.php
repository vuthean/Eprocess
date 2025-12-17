<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTAXCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tax_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('rate')->nullable();
            $table->string('gl_description')->nullable();
            $table->string('name')->nullable();
            $table->string('description')->nullable();

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
        Schema::dropIfExists('tax_codes');
    }
}
