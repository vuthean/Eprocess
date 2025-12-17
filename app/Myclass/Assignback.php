<?php 
namespace App\Myclass;
use Illuminate\Http\Request;
use DB;
use Auth;
use Crypt;
use App\Models\Requester;
use App\Models\Led;
use App\Models\Tasklist;
use App\Models\Groupid;
use App\Models\Auditlog;
use Carbon\Carbon;
class Assignback {
    public static function assignbackRecord($email,$form_id,$comment,$record_id)
    {   
        $req_recid=Tasklist::where('req_recid',$record_id)->where('req_status',1)->first(); 
        if(empty($req_recid)){
            return "norequestid";
        }

        $requester_cond=Requester::where('req_recid',$record_id)->first();
        $req_from_cond=$requester_cond->req_from;
        $requester_group=Groupid::where('email',$requester_cond->req_email)->first();
        // *********** Start condition request from 


        $operational_risk=Groupid::where('email',$email)->where('group_id','RISK')->first();
        $distribution_risk=Groupid::where('email',$email)->where('group_id','DIS')->first();
        if(!empty($operational_risk)){
            $tasklist_update=array(
                "step_number"=>'assignback',
                "next_checker_group"=>$requester_cond->req_email,
                "next_checker_role"=>1,
                'req_status'=>3
            );
            Tasklist::where('req_recid',$record_id)->update($tasklist_update);
            $return="success";
        }elseif(!empty($distribution_risk)){
            $tasklist_update=array(
                "step_number"=>'assignback',
                "next_checker_group"=>$requester_cond->req_email,
                "next_checker_role"=>1,
                'req_status'=>3
            );
            Tasklist::where('req_recid',$record_id)->update($tasklist_update);
            $return="success";
        }else{
            // get group id and role user
            $user_cond=Groupid::where('email',$email)
                            ->whereIn('role_id',[2,3])
                            ->select('group_id','role_id')
                            ->orderby('role_id','desc')
                            ->get();  
            $result=[];
            foreach ($user_cond as $key => $value) {
                $tasklist=Tasklist::where('req_recid',$record_id)
                            ->where('next_checker_group',$value->group_id)
                            ->where('next_checker_role',$value->role_id)
                            ->where('req_status',1)                      
                            ->get();
                if(count($tasklist)>0){
                    array_push($result,$tasklist);  
                }
            }
            // check is user can authorize request or not
            if(count($result)>0){
            //case correct          
                $tasklist_update=array(
                    "step_number"=>'assignback',
                    "next_checker_group"=>$requester_cond->req_email,
                    "next_checker_role"=>1,
                    'req_status'=>3
                );
                Tasklist::where('req_recid',$record_id)->update($tasklist_update);
                $return="success";      

            }else{
                // case incorrect 
                return "Unauthorize";
            }
        }
        
        // $approve_group=Tasklist::where('req_recid',$record_id)->first();
        $doer_all=Groupid::where('email',$email)->first();
        $dt = Carbon::now();
        $date_time = $dt->toDayDateTimeString();
        $activity = new Auditlog();

        $activity->req_recid=$record_id;
        $activity->doer_email=$email;
        $activity->doer_name=$doer_all->login_id;
        $activity->doer_branch=$record_id;
        $activity->doer_position=$record_id;
        $activity->activity_code='A004';
        $activity->activity_description=$comment;
        $activity->activity_form=$form_id;
        $activity->activity_datetime=$date_time;
        $activity->save();

        return $return;
    }
}