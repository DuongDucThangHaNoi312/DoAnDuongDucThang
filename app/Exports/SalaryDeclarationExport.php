<?php

namespace App\Exports;

use App\Define\Report;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class SalaryDeclarationExport implements WithMultipleSheets
{
    use Exportable;
    protected $type;
    protected $data;
    protected $infor;
    protected $total;

    public function __construct($data, $infor, $total)
    {
        $this->data = $data;
        $this->infor = $infor;
        $this->total = $total;
    }

    public function sheets(): array
    {
        $sheets = [];
        $data = $this->data;
        $infor = $this->infor;
        $total = $this->total;
        foreach ($data as $key => $value) {
            $template = "DETAIL";
            if ($key == 'TOTAL') $template = "GENERAL";
            $sheets[] = new SalaryDeclarationMultipleExport($template, $value, $key, $infor, $data, $total);
        }

        return $sheets;
    }

}
