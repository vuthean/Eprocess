<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewapprovesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviewapprove', function (Blueprint $table) {
            $table->id();
            $table->string('req_recid');
            $table->string('review')->nullable();
            $table->string('second_review')->nullable();
            $table->string('third_review')->nullable();
            $table->string('budget_owner')->nullable();
            $table->string('approve');
            $table->string('final')->nullable();
            $table->string('final_group',50)->nullable();
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
        Schema::dropIfExists('reviewapprove');
    }
}
