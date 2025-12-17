<?php

namespace App\Exports;
use App\Models\Budgetcode;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use DateTime;
use Response;
use Session;


class BudgetCodeExport implements FromView,WithHeadings,WithCustomStartCell,WithStyles
{
    /**
    * @return \Illuminate\Support\FromCollection
    */
    
    public function view(): View
    {
        $budgetCode = Budgetcode::select('budget_code','budget_item','users.fullname','total','remaining','payment_remaining')
                                           ->join('users','users.email','budgetdetail.budget_owner')
                                           ->where('budget_code','!=','')->get(); 
        return view('export.budget-code',['budgetCode'=>$budgetCode]);
    }
    public function headings(): array
    {
        return [
            'Budget Code',
            'Budget Item',
            'Budget Owner',
            'Total Budget',
            'Remaining  Procurement',
            'Remaining  Payment'

        ];
    }
   
    public function startCell(): string
    {
        return 'B3';
    }
    public function styles(Worksheet $sheet)
    {
        
        return [
            '1'    => ['font' => ['bold' => true]],
            '1'  => ['font' => ['size' => 14]],
        ];
    }
}
