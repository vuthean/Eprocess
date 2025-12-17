<?php

namespace App\Http\Controllers;

use App\Models\TAXCode;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Rap2hpoutre\FastExcel\FastExcel;

class TAXCodeController extends Controller
{
    public function index()
    {
        return view('tax_code.index');
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

        $record_query = new TAXCode();
        $totalRecords = $record_query->count();
        $totalRecordswithFilter=$record_query->where(function ($query) use ($searchValue) {
            $query->where('code', 'like', '%' . $searchValue . '%');
            $query->orWhere('rate', 'like', '%' . $searchValue . '%');
            $query->orWhere('gl_description', 'like', '%' . $searchValue . '%');
            $query->orWhere('name', 'like', '%' . $searchValue . '%');
            $query->orWhere('created_at', 'like', '%' . $searchValue . '%');
        })->count();
        $records =$record_query->orderBy($columnName, $columnSortOrder)
            ->where(function ($query) use ($searchValue) {
                $query->where('code', 'like', '%' . $searchValue . '%');
                $query->orWhere('rate', 'like', '%' . $searchValue . '%');
                $query->orWhere('gl_description', 'like', '%' . $searchValue . '%');
                $query->orWhere('name', 'like', '%' . $searchValue . '%');
                $query->orWhere('created_at', 'like', '%' . $searchValue . '%');
            })
            ->skip($start)
            ->take($rowperpage)
            ->get();
        $data_arr = [];
        foreach ($records as $key => $record) {
            $htmlTAXCode = '<a href="#"​ class="edit_item" data-toggle="modal" data-target="#update_group_Modal"
            data-id="'.$record->id.'"
            data-tax_code="'.$record->code.'"
            data-tax_rate="'.$record->rate.'"
            data-gl_description="'.$record->gl_description.'"
            data-tax_name="'.$record->name.'">
                '.$record->code.'
                </a>';

            $data_arr[] = array(
                    "no" => $start + ($key+1),
                    "tax_code" => $htmlTAXCode,
                    "tax_rate" => $record->rate,
                    "gl_description" => $record->gl_description,
                    "tax_name" => $record->name,
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
        if (!$request->tax_code) {
            Session::flash('error', "TAX CODE is required.");
            return redirect()->back();
        }

        /** check if exist account number */
        $isExist = TAXCode::firstWhere('code', $request->tax_code);
        if ($isExist) {
            Session::flash('error', "TAX CODE is already exist");
            return redirect()->back();
        }

        TAXCode::create([
            'code' => $request->tax_code,
            'rate' => $request->tax_rate,
            'name' => $request->tax_name,
            'gl_description' => $request->gl_description,
        ]);

        return view('tax_code.index');
    }

    public function update(Request $request)
    {
        if ($request->submit == 'delete') {
            TAXCode::where('id', $request->updated_id)->delete();
            return view('tax_code.index');
        }

        /** find exist request */
        $isExist = TAXCode::where('id', '<>', $request->updated_id)
               ->where('code', $request->updated_tax_code)
               ->first();
        if ($isExist) {
            Session::flash('error', "Tax Code is already exist");
            return redirect()->back();
        }

        /** update general ledger */
        TAXCode::where('id', $request->updated_id)->update([
         'code' => $request->updated_tax_code,
         'rate' => $request->updated_tax_rate,
         'name' => $request->updated_tax_name,
         'gl_description' => $request->updated_gl_description,
      ]);

        return view('tax_code.index');
    }

    public function downloadTemplate()
    {
        $path = public_path('/static/template/tax-code-template.xlsx');
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
                    $taxCode = $line['CODE'];
                    $taxRate = $line['RATE'];
                    $name    = $line['NAME'];
                    $glDescription = $line['GL_DESCRIPTION'];
                    if ($taxCode) {
                        $result = TAXCode::firstOrNew(['code' => $taxCode]);
                        $result->rate = $taxRate;
                        $result->gl_description = $glDescription;
                        $result->name = $name;
                        $result->save();
                    }
                });
            });

            return view('general_ledger.index');
        } catch (Exception $e) {
            Log::info($e);

            Session::flash('error', 'Please Contact Admin');
            return redirect()->back();
        }
    }
}
