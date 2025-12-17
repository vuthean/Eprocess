<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditusersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audituser', function (Blueprint $table) {
            $table->id();
            $table->string('doer_email');
            $table->string('doer_name');
            $table->string('doer_branch')->nullable();
            $table->string('doer_position')->nullable();
            $table->string('activity_code')->nullable();
            $table->string('activity_description')->nullable();
            $table->string('activity_form')->nullable();
            $table->string('activity_datetime')->nullable();

            /** add more audit log */
            $table->string('model_type')->nullable();
            $table->string('model_id')->nullable();
            $table->json('old_value')->nullable();
            $table->json('new_value')->nullable();

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
        Schema::dropIfExists('audituser');
    }
}
