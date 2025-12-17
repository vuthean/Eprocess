<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Tasklist;
use Auth;
use App\Models\Groupid;
use App\Models\Paymentbody;
use App\Models\User;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        view()->composer('layouts.master', function ($view) {
            $email=Auth::user()->email;
            $result=Tasklist::whereIn('next_checker_group', [$email,Auth::user()->group_id])
                        // ->where('next_checker_role',$value->role_id)
                        ->orwhere('tasklist.req_email', $email)
                        ->where('tasklist.req_status', '006')
                        // ->whereIn('req_status',[1,2,3])
                        // ->select('req_recid','req_name','req_branch','req_position','req_from','req_tpye','req_date')
                        ->get();
            $count_record=Tasklist::where('req_status', '<>', '001')->get();
            $admin=Groupid::where(['email'=>$email,'group_id'=>'GROUP_ADMIN'])->first();
            if (!empty($admin)) {
                $is_admin="1";
            } else {
                $is_admin="0";
            }

            
            /**@var User $currentUser*/
            $currentUser = FacadesAuth::user();

            /** check for payment record */
            $isAllowForViewPaymentRecord = $currentUser->isAllowToViewPaymentRecord();
            $totalPaymentRecords = 0;
            // if ($isAllowForViewPaymentRecord) {
            //     $paymentRecords = $currentUser->getPaymentRecord();
            //     $totalPaymentRecords = collect($paymentRecords)->count();
            // }
            Session::put('is_allow_to_view_payment_record',  $isAllowForViewPaymentRecord);

            /** check for advance record */
            $totalAdvanceRecord   = 0;
            $isAllowAdvanceRecord = $currentUser->isAllowToAccessAdvanceRecord();
            if($isAllowAdvanceRecord){
                // $totalAdvanceRecord = $currentUser->countTotalAdvanceRecord();
                Session::put('is_allow_to_view_advance_record',  true);
            }else{
                Session::put('is_allow_to_view_advance_record',  false);
            }



            $view->with(array(
                'data'         => $result,
                'is_admin'     => $is_admin,
                'count_record' => $count_record,
                'is_allow_to_view_payment_record' => $isAllowForViewPaymentRecord,
                'total_payment_record'            => $totalPaymentRecords,
                'total_advance_record'            => $totalAdvanceRecord,
            ));
        });
        $ip = parse_url(url('/'), PHP_URL_HOST);

        $isIP = (bool)ip2long($ip);

        if(config('app.env') == 'prd' && $isIP == false || config('app.env') == 'pre-production' && $isIP == false || config('app.env') == 'uat' && $isIP == false || config('app.env') == 'sit' || config('app.env') == 'local' && $isIP == false) {
            URL::forceScheme('https');
        }
    }
}
