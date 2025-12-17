<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupdescriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groupdescription', function (Blueprint $table) {
            $table->id();
            $table->string('group_id');
            $table->string('group_name')->nullable();
            $table->string('group_description')->nullable();
            $table->string('special')->nullable();
            $table->string('is_procurement_record',5)->nullable();
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
        Schema::dropIfExists('groupdescription');
    }
}
