<?php

namespace App\Exports;

use App\Models\GeneralLedgerCode;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GlExport implements FromCollection,WithHeadings,WithColumnFormatting,WithCustomStartCell,WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $department = GeneralLedgerCode::select('account_number','account_name')->where('account_number','!=','')->get(); 
        return $department;
    }
    public function headings(): array
    {
        return [
            'Account Number',
            'Account Name',
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
