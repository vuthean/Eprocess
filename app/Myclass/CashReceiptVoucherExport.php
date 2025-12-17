<?php

namespace App\Myclass;

use App\Enums\FormTypeEnum;
use App\Models\CashReceiptVoucherDetail;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CashReceiptVoucherExport implements FromQuery, WithHeadings
{
    protected $bankPaymentRequestNumbers =[];
    protected $formType = '';

    public function __construct($req_recids, $formType)
    {
        $this->bankPaymentRequestNumbers = $req_recids;
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
        if ($this->formType == FormTypeEnum::CashReceiptVourcherRequest()) {
            $reqRecids = $this->bankPaymentRequestNumbers;
            DB::statement(DB::raw('SET @row_number = 0'));
            return CashReceiptVoucherDetail::query()
            ->selectRaw('
                (@row_number:=@row_number + 1) AS num,
                cash_receipt_voucher_details.gl_code,
                cash_receipt_voucher_details.branch_code,
                cash_receipt_voucher_details.currency,
                if(cash_receipt_voucher_details.dr_cr = "DEBIT","D","C") as dr_cr,
                cash_receipt_voucher_details.amount,
                cash_receipt_voucher_details.lcy_amount,
                "" as transaction_code,
                "" as value_date,
                cash_receipt_voucher_details.naratives,
                "" as instrument_no,
                "" as related_account,
                "" as related_reference,
                cash_receipt_voucher_details.budget_code,
                cash_receipt_voucher_details.tax_code,
                cash_receipt_voucher_details.supp_code,
                cash_receipt_voucher_details.department_code,
                cash_receipt_voucher_details.product_code,
                cash_receipt_voucher_details.segment_code')
            ->join('cash_receipt_vouchers', 'cash_receipt_vouchers.req_recid', '=', 'cash_receipt_voucher_details.req_recid')
            ->whereIn('cash_receipt_vouchers.req_recid', $reqRecids);
        }
        return CashReceiptVoucherDetail::query();
    }
}
