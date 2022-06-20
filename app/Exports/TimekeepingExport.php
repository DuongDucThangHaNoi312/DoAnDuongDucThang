<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TimekeepingExport implements FromView, WithStyles
{
    public function __construct($data)
    {
        $this->data = $data;    
    }
    
    public function styles(Worksheet $sheet)
    {
        $getStyle = '';
        $getDays = $this->data['getDays'];
        if (count($getDays) == 28) {
            $getStyle = 'D4:AE4';
        } else if (count($getDays) == 29) {
            $getStyle = 'D4:AF4';
        } else if (count($getDays) == 30) {
            $getStyle = 'D4:AG4';
        } else if (count($getDays) == 31) {
            $getStyle = 'D4:AH4';
        }
        $sheet->getStyle($getStyle)->getAlignment()->setTextRotation(90);
    }
    
    /**
    * @return \Illuminate\Support\Collection
    */

    public function view(): View
    {
        $timekeeping = $this->data['timekeeping'];
        if ($timekeeping->version == 1) {
            return view('backend.timekeeping.v1.export', [
                'items'             => $this->data['items'],
                'detail'            => $this->data['detail'],
                'getDays'           => $this->data['getDays'],
                'getDates'          => $this->data['getDates'],
                'getShift'          => $this->data['getShift'],
            ]);
        } else {
            return view('backend.timekeeping.export-copy', [
                'items'             => $this->data['items'],
                'detail'            => $this->data['detail'],
                'getDays'           => $this->data['getDays'],
                'getDates'          => $this->data['getDates'],
                'total_day_request' => $this->data['total_day_request'],
                'nghi_phong_ban' => $this->data['nghi_phong_ban'],
                'nghi_nhan_vien' => $this->data['nghi_nhan_vien'],
                'workSchedule' => $this->data['workSchedule'],
                'getShift' => $this->data['getShift'],
            ]);
        }
       
    }
}
