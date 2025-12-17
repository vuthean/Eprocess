<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->string('action');
            $table->morphs('model');
            $table->string('description')->nullable();
            $table->json('request_body')->nullable();
            $table->json('old_data')->nullable();
            $table->json('new_data')->nullable();
            $table->timestamp('proceeded_at');
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
        Schema::dropIfExists('user_logs');
    }
}
