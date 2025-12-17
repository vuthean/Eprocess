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
class Approval {
	public static function approveRecord($email,$form_id,$comment,$record_id,$risk_category,$risk_level)
	{	
        $req_recid=Tasklist::where('req_recid',$record_id)->where('req_status',1)->first();	
        if(empty($req_recid)){
            return "norequestid";
        }

        $requester_cond=Requester::where('req_recid',$record_id)->first();
        $req_from_cond=$req_recid->req_from;
        $requester_group=Groupid::where(['email'=>$req_recid->req_email,'role_id'=>1])->first();
        // *********** Start condition request from 
        switch ($req_from_cond) {
            // ******* Start request From Head office
            case 'ho':
                $operational_risk=Groupid::where('email',$email)->where('group_id','RISK')->first();
                if(!empty($operational_risk)){

                    $step=Tasklist::where('req_recid',$record_id)->first();
                    $step_number=$step->step_number;            
                    switch($step_number){
                        case "step1":
                            if($form_id=='1'){
                                $tasklist_update=array(
                                    "step_number"=>"step2",
                                    "next_checker_role"=>3
                                );
                            }else{
                                $tasklist_update=array(
                                    "step_number"=>"step2",
                                    "next_checker_group"=>'RISK',
                                    "next_checker_role"=>1
                                );
                            }
                            Tasklist::where('req_recid',$record_id)->update($tasklist_update);
                            $return="success";
                        break;
                        case "step2": 
                            if($form_id=='1'){                           
                                $tasklist_update=array(
                                    "step_number"=>"step3",
                                    "next_checker_group"=>'RISK',
                                    "next_checker_role"=>1
                                );
                            }else{
                                $tasklist_update=array(
                                        "step_number"=>"step3",
                                        "next_checker_group"=>$requester_group->group_id,
                                        "next_checker_role"=>3
                                    );
                            }
                            Tasklist::where('req_recid',$record_id)->update($tasklist_update);
                            $return="success";
                            break;
                        case "step3":
                            $tasklist_update=array(
                                "step_number"=>"close",
                                "next_checker_group"=>'close',
                                "next_checker_role"=>'close',
                                "req_status"=>4
                            );
                            Tasklist::where('req_recid',$record_id)->update($tasklist_update);
                            $return="success";
                            break;
                    }
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
                        $step=Tasklist::where('req_recid',$record_id)->first();
                        $step_number=$step->step_number;            
                        switch($step_number){
                            case "step1":
                                if($form_id=='1'){
                                    // return "led";
                                    $tasklist_update=array(
                                        "step_number"=>"step2",
                                        "next_checker_role"=>3
                                    );
                                }else{
                                    // return "project";
                                    $tasklist_update=array(
                                        "step_number"=>"step2",
                                        "next_checker_group"=>'RISK',
                                        "next_checker_role"=>1
                                    );
                                }
                                
                                Tasklist::where('req_recid',$record_id)->update($tasklist_update);
                                $return="success";
                            break;
                            case "step2":
                                if($form_id=='1'){
                                    $tasklist_update=array(
                                        "step_number"=>"step3",
                                        "next_checker_group"=>'RISK',
                                        "next_checker_role"=>1
                                    );
                                }else{
                                    $tasklist_update=array(
                                        "step_number"=>"step3",
                                        "next_checker_group"=>$requester_group->group_id,
                                        "next_checker_role"=>3
                                    );
                                }
                                Tasklist::where('req_recid',$record_id)->update($tasklist_update);
                                $return="success";
                            break;
                            case "step3":
                                $tasklist_update=array(
                                    "step_number"=>"close",
                                    "next_checker_group"=>'close',
                                    "next_checker_role"=>'close',
                                    "req_status"=>4
                                );
                                Tasklist::where('req_recid',$record_id)->update($tasklist_update);
                                $return="success";
                            break;
                        }
                    }else{
                        // case incorrect 
                        return "Unauthorize";
                    }
                }
                break;
            // ******** End request from head office

            // ******** Start request from branch
            case 'branch':

                $operational_risk=Groupid::where('email',$email)->where('group_id','RISK')->first();
                $distribution_risk=Groupid::where('email',$email)->where('group_id','DIS')->first();

                if(!empty($operational_risk)){

                    $step=Tasklist::where('req_recid',$record_id)->first();
                    $step_number=$step->step_number;            
                    switch($step_number){
                        case "step1":
                            if($form_id=='1'){
                                $tasklist_update=array(
                                    "step_number"=>"step2",
                                    "next_checker_group"=>'DIS',
                                    "next_checker_role"=>1
                                );
                            }else{
                                $tasklist_update=array(
                                    "step_number"=>"step2",
                                    "next_checker_group"=>'RISK',
                                    "next_checker_role"=>1
                                );
                            }
                            Tasklist::where('req_recid',$record_id)->update($tasklist_update);
                            $return="success";
                        break;
                        case "step2":
                            if($form_id=='1'){
                                $tasklist_update=array(
                                    "step_number"=>"step3",
                                    "next_checker_group"=>'RISK',
                                    "next_checker_role"=>1
                                );
                            }else{
                                $tasklist_update=array(
                                    "step_number"=>"step3",
                                    "next_checker_group"=>'DIS',
                                    "next_checker_role"=>1
                                );
                            }
                            Tasklist::where('req_recid',$record_id)->update($tasklist_update);
                            $return="success";
                            break;
                        case "step3":
                            $tasklist_update=array(
                                "step_number"=>"close",
                                "next_checker_group"=>'close',
                                "next_checker_role"=>'close',
                                "req_status"=>4
                            );
                            Tasklist::where('req_recid',$record_id)->update($tasklist_update);
                            $return="success";
                            break;
                    }
                }elseif(!empty($distribution_risk)){
                    $step=Tasklist::where('req_recid',$record_id)->first();
                    $step_number=$step->step_number;

                    switch($step_number){
                        case "step2":
                            if($form_id=='1'){
                                $tasklist_update=array(
                                    "step_number"=>"step3",
                                    "next_checker_group"=>'RISK',
                                    "next_checker_role"=>1
                                );
                            }else{
                                $tasklist_update=array(
                                    "step_number"=>"close",
                                    "next_checker_group"=>'close',
                                    "req_status"=>4,
                                    "next_checker_role"=>'close'
                                );
                            }
                            Tasklist::where('req_recid',$record_id)->update($tasklist_update);
                            $return="success";
                        break;
                        case "step3":
                            if($form_id=='1'){
                                $tasklist_update=array(
                                    "step_number"=>"step3",
                                    "next_checker_group"=>'RISK',
                                    "next_checker_role"=>1
                                );
                            }else{
                                $tasklist_update=array(
                                    "step_number"=>"close",
                                    "next_checker_group"=>'close',
                                    "req_status"=>4,
                                    "next_checker_role"=>'close'
                                );
                            }
                            Tasklist::where('req_recid',$record_id)->update($tasklist_update);
                            $return="success";
                        break;
                        default:
                            $return="fail";
                        break;                
                    }
                }
                else{
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
                        $step=Tasklist::where('req_recid',$record_id)->first();
                        $step_number=$step->step_number;            
                        switch($step_number){
                            case "step1":
                                if($form_id=='1'){
                                    $tasklist_update=array(
                                        "step_number"=>"step2",
                                        "next_checker_group"=>'DIS',
                                        "next_checker_role"=>1
                                    );
                                }else{
                                    $tasklist_update=array(
                                        "step_number"=>"step2",
                                        "next_checker_group"=>'RISK',
                                        "next_checker_role"=>1
                                    );
                                }
                                Tasklist::where('req_recid',$record_id)->update($tasklist_update);
                                $return="success";
                            break;
                            case "step2":
                                if($form_id=='1'){
                                    $tasklist_update=array(
                                        "step_number"=>"step3",
                                        "next_checker_group"=>'RISK',
                                        "next_checker_role"=>1
                                    );
                                }else{
                                    $tasklist_update=array(
                                        "step_number"=>"step3",
                                        "next_checker_group"=>'DIS',
                                        "next_checker_role"=>1
                                    );
                                }
                                Tasklist::where('req_recid',$record_id)->update($tasklist_update);
                                $return="success";
                            break;
                            case "step3":
                                $tasklist_update=array(
                                    "step_number"=>"close",
                                    "next_checker_group"=>'close',
                                    "next_checker_role"=>'close',
                                    "req_status"=>4
                                );
                                Tasklist::where('req_recid',$record_id)->update($tasklist_update);
                                $return="success";
                            break;
                        }
                    }else{
                        // case incorrect 
                        return "Unauthorize";
                    }
                }

                break;
            // ******** End request from head office

        }
        // *********** End condition request from 

        $approve_group=Tasklist::where('req_recid',$record_id)->first();


        $doer_all=Groupid::where('email',$email)->first();
        $dt = Carbon::now();
        $date_time = $dt->toDayDateTimeString();
        $activity = new Auditlog();

        $activity->req_recid=$record_id;
        $activity->doer_email=$email;
        $activity->doer_name=$doer_all->login_id;
        $activity->doer_branch=$record_id;
        $activity->doer_position=$record_id;
        $activity->activity_code='A002';
        $activity->activity_description=$comment;
        $activity->activity_form=$form_id;
        $activity->activity_datetime=$date_time;
        $activity->risk_category=$risk_category;
        $activity->risk_level=$risk_level;
        $activity->save();

        return $return;
    }
}