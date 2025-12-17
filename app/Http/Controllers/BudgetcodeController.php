<?php

namespace App\Http\Controllers;

use App\Imports\BudgetcodeImport;
use App\Imports\CollectionBudgetcodeImport;
use App\Models\Budgetcode;
use App\Models\Usermgt;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB as DB_TRANSACTION;
use Maatwebsite\Excel\Facades\Excel;
use Session;

class BudgetcodeController extends Controller
{
    public function index()
    {
        $result  = Budgetcode::Leftjoin('usermgt', 'budgetdetail.budget_owner', 'usermgt.email')->orderBy('budgetdetail.budget_code', 'asc')->get();
        $alluser = Usermgt::all();
        return view('budgetmgt.index', compact('result', 'alluser'));
    }
    public function getBudgetCodeData(Request $request){
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
        $record_query = Budgetcode::Leftjoin('usermgt', 'budgetdetail.budget_owner', 'usermgt.email');
        $totalRecords = $record_query->count();
        $totalRecordswithFilter=$record_query->where(function($query) use( $searchValue)
            {
                $query->where('budgetdetail.budget_code', 'like', '%' . $searchValue . '%');
                $query->orWhere('budgetdetail.budget_item', 'like', '%' . $searchValue . '%');
                $query->orWhere('usermgt.fullname', 'like', '%' . $searchValue . '%');
                $query->orWhere('budgetdetail.total', 'like', '%' . $searchValue . '%');
                $query->orWhere('budgetdetail.remaining', 'like', '%' . $searchValue . '%');
                $query->orWhere('budgetdetail.payment_remaining', 'like', '%' . $searchValue . '%');
            })->count();
        	$records =$record_query->orderBy($columnName, $columnSortOrder)
			->where(function($query) use( $searchValue)
			{
                $query->where('budgetdetail.budget_code', 'like', '%' . $searchValue . '%');
                $query->orWhere('budgetdetail.budget_item', 'like', '%' . $searchValue . '%');
                $query->orWhere('usermgt.fullname', 'like', '%' . $searchValue . '%');
                $query->orWhere('budgetdetail.total', 'like', '%' . $searchValue . '%');
                $query->orWhere('budgetdetail.remaining', 'like', '%' . $searchValue . '%');
                $query->orWhere('budgetdetail.payment_remaining', 'like', '%' . $searchValue . '%');
			})
			->skip($start)
			->take($rowperpage)
			->get();
            $data_arr = [];
			foreach ($records as $key => $record) {
                $action = '<a href="'.url('budgetcode/detail/' . Crypt::encrypt($record->budget_code)).'" data-toggle="tooltip" data-placement="top" title="View">
                <i class="fa fa-folder-open" aria-hidden="true"></i>
                </a> &nbsp;';
                $action .= '<a href="#" data-toggle="tooltip" data-placement="top" title="Edit"
                class="view_budget_code" data-budget_code='.$record->budget_code.'
                data-budget_item='.$record->budget_item.'
                data-budget_name='.$record->budget_owner.'
                data-budget_f_name='.$record->firstname.'
                data-budget_l_name='.$record->lastname.'
                data-total='.$record->total.'
                data-procurement='.$record->remaining.'
                data-payment='.$record->payment_remaining.'>
                <i class="fa fa-edit"></i></a>';
				$data_arr[] = array(
                    "budget_code" => $record->budget_code,
					"budget_item" => $record->budget_item,
					"budget_owner" => $record->fullname,
					"total" => "$".number_format($record->total, 2),
                    "remaining" => "$".number_format($record->remaining, 2),
                    "payment_remaining" => "$".number_format($record->payment_remaining, 2),
                    'action' =>  $action
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
    public function uploadFile(Request $request)
    {
        try {
            if ($request->hasfile('fileupload')) {
                Excel::import(new CollectionBudgetcodeImport, request()->file('fileupload'));
            } else {
                $filter            = $request->budgetcode;
                $budget_code       = $request->budgetcode;
                $budget_item       = $request->budget_item;
                $budget_owner      = $request->owner1;
                $budget_name       = $request->owner_name;
                $total             = $request->total_b;
                $procurement       = $request->total_pr;
                $temp              = $request->total_pr;
                $temp_payment      = $request->total_pay;
                $remaining         = $request->total_pr;
                $payment           = $request->total_b - $request->total_pay;
                $payment_remaining = $request->total_pay;
                $year              = date('Y');
                $modify            = 'Y';
                $modify_by         = Auth::user()->email;
                $modify_date       = Carbon::now()->toDateTimeString();

                $result = Budgetcode::where(['budget_code' => $filter])->first();
                if (!empty($result)) {
                    Session::flash('error', 'Budget Code already exist');
                    return redirect()->back();
                }
                $budget                    = new Budgetcode();
                $budget->budget_code       = $budget_code;
                $budget->budget_item       = $budget_item;
                $budget->budget_owner      = $budget_owner;
                $budget->budget_name       = $budget_name;
                $budget->total             = $total;
                $budget->procurement       = $procurement;
                $budget->temp              = $temp;
                $budget->temp_payment      = $temp_payment;
                $budget->remaining         = $remaining;
                $budget->payment           = $payment;
                $budget->payment_remaining = $payment_remaining;
                $budget->year              = $year;
                $budget->modify            = $modify;
                $budget->modify_by         = $modify_by;
                $budget->modify_date       = $modify_date;
                $budget->save();

                /** log user activity */
                $budget->logNew();

                Session::flash('success', 'request was save');
            }

            \DB::commit();
            return redirect()->back();
        } catch (\Exception $e) {
            \Log::info($e);
            \DB::rollback();
            Session::flash('error', 'Please Contact Admin');
            return redirect()->back();
        }
    }
    public function detail($id)
    {
        try {
            $budget_code = Crypt::decrypt($id);
            $result      = DB::table('budgethistory')
                ->join('tasklist', 'tasklist.req_recid', 'budgethistory.req_recid')
                ->join('formname', 'tasklist.req_type', 'formname.id')
                ->where('budget_code', $budget_code)
                ->orwhere('alternative_budget_code', $budget_code)
                ->get();
            return view('budgetmgt.detail', compact('result'));
        } catch (\Exception $e) {
            Session::flash('error', 'Please Contact Admin');
            return redirect()->back();
        }
    }

    public function saveBudget(Request $request)
    {
        if ($request->submit == 'delete') {
            /**@var Budgetcode $budget*/
            $budget = Budgetcode::firstWhere('budget_code', $request->budgetcode);
            if (!$budget) {
                Session::flash('Failed', 'We cannot find this budget.');
                return redirect()->back();
            }

            /** make sure budget code is not in used */
            if ($budget->isAlreadyInUsed()) {
                Session::flash('Failed', 'This budgetCode is already used somewhere.');
                return redirect()->back();
            }

            DB_TRANSACTION::transaction(function () use ($budget) {
                /** delete budget code */
                $budget->delete();

                /** user tracking log */
                $budget->logDelete();
            });

            Session::flash('success', 'Record was deleted!');
            return redirect()->back();
        }

        /*** update or create budget code */
        $payment     = $request->total_b - $request->total_pay;
        $year        = date('Y');
        $modify      = 'Y';
        $modify_by   = \Illuminate\Support\Facades\Auth::user()->email;
        $modify_date = Carbon::now()->toDateTimeString();

        /**@var Budgetcode $budget*/
        $budget = Budgetcode::firstWhere('budget_code', $request->budgetcode);

        if ($budget) {
            /** update */
            $oldBudget = collect($budget);
            $budget->update([
                'budget_item'       => $request->budget_item,
                'budget_owner'      => $request->owner,
                'budget_name'       => $request->owner_name,
                'total'             => $request->total_b,
                'procurement'       => $request->total_pr,
                'temp'              => $request->total_pr,
                'temp_payment'      => $request->total_pay,
                'remaining'         => $request->total_pr,
                'payment'           => $payment,
                'payment_remaining' => $request->total_pay,
                'year'              => $year,
                'modify'            => $modify,
                'modify_by'         => $modify_by,
                'modify_date'       => $modify_date,
            ]);

            /** log user activity */
            $budget->logUpdate($oldBudget);

            Session::flash('Success', 'Update successfully.');
            return redirect()->back();
        }

        /** create new budget code */
        $newBudget = Budgetcode::create([
            'budget_code'       => $request->budgetcode,
            'budget_item'       => $request->budget_item,
            'budget_owner'      => $request->owner,
            'budget_name'       => $request->owner_name,
            'total'             => $request->total_b,
            'procurement'       => $request->total_pr,
            'temp'              => $request->total_pr,
            'temp_payment'      => $request->total_pay,
            'remaining'         => $request->total_pr,
            'payment'           => $payment,
            'payment_remaining' => $request->total_pay,
            'year'              => $year,
            'modify'            => $modify,
            'modify_by'         => $modify_by,
            'modify_date'       => $modify_date,
        ]);

        /** log user activity */
        $newBudget->logNew();

        Session::flash('success', 'request was save');
        return redirect()->back();
    }
}
