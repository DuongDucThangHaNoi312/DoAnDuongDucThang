<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class LeaveMultipleExport implements FromView, WithTitle, ShouldAutoSize
{
    protected $type;
    protected $data;
    protected $name;

    public function __construct($type, $data, $name)
    {
        $this->type = $type;
        $this->data = $data;
        $this->name = $name;
    }

    public function view(): View
    {
        $type = $this->type;
        $data = $this->data;
        return view("backend.reports.excel.EXCEL_" . $type, compact('type', 'data'));
    }

    public function title(): string
    {
        return trans('reports.leave_types.' . $this->name);
    }
}
