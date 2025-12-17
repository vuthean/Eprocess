<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\BlockForm;
use Illuminate\Support\Facades\Session;

class BlockFormController extends Controller
{
   public function index(Request $request){
        $block_date = BlockForm::all();
        return view('block_form.block_form',compact('block_date'));
   }
   public function store(Request $req){
        try {
            $dt = Carbon::now();
            $date_time = $dt->toDayDateTimeString();
            $block_date = $req->block_date;
            $same_date = BlockForm::where('block_day',$block_date)->select('block_day')->count();
            if($same_date>0){
                Session::flash('error', 'This day have in list, Please check again!');
                return redirect()->back();
            }

            BlockForm::create([
                'block_day'            => $block_date,
                'block_date'           => $date_time
            ]);
            
            Session::flash('success', 'request was save');

            \DB::commit();
            return redirect()->back();
        } catch (\Exception $e) {
            \DB::rollback();
            Session::flash('error', 'Please Contact Admin');
            return redirect()->back();
        }
           
   }
   public function update(Request $req){
        try {
            $submit = $req->submit;
            $block_id = $req->block_day_value;
            $block_date = $req->block_date;
            if($submit !== 'delete'){
                $block_form = BlockForm::firstWhere('id', $block_id);
                if($block_form){
                    $block_form->update([
                        'block_day'            => $block_date
                    ]);
                }
            }else{
                $block_form = BlockForm::findOrFail($block_id);

                $block_form->delete();
            }
            
            \DB::commit();
            Session::flash('success', 'request was save');
            return redirect()->back();
        }catch (\Exception $e) {
            \DB::rollback();
            Session::flash('error', 'Please Contact Admin');
            return redirect()->back();
        }
   }
}
