<?php

namespace App\Myclass;

use App\Models\BankReceiptVoucher;
use App\Enums\FormTypeEnum;
use App\Models\BankReceiptVourcherDetail;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BankReceiptVoucherExport implements FromQuery, WithHeadings
{
    protected $bankReceiptRequestNumbers =[];
    protected $formType = '';

    public function __construct($req_recids, $formType)
    {
        $this->bankReceiptRequestNumbers = $req_recids;
        $this->formType = $formType;

        return $this;
    }

    public function headings(): array
    {
        return [
            'Sl No.',
            'Account Number',
            'Account Branch',
            'Account Currency',
            'Dr Cr',
            'Amount',
            'Lcy Amount',
            'Transaction Code',
            'Value Date',
            'Narrative',
            'Instrument No.',
            'Related Account',
            'Related Reference',
            'Budget Code',
            'Taxcode',
            'Suppcode',
            'DeptCode',
            'ProdCode',
            'SegCode',
        ];
    }

    public function query()
    {
        if ($this->formType == FormTypeEnum::BankReceiptVourcherRequest()) {
            $reqRecids = $this->bankReceiptRequestNumbers;
            DB::statement(DB::raw('SET @row_number = 0'));
            return BankReceiptVourcherDetail::query()
            ->selectRaw('
                (@row_number:=@row_number + 1) AS num,
                bank_receipt_voucher_details.gl_code,
                bank_receipt_voucher_details.branch_code,
                bank_receipt_voucher_details.currency,
                if(bank_receipt_voucher_details.dr_cr = "DEBIT","D","C") as dr_cr,
                bank_receipt_voucher_details.amount,
                bank_receipt_voucher_details.lcy_amount,
                "" as transaction_code,
                "" as value_date,
                bank_receipt_voucher_details.naratives,
                "" as instrument_no,
                "" as related_account,
                "" as related_reference,
                bank_receipt_voucher_details.budget_code,
                bank_receipt_voucher_details.tax_code,
                bank_receipt_voucher_details.supp_code,
                bank_receipt_voucher_details.department_code,
                bank_receipt_voucher_details.product_code,
                bank_receipt_voucher_details.segment_code')
            ->join('bank_receipt_vouchers', 'bank_receipt_vouchers.req_recid', '=', 'bank_receipt_voucher_details.req_recid')
            ->whereIn('bank_receipt_vouchers.req_recid', $reqRecids);
        }
        return BankReceiptVourcherDetail::query();
    }
}
