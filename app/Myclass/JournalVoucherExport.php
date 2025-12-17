<?php

namespace App\Myclass;

use App\Enums\FormTypeEnum;
use App\Models\JournalVourcherDetail;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class JournalVoucherExport implements FromQuery, WithHeadings
{
    protected $journalRequestNumbers =[];
    protected $formType = '';

    public function __construct($req_recids, $formType)
    {
        $this->journalRequestNumbers = $req_recids;
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
        if ($this->formType == FormTypeEnum::JournalVourcherRequest()) {
            $reqRecids = $this->journalRequestNumbers;
            DB::statement(DB::raw('SET @row_number = 0'));
            return JournalVourcherDetail::query()
            ->selectRaw('
                (@row_number:=@row_number + 1) AS num,
                journal_voucher_details.gl_code,
                journal_voucher_details.branch_code,
                journal_voucher_details.currency,
                if(journal_voucher_details.dr_cr = "DEBIT","D","C") as dr_cr,
                journal_voucher_details.amount,
                journal_voucher_details.lcy_amount,
                "" as transaction_code,
                "" as value_date,
                journal_voucher_details.naratives,
                "" as instrument_no,
                "" as related_account,
                "" as related_reference,
                journal_voucher_details.budget_code,
                journal_voucher_details.tax_code,
                journal_voucher_details.supp_code,
                journal_voucher_details.department_code,
                journal_voucher_details.product_code,
                journal_voucher_details.segment_code')
            ->join('journal_vouchers', 'journal_vouchers.req_recid', '=', 'journal_voucher_details.req_recid')
            ->whereIn('journal_vouchers.req_recid', $reqRecids);
        }
        return JournalVourcherDetail::query();
    }
}
