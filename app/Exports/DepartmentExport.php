<?php

namespace App\Exports;
use App\Models\User;
use App\Models\Branchcode;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DepartmentExport implements FromCollection,WithHeadings,WithColumnFormatting,WithCustomStartCell,WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $department = Branchcode::select('branch_code','branch_name')->where('branch_code','!=','')->get(); 
        return $department;
    }
    public function headings(): array
    {
        return [
            'Department Code',
            'Department Name',
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
