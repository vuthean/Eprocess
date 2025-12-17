<?php

namespace App\Exports;
use App\Models\Groupid;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use DB;

class GroupSpesialExport implements FromCollection,WithHeadings,WithColumnFormatting,WithCustomStartCell,WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $department = Groupid::select('login_id',
                                    'groupid.email',
                                    'users.fullname',
                                    'groupid.group_id',
                                    'groupdescription.group_name',
                                    'role_id',
                                    'budget',
                                     DB::raw('(CASE WHEN groupid.status = 1 THEN "Active" ELSE "Inactive" END) AS is_user')
                                    )
                                    ->join('groupdescription','groupdescription.group_id','groupid.group_id')
                                    ->join('users','users.email','groupid.email')
                                    ->where('special','Y')
                                    ->get(); 
        return $department;
    }
    public function headings(): array
    {
        return [
            'Login Id',
            'Email',
            'Full Name User',
            'Group Id',
            'Group Name',
            'Role Id',
            'Budget',



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
