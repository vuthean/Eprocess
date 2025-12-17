<?php

namespace App\Http\Controllers;

use App\Models\Groupid;
use App\Models\Usermgt;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;

class GroupmemberandroleController extends Controller
{
    public function groupListingFilter(Request $request)
    {
        try {
            $filter = Crypt::decrypt($request->group_id);
            $result = DB::table('groupid')
                ->join('usermgt', 'groupid.email', 'usermgt.email')
                ->join('role', 'groupid.role_id', 'role.id')
                ->select('groupid.email', 'groupid.group_id', 'groupid.login_id', 'role.role_name', 'groupid.budget', 'usermgt.firstname', 'usermgt.lastname', 'usermgt.department', 'usermgt.position', 'groupid.created_at', 'groupid.role_id','groupid.status','groupid.is_cfo')
                ->where('groupid.group_id', $filter)
                ->get();
            $alluser = Usermgt::all();
            return view('groupmanagement.member', compact('result', 'filter', 'alluser'));
        } catch (\Exception$e) {
            Session::flash('error', 'Please Contact Admin');
            return redirect()->back();
        }
    }

    public function saveGroupListing(Request $request)
    {
        if($request->is_cfo == 1){
            $cfo_is_has = Groupid::where(['group_id' => $request->group_id,'is_cfo'=> '1'])->first();
            if( $cfo_is_has !== null and $request->group_id =="GROUP_CFO"){
                return redirect()->back()->with('error', 'Can not assign two CFO!');
            }
        }
        
        \DB::beginTransaction();
        try {
            $dt        = Carbon::now();
            $date_time = $dt->toDayDateTimeString();

            $doer_email = Auth::user()->email;
            
            $filter     = $request->email;
            $login_id   = $request->login_id;
            $group_id   = $request->group_id;
            $role_id    = $request->role;

            $department = $request->department;
            $position   = $request->position;
            $budget     = $request->budget_value;
            $is_cfo     = $request->is_cfo;
            

            $condition = $request->submit;

            if ($condition == 'delete') {
                if ($group_id == 'GROUP_ADMIN') {
                    $role_id = 4;
                }
                if ($group_id == 'GROUP_PROCUREMENT') {
                    $role_id = 4;
                }
                if ($group_id == 'GROUP_FINANCE') {
                    $role_id = 4;
                }
                if ($group_id == 'GROUP_ACCOUNTING') {
                    $role_id = 4;
                }
                if ($group_id == 'GROUP_MEMBER_EXCO') {
                    $role_id = 4;
                }
                if ($group_id == 'GROUP_SECONDLINE_EXCO') {
                    $role_id = 4;
                }
                if ($group_id == 'GROUP_MARKETING') {
                    $role_id = 4;
                }
                if ($group_id == 'GROUP_MDOFFICE') {
                    $role_id = 4;
                }
                if ($group_id == 'GROUP_CEO') {
                    $role_id = 4;
                }
                if ($group_id == 'GROUP_CFO') {
                    $role_id = 4;
                }
                if ($group_id == 'GROUP_LEARNING_PEOPLE') {
                    $role_id = 4;
                }
                if($group_id == 'GROUP_ADMINISTRATION'){
                    $role_id = 4;
                }
                $result = Groupid::where(['email' => $filter, 'role_id' => $role_id, 'group_id' => $group_id])->update([
                    'status'=>'0'
                ]);
                /** track log */
                // $result->logDelete();
            } else {

                $result = Groupid::where(['email' => $filter, 'group_id' => $group_id, 'role_id' => $role_id])->first();

                if (!empty($result) && $condition == 'add') {
                    if($result->status == 0){
                       Groupid::where(['email' => $filter, 'group_id' => $group_id, 'role_id' => $role_id])->update(['status'=>1]);
                       Session::flash('success', 'request was saved');
                       \DB::commit();
                       return redirect()->back();
                    }else{
                        $description_response = ["description" => "Duplicate user role in the same group"];

                        Session::flash('error', 'duplicate user role in the same group!');
                        return redirect()->back();
                    }
                   
                }
                $result = Groupid::where(['email' => $filter, 'group_id' => $group_id, 'role_id' => $role_id, 'budget' => $budget])->first();
                if (!empty($result)) {
                    $description_response = ["description" => "Nothing Change"];

                    Session::flash('error', 'nothing Change!');
                    return redirect()->back();
                }

                $result = Groupid::where(['email' => $filter, 'group_id' => $group_id])->first();
                if (!empty($result) && $condition == 'add') {
                    $description_response = ["description" => "Duplicate user in the same group1"];

                    Session::flash('error', 'duplicate user in the same group!');
                    return redirect()->back();
                }

                $maker_role = Groupid::where(['email' => $filter, 'role_id' => 1])->first();
                if (!empty($maker_role) and $role_id == '1') {
                    $description_response = ["description" => "Duplicate maker user to another group"];
                    Session::flash('error', 'duplicate maker user to another group');
                    return redirect()->back();
                }
                

                if ($role_id == '1' or $role_id == '2') {
                    $budget = "0";
                }

                if ($group_id == 'GROUP_ADMIN') {
                    $role_id = 4;
                }
                if ($group_id == 'GROUP_PROCUREMENT') {
                    $role_id = 4;
                }
                if ($group_id == 'GROUP_FINANCE') {
                    $role_id = 4;
                }
                if ($group_id == 'GROUP_ACCOUNTING') {
                    $role_id = 4;
                }
                if ($group_id == 'GROUP_MEMBER_EXCO') {
                    $role_id = 4;
                }
                if ($group_id == 'GROUP_SECONDLINE_EXCO') {
                    $role_id = 4;
                }
                if ($group_id == 'GROUP_MARKETING') {
                    $role_id = 4;
                }
                if ($group_id == 'GROUP_MDOFFICE') {
                    $role_id = 4;
                }
                if ($group_id == 'GROUP_CEO') {
                    $role_id = 4;
                }
                if ($group_id == 'GROUP_CFO') {
                    $role_id = 4;
                }
                if ($group_id == 'GROUP_LEARNING_PEOPLE') {
                    $role_id = 4;
                }
                if($group_id == 'GROUP_ADMINISTRATION'){
                    $role_id = 4;
                }

                $budget = '';
                if ($request->has('budget_value')) {
                    $budget = $request->budget_value;
                }
                if($group_id == 'GROUP_ACCOUNT_VOUCHER'){
                    DB::table('users')->where('email',$filter)->update([
                        'accounting_voucher_group' => 'accounting_voucher'
                    ]);
                    
                }

                /** process update or create */
                $group = Groupid::where('email', $filter)->where('group_id', $group_id)->first();
                if ($group) {
                    $oldGroup = collect($group);
                  
                    /** update */
                    $group->update([
                        'login_id' => $login_id,
                        'group_id' => $group_id,
                        'role_id'  => $role_id,
                        'budget'   => $budget,
                        'is_cfo'   => $is_cfo
                    ]);

                    /** track log */
                    $group->logUpdate($oldGroup);
                } else {
                    /** create new group */
                    $newGroup = Groupid::create([
                        'email'    => $filter,
                        'group_id' => $group_id,
                        'login_id' => $login_id,
                        'role_id'  => $role_id,
                        'budget'   => $budget
                    ]);
                    /** track log */
                    $newGroup->logNew();
                }
            }
            Session::flash('success', 'request was saved');
            \DB::commit();
            return redirect()->back();
        } catch (\Exception$e) {
            \Log::info($e);
            \DB::rollback();
            Session::flash('error', 'Please Contact Admin');
            return redirect()->back();
        }
    }
    public function listingUsers(Request $request){
        return view('groupmanagement.userList');
    }
    public function listingUserData(Request $request){
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");
        $search_arr = $request->get('search');
        $searchValue = $search_arr['value']; // Search value
        $dataSearch = '';
        if($request->fullName){
            $dataSearch = $request->fullName;
        }else{
            $dataSearch= '#';
        }
        $record_query = new Groupid();
        $record_query =  $record_query
                        ->select('users.fullname','groupid.email','groupdescription.group_description','groupid.status','groupid.group_id')
                        ->leftjoin('groupdescription','groupdescription.group_id','groupid.group_id')
                        ->leftjoin('users','users.email','groupid.email')
                        ->where('users.fullname','like', '%'. $dataSearch . '%')
                        ->where('groupid.status',1);
                      
        $totalRecords = $record_query->count();
        $totalRecordswithFilter =   $record_query->where(function ($query) use ($searchValue) {
            $query->where('fullname', 'like', '%' . $searchValue . '%');
        })->count();
        $records =  $record_query
        ->where(function ($query) use ($searchValue) {
            $query->where('fullname', 'like', '%' . $searchValue . '%');
        });
        $data_arr = [];
        $recordDatas = $records->get();
        foreach ($recordDatas as $key => $record) {
            if($record->status == 1){
                $status ="ACTIVE";
            }else{
                $status ="INACTIVE";
            }
            $data_arr[] = array(
                'number' => $key+1,
                'fullname' => $record->fullname,
                'groupId' => $record->group_id,
                'groupName' => $record->group_description,
                'email' => $record->email,
                'status' => $status,
                
            );
        }


        $response = array(
            "draw"                  => intval($draw),
            "iTotalRecords"         => $totalRecords,
            "iTotalDisplayRecords"  => $totalRecordswithFilter,
            "aaData"                => $data_arr
        );
        return response()->json($response);
    }
}
