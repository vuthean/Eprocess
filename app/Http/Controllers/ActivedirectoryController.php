<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Groupid;
use App\Models\Usermgt;
use App\Models\ServerAd;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ActivedirectoryController extends Controller
{
    public function login(Request $request)
    {
        $server = ServerAd::where('status', 1)->first();
        $username = $request->email;
        $password = $request->password;
        $now = Carbon::now();

        /*** check invironment for this system if production, it will excluded */
        if (App::environment('local', 'dev', 'uat', 'staging')) {
            if (\Auth::attempt(['email' => $username . '@princebank.com.kh', 'password' => $password])) {
                $email = \Auth::user()->email;

                $authorize = Groupid::where('email', $email)->first();
                if (empty($authorize)) {
                    \Auth::logout();
                    Session::flash('error', 'Unauthorize user! please contact Administrator');
                    return redirect()->back();
                }
                User::where('email', $email)->update(['lastlogin' => $now]);
                $admin = Groupid::where(['email' => $email, 'group_id' => 'GROUP_ADMIN'], ['status', 1])->first();
                $procurement = Groupid::where(['email' => $email, 'group_id' => 'GROUP_PROCUREMENT'], ['status', 1])->first();
                $treasury = Groupid::where(['email' => $email, 'group_id' => 'GROUP_TREASURY'], ['status', 1])->first();
                $markating = Groupid::where(['email' => $email, 'group_id' => 'GROUP_MARKETING'], ['status', 1])->first();
                $admin_team = Groupid::where(['email' => $email, 'group_id' => 'GROUP_ADMINISTRATION'], ['status', 1])->first();
                $PLD_team = Groupid::where(['email' => $email, 'group_id' => 'GROUP_LEARNING_PEOPLE'], ['status', 1])->first();
                $accounting_team = Groupid::where(['email' => $email, 'group_id' => 'GROUP_ACCOUNTING'], ['status', 1])->first();
                $finance_team = Groupid::where(['email' => $email, 'group_id' => 'GROUP_FINANCE'], ['status', 1])->first();
                $infra_team = Groupid::where(['email' => $email, 'group_id' => 'GROUP_INFRA'], ['status', 1])->first();
                $alternative_team = Groupid::where(['email' => $email, 'group_id' => 'GROUP_ACD'], ['status', 1])->first();

                if (!empty($procurement)) {
                    $is_procurement = '1';
                } else {
                    $is_procurement = '0';
                }
                if (!empty($admin)) {
                    $is_admin = '1';
                } else {
                    $is_admin = '0';
                }
                if (!empty($treasury)) {
                    $is_treasury  = '1';
                } else {
                    $is_treasury  = '0';
                }
                if (!empty($markating)) {
                    $is_markating  = '1';
                } else {
                    $is_markating  = '0';
                }
                if (!empty($admin_team)) {
                    $is_admin_team  = '1';
                } else {
                    $is_admin_team  = '0';
                }
                if (!empty($PLD_team)) {
                    $PLD_team  = '1';
                } else {
                    $PLD_team  = '0';
                }
                if (!empty($accounting_team)) {
                    $accounting_team  = '1';
                } else {
                    $accounting_team  = '0';
                }
                if (!empty($finance_team)) {
                    $finance_team  = '1';
                } else {
                    $finance_team  = '0';
                }
                if (!empty($infra_team)) {
                    $infra_team  = '1';
                } else {
                    $infra_team  = '0';
                }
                if (!empty($alternative_team)) {
                    $alternative_team  = '1';
                } else {
                    $alternative_team  = '0';
                }
                Session::put('is_admin', $is_admin);
                Session::put('is_procurement', $is_procurement);
                Session::put('is_treasury', $is_treasury);
                Session::put('is_markating', $is_markating);
                Session::put('PLD_team', $PLD_team);
                Session::put('is_admin_team', $is_admin_team);
                Session::put('is_accounting_team', $accounting_team);
                Session::put('is_finance_team', $finance_team);
                Session::put('is_infra_team', $infra_team);
                Session::put('is_alternative_team', $alternative_team);

                /** check is current user allow to see procurement record */
                /**@var User $user*/
                $user = Auth::user();

                if ($user->isAllowToAccessProcurementRecord()) {
                    Session::put('is_allow_procurement', true);
                } else {
                    Session::put('is_allow_procurement', false);
                }

                if ($user->isAllowToViewPaymentRecord()) {
                    Session::put('is_allow_to_view_payment_record', true);
                } else {
                    Session::put('is_allow_to_view_payment_record', false);
                }

                if ($user->isAllowToAccessAdvanceRecord()) {
                    Session::put('is_allow_to_view_advance_record', true);
                } else {
                    Session::put('is_allow_to_view_advance_record', false);
                }

                return redirect()->route('dashboard');
            } else {
                return redirect()->back();
            }
        }



        // $server = '192.168.1.85';
        // $domain = '@princeplc.com.kh';
        // $server = '10.1.1.76';
        // $server = '192.168.21.105';
        try {
            $server_dc = $server->ip;
            $server_dr = $server->dr_ip;
            $princeplc  = '@princeplc.com.kh';
            $princebank = '@princebank.com.kh';
            $port = "389";
            $ldap_connection = ldap_connect($server_dc, $port);

            $ldap_connection_princebank = ldap_connect($server_dc, $port);

            if (!$ldap_connection or !$ldap_connection_princebank) {
                Session::flash('error', 'Server connection fail');
                return redirect()->back();
            }
            $ldap_dn = 'DC=princeplc,DC=com,DC=kh';
            ldap_set_option($ldap_connection, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($ldap_connection, LDAP_OPT_REFERRALS, 0);
            ldap_set_option($ldap_connection, LDAP_OPT_TIMELIMIT, 60);

            // for prince bank
            ldap_set_option($ldap_connection_princebank, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($ldap_connection_princebank, LDAP_OPT_REFERRALS, 0);
            ldap_set_option($ldap_connection_princebank, LDAP_OPT_TIMELIMIT, 60);

            // $ldap_bind = @ldap_bind($ldap_connection, $username . $domain, $password);
            $princeplc_domain  = @ldap_bind($ldap_connection, $username . $princeplc, $password);
            $princebank_domain = @ldap_bind($ldap_connection_princebank, $username . $princebank, $password);

            // if can not bind with DUO DC
            if (!$princeplc_domain && !$princebank_domain) {
                Log::info("can not bind to server duo dc.");
                $ldap_connection = ldap_connect($server_dr, $port);
                $ldap_connection_princebank = ldap_connect($server_dr, $port);
                // for dr
                ldap_set_option($ldap_connection, LDAP_OPT_PROTOCOL_VERSION, 3);
                ldap_set_option($ldap_connection, LDAP_OPT_REFERRALS, 0);

                ldap_set_option($ldap_connection_princebank, LDAP_OPT_PROTOCOL_VERSION, 3);
                ldap_set_option($ldap_connection_princebank, LDAP_OPT_REFERRALS, 0);
                $princeplc_domain  = @ldap_bind($ldap_connection, $username . $princeplc, $password);
                $princebank_domain = @ldap_bind($ldap_connection_princebank, $username . $princebank, $password);
                if (!$princeplc_domain) {
                    Log::info("cann't bind to server duo dr.");
                }
            } else {
                Log::info("can bind to server duo dc.");
            }

            // if (!$ldap_bind) {
            if ($princeplc_domain == false and $princebank_domain == false) {
                Session::flash('error', 'Wrong username or password');
                return redirect()->back();
            }

            // if ($ldap_bind = @ldap_bind($ldap_connection, $username . $domain, $password)) {
            if ($princeplc_domain = @ldap_bind($ldap_connection, $username . $princeplc, $password) or ($princebank_domain = @ldap_bind($ldap_connection_princebank, $username . $princebank, $password))) {
                $search_filter = '(&(objectCategory=person)(samaccountname=*)(objectCategory=user)(!(userAccountControl=514))' . '(sAMAccountName=' . $username . ')' . ')';
                $attributes = [];
                $attributes[] = 'givenname';
                $attributes[] = 'mail';
                $attributes[] = 'samaccountname';
                $attributes[] = 'sn';
                $attributes[] = 'title';
                $attributes[] = 'department';
                $attributes[] = 'dn';
                $attributes[] = 'whencreated';
                $attributes[] = 'mobile';
                $entries = '';
                if (!$ldap_connection) {
                    $result = ldap_search($ldap_connection_princebank, $ldap_dn, $search_filter, $attributes) or exit('Unable to search LDAP server');
                    $entries = ldap_get_entries($ldap_connection_princebank, $result);
                } else {
                    $result = ldap_search($ldap_connection, $ldap_dn, $search_filter, $attributes) or exit('Unable to search LDAP server');
                    $entries = ldap_get_entries($ldap_connection, $result);
                }

                for ($x = 0; $x < $entries['count']; $x++) {
                    if (
                        !empty($entries[$x]['givenname'][0]) &&
                        !empty($entries[$x]['mail'][0]) &&
                        !empty($entries[$x]['samaccountname'][0]) &&
                        !empty($entries[$x]['sn'][0]) &&

                        !empty($entries[$x]['title'][0]) &&
                        'Shop' !== $entries[$x]['sn'][0] &&
                        'Account' !== $entries[$x]['sn'][0]
                    ) {
                        if (!empty($entries[$x]['department'][0])) {
                            $department_s = $entries[$x]['department'][0];
                        } else {
                            $department_s = 'N/A';
                        }

                        if (!empty($entries[$x]['mobile'][0])) {
                            $mobile_s = $entries[$x]['mobile'][0];
                        } else {
                            $mobile_s = 'N/A';
                        }
                        $ad_users[] = [
                            'login_name' => strtoupper(trim($entries[$x]['samaccountname'][0])),
                            'email' => strtolower(trim($entries[$x]['mail'][0])),
                            'first_name' => trim($entries[$x]['givenname'][0]),
                            'last_name' => trim($entries[$x]['sn'][0]),
                            'mobile' => $mobile_s,
                            'department_name' => $department_s,
                            'position' => trim($entries[$x]['title'][0]),
                            'whencreated' => trim($entries[$x]['whencreated'][0]),
                        ];
                    }
                }

                $login_name = $ad_users[0]['login_name'];
                $email = $ad_users[0]['email'];
                $first_name = $ad_users[0]['first_name'];
                $last_name = $ad_users[0]['last_name'];
                $department_name = $ad_users[0]['department_name'];
                $position = $ad_users[0]['position'];
                $mobile = $ad_users[0]['mobile'];
                $user_in_db = \DB::table('users')
                    ->where('email', $email)
                    ->select('email')
                    ->first();

                if (empty($user_in_db)) {
                    $newuser = new User();
                    $newuser->firstname = $last_name;
                    $newuser->lastname = $first_name;
                    $newuser->fullname = $last_name . ' ' . $first_name;
                    $newuser->email = $email;
                    $newuser->userid = $login_name;
                    $newuser->division = $department_name;
                    $newuser->department = $department_name;
                    $newuser->position = $position;
                    $newuser->mobile = $mobile;
                    $newuser->status = 'A';
                    $newuser->password = \Hash::make('Hello@123');
                    $newuser->lastlogin = $now;
                    $newuser->save();

                    $newusermgt = new Usermgt();
                    $newusermgt->firstname = $last_name;
                    $newusermgt->lastname = $first_name;
                    $newusermgt->fullname = $last_name . ' ' . $first_name;
                    $newusermgt->email = $email;
                    $newusermgt->userid = $login_name;
                    $newusermgt->division = $department_name;
                    $newusermgt->department = $department_name;
                    $newusermgt->position = $position;
                    $newusermgt->mobile = $mobile;
                    $newusermgt->status = 'A';
                    $newusermgt->password = \Hash::make('Hello@123');
                    $newusermgt->save();
                } else {
                    $newuser = User::firstOrNew(['email' => $email]);
                    $newuser->firstname = $last_name;
                    $newuser->lastname = $first_name;
                    $newuser->fullname = $last_name . ' ' . $first_name;
                    $newuser->department = $department_name;
                    $newuser->position = $position;
                    $newuser->mobile = $mobile;
                    $newuser->lastlogin = $now;
                    $newuser->save();

                    $newusermgt = Usermgt::firstOrNew(['email' => $email]);
                    $newusermgt->firstname = $last_name;
                    $newusermgt->lastname = $first_name;
                    $newusermgt->fullname = $last_name . ' ' . $first_name;
                    $newusermgt->department = $department_name;
                    $newusermgt->position = $position;
                    $newusermgt->mobile = $mobile;
                    $newusermgt->save();
                }
                if (\Auth::attempt(['email' => $email, 'password' => 'Hello@123'])) {
                    $email = \Auth::user()->email;

                    $authorize = Groupid::where('email', $email)->first();
                    if (empty($authorize)) {
                        \Auth::logout();
                        Session::flash('error', 'Unauthorize user! please contact Administrator');
                        return redirect()->back();
                    }

                    $admin = Groupid::where(['email' => $email, 'group_id' => 'GROUP_ADMIN'])->first();
                    $procurement = Groupid::where(['email' => $email, 'group_id' => 'GROUP_PROCUREMENT'])->first();
                    $treasury = Groupid::where(['email' => $email, 'group_id' => 'GROUP_TREASURY'])->first();
                    $markating = Groupid::where(['email' => $email, 'group_id' => 'GROUP_MARKETING'])->first();
                    $admin_team = Groupid::where(['email' => $email, 'group_id' => 'GROUP_ADMINISTRATION'])->first();
                    $PLD_team = Groupid::where(['email' => $email, 'group_id' => 'GROUP_LEARNING_PEOPLE'])->first();
                    $accounting_team = Groupid::where(['email' => $email, 'group_id' => 'GROUP_ACCOUNTING'])->first();
                    $finance_team = Groupid::where(['email' => $email, 'group_id' => 'GROUP_FINANCE'])->first();
                    $infra_team = Groupid::where(['email' => $email, 'group_id' => 'GROUP_INFRA'], ['status', 1])->first();
                    $alternative_team = Groupid::where(['email' => $email, 'group_id' => 'GROUP_ACD'], ['status', 1])->first();

                    if (!empty($procurement)) {
                        $is_procurement = '1';
                    } else {
                        $is_procurement = '0';
                    }
                    if (!empty($admin)) {
                        $is_admin = '1';
                    } else {
                        $is_admin = '0';
                    }
                    if (!empty($treasury)) {
                        $is_treasury  = '1';
                    } else {
                        $is_treasury  = '0';
                    }
                    if (!empty($markating)) {
                        $is_markating  = '1';
                    } else {
                        $is_markating  = '0';
                    }
                    if (!empty($admin_team)) {
                        $is_admin_team  = '1';
                    } else {
                        $is_admin_team  = '0';
                    }
                    if (!empty($PLD_team)) {
                        $PLD_team  = '1';
                    } else {
                        $PLD_team  = '0';
                    }
                    if (!empty($accounting_team)) {
                        $accounting_team  = '1';
                    } else {
                        $accounting_team  = '0';
                    }
                    if (!empty($finance_team)) {
                        $finance_team  = '1';
                    } else {
                        $finance_team  = '0';
                    }
                    if (!empty($infra_team)) {
                        $infra_team  = '1';
                    } else {
                        $infra_team  = '0';
                    }
                    if (!empty($alternative_team)) {
                        $alternative_team  = '1';
                    } else {
                        $alternative_team  = '0';
                    }
                    Session::put('is_admin', $is_admin);
                    Session::put('is_procurement', $is_procurement);
                    Session::put('is_treasury', $is_treasury);
                    Session::put('is_markating', $is_markating);
                    Session::put('PLD_team', $PLD_team);
                    Session::put('is_admin_team', $is_admin_team);
                    Session::put('is_accounting_team', $accounting_team);
                    Session::put('is_finance_team', $finance_team);
                    Session::put('is_infra_team', $infra_team);
                    Session::put('is_alternative_team', $alternative_team);

                    /** check is current user allow to see procurement record */
                    /**@var User $user*/
                    $user = Auth::user();
                    if ($user->isAllowToAccessProcurementRecord()) {
                        Session::put('is_allow_procurement', true);
                    } else {
                        Session::put('is_allow_procurement', false);
                    }

                    if ($user->isAllowToAccessAdvanceRecord()) {
                        Session::put('is_allow_to_view_advance_record', true);
                    } else {
                        Session::put('is_allow_to_view_advance_record', false);
                    }

                    return redirect()->route('dashboard');
                } else {
                    return redirect()->back();
                }
            }
            if (!$ldap_connection) {
                ldap_unbind($ldap_connection_princebank);
            } else {
                ldap_unbind($ldap_connection);
            }
        } catch (\Exception $e) {
            Log::info($e);
        }
    }
}
