<?php

namespace App\Exports;

use App\Models\Contract;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class AppendixExport implements FromView, ShouldAutoSize, WithTitle
{
    use Exportable;
    protected $contractIds;

    public function __construct($contractIds = [])
    {
        $this->contractIds = $contractIds;
    }

    public function view(): View
    {
        $contracts = [];
        if ($this->contractIds) {
            $contracts = Contract::with('user', 'company', 'department', 'appendixAllowances3')
                ->whereIn('id', $this->contractIds)
                ->orderBy('created_at', 'desc')
                ->get();
        }
        $maxCountAppendix = 0;
        foreach ($contracts as $key => $contract) {
            /*if (count($contract->appendixAllowances3) == 0) {
                unset($contracts[$key]);
                continue;
            }*/
            $count = count($contract->appendixAllowances3) ? count($contract->appendixAllowances3->sortByDesc('created_at')->groupBy('code')->all()) : 0;;
            if ($count > $maxCountAppendix) $maxCountAppendix = $count;
        }
        return view('backend.contracts.partitions._table_appendix_excel', [
            'contracts' => $contracts,
            'maxCountAppendix' => $maxCountAppendix
        ]);
    }

    public function title(): string
    {
        return 'Phụ lục';
    }
}
