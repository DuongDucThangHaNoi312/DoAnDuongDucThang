<?php

namespace App\Exports;

use App\Http\Controllers\Backend\SalaryDeclarationController;
use App\Models\Company;
use App\Models\DepartmentGroup;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class SalaryDeclarationMultipleExport implements FromView, WithTitle, ShouldAutoSize
{
    protected $type;
    protected $value;
    protected $infor;
    protected $data;
    protected $total;
    protected $companyCode;

    public function __construct($type, $value, $key, $infor, $data, $total)
    {
        $this->type = $type;
        $this->value = $value;
        $this->infor = $infor;
        $this->key = $key;
        $this->data = $data;
        $this->total = $total;
        $this->companyCode = Company::where('status', 1)->pluck('shortened_name', 'id')->toArray();
    }

    public function view(): View
    {
        $type = $this->type;
        $value = $this->value;
        $infor = $this->infor;
        $key = $this->key;
        $data = $this->data;
        $total = $this->total;

        $companyCode = Company::where('status', 1)->pluck('shortened_name', 'id')->toArray();
        $departmentGroupCode = DepartmentGroup::where('status', 1)->where('type', \App\Define\Department::DECLARATION_OFFICE)->pluck('name', 'id')->toArray();
        $declarationWithPoint = DB::table('type_declarations')->pluck('point', 'name')->toArray(); 

        return view("backend.salary-declaration.excel.EXCEL_" . $type, compact('type', 'value', 'companyCode', 'departmentGroupCode', 'infor', 'key', 'declarationWithPoint', 'data', 'total'));
    }

    public function title(): string
    {
        $string = $this->companyCode[$this->key];
        if ($this->key == "TOTAL") $string = 'Tổng hợp';
        return $string;
    }
}
