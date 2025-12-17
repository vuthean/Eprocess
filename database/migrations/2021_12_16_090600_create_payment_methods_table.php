<?php

use App\Models\PaymentMethod;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentMethodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();

            $table->blamable();
            $table->timestamps();
        });

        Schema::create('payment_method_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(PaymentMethod::class);
            $table->string('group_id');

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
        Schema::dropIfExists('payment_method_details');
        Schema::dropIfExists('payment_methods');
    }
}
