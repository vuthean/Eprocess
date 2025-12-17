<?php

namespace App\Exports;
use App\Models\RealBranch;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BranchExport implements FromCollection,WithHeadings,WithColumnFormatting,WithCustomStartCell,WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $department = RealBranch::select('code','name')->where('code','!=','')->get(); 
        return $department;
    }
    public function headings(): array
    {
        return [
            'Branch Code',
            'Branch Name',
        ];
    }
    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT,
        ];
    }
    public function startCell(): string
    {
        return 'B3';
    }
    public function styles(Worksheet $sheet)
    {
        
        return [
            '3'    => ['font' => ['bold' => true]],
            '3'  => ['font' => ['size' => 14]],
        ];
    }
}
