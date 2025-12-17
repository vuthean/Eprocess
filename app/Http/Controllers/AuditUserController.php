<?php

namespace App\Http\Controllers;

use App\Models\Audituser;
use App\Models\Budgetcode;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuditUserController extends Controller
{
    public function index(){
        $logs = Audituser::get();
        return view('auditlog.userlog',compact('logs'));
    }
    public function budgetCodeTracking($id){   
        $log = Audituser::firstWhere('id',Crypt::decrypt($id));   
        $budget = Budgetcode::get();
        return view('auditlog.budgetcodelog',compact('budget','log'));
    }
    public function getUserTrackingData(Request $request){
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // total number of rows per page
        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');
        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value
        if($columnName=='no'){
            $columnName='doer_name';
        }
        $record_query =  new Audituser();   
        $totalRecords = $record_query->count();
        $totalRecordswithFilter=$record_query->where(function($query) use( $searchValue)
        {
            $query->where('doer_name', 'like', '%' . $searchValue . '%');
            $query->orWhere('doer_email', 'like', '%' . $searchValue . '%');
            $query->orWhere('activity_code', 'like', '%' . $searchValue . '%');
            $query->orWhere('activity_form', 'like', '%' . $searchValue . '%');
            $query->orWhere('activity_datetime', 'like', '%' . $searchValue . '%');
        })->count();
        $records =$record_query->orderBy($columnName, $columnSortOrder)
        ->where(function($query) use( $searchValue)
        {
            $query->where('doer_name', 'like', '%' . $searchValue . '%');
            $query->orWhere('doer_email', 'like', '%' . $searchValue . '%');
            $query->orWhere('activity_code', 'like', '%' . $searchValue . '%');
            $query->orWhere('activity_form', 'like', '%' . $searchValue . '%');
            $query->orWhere('activity_datetime', 'like', '%' . $searchValue . '%');
        })
        ->skip($start)
        ->take($rowperpage)
        ->get();
        $data_arr = [];
        $action = '';
        foreach ($records as $key => $record) {
            if ($record->activity_form == 'upload_budget_code'){
                $action = '<a href="'.route('budget-code-tracking', ['track_id' =>  Crypt::encrypt($record->id)]) .'"><i class="fa fa-eye"></i> view</a>';
            }else{
                $action = '<a href="#" data-toggle="modal" data-target="#large-Modal"
                data-old-value='.json_encode($record->old_value, true).'
                data-new-value='.json_encode($record->new_value, true).'
                data-module-name="'.$record->activity_form .'" style="cursor: pointer;"><i class="fa fa-eye"></i> view</a>';
            }
            
            $data_arr[] = array(
                "no" => $start + ($key+1),
                "doer_name" => $record->doer_name,
                "doer_email" => $record->doer_email,
                "activity_code" => $record->activity_code,
                "activity_form" => $record->activity_form,
                "activity_datetime" => $record->activity_datetime,
                "action" => $action
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
}
