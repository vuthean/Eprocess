<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Groupid;
use App\Models\Groupdescription;
use Session;
use Hash;
class LoginController extends Controller
{
    public $successStatus = 200;
	public $failStatus = 403;

	public function signIn(Request $request)
    {    	
    	$username=$request->email.'@princebank.com.kh';
		$password=$request->password;
		if (\Auth::attempt(['email' => $username,'password' => $password])){
			$email=Auth::user()->email;
	        $admin=Groupid::where(['email'=>$email,'group_id'=>'GROUP_ADMIN'])->first();
	        if(!empty($admin)){
	            $is_admin="1";
	        }else{
	            $is_admin="0";
	        }
	        Session::put('is_admin',$is_admin);
		    return redirect()->route('dashboard');
		}else{
			return redirect()->back();
		}
		// $condition=$request->condition;
		// if(!empty($condition)){
			
		// }else{
		// 	$server = '10.80.80.113';	
		// 	$domain = '@usct.local';
		// 	// $server = '192.168.1.65';	
		// 	// $domain = '@princeplc.com.kh';
		// 	$port       = 389;

		// 	$ldap_connection = ldap_connect($server, $port);

		// 	if (! $ldap_connection)
		// 	{
		// 	    $fail['status']='Fail to login';
		// 		return response()->json(['response' => $fail], $this->failStatus);
		// 	}
		// 	$ldap_dn = "DC=usct,DC=local";
		// 	// $ldap_dn = "DC=princeplc,DC=com,DC=kh";
		// 	// Help talking to AD
		// 	ldap_set_option($ldap_connection, LDAP_OPT_PROTOCOL_VERSION, 3);
		// 	ldap_set_option($ldap_connection, LDAP_OPT_REFERRALS, 0);

		// 	$ldap_bind = @ldap_bind($ldap_connection, $username.$domain, $password);

		// 	if (! $ldap_bind)
		// 	{		    
		// 	    $fail['status']='Fail to login';
		// 		return response()->json(['response' => $fail], $this->failStatus);
		// 	}

		// 	if($ldap_bind = @ldap_bind($ldap_connection, $username.$domain, $password)){			
		// 		$search_filter = '(&(objectCategory=person)(samaccountname=*)(objectCategory=user)(!(userAccountControl=514))'."(sAMAccountName=".$username.")".')';			
		// 	    $attributes = array();
		// 	    $attributes[] = 'givenname';
		// 	    $attributes[] = 'mail';
		// 	    $attributes[] = 'samaccountname';
		// 	    $attributes[] = 'sn';
		// 	    $attributes[] = 'title';
		// 	    $attributes[] = 'department';
		// 	    $attributes[] = 'dn';	
		// 		$attributes[] = 'company';
		// 		$attributes[] = 'mobile';
					
		// 			// mobile	    		
		// 		$result = ldap_search($ldap_connection, $ldap_dn, $search_filter, $attributes) or exit("Unable to search LDAP server");

		// 		$entries = ldap_get_entries($ldap_connection, $result);
		// 		for ($x=0; $x<$entries['count']; $x++){
		//             if (!empty($entries[$x]['givenname'][0]) &&
		//                  !empty($entries[$x]['mail'][0]) &&
		//                  !empty($entries[$x]['samaccountname'][0]) &&
		//                  // !empty($entries[$x]['sn'][0]) &&
		//                  // !empty($entries[$x]['department'][0]) &&
		//                  // !empty($entries[$x]['title'][0]) &&
		//                  'Shop' !== $entries[$x]['sn'][0] &&
		//                  'Account' !== $entries[$x]['sn'][0]){

		//                 $ad_users[] = array(
		//                 	'login_name'=>strtoupper(trim($entries[$x]['samaccountname'][0])),
		//                 	'email' => strtolower(trim($entries[$x]['mail'][0])),
		//                 	'first_name' => trim($entries[$x]['givenname'][0]),
		//                 	'last_name' => trim($entries[$x]['sn'][0]),
		//                 	'department_name' => trim($entries[$x]['department'][0]),
		//                 	'position' => trim($entries[$x]['title'][0]),
		//                 	// 'mobile' => trim($entries[$x]['mobile'][0]),
		//                 	// 'division'=>trim($entries[$x]['company'][0])	                		                	
		//                 );
		//             }
		//         }
		//         // return dd($ad_users);

		// 		$login_name=$ad_users[0]['login_name'];
		// 		$email=$ad_users[0]['email'];			
		// 		$first_name=$ad_users[0]['first_name'];
		// 		$last_name=$ad_users[0]['last_name'];
		// 		// $division=$ad_users[0]['division'];
		// 		// $mobile=$ad_users[0]['mobile'];	
		// 		$department_name=$ad_users[0]['department_name'];
		// 		$position=$ad_users[0]['position'];			
		// 		$dt = Carbon::now();
	 //            $date_time_now = $dt->toDayDateTimeString();
		// 		// verify user login in AD have in our DB ornot
		// 		$user_indb=User::where('email',$email)->first();

		// 		// Find group ID 
		// 		$group_id_condition = Groupid::where('login_id',$login_name)->first();
		// 		if(!empty($group_id_condition)){
		// 			$group_id=$group_id_condition->group_id;
		// 		}else{
		// 			$group_id="group1";
		// 		}

		// 		// if didn't have we will insert to our DB
		// 		if(empty($user_indb)){				
		// 			$user = new User();
		// 			$user->password = Hash::make('123');
		// 			$user->firstname = $first_name;
		// 			$user->lastname = $last_name;
		// 			$user->email = $email;
		// 			$user->userid = $login_name;
		// 			// $user->division = $division;
		// 			$user->department = $department_name;
		// 			$user->position = $position;
		// 			// $user->mobile = $mobile;
		// 			$user->status = "A";
		// 			$user->groupid = $group_id;
		// 			$user->lastlogin = $date_time_now;				
		// 			$user->save();
		// 		}
				
		// 		$success['status']='Success';
		// 		$success['login_name']=$login_name;
		// 		$success['email']=$email;
		// 		$success['first_name']=$first_name;
		// 		$success['last_name']=$last_name;
		// 		$success['department_name']=$department_name;
		// 		$success['position']=$position;			
		// 		// $success['division']=$division;
		// 		// $success['mobile']=$mobile;
		// 		$success['group_id']=$group_id;		
		// 		return response()->json(['data' => $success], $this->successStatus);
		// 	}
		// 	ldap_unbind($ldap_connection);			
		// }				
    }
 //    public function logout(Request $request) {
	// 	\Auth::logout();
	// 	$request->session()->forget('is_admin');
	// 	$request->session()->forget('flowchange');
		
	// 	return redirect('/');
	// }
}
