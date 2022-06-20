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


class SalaryChooseContainerExport implements FromView, ShouldAutoSize
{
	use Exportable;

    protected $data;

	public function __construct($data)
	{
        $this->data = $data;
	}

	public function view(): View
    {
        $data = $this->data;
        return view("backend.salary-choose-cont.excel", compact('data'));
    }
}
