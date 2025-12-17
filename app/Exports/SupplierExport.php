<?php

namespace App\Exports;

use App\Models\Supplier;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SupplierExport implements FromCollection,WithHeadings,WithColumnFormatting,WithCustomStartCell,WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $department = Supplier::select('code',
                                        'first_name_eng',
                                        'last_name_eng',
                                        'first_name_kh',
                                        'last_name_kh',
                                        'full_name_eng',
                                        'full_name_kh',
                                        'gender',
                                        'date_of_birth',
                                        'race',
                                        'nationality',
                                        'id_card_number',
                                        'passport_number',
                                        'phone_number',
                                        'email',
                                        'address',
                                        'type',
                                        'acct_name',
                                        'acct_number',
                                        'acct_currency',
                                        'pay_to_bank')->where('code','!=','')->get(); 
        return $department;
    }
    public function headings(): array
    {
        return [
            'Code',
            'First_Name_Eng',
            'Last_Name_Eng',
            'First_Name_Kh',
            'Last_Name_Kh',
            'Full_Name_Eng',
            'Full_Name_Kh',
            'Gender',
            'Date_Of_Birth',
            'Race',
            'Nationality',
            'Id_Card_Number',
            'Passport_Number',
            'Phone_Number',
            'Email',
            'Address',
            'Type',
            'Acct_Name',
            'Acct_Number',
            'Acct_Currency',
            'Pay_To_Bank',
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
