<?php

namespace DreamTeam\Base\Export;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultipleSheetExport implements FromArray, WithMultipleSheets
{
    protected $sheets;

    public function __construct(array $sheets)
    {
        $this->sheets = $sheets;
    }

    public function array(): array
    {
        return $this->sheets;
    }

    public function sheets(): array
    {
        $sheets = [];
        $arr_data = $this->sheets;
        unset($arr_data['file_name']);
        foreach ($arr_data as $key => $value) {
            $sheets[] = new GeneralExports($value);
        }

        return $sheets;
    }
}
