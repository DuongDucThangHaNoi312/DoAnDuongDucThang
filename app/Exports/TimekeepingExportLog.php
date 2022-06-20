<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class TimekeepingExportLog implements FromView
{
    public $data;
    public $timekeeping;

    public function __construct($data, $timekeeping)
    {
        $this->data = $data;
        $this->timekeeping = $timekeeping;
    }

    public function view(): View
    {
        return view('backend.timekeeping.v1.export-log', ['data' => $this->data, 'timekeeping' => $this->timekeeping]);
    }
}
