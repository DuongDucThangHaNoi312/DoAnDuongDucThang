<?php

namespace App\Exports;

use App\Models\Contract;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;

class ContractExport implements FromView, ShouldAutoSize, WithTitle, WithEvents
{
    use Exportable;
    use RegistersEventListeners;
    protected $contractIds;

    public function __construct($contractIds = [])
    {
        $this->contractIds = $contractIds;
    }

    public function view(): View
    {
        $contracts = [];
        if ($this->contractIds) {
            $contracts =Contract::with('user', 'company', 'department', 'position', 'qualification', 'allowances')
                ->whereIn('id', $this->contractIds)
                ->orderByRaw(\DB::raw("FIELD(id," . implode(',', $this->contractIds) ." )"))
                ->get();
        } else {
            $contracts =Contract::with('user', 'company', 'department', 'position', 'qualification', 'allowances')
                ->orderBy('company_id', 'asc')
                ->orderBy('department_id', 'asc')
                ->get();
        }
        return view('backend.contracts.partitions._table_contract_excel', [
            'contracts' => $contracts,
        ]);
    }

    public function title(): string
    {
        return 'Hợp đồng';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = 'A2:B2';
                $delegateSheet = $event->sheet->getDelegate();
                $delegateSheet->getParent()->getDefaultStyle()->getFont()->setName('Times New Roman');
            }
        ];
    }
}
