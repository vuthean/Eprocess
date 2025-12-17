<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Rap2hpoutre\FastExcel\FastExcel;

class SupplierController extends Controller
{
    public function index()
    {
        return view('supplier.index');
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

        $record_query = new Supplier();
        $totalRecords = $record_query->count();
        $totalRecordswithFilter=$record_query->where(function ($query) use ($searchValue) {
            $query->where('code', 'like', '%' . $searchValue . '%');
            $query->orWhere('full_name_eng', 'like', '%' . $searchValue . '%');
            $query->orWhere('full_name_kh', 'like', '%' . $searchValue . '%');
        })->count();
        $records =$record_query->orderBy($columnName, $columnSortOrder)
            ->where(function ($query) use ($searchValue) {
                $query->where('code', 'like', '%' . $searchValue . '%');
                $query->orWhere('full_name_eng', 'like', '%' . $searchValue . '%');
                $query->orWhere('full_name_kh', 'like', '%' . $searchValue . '%');
            })
            ->skip($start)
            ->take($rowperpage)
            ->get();
        $data_arr = [];
        foreach ($records as $key => $record) {
            $htmlSupplier = '<a href="#"​ class="edit_supplier" data-toggle="modal" data-target="#update_supplier_Modal"
                data-id="'.$record->id.'"
                data-code="'.$record->code.'"
                data-first_name_eng="'.$record->first_name_eng.'"
                data-last_name_eng="'.$record->last_name_eng.'"
                data-first_name_kh="'.$record->first_name_kh.'"
                data-last_name_kh="'.$record->last_name_kh.'"
                data-full_name_eng="'.$record->full_name_eng.'"
                data-full_name_kh="'.$record->full_name_kh.'"
                data-gender="'.$record->gender.'"
                data-date_of_birth="'.$record->date_of_birth.'"
                data-race="'.$record->race.'"
                data-nationality="'.$record->nationality.'"
                data-id_card_number="'.$record->id_card_number.'"
                data-passport_number="'.$record->passport_number.'"
                data-phone_number="'.$record->phone_number.'"
                data-email="'.$record->email.'"
                data-address="'.$record->address.'"
                data-type="'.$record->type.'"
                data-acct_name="'.$record->acct_name.'"
                data-acct_number="'.$record->acct_number.'"
                data-acct_currency="'.$record->acct_currency.'"
                data-pay_to_bank="'.$record->pay_to_bank.'">
                '.$record->code.'
            </a>';

            $data_arr[] = array(
                    "no" => $start + ($key+1),
                    "supplier_code" => $htmlSupplier,
                    "full_name_eng" => $record->full_name_eng,
                    "full_name_kh" => $record->full_name_kh,
                    "type" => $record->type,
                    "acct_name" => $record->acct_name,
                    "acct_number" => $record->acct_number,
                    "acct_currency" => $record->acct_currency,
                    "pay_to_bank" => $record->pay_to_bank,
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
        /** Validation */
        $validator = Validator::make($request->all(), [
            'code'           => 'required',
            'first_name_eng' => 'required',
            'last_name_eng'  => 'required',
            'first_name_kh'  => 'required',
            'last_name_kh'   => 'required',
        ]);
        if ($validator->fails()) {
            Session::flash('error', $validator->getMessageBag());
            return redirect()->back();
        }

        /** check exist supplier */
        $exist = Supplier::firstWhere('code', $request->code);
        if ($exist) {
            Session::flash('error', 'Supplier code is already exist.');
            return redirect()->back();
        }

        /** create new supplier */
        $validateField = $request->input();
        $validateField['full_name_eng'] = "{$request->first_name_eng} {$request->last_name_eng}";
        $validateField['full_name_kh']  = "{$request->first_name_kh} {$request->last_name_kh}";
        Supplier::create($validateField);

        return Redirect::to('suppliers');
    }

    public function update(Request $request)
    {
        if ($request->submit == 'delete') {
            Supplier::where('id', $request->updated_id)->delete();
            return Redirect::to('suppliers');
        }

        /** Validation */
        $validator = Validator::make($request->all(), [
            'updated_code'           => 'required',
            'updated_first_name_eng' => 'required',
            'updated_last_name_eng'  => 'required',
            'updated_first_name_kh'  => 'required',
            'updated_last_name_kh'   => 'required',
        ]);
        if ($validator->fails()) {
            Session::flash('error', $validator->getMessageBag());
            return redirect()->back();
        }

        /** check exist supplier */
        $exist = Supplier::where('code', $request->code)->where('id', '<>', $request->updated_id)->first();
        if ($exist) {
            Session::flash('error', 'Supplier code is already exist.');
            return redirect()->back();
        }

        /**udpate supplier */
        Supplier::where('id', $request->updated_id)->update([
            'code'=>$request->updated_code,
            'first_name_eng'=>$request->updated_first_name_eng,
            'last_name_eng'=>$request->updated_last_name_eng,
            'first_name_kh'=>$request->updated_first_name_kh,
            'last_name_kh'=>$request->updated_last_name_kh,
            'full_name_eng'=>"{$request->updated_first_name_eng} {$request->updated_last_name_eng}",
            'full_name_kh'=>"{$request->updated_first_name_kh} {$request->updated_last_name_kh}",
            'gender'=>$request->updated_gender,
            'date_of_birth'=>$request->updated_date_of_birth,
            'race'=>$request->updated_race,
            'nationality'=>$request->updated_nationality,
            'id_card_number'=>$request->updated_id_card_number,
            'passport_number'=>$request->updated_id_card_number,
            'phone_number'=>$request->updated_phone_number,
            'email'=>$request->updated_email,
            'address'=>$request->updated_address,
            'type'=>$request->updated_type,
            'acct_name'=>$request->updated_acct_name,
            'acct_number'=>$request->updated_acct_number,
            'acct_currency'=>$request->updated_acct_currency,
            'pay_to_bank'=>$request->updated_pay_to_bank,
        ]);

        return Redirect::to('suppliers');
    }

    public function downloadTemplate()
    {
        $path = public_path('/static/template/supplier-template.xlsx');
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
                        Supplier::updateOrInsert(
                            ['code'=> $line['code']],
                            [
                                'code' => $line['code'],
                                'first_name_eng' => $line['first_name_eng'],
                                'last_name_eng' => $line['last_name_eng'],
                                'first_name_kh' => $line['first_name_kh'],
                                'last_name_kh' => $line['last_name_kh'],
                                'full_name_eng' => $line['full_name_eng'],
                                'full_name_kh' => $line['full_name_kh'],
                                'gender' => $line['gender'],
                                'date_of_birth' => $line['date_of_birth'],
                                'race' => $line['race'],
                                'nationality' => $line['nationality'],
                                'id_card_number' => $line['id_card_number'],
                                'passport_number' => $line['passport_number'],
                                'phone_number' => $line['phone_number'],
                                'email' => $line['email'],
                                'address' => $line['address'],
                                'type' => $line['type'],
                                'acct_name' => $line['acct_name'],
                                'acct_number' => $line['acct_number'],
                                'acct_currency' => $line['acct_currency'],
                                'pay_to_bank' => $line['pay_to_bank'],
                            ]
                        );
                    }
                });
            });

            return Redirect::to('suppliers');
        } catch (Exception $e) {
            Log::info($e);

            Session::flash('error', 'Please Contact Admin');
            return redirect()->back();
        }
    }
}
