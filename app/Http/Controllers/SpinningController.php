<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
class SpinningController extends Controller
{
    public function index()
    {
    	return view('spin.spin');
    }
    public function getTicket()
    {
    	$random=DB::table('ticket')
    			->where('status','I')
    			->inRandomOrder()
    			->first();
    	
    	return response()->json($random);
    }
}
