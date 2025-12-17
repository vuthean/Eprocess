<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateBankVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('req_recid');
            $table->string('voucher_date')->nullable();
            $table->string('voucher_number')->nullable();
            $table->string('batch_number')->nullable();
            $table->string('department')->nullable();
            $table->date('request_date');
            $table->string('currency')->default('USD');
            $table->string('exchange_rate')->default(1);

            $table->text('note')->nullable();
            $table->text('description')->nullable();

            $table->float('total_for_approval_usd', 20, 2)->default(0);
            $table->timestamp('exported_at')->nullable();

            $table->blamable();
            $table->timestamps();
        });
        Schema::create('bank_voucher_details', function (Blueprint $table) {
            $table->id();
            $table->string('req_recid');
            $table->string('gl_code');
            $table->string('account_name');
            $table->string('branch_code')->nullable();
            $table->string('budget_code')->nullable();

            $table->string('currency');
            $table->string('dr_cr');
            $table->float('amount', 20, 2);
            $table->float('lcy_amount', 20, 2);
            $table->string('supp_code')->nullable();
            $table->string('department_code')->nullable();

            $table->blamable();
            $table->timestamps();
        });
        Schema::create('bank_voucher_sequence', function (Blueprint $table) {
            $table->id();
        });
        DB::unprepared('
            CREATE DEFINER=`admuser`@`%` TRIGGER `trg_bank_voucher` BEFORE INSERT ON `bank_vouchers` FOR EACH ROW 
            BEGIN 
                DECLARE updatecount INT;
                IF (select req_recid from bank_vouchers caf  where YEAR(caf.created_at ) = year(CURDATE()) and month(caf.created_at ) = month(CURDATE()) limit 1) is null THEN 
                    DELETE FROM bank_voucher_sequence;
                END IF;
                INSERT INTO bank_voucher_sequence VALUES (NULL);
                
                set updatecount = (select count(*) from bank_voucher_sequence);
                SET NEW.req_recid = CONCAT("T",DATE_FORMAT(NOW(),"%Y"),DATE_FORMAT(NOW(),"%m"),DATE_FORMAT(NOW(),"%d"),LPAD(updatecount,5, "0"));
            END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bank_vouchers');
        Schema::dropIfExists('bank_voucher_details');
    }
}
