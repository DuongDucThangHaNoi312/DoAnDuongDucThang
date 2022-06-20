<?php

namespace App\Exports;

use App\Models\AllowanceCategory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PayrollsExport implements FromView, WithStyles
{
    public function __construct($payroll, $payroll_detail)
    {
        $this->payroll = $payroll;
        $this->payroll_detail = $payroll_detail;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function styles(Worksheet $sheet)
    {
        return [
            'X5:AG5'    => ['alignment' => ['wrapText' => true]],
        ];
    }
    

    public function view(): View
    {
        return view('backend.payroll.export_test', [
            'payroll'   => $this->payroll,
            'payroll_detail'      => $this->payroll_detail,
            
        ]);
    }
}
