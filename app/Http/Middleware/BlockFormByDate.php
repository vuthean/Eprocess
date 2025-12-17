<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\BlockForm;
use Carbon\Carbon;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Session;


class BlockFormByDate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        $dt = Carbon::now();
        $date_time = $dt->format('d');
        $block_day = BlockForm::where('block_day',$date_time)->count();
        if($block_day>=1){
            Session::flash('error', "Today this form was blocked so you can't request. Please wait Admin unblock, Thanks.");
            return redirect(RouteServiceProvider::HOME);
        }
        return $next($request);
    }
}
