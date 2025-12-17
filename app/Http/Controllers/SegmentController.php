<?php

namespace App\Http\Controllers;

use App\Models\SegmentCode;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Rap2hpoutre\FastExcel\FastExcel;

class SegmentController extends Controller
{
    public function index()
    {
        return view('segment_code.index');
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

        $record_query = new SegmentCode();
        $totalRecords = $record_query->count();
        $totalRecordswithFilter=$record_query->where(function ($query) use ($searchValue) {
            $query->where('code', 'like', '%' . $searchValue . '%');
            $query->orWhere('type', 'like', '%' . $searchValue . '%');
        })->count();
        $records =$record_query->orderBy($columnName, $columnSortOrder)
            ->where(function ($query) use ($searchValue) {
                $query->where('code', 'like', '%' . $searchValue . '%');
                $query->orWhere('type', 'like', '%' . $searchValue . '%');
            })
            ->skip($start)
            ->take($rowperpage)
            ->get();
        $data_arr = [];
        foreach ($records as $key => $record) {
            $htmlProductCode = '<a href="#"​ class="edit_item" data-toggle="modal" data-target="#update_group_Modal"
            data-id="'.$record->id.'"
            data-code="'.$record->code.'"
            data-type="'.$record->type.'"
            data-description="'.$record->description.'">
                '.$record->code.'
                </a>';

            $data_arr[] = array(
                    "no" => $start + ($key+1),
                    "code" => $htmlProductCode,
                    "type" => $record->type,
                    "description" => $record->description,
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
        $isExist = SegmentCode::firstWhere('code', $request->code);
        if ($isExist) {
            Session::flash('error', "CODE is already exist");
            return redirect()->back();
        }

        SegmentCode::create([
            'code' => $request->code,
            'type' => $request->type,
            'description' => $request->description,
        ]);

        return Redirect::to('segment-codes');
    }

    public function update(Request $request)
    {
        if ($request->submit == 'delete') {
            SegmentCode::where('id', $request->updated_id)->delete();
            return Redirect::to('segment-codes');
        }

        /** find exist request */
        $isExist = SegmentCode::where('id', '<>', $request->updated_id)
               ->where('code', $request->updated_code)
               ->first();
        if ($isExist) {
            Session::flash('error', "Code is already exist");
            return redirect()->back();
        }

        /** update general ledger */
        SegmentCode::where('id', $request->updated_id)->update([
            'code' => $request->updated_code,
            'type' => $request->updated_type,
            'description' => $request->updated_description,
      ]);

      return Redirect::to('segment-codes');
    }

    public function downloadTemplate()
    {
        $path = public_path('/static/template/segment-code-template.xlsx');
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
                        $result = SegmentCode::firstOrNew(['code' => $line['code']]);
                        $result->type = $line['type'];
                        $result->description = $line['description'];
                        $result->save();
                        
                    }
                });
            });

            return Redirect::to('segment-codes');
        } catch (Exception $e) {
            Log::info($e);

            Session::flash('error', 'Please Contact Admin');
            return redirect()->back();
        }
    }
}
