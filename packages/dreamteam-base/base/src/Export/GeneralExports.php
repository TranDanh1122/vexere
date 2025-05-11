<?php 
namespace DreamTeam\Base\Export;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class GeneralExports implements WithHeadings, WithTitle
{
    protected $data = [];

    // Xuất excel chung cho bản 3.1
    /* Biến $data có dạng
        $data = [
            'file_name' => 'Tên file',
            'fields' => [
                các bản ghi đầu tiên trong file
            ],
            'data' => [
                toàn bộ các bản ghi được query ra và lấy giá trị tương ứng với các fields
            ]
        ];
    */

    function __construct($data) {
        $this->data = $data;
    }

    public function title(): string
    {
        return $this->data['file_name'];
    }

    public function headings(): array
    {
        $data_export = [];
        if (isset($this->data['fields'])) {
            $data_export[] = $this->data['fields'] ?? [];
        }
        if (isset($this->data['data'])) {
            foreach ($this->data['data'] as $key => $value) {
                $data_export[] = $value;
            }
        }
        return $data_export;
    }
}
