<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TimekeepingSign implements FromView
{
    public function __construct($data)
    {
        $this->data = $data;    
    }
    
    /**
    * @return \Illuminate\Support\Collection
    */

    public function view(): View
    {
        return view('backend.timekeeping.export-temp', [
            'items'             => $this->data['items'],
            'detail'            => $this->data['detail'],
            'getDays'           => $this->data['getDays'],
            'getDates'          => $this->data['getDates'],
            'total_day_request' => $this->data['total_day_request'],
        ]);
    }
}
