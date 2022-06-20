<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\Exportable;


class ReportExport implements FromView, ShouldAutoSize
{
	use Exportable;

    protected $type;
    protected $data;

	public function __construct($type, $data)
	{
        $this->type = $type;
        $this->data = $data;
	}

	public function view(): View
    {
        $type = $this->type;
        $data = $this->data;
        // dd($data, $type);
        return view("backend.reports.excel.EXCEL_" . $type, compact('type', 'data'));
    }
}
