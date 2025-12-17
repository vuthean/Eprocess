<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasklistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasklist', function (Blueprint $table) {
            $table->id();
            $table->string('req_recid');
            $table->string('req_email');
            $table->string('req_name');
            $table->string('req_branch')->nullable();
            $table->string('req_position')->nullable();
            $table->string('req_from');
            $table->string('req_type');
            $table->string('next_checker_group');
            $table->string('next_checker_role');
            $table->string('step_number');
            $table->string('step_status');
            $table->string('req_status');           
            $table->string('req_date');
            $table->string('assign_back_by');
            $table->string('by_role');
            $table->string('by_step');
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
        Schema::dropIfExists('tasklist');
    }
}
