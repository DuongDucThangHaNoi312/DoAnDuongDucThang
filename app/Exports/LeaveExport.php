<?php

namespace App\Exports;

use App\Define\Report;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class LeaveExport implements WithMultipleSheets
{
    use Exportable;
    protected $type;
    protected $data;

    public function __construct($type, $data)
    {
        $this->type = $type;
        $this->data = $data;
    }

    public function sheets(): array
    {
        $sheets = [];
        foreach (Report::LEAVE_TYPE as $name) {
            $template = Report::STAFF_LEAVE . '_' . $name;
            $sheets[] = new LeaveMultipleExport($template, $this->data, $name);
        }
        return $sheets;
    }

}
