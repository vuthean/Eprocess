<?php

namespace App\Http\Controllers;

use App\Models\RealBranch;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Rap2hpoutre\FastExcel\FastExcel;

class RealBranchController extends Controller
{
    public function index()
    {
        return view('real_branch.index');
    }

    public function listPagination(Request $request)
    {
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
        if ($columnName=='no') {
            $columnName = 'code';
        }

        $record_query = new RealBranch();
        $totalRecords = $record_query->count();
        $totalRecordswithFilter=$record_query->where(function ($query) use ($searchValue) {
            $query->where('code', 'like', '%' . $searchValue . '%');
            $query->orWhere('name', 'like', '%' . $searchValue . '%');
        })->count();
        $records =$record_query->orderBy($columnName, $columnSortOrder)
            ->where(function ($query) use ($searchValue) {
                $query->where('code', 'like', '%' . $searchValue . '%');
                $query->orWhere('name', 'like', '%' . $searchValue . '%');
            })
            ->skip($start)
            ->take($rowperpage)
            ->get();
        $data_arr = [];
        foreach ($records as $key => $record) {
            $htmlProductCode = '<a href="#"​ class="edit_item" data-toggle="modal" data-target="#update_group_Modal"
            data-id="'.$record->id.'"
            data-code="'.$record->code.'"
            data-name="'.$record->name.'">
            '.$record->code.'
                </a>';

            $data_arr[] = array(
                    "no" => $start + ($key+1),
                    "code" => $htmlProductCode,
                    "name" => $record->name,
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

    public function store(Request $request)
    {
        if (!$request->code) {
            Session::flash('error', "CODE is required.");
            return redirect()->back();
        }

        /** check if exist account number */
        $isExist = RealBranch::firstWhere('code', $request->code);
        if ($isExist) {
            Session::flash('error', "CODE is already exist");
            return redirect()->back();
        }

        RealBranch::create([
            'code' => $request->code,
            'name' => $request->name,
        ]);

        return Redirect::to('real-branches');
    }

    public function update(Request $request)
    {
        if ($request->submit == 'delete') {
            RealBranch::where('id', $request->updated_id)->delete();
            return Redirect::to('real-branches');
        }

        /** find exist request */
        $isExist = RealBranch::where('id', '<>', $request->updated_id)
               ->where('code', $request->updated_code)
               ->first();
        if ($isExist) {
            Session::flash('error', "Code is already exist");
            return redirect()->back();
        }

        /** update general ledger */
        RealBranch::where('id', $request->updated_id)->update([
         'code' => $request->updated_code,
         'name' => $request->updated_name,
      ]);

        return Redirect::to('real-branches');
    }

    public function downloadTemplate()
    {
        $path = public_path('/static/template/real-branches-template.xlsx');
        return response()->download($path);
    }

    public function importExcelData(Request $request)
    {
        if (!$request->has('fileUpload')) {
            return redirect()->back();
        }

        try {
            DB::transaction(function () use ($request) {
                (new FastExcel())->import($request->fileUpload, function ($line) {
                    if ($line['code']) {
                        RealBranch::updateOrInsert(
                            ['code'=> $line['code']],
                            [
                              'code' => $line['code'],
                              'name' => $line['name'],
                            ]
                        );
                    }
                });
            });

            return Redirect::to('real-branches');
        } catch (Exception $e) {
            Log::info($e);

            Session::flash('error', 'Please Contact Admin');
            return redirect()->back();
        }
    }
}
