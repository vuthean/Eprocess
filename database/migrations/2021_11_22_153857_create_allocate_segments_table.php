<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllocateSegmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('allocate_segments', function (Blueprint $table) {
            $table->id();
            $table->string('req_recid');
            $table->float('general', 5)->default(0);
            $table->float('bfs', 5)->default(0);
            $table->float('rfs_ex_pb', 5)->default(0);
            $table->float('pb', 5)->default(0);
            $table->float('pcp', 5)->default(0);
            $table->float('afs', 5)->default(0);

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
        Schema::dropIfExists('allocate_segments');
    }
}
