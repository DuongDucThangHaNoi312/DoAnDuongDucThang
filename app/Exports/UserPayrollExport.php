<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Models\Contract;
use App\Models\AllowanceCategory;

class UserPayrollExport implements FromView
{
    public function __construct($user_payroll, $payroll)
    {
        $this->user_payroll = $user_payroll;
        $this->payroll = $payroll;
    }

    public function view(): View
    {
        $getSeniority = Contract::getSeniority($this->user_payroll['user_payroll']->user_id);
        $allowance_categories = AllowanceCategory::whereNotIn('id', [1])->get();
        foreach ($this->user_payroll['allowances'] as $k => $v) {
            $allowances[$v->category_id] = $v->toArray();
        }
        foreach ($this->user_payroll['allowances1'] as $k => $v) {
            $allowances1[$v->allowance_id] = $v->toArray();
        }

        return view('backend.payroll.user-export', [
            'payroll'   => $this->payroll,
            'user_payroll' => $this->user_payroll['user_payroll'],
            'getSeniority'  => $getSeniority,
            'allowance_categories' => $allowance_categories,
            'allowances' => $allowances,
            'allowances1' => $allowances1,
        ]);
    }
}
