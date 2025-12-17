<?php

namespace App\Http\Controllers;

use App\Models\Branchcode;
use Auth;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;
use Session;
use Carbon\Carbon;
// use Auth;
// use App\Models\Budgetcode;
class BranchcodeController extends Controller
{
    public function index()
    {
        $result = Branchcode::all();
        return view('groupmanagement.branch', compact('result'));
    }
    public function getBranchCodeData(Request $request){
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
        if( $columnName=='no'){
            $columnName = 'branch_code';
        }
        $record_query = new Branchcode;
        $totalRecords = $record_query->count();
		$totalRecordswithFilter=$record_query->where(function($query) use( $searchValue)
            {
                $query->where('branch_code', 'like', '%' . $searchValue . '%');
                $query->orWhere('branch_name', 'like', '%' . $searchValue . '%');
                $query->orWhere('created_at', 'like', '%' . $searchValue . '%');
            })->count();
        	$records =$record_query->orderBy($columnName, $columnSortOrder)
			->where(function($query) use( $searchValue)
			{
                $query->where('branch_code', 'like', '%' . $searchValue . '%');
                $query->orWhere('branch_name', 'like', '%' . $searchValue . '%');
                $query->orWhere('created_at', 'like', '%' . $searchValue . '%');
			})
			->skip($start)
			->take($rowperpage)
			->get();
            $data_arr = [];
			foreach ($records as $key => $record) {
                $html_branch_code = '<a href="#"​ class="group_id_click" data-toggle="modal" data-target="#editgroup-Modal"
                data-branchid="'.$record->id.'"
                data-branchcode="'.$record->branch_code.'"
                data-branchname="'.$record->branch_name.'">
                '.$record->branch_code.'
                </a>';
              
				$data_arr[] = array(
                    "no" => $start + ($key+1),
					"branch_code" => $html_branch_code,
					"branch_name" => $record->branch_name,
					"created_at" =>  Carbon::parse($record->created_at)->format('Y-m-d h:i:s')
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
    public function saveBranchCode(Request $request)
    {
        \DB::beginTransaction();
        try {
            $file_upload = $request->fileupload;
            if ($request->hasfile('fileupload')) {
                (new FastExcel)->import($file_upload, function ($line) {
                    if (!empty($line['branch_code'])) {
                        /**@var Branchcode $newBranch*/
                        $newBranch = Branchcode::create([
                            'branch_code' => $line['branch_code'],
                            'branch_name' => $line['branch_name'],
                        ]);

                        /** track log */
                        $newBranch->logNew();

                        return $newBranch;
                    }
                });
                \DB::commit();
                return redirect()->back();
            }

            $condition = $request->submit;
            if ($condition == 'delete') {
                $filter = $request->branchcode;

                /**@var Branchcode $group*/
                $group = Branchcode::where('branch_code', $filter)->first();
                $group->delete();

                /** track log */
                $group->logDelete();

                Session::flash('success', 'Record was deleted!');
            } else {
                $branchCode = Branchcode::firstWhere('id', $request->branchId);
                if ($branchCode) {
                    $oldBranchCode = collect($branchCode);

                    /** update branchCode */
                    $branchCode->update([
                        'branch_code' => $request->branchcode,
                        'branch_name' => $request->branchname,
                    ]);

                    /** track log */
                    $branchCode->logUpdate($oldBranchCode);
                } else {
                    /** create new BranchCode */
                    $newBranchCode = Branchcode::create([
                        'branch_code' => $request->branchcode,
                        'branch_name' => $request->branchname,
                    ]);

                    /** track log */
                    $newBranchCode->logNew();
                }
                Session::flash('success', 'request was save');
            }
            \DB::commit();
            return redirect()->back();
        } catch (\Exception $e) {
            \DB::rollback();
            Session::flash('error', 'Please Contact Admin');
            return redirect()->back();
        }
    }
}
