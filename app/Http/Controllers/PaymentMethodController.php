<?php

namespace App\Http\Controllers;

use App\Models\Groupdescription;
use App\Models\PaymentMethod;
use App\Models\PaymentMethodDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class PaymentMethodController extends Controller
{
    public function index()
    {
        /** find group description */
        $groupIds = Groupdescription::get();
        return view('payment_method.index', compact('groupIds'));
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
            $columnName = 'name';
        }

        $record_query = new PaymentMethod();
        $totalRecords = $record_query->count();
        $totalRecordswithFilter=$record_query->where(function ($query) use ($searchValue) {
            $query->where('name', 'like', '%' . $searchValue . '%');
            $query->orWhere('description', 'like', '%' . $searchValue . '%');
            $query->orWhere('created_at', 'like', '%' . $searchValue . '%');
        })->count();
        $records =$record_query->orderBy($columnName, $columnSortOrder)
            ->where(function ($query) use ($searchValue) {
                $query->where('name', 'like', '%' . $searchValue . '%');
                $query->orWhere('description', 'like', '%' . $searchValue . '%');
                $query->orWhere('created_at', 'like', '%' . $searchValue . '%');
            })
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = [];
        foreach ($records as $key => $record) {

            /** find payment method detail */
            $paymentMethodDetials = PaymentMethodDetail::where('payment_method_id', $record->id)->get();
            $groupIds = collect($paymentMethodDetials)->pluck('group_id');
            $groupString = collect($groupIds)->implode(',');

            $htmlName = '<a href="#"​ class="edit_item" data-toggle="modal" data-target="#update_group_Modal"
            data-id="'.$record->id.'"
            data-name="'.$record->name.'"
            data-group_id_strs="'.$groupString.'"
            data-description="'.$record->description.'">
                '.$record->name.'
                </a>';

            $data_arr[] = array(
                    "no" => $start + ($key+1),
                    "name" => $htmlName,
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
        if (!$request->name) {
            Session::flash('error', "Name is required.");
            return redirect()->back();
        }

        DB::transaction(function () use ($request) {
            /**@var PaymentMethod $paymentMethod */
            $paymentMethod = PaymentMethod::create([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            /** add group id to payment method */
            $paymentMethod->addGroupId($request->group_ids);
        });



        return Redirect::to('payment-methods');
    }

    public function update(Request $request)
    {
        $paymentMethodId = $request->updated_id;

        if ($request->submit == 'delete') {
            PaymentMethod::where('id', $paymentMethodId)->delete();
            return Redirect::to('payment-methods');
        }

        /** find exist request */
        $isExist = PaymentMethod::where('id', '<>', $paymentMethodId)
               ->where('name', $request->updated_name)
               ->first();
        if ($isExist) {
            Session::flash('error', "Payment method Name is already exist");
            return redirect()->back();
        }

        DB::transaction(function () use ($request) {
            /**@var PaymentMethod $paymentMethod*/
            $paymentMethod = PaymentMethod::where('id', $request->updated_id)->first();

            /** update payment method */
            $paymentMethod->update([
                'name' => $request->updated_name,
                'description' => $request->updated_description
            ]);

            /** update group Id */
            $paymentMethod->updateGroupId($request->updated_group_ids);
        });

        return Redirect::to('payment-methods');
    }
}
