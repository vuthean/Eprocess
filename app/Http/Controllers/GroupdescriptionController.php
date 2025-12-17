<?php

namespace App\Http\Controllers;

use App\Models\Groupdescription;
use App\Models\Groupid;
use Illuminate\Http\Request;
use Response;
use Session;
use Crypt;
use Carbon\Carbon;
class GroupdescriptionController extends Controller
{
    public function groupListing()
    {
        $result = Groupdescription::where('special', null)->get();
        return view('groupmanagement.group', compact('result'));
    }
    public function getGroupListingData(Request $request){
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
            $columnName = 'group_id';
        }
        $record_query = Groupdescription::where('special', null);
        $totalRecords = $record_query->count();
		$totalRecordswithFilter=$record_query->where(function($query) use( $searchValue)
            {
                $query->where('group_id', 'like', '%' . $searchValue . '%');
                $query->orWhere('group_name', 'like', '%' . $searchValue . '%');
                $query->orWhere('group_description', 'like', '%' . $searchValue . '%');
                $query->orWhere('created_at', 'like', '%' . $searchValue . '%');
            })->count();
        	$records =$record_query->orderBy($columnName, $columnSortOrder)
			->where(function($query) use( $searchValue)
			{
                $query->where('group_id', 'like', '%' . $searchValue . '%');
                $query->orWhere('group_name', 'like', '%' . $searchValue . '%');
                $query->orWhere('group_description', 'like', '%' . $searchValue . '%');
                $query->orWhere('created_at', 'like', '%' . $searchValue . '%');
			})
			->skip($start)
			->take($rowperpage)
			->get();
            $data_arr = [];
			foreach ($records as $key => $record) {
                $html_group_id = '<a href="#"​ class="group_id_click" data-toggle="modal" data-target="#editgroup-Modal"
                data-groupid="'.$record->group_id.'"
                data-groupname="'.$record->group_name.'"
                data-groupdesc="'.$record->group_description.'">
                '.$record->group_id.'
                </a>';
                $html_group_name = '<a href="'.url('group/member/listing/'.Crypt::encrypt($record->group_id)).'">'.$record->group_name.'</a>';
				$data_arr[] = array(
                    "no" => $start + ($key+1),
					"group_id" => $html_group_id,
					"group_name" => $html_group_name,
					"group_description" => $record->group_description,
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
    public function specialGroupListing()
    {
        $result = Groupdescription::where('special', 'Y')->get();
        return view('groupmanagement.specialgroup', compact('result'));
    }
    public function getSpecialListingData(Request $request){
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
            $columnName = 'group_id';
        }
        $record_query = Groupdescription::where('special','Y');
        $totalRecords = $record_query->count();
		$totalRecordswithFilter=$record_query->where(function($query) use( $searchValue)
            {
                $query->where('group_id', 'like', '%' . $searchValue . '%');
                $query->orWhere('group_name', 'like', '%' . $searchValue . '%');
                $query->orWhere('group_description', 'like', '%' . $searchValue . '%');
                $query->orWhere('created_at', 'like', '%' . $searchValue . '%');
            })->count();
        	$records =$record_query->orderBy($columnName, $columnSortOrder)
			->where(function($query) use( $searchValue)
			{
                $query->where('group_id', 'like', '%' . $searchValue . '%');
                $query->orWhere('group_name', 'like', '%' . $searchValue . '%');
                $query->orWhere('group_description', 'like', '%' . $searchValue . '%');
                $query->orWhere('created_at', 'like', '%' . $searchValue . '%');
			})
			->skip($start)
			->take($rowperpage)
			->get();
            $data_arr = [];
			foreach ($records as $key => $record) {
                $html_group_id = '<a href="#"​ class="group_id_click" data-toggle="modal" data-target="#editgroup-Modal"
                data-groupid="'.$record->group_id.'"
                data-groupname="'.$record->group_name.'"
                data-groupdesc="'.$record->group_description.'">
                '.$record->group_id.'
                </a>';
                $html_group_name = '<a href="'.url('group/member/listing/'.Crypt::encrypt($record->group_id)).'">'.$record->group_name.'</a>';
				$data_arr[] = array(
                    "no" => $start + ($key+1),
					"group_id" => $html_group_id,
					"group_name" => $html_group_name,
					"group_description" => $record->group_description,
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
    public function groupListingFilter(Request $request)
    {
        $filter                  = $request->group_id;
        $result                  = Groupdescription::where('group_id', $filter)->first();
        $success["reposnseCode"] = "000";
        $success["data"]         = $result;
        return Response::json($success, 200);
    }

    public function saveGroupListing(Request $request)
    {
        \DB::beginTransaction();
        try {
            $filter    = $request->group_id;
            $condition = $request->submit;
            if ($condition == 'delete') {
                $filter = $request->group_id;
                if ($filter == 'GROUP_ADMIN' or $filter == 'GROUP_PROCUREMENT' or $filter == 'GROUP_FINANCE' or $filter == 'GROUP_ACCOUNTING' or $filter == 'GROUP_CFO' or $filter == 'GROUP_CEO' or $filter == 'GROUP_MDOFFICE') {
                    Session::flash('error', 'can not delete!');
                    return redirect()->back();
                }
                /** delete group description */
                $group = Groupdescription::firstWhere('group_id', $filter);
                $group->delete();

                /** track log */
                $group->logDelete();

                /** delete group id */
                $member = Groupid::where('group_id', $filter);
                $member->delete();

                Session::flash('success', 'group was deleted!');
            } else {
                $groupDescription = Groupdescription::firstWhere('group_id', $request->group_id);
                if ($groupDescription) {
                    $oldGroupDescription = collect($groupDescription);

                    /** update */
                    $groupDescription->update([
                        'group_name'        => $request->group_name,
                        'group_description' => $request->group_description,
                    ]);

                    /** track log */
                    $groupDescription->logUpdate($oldGroupDescription);
                } else {
                    /** create new */
                    $newGroupDescription = Groupdescription::create([
                        'group_id'          => $filter,
                        'group_name'        => $request->group_name,
                        'group_description' => $request->group_description,
                    ]);

                    /** tracking log */
                    $newGroupDescription->logNew();
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
