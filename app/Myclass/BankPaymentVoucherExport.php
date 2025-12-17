<?php

namespace App\Myclass;

use App\Models\BankPaymentVoucher;
use Illuminate\Support\Facades\Date;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BankPaymentVoucherExport implements FromQuery, WithHeadings
{
    public function headings(): array
    {
        return [
            'req_recid',
            'voucher_number',
            'batch_number',
            'branch',
            'department',
            'request_date',
            'currency',
            'ref_no',
            'bank_name',
            'account_name',
            'account_number',
            'account_currency',
            'swift_code',
            'beneficiary_number',
            'invoice_number',
            'note',
            'summary_budgets',
            'exchange_rate',
            'total_for_approval_usd',
        ];
    }
    public function query()
    {
        return BankPaymentVoucher::query()->whereNotNull('id');
    }
    public function map($bankPayment): array
    {
        return [
            $bankPayment->req_recid,
            $bankPayment->voucher_number,
            $bankPayment->batch_number,
            $bankPayment->branch,
            $bankPayment->department,
            $bankPayment->request_date,
            $bankPayment->currency,
            $bankPayment->ref_no,
            $bankPayment->bank_name,
            $bankPayment->account_name,
            $bankPayment->account_number,
            $bankPayment->account_currency,
            $bankPayment->swift_code,
            $bankPayment->beneficiary_number,
            $bankPayment->invoice_number,
            $bankPayment->note,
            $bankPayment->summary_budgets,
            $bankPayment->exchange_rate,
            $bankPayment->total_for_approval_usd,
            Date::dateTimeToExcel($bankPayment->created_at),
            Date::dateTimeToExcel($bankPayment->updated_at),
        ];
    }
}
