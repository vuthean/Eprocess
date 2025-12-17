<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\CashReceiptVoucher;
use App\Models\CashReceiptVoucherDetail;
use App\Models\BudgetDetail;
use App\Models\Documentupload;
use App\Models\Auditlog;
use Illuminate\Support\Facades\DB;

class ExportCashReceipts implements FromView,WithDrawings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public $req_recid;
    function __construct($req_recid) {
	    $this->req_recid = $req_recid;
	}
    public function view(): View
        {
            /**@var CashReceiptVoucher $bankPayment */
            $req_recid =  $this->req_recid;
            $bankPayment = CashReceiptVoucher::firstWhere('req_recid', $req_recid);
            $bankPaymentDetails = CashReceiptVoucherDetail::where('req_recid', $req_recid)->get();

            /** find approver log */
            $preparedBy = $bankPayment->findPreparedByUser();
            $firstReviewer = $bankPayment->findFirstReviewer();
            $approver = $bankPayment->findApprover();
            $paidBy = $bankPayment->findPaidBy();

            $totalDRCR = $bankPayment->getTotalDRCR();

            /**find is cross currency */
            $currencies = collect($bankPaymentDetails)->pluck('currency');
            $isCrossCurrency = $bankPayment->isCrossCurrency($currencies);
            $defaultCurrency = 'USD';
            if (!$isCrossCurrency) {
                $defaultCurrency = $currencies[0];
            }
            $al_budget_codes = collect($bankPaymentDetails)->pluck('al_budget_code');
            $totalAndYTDAL = BudgetDetail::whereIn('budget_code',$al_budget_codes)
                        ->select('budget_code','total','payment_remaining as remaining',DB::raw('(total-payment_remaining) as total_YTD'))
                        ->get();
            $budget_codes = collect($bankPaymentDetails)->pluck('budget_code');
            $totalAndYTD = BudgetDetail::whereIn('budget_code',$budget_codes)
                        ->select('budget_code','total','payment_remaining as remaining',DB::raw('(total-payment_remaining) as total_YTD'))
                        ->get();
            $budgetcode_na = $bankPayment->getBudgetNA($budget_codes);
            $al_budgetcode_na = $bankPayment->getBudgetNA($al_budget_codes);
            
            $auditlogs = Auditlog::join('activitydescription', 'auditlog.activity_code', 'activitydescription.activity_code')
                    ->where('auditlog.req_recid', $req_recid)
                    ->select('auditlog.doer_name AS name', 'activitydescription.activity_description AS activity', 'auditlog.activity_description AS comment', 'auditlog.activity_datetime AS datetime')
                    ->get();
            $documents = Documentupload::where('req_recid', $req_recid)->get();
            $merge_req = explode(',',$bankPayment->ref_no,10);
            return view('accounting_voucher.cash_receipt.exportExcel', compact(
                'merge_req',
                'bankPayment',
                'bankPaymentDetails',
                'preparedBy',
                'firstReviewer',
                'approver',
                'paidBy',
                'totalDRCR',
                'defaultCurrency',
                'totalAndYTD',
                'auditlogs',
                'documents',
                'totalAndYTDAL',
                'budgetcode_na',
                'al_budgetcode_na'
            ));
    }
    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('This is my logo');
        $drawing->setPath(public_path('/src/image/prince.jpg'));
        $drawing->setHeight(58);
        $drawing->setCoordinates('A1');

        return $drawing;
    }
}
