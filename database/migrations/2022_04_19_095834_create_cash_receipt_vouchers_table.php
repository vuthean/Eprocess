<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateCashReceiptVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_receipt_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('req_recid');
            $table->string('voucher_number')->nullable();
            $table->string('batch_number')->nullable();
            $table->string('department')->nullable();
            $table->date('request_date');
            $table->string('currency')->default('USD');
            $table->string('exchange_rate')->default(1);
            $table->string('ref_no')->nullable();

            $table->string('payment_method_code')->nullable();
            $table->string('payment_method_group_id')->nullable();
            $table->string('payment_method_email_content')->nullable();
            $table->boolean('is_sent_email')->default(false);
            $table->boolean('is_sent_email_failed')->default(false);

            $table->string('bank_name')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_currency')->nullable();
            $table->string('swift_code')->nullable();
            $table->string('beneficiary_number')->nullable();
            $table->string('invoice_number')->nullable();

            $table->string('note')->nullable();
            $table->string('summary_budgets')->nullable();

            $table->float('total_for_approval_usd', 20, 2)->default(0);
            $table->timestamp('exported_at')->nullable();

            $table->blamable();
            $table->timestamps();
        });
        Schema::create('cash_receipt_voucher_details', function (Blueprint $table) {
            $table->id();
            $table->string('req_recid');
            $table->string('gl_code');
            $table->string('account_name');
            $table->string('branch_code')->nullable();

            $table->string('currency');
            $table->string('dr_cr');
            $table->float('amount', 20, 2);
            $table->float('lcy_amount', 20, 2);

            $table->string('budget_code');
            $table->string('al_budget_code')->nullable();
            $table->string('tax_code')->nullable();
            $table->string('supp_code')->nullable();
            $table->string('department_code')->nullable();
            $table->string('product_code')->nullable();
            $table->string('segment_code')->nullable();
            $table->string('naratives')->nullable();

            $table->string('reference_req_recid')->nullable();
            $table->string('reference_item_id')->nullable();

            $table->blamable();
            $table->timestamps();
        });
        Schema::create('cash_receipt_voucher_sequence', function (Blueprint $table) {
            $table->id();
        });
        DB::unprepared('
            CREATE DEFINER=`admuser`@`%` TRIGGER `trg_cash_receipt_voucher` BEFORE INSERT ON `cash_receipt_vouchers` FOR EACH ROW 
            BEGIN 
                DECLARE updatecount INT;
                IF (select req_recid from cash_receipt_vouchers caf  where YEAR(caf.created_at ) = year(CURDATE()) and month(caf.created_at ) = month(CURDATE()) limit 1) is null THEN 
                    DELETE FROM cash_receipt_voucher_sequence;
                END IF;
                INSERT INTO cash_receipt_voucher_sequence VALUES (NULL);
                
                set updatecount = (select count(*) from cash_receipt_voucher_sequence);
                SET NEW.req_recid = CONCAT("CR-",DATE_FORMAT(NOW(),"%Y"),"-",DATE_FORMAT(NOW(),"%m"),"-",LPAD(updatecount,5, "0"));
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
        Schema::dropIfExists('cash_receipt_vouchers');
        Schema::dropIfExists('cash_receipt_form_sequences');
    }
}
