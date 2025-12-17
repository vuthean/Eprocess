<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\F;
use App\Models\ViewReportAdvanceTrackingRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class ReportAdvanceController extends Controller
{
    public function advanceRequestTracking(){
        return view('reports.advance_tracking');
    }
    public function getAdvanceRequestTrackingData( Request $request){
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); 
        $search_arr = $request->get('search');
        $searchValue = $search_arr['value']; // Search value
      
        $record_query = new ViewReportAdvanceTrackingRequest();
        if(!empty($request->dStart)) 
        {
            $record_query = $record_query->whereDate('created_at', '>=',$request->dStart);
        }
        
        if (!empty($request->dEnd)) 
        {
            $record_query = $record_query->whereDate('created_at', '<=',$request->dEnd);
        }
        $totalRecords = $record_query->count();
        $totalRecordswithFilter=$record_query->where(function($query) use( $searchValue)
        {
            $query->where('rp_ref_no', 'like', '%' . $searchValue . '%');
            $query->orWhere('requester', 'like', '%' . $searchValue . '%');
        })->count();
        $records =$record_query
        ->where(function($query) use( $searchValue)
        {
            $query->where('rp_ref_no', 'like', '%' . $searchValue . '%');
            $query->orWhere('requester', 'like', '%' . $searchValue . '%');  
        });
        if ($rowperpage == -1) {
			$records =  $records->get();
		} else {
			$records = $records->skip($start)
				->take($rowperpage)
				->get();
		}
        
        $data_arr = [];
        foreach ($records as $key => $record) {
            $subject = '<p style="height: 5px;"><a class="mytooltip tooltip-effect-9"
            href="javascript:void(0)" style="font-weight: 400;"
            onmouseover="hoverTooltip(`'.$record->subject.'`)">
            <span class="subject">'.$record->subject.'</span>
            <span class="tooltip-content5">
                <span class="tooltip-text3">
                    <span class="tooltip-inner2" style="padding: 2px;">
                        <span class="tooltip_body"></span>
                    </span>
                </span>
            </span>
        </a></p>';

        /** find request id */
        $cryp = Crypt::encrypt($record->rp_ref_no . '___no');
        $url  = url("form/advances/detail/{$cryp}");
        $rp_ref_no = '<a href="'.$url.'"​>'.$record->rp_ref_no.'</a>';
        // end
        $cryp_clear = Crypt::encrypt($record->adc_ref_no . '___no');
        $url_clear = url("form/clear-advances/detail/{$cryp_clear}");
        $rp_ref_no_clear = '<a href="'.$url_clear.'"​>'.$record->adc_ref_no.'</a>';

            $data_arr[] = array(
                "number" => $start + ($key+1),
                "rp_ref_no" => $rp_ref_no,
                "subject" => $subject,
                "req_date" => $record->req_date,
                "approve_date" => $record->approve_date,
                "line_review_date" => $record->line_review_date,
                "accounting_review_date" => $record->accounting_review_date,
                "accounting_review_name" => $record->accounting_review_name,
                "requester" => $record->requester,
                "reviewers" => $record->reviewers,
                "approvers" => $record->approvers,
                "req_department" => $record->req_department,
                "ccy" => $record->ccy,
                "amount" => $record->amount,
                "supplier_name" => $record->supplier_name,
                "payment_method" => $record->payment_method,
                "budget_code" => $record->budget_code,
                "alt_code" => $record->alt_code,
                "budget_items" => $record->budget_items,
                "total_budget" => $record->total_budget,
                "ytd_expense" => $record->ytd_expense,
                "total_budget_remaining" => $record->total_budget_remaining,
                "paid_date" => $record->paid_date,
                "paid_by" => $record->paid_by,
                "status" => $record->status,
                "adc_ref_no"=>$rp_ref_no_clear,
                "clear_date"=>$record->clear_date,
                "due_date" => $record->due_date
            );
        }
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );
        return response()->json($response);

    }
    public function filterAdvanceReportTracking(){

    }
}
