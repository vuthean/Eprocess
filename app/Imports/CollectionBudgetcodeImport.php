<?php

namespace App\Imports;

use App\Models\Budgetcode;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Carbon\Carbon;

class CollectionBudgetcodeImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        $oldData = Budgetcode::all();
        $newData = [];
        foreach ($rows as $row) {
            $data = [
                'budget_code' => $row[0],
                'budget_item' => $row[1],
                'budget_owner' => $row[2],
                'budget_name' => $row[3],
                'total' => number_format($row[4], 2, '.', ''),
                'procurement' => '0',
                'temp' => number_format($row[5], 2, '.', ''),
                'temp_payment' =>  number_format($row[6], 2, '.', ''),
                'remaining' =>  number_format($row[7], 2, '.', ''),
                'payment' => '0',
                'payment_remaining' => number_format($row[8], 2, '.', ''),
                'year' => date("Y")
            ];
            $budget = Budgetcode::firstWhere('budget_code', $row[0]);
            if ($budget) {
                $modify      = 'Y';
                $modify_by   = \Illuminate\Support\Facades\Auth::user()->email;
                $modify_date = Carbon::now()->toDateTimeString();
                $data['modify'] = $modify;
                $data['modify_by'] = $modify_by;
                $data['modify_date'] = $modify_date;
                $budget->update($data);
               
            } else {
                $budget = Budgetcode::create($data);
            }
            array_push($newData, $budget);
        }
        if ($oldData->isNotEmpty()) {
            $budgets = new Budgetcode();
            $budgets->logCollectionData($newData, $oldData);
        }
    }
}
