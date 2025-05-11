<?php
namespace DreamTeam\Base\Export;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ExcelToArray implements FromCollection, WithHeadingRow
{
    public function collection()
    {   
        return [];
    }

    public function headingRow(): int
    {
        return 1;
    }
}
