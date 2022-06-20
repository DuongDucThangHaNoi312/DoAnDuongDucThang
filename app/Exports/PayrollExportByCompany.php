<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PayrollExportByCompany implements FromView, WithStyles, WithTitle
{

    protected $payrolls;
    protected $salaryDriver;
    protected $deptData;
    protected $companyData;

    public function __construct($data)
    {
        $this->payrolls = $data['payroll'];
        $this->salaryDriver = $data['salary_driver'];
        $this->deptData = $data['dept'];
        $this->companyData = $data['company'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            'W3:AG3'    => ['alignment' => ['wrapText' => true]],
        ];
    }

    public function view(): View
    {
        if (($this->payrolls)[0]->version == 1) {
            return view('backend.payroll.v1.export_com', [
                'payrolls'   => $this->payrolls,
                'salaryDrivers' => $this->salaryDriver,
                'deptData' => $this->deptData,
                'companyData' => $this->companyData
            ]);
        } else {
            return view('backend.payroll.export1', [
                'payrolls'   => $this->payrolls,
                'salaryDrivers' => $this->salaryDriver,
                'deptData' => $this->deptData
            ]);
        }
    }
    public function title(): string
    {
        return 'LƯƠNG';
    }
}
