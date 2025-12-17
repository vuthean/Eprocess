<?php 
namespace App\Myclass;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Session;
use Illuminate\Support\Facades\Crypt;
use Toastr;
use Input;
use App\Models\Requester;
// use App\Models\Led;
use App\Models\Tasklist;
use App\Models\Groupid;
use App\Models\Auditlog;
use App\Myclass\Sendemail;
use Illuminate\Support\Facades\Log;
use Response;
class Defaultsave {


	public static function requesterSave($req_email,$req_name,$req_branch,$req_position,$req_from)
	{
        $dt = Carbon::now();
        $date_time = $dt->toDayDateTimeString();
        $requester = new Requester();        
        $requester->req_email=$req_email;
        $requester->req_name=$req_name;
        $requester->req_branch=$req_branch;
        $requester->req_position=$req_position;
        $requester->req_from=$req_from;
        $requester->req_date=$date_time;
        $requester->save();
        $req_id = $requester->id;
        $req_recid=Requester::where('id',$req_id)->select('req_recid')->first();
        return $req_recid->req_recid;
	}

    public static function defaultSave($req_recid,$req_email,$req_name,$req_branch,$req_position,$form_type,$due_expect_date,$ref,$subject,$ccy)
    {
        $dt = Carbon::now();
        $date_time = $dt->toDayDateTimeString();
        $req_from=1;
        $requester= Requester::firstOrNew(['req_recid'=>$req_recid]);
        $requester->req_recid=$req_recid;
        $requester->req_email=$req_email;
        $requester->req_name=$req_name;
        $requester->req_branch=$req_branch;
        $requester->req_position=$req_position;
        $requester->req_from=$req_recid;
        $requester->req_date=$date_time;
        $requester->due_expect_date=$due_expect_date;
        $requester->ref=$ref;
        $requester->subject=$subject;
        $requester->ccy=$ccy;
        $requester->save();


        $tasklist = Tasklist::firstOrNew(['req_recid'=>$req_recid]);
        $tasklist->req_recid=$req_recid;
        $tasklist->req_email=$req_email;
        $tasklist->req_name=$req_name;
        $tasklist->req_branch=$req_branch;
        $tasklist->req_position=$req_position;
        $tasklist->req_from=$req_from;
        $tasklist->req_type=$form_type;
        $tasklist->next_checker_group='1';
        $tasklist->next_checker_role=1;
        $tasklist->step_number='1';
        $tasklist->step_status=1;
        $tasklist->req_status='001';
        $tasklist->req_date=$date_time;   
        $tasklist->is_new_flow = 1; 
        $tasklist->save();
        // $checker_next_list = Groupid::where('group_id',$group_id_requestuser->group_id)->where('role_id',2)->get();
        // $checker_mail=[];
        // foreach ($checker_next_list as $key => $value) {
        //     array_push($checker_mail,$value->email);      
        // }

        $remark="Please kindly check";
        // $send_mail = new Sendemail();
        // $send_mail->sendEmail($req_recid,'submit','Lost Even Data',$checker_mail,$req_name,$req_branch,$req_position,$remark);

        return $req_recid;
    }

    public static function auditlogSave($req_recid, $req_email, $req_name, $req_branch, $req_position, $form_type, $activity_code, $comment, $doerRole, $doerAction)
    {
        $dt = Carbon::now();
        $date_time = $dt->toDayDateTimeString();
        $req_from=1;
        $auditlog = new Auditlog();
        $auditlog->req_recid            = $req_recid;
        $auditlog->doer_email           = $req_email;
        $auditlog->doer_name            = $req_name;
        $auditlog->doer_branch          = $req_branch;
        $auditlog->doer_position        = $req_position;
        $auditlog->activity_code        = $activity_code;
        $auditlog->activity_description = $comment;
        $auditlog->activity_form        = $form_type;
        $auditlog->activity_datetime    = $date_time;
        $auditlog->doer_role            = $doerRole;
        $auditlog->doer_action          = $doerAction;
        $auditlog->save();
    }
    public static function resubmitRequest($req_recid,$req_email,$req_name,$req_branch,$req_position,$form_type,$due_expect_date,$ref,$subject,$ccy,$email)
    {
        $tasklist_cond=Tasklist::where('req_recid',$req_recid)->where('next_checker_group',$email)->first();
        if(!empty($tasklist_cond)){
             $dt = Carbon::now();
            $date_time = $dt->toDayDateTimeString();

            Requester::where('req_recid',$req_recid)->update(['req_from'=>$req_from]);
            $requester_detail=Requester::where('req_recid',$req_recid)->first();
            $group_id_requestuser = Groupid::where('email',$email)->where('role_id',1)->first();
            $tasklist = Tasklist::firstOrNew(['req_recid'=>$req_recid]);
            $tasklist->req_from=$req_from;
            $tasklist->req_tpye=$form_type;
            $tasklist->next_checker_group=$group_id_requestuser->group_id;
            $tasklist->next_checker_role=1;
            $tasklist->step_number='step1';
            $tasklist->step_status=1;
            $tasklist->req_status=1;
            $tasklist->req_date=$date_time;    
            $tasklist->save();

            $auditlog = new Auditlog();
            $auditlog->req_recid=$req_recid;
            $auditlog->doer_email=$email;
            $auditlog->doer_name=$requester_detail->req_name;
            $auditlog->doer_branch=$requester_detail->req_branch;
            $auditlog->doer_position=$requester_detail->req_position;
            $auditlog->activity_code='A005';
            $auditlog->activity_description='Resubmitted';
            $auditlog->activity_form=$form_type;
            $auditlog->activity_datetime=$date_time;
            $auditlog->save();

            $send_mail= new Sendemail();
            $send_mail->sendEmail($folder_name,'request',$request_name,$checker_mail,$reqby_name,$reqby_division,$reqby_department,$custmer_cif,$custmer_name,$card_type,$card_status,$remark,$checker_mail_cc);




            return 'success';
        }else{
            return 'fail';
        }
        
    }
}