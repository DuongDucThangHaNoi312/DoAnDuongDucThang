<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TimekeepingExportOt implements FromView, WithStyles
{
    public function __construct($data)
    {
        $this->data = $data;    
    }
    public function styles(Worksheet $sheet)
    {
        // $style = '';
        // if (count($this->data['getDays']) == 31) {
        //     $style = 'D4:BM4';
        // } else if (count($this->data['getDays']) == 30) {
        //     $style = 'D4:BK4';
        // } else if (count($this->data['getDays']) == 29) {
        //     $style = 'D4:BI4';
        // }

        // $sheet->getStyle($style)->getAlignment()->setTextRotation(90);
    }
    
    /**
    * @return \Illuminate\Support\Collection
    */

    public function view(): View
    {
        $timekeeping = $this->data['timekeeping'];
        if ($timekeeping->version == 1) {
            return view('backend.timekeeping.v1.export-ot', [
                'items'             => $this->data['items'],
                'getDays'            => $this->data['getDays'],
                'getDates'           => $this->data['getDates'],
                'timekeeping'        => $this->data['timekeeping'],
            ]);
        } else {
            return view('backend.timekeeping.export-ot', [
                'items'             => $this->data['items'],
                'getDays'            => $this->data['getDays'],
                'getDates'           => $this->data['getDates'],
                'data'          => $this->data['data'],
            ]);
        }
      
    }
}
