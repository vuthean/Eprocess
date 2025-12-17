<?php
namespace App\Myclass;

// use App\Models\Led;
use Carbon\Carbon;
use App\Models\Requester;
use App\Models\Procurement;
use App\Models\Procurementbody;
use App\Models\Procurementfooter;
use App\Models\Procurementbottom;
use App\Models\Budgetcode;
use App\Models\Branchcode;
use App\Models\Documentupload;
use App\Models\Auditlog;
use App\Models\Tasklist;
use App\Models\Budgethistory;
class Deduct
{

    public static function requesterSave($req_email, $req_name, $req_branch, $req_position, $req_from)
    {
        for ($i = 0; $i < count($unit_price); $i++) {

            if ($ccy == 'KHR') {
                $conversion = 4000;
            } else {
                $conversion = 1;
            }
            // return dd($unit_price);
            $total_1 = $qty[$i] * $unit_price[$i] / $conversion;
            $unitprice = $unit_price[$i] / $conversion;
            // if($conversion=4000)
            // {
            //     $total_khr = $qty[$i] * $unit_price[$i];
            // }

            // return dd($total_1);

            $budget = Budgetcode::where('budget_code', $budget_code[$i])->first();
            $alternative_budget = Budgetcode::where('budget_code', $alternativebudget_code[$i])->first();
            if (!empty($alternative_budget)) {
                $alternative_budget_remain = $alternative_budget->temp;
            } else {
                $alternative_budget_remain = 0;
            }

            if ($budget_code[$i] == $alternativebudget_code[$i]) {
                $alternative_budget = null;
                $alternative_budget_remain = 0;
            }
            // return dd($alternative_budget);
            $budget_remain = $budget->temp;

            $withinbudget_cond = $budget_remain + $alternative_budget_remain - $total_1;
            // return dd($withinbudget_cond);
            if ($withinbudget_cond >= 0) {
                $withinbudget = "Y";
                // if(empty($alternativebudget_code[$i])){
                if (empty($alternative_budget)) {

                    $budget_spent = $total_1;
                    $budget_al_spent = 0;
                } else {
                    if ($budget_remain > $total_1) {
                        $budget_spent = $budget_remain - $total_1;
                        $budget_al_spent = 0;
                    } else {
                        $budget_spent = $budget_remain;
                        $budget_al_spent = $total_1 - $budget_spent;
                    }
                }

            } else {
                $withinbudget = "N";

                // if(empty($alternativebudget_code[$i])){
                if (empty($alternative_budget)) {
                    $current_budget_proc = Budgetcode::where('budget_code', $budget_code[$i])->first();
                    $budget_spent = $current_budget_proc->temp;
                    $budget_al_spent = 0;
                } else {
                    if ($budget_remain > $total_1) {
                        $budget_spent = $budget_remain - $total_1;
                        $budget_al_spent = 0;
                    } else {
                        $budget_spent = $budget_remain;
                        $budget_al_spent = $total_1 - $budget_spent;
                    }
                }
            }

            array_push($total_2, $total_1);
            $sum += $total_1;

            $unit_price_khr = $unitprice * 4000;
            $total_estimate_khr = $qty[$i] * $unit_price_khr;

            $procurementbody = new Procurementbody();
            $procurementbody->req_recid = $req_recid;
            $procurementbody->description = $description[$i];
            $procurementbody->br_dep_code = $br_dep_code[$i];
            $procurementbody->budget_code = $budget_code[$i];
            $procurementbody->alternativebudget_code = $alternativebudget_code[$i];

            $procurementbody->unit = $unit[$i];
            $procurementbody->qty = $qty[$i];
            $procurementbody->unit_price = $unitprice;
            $procurementbody->total_estimate = $total_1;
            $procurementbody->delivery_date = $delivery_date[$i];
            $procurementbody->budget_use = $budget_spent;
            $procurementbody->alternative_use = $budget_al_spent;
            $procurementbody->total = $total_1;
            //  if($conversion=4000)
            // {
            $procurementbody->unit_price_khr = $unit_price_khr;
            $procurementbody->total_estimate_khr = $total_estimate_khr;
            $procurementbody->total_khr = $total_estimate_khr;
            // }
            // $procurementbody->total=$sum;
            $procurementbody->within_budget_code = $withinbudget;
            $procurementbody->save();
            $remainin = Budgetcode::where('budget_code', $budget_code[$i])->first();
            if ($total_1 > $remainin->temp) {
                $procurement_budget = ['temp' => '0'];
                // $budget_hsitory_arr=['budget_amount_use'=>];
            } else {
                $procurement_remaining = $remainin->temp - $total_1;
                $procurement_budget = ['temp' => $procurement_remaining];
            }

            Budgetcode::where('budget_code', $budget_code[$i])->update($procurement_budget);

            $remainin_al = Budgetcode::where('budget_code', $alternativebudget_code[$i])->first();
            // return
            if (!empty($remainin_al)) {
                if ($total_1 > $remainin_al->temp + $remainin->temp) {
                    $procurement_budget_al = ['temp' => '0'];
                } else {
                    $procurement_remaining_al = $remainin_al->temp - ($total_1 - $remainin->temp);
                    $procurement_budget_al = ['temp' => $procurement_remaining_al];
                }
                Budgetcode::where('budget_code', $alternativebudget_code[$i])->update($procurement_budget_al);

            }

            $budget_hsitory = new Budgethistory();
            $budget_hsitory->req_recid = $req_recid;
            $budget_hsitory->budget_code = $budget_code[$i];
            $budget_hsitory->alternative_budget_code = $alternativebudget_code[$i];
            $budget_hsitory->budget_amount_use = $budget_spent;
            $budget_hsitory->alternative_amount_use = $budget_al_spent;
            $budget_hsitory->save();

        }
    }
}
