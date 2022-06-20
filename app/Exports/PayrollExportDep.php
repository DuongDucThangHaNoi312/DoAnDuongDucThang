<?php

namespace App\Exports;

use App\Models\AllowanceCategory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PayrollExportDep implements FromView, WithStyles
{
    public function __construct($payroll, $payroll_details)
    {
        $this->payroll = $payroll;
        $this->payroll_details = $payroll_details;
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
        return view('backend.payroll.v1.export', [
            'payroll'   => $this->payroll,
            'payroll_details'      => $this->payroll_details,
        ]);
    }
}
