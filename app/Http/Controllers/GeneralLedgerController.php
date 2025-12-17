<?php

namespace App\Http\Controllers;

use App\Models\GeneralLedgerCode;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Rap2hpoutre\FastExcel\FastExcel;

class GeneralLedgerController extends Controller
{
    public function index()
    {
        return view('general_ledger.index');
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
            $columnName = 'account_number';
        }

        $record_query = new GeneralLedgerCode();
        $totalRecords = $record_query->count();
        $totalRecordswithFilter=$record_query->where(function ($query) use ($searchValue) {
            $query->where('account_number', 'like', '%' . $searchValue . '%');
            $query->orWhere('account_name', 'like', '%' . $searchValue . '%');
            $query->orWhere('created_at', 'like', '%' . $searchValue . '%');
        })->count();
        $records =$record_query->orderBy($columnName, $columnSortOrder)
            ->where(function ($query) use ($searchValue) {
                $query->where('account_number', 'like', '%' . $searchValue . '%');
                $query->orWhere('account_name', 'like', '%' . $searchValue . '%');
                $query->orWhere('created_at', 'like', '%' . $searchValue . '%');
            })
            ->skip($start)
            ->take($rowperpage)
            ->get();
        $data_arr = [];
        foreach ($records as $key => $record) {
            $htmlAccountNumber = '<a href="#"​ class="edit_item" data-toggle="modal" data-target="#update_group_Modal"
            data-id="'.$record->id.'"
            data-account_number="'.$record->account_number.'"
            data-account_name="'.$record->account_name.'">
                '.$record->account_number.'
                </a>';

            $data_arr[] = array(
                    "no" => $start + ($key+1),
                    "account_number" => $htmlAccountNumber,
                    "account_name" => $record->account_name,
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
        if (!$request->account_number) {
            Session::flash('error', "Account number is required.");
            return redirect()->back();
        }

        if (!$request->account_name) {
            Session::flash('error', "Account name is required.");
            return redirect()->back();
        }

        /** check if exist account number */
        $isExist = GeneralLedgerCode::firstWhere('account_number', $request->account_number);
        if ($isExist) {
            Session::flash('error', "Account number is already exist");
            return redirect()->back();
        }

        GeneralLedgerCode::create($request->input());

        return view('general_ledger.index');
    }

    public function update(Request $request)
    {
        if ($request->submit == 'delete') {
            GeneralLedgerCode::where('id', $request->updated_id)->delete();
            return view('general_ledger.index');
        }

        /** find exist request */
        $isExist = GeneralLedgerCode::where('id', '<>', $request->updated_id)
                ->where('account_number', $request->updated_account_number)
                ->first();
        if ($isExist) {
            Session::flash('error', "Account Number is already exist");
            return redirect()->back();
        }

        /** update general ledger */
        GeneralLedgerCode::where('id', $request->updated_id)->update([
            'account_number'=> $request->updated_account_number,
            'account_name'  => $request->updated_account_name
        ]);

        return view('general_ledger.index');
    }

    public function downloadTemplate()
    {
        $path = public_path('/static/template/general-ledger-template.xlsx');
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
                    $accountNumber = $line['account_number'];
                    $accountName   = $line['account_name'];
                    if ($accountNumber && $accountName) {
                        $result = GeneralLedgerCode::firstOrNew(['account_number' => $accountNumber]);
                        $result->account_name = $accountName;
                        $result->save();

                    }
                });
            });

            return redirect()->back();
        } catch (Exception $e) {
            Log::info($e);

            Session::flash('error', 'Please Contact Admin');
            return redirect()->back();
        }
    }
}
