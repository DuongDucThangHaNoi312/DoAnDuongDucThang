<?php

namespace App\Exports;

use App\Models\Contract;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class ConcurrentExport implements FromView, ShouldAutoSize, WithTitle
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
            $contracts = Contract::with('user', 'company', 'department', 'concurrentContracts')
                ->whereIn('id', $this->contractIds)
                ->orderBy('user_id')
                ->get();
        }
        $maxCountConcurrent = 0;
        foreach ($contracts as $key => $contract) {
            $count = count($contract->concurrentContracts);
            if ($count == 0) {
                unset($contracts[$key]);
                continue;
            }
            if ($count > $maxCountConcurrent) $maxCountConcurrent = $count;
        }

        return view('backend.contracts.partitions._table_concurrent_excel', [
            'contracts' => $contracts,
            'maxCountConcurrent' => $maxCountConcurrent
        ]);
    }

    public function title(): string
    {
        return 'Hợp đồng kiêm nhiệm';
    }
}
