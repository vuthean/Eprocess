<?php

namespace App\Imports;

use App\Models\Budgetcode;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BudgetcodeImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {   
        return new Budgetcode([
            // 'branch_code' => $row['branch_code'],
            'budget_code' => $row[0],
            'budget_item' => $row[1],
            'budget_owner' => $row[2],
            'budget_name' => $row[3],
            'total' => $row[4],
            'procurement' => '0',
            'temp' => $row[5],
            'temp_payment' => $row[6],
            'remaining' => $row[7],
            'payment' => '0',
            'payment_remaining' => $row[8],
            'year' => date("Y")
        ]);
    }
}
