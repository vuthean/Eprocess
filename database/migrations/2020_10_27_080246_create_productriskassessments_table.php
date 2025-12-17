<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductriskassessmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productriskassessment', function (Blueprint $table) {
            $table->id();
            $table->string('prdrisk_recid');
            $table->string('product_name');
            $table->string('prepare_by')->nullable();
            $table->string('department_own')->nullable();
            $table->string('date')->nullable();
            $table->string('product_description')->nullable();
            $table->string('a1')->nullable();
            $table->string('a2')->nullable();
            $table->string('a3')->nullable();
            $table->string('a4')->nullable();
            $table->string('a5')->nullable();
            $table->string('a6')->nullable();
            $table->string('b1')->nullable();
            $table->string('b2')->nullable();
            $table->string('c1')->nullable();
            $table->string('c2')->nullable();
            $table->string('d1')->nullable();
            $table->string('d2')->nullable();
            $table->string('d3')->nullable();
            $table->string('d4')->nullable();
            $table->string('d5')->nullable();
            $table->string('d6')->nullable();
            $table->string('e1')->nullable();
            $table->string('e2')->nullable();
            $table->string('e3')->nullable();
            $table->string('f1')->nullable();
            $table->string('f2')->nullable();
            $table->string('g1')->nullable();
            $table->string('g2')->nullable();            
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
        Schema::dropIfExists('productriskassessment');
    }
}
