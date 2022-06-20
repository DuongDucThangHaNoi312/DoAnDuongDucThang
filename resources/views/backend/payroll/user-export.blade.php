<h2>Lương tháng {{ $payroll->month }} / {{ $payroll->year }}</h2>
<table>
    <thead>
        <tr>
             <th style="width: 30px">Họ và tên</th>
             <th style="width: 30px; text-align: center">{{ $user_payroll->staff->fullname }}</th>
        </tr>
        <tr>
            <th style="width: 30px">Mã nhân viên</th>
            <th style="width: 30px; text-align: center">{{ $user_payroll->staff->code }}</th>
       </tr>
        <tr>
            <th style="width: 30px">Công ty</th>
            <th style="width: 30px; text-align: center">{{ $payroll->company->name }}</th>
        </tr>
        <tr>
            <th style="width: 30px">Phòng ban</th>
            <th style="width: 30px; text-align: center">{{ $payroll->department->name }}</th>
        </tr>
        <tr>
            <th style="width: 30px"></th>
            <th style="width: 30px; text-align: center"></th>
        </tr>
        <tr>
            <th style="width: 30px">Thâm niên</th>
            <th style="width: 30px; text-align: center">{{ $getSeniority }} năm</th>
        </tr>
        <tr>
            <th style="width: 30px">Số ngày công theo tháng</th>
            <th style="width: 30px; text-align: center">{{ $user_payroll->total_day_request }}</th>
        </tr>
         <tr>
            <th style="width: 30px">Số ngày công thực tế</th>
            <th style="width: 30px; text-align: center">{{ $user_payroll->actual_workdays }}</th>
        </tr>
        <tr>
            <th style="width: 30px">Số bữa ăn chính</th>
            <th style="width: 30px; text-align: center">{{ $user_payroll->an_chinh }}</th>
        </tr>
        <tr>
            <th style="width: 30px">Số bữa ăn phụ</th>
            <th style="width: 30px; text-align: center">{{ $user_payroll->an_phu }}</th>
        </tr>
        <tr>
            <th style="width: 30px">Phụ cấp ăn</th>
            <th style="width: 30px; text-align: center">{{ number_format(intval($user_payroll->food_allowance_nonTax + $user_payroll->food_allowance_tax)) }}</th>
        </tr>
        <tr>
            <th style="width: 30px"></th>
            <th style="width: 30px; text-align: center"></th>
        </tr>
        <tr>
            <th style="width: 30px">Lương cơ bản</th>
            <th style="width: 30px; text-align: center">{{ number_format($user_payroll->basic_salary_hd) }}</th>
        </tr>
        <tr>
            <th style="width: 30px">Lương đóng bảo hiểm</th>
            <th style="width: 30px; text-align: center">{{ number_format(intval($user_payroll->salary_bh)) }}</th>
        </tr>
        @foreach ($allowance_categories as $key => $item)
        <tr>
            <th style="width: 30px">{{ $item->name }}</th>
            <th style="width: 30px; text-align: center">
                @if (!empty($allowances[$item->id]))
                    {{ number_format($allowances[$item->id]['money']) }}
                @endif

                @if (!empty($allowances1[$item->id]))
                {{ number_format($allowances1[$item->id]['money']) }}
                @endif

            </th>
        </tr>
        @endforeach
        <tr>
            <th style="width: 30px"></th>
            <th style="width: 30px; text-align: center"></th>
        </tr>
        <tr>
            <th style="width: 30px">Tổng các khoản phụ cấp</th>
            <th style="width: 30px; text-align: center">{{ number_format(intval($user_payroll->total_allowances)) }}</th>
        </tr>
        <tr>
            <th style="width: 30px">Tổng lương thực tế</th>
            <th style="width: 30px; text-align: center">{{ number_format(intval($user_payroll->working_salary_non_tax + $user_payroll->working_salary_tax)) }}</th>
        </tr>
        <tr>
            <th style="width: 30px">Tổng lương làm thêm</th>
            <th style="width: 30px; text-align: center">{{ number_format(intval($user_payroll->salary_ot_non_tax + $user_payroll->salary_ot_tax)) }}</th>
        </tr>
        <tr>
            <th style="width: 30px">Lương kiêm nhiệm</th>
            <th style="width: 30px; text-align: center">{{ number_format(intval($user_payroll->salary_concurrent)) }}</th>
        </tr>
        <tr>
            <th style="width: 30px"></th>
            <th style="width: 30px; text-align: center"></th>
        </tr>
        <tr>
            <th style="width: 30px">Khoản điều chỉnh</th>
            <th style="width: 30px; text-align: center">{{ number_format($total_other_amounts) }}</th>
        </tr>
        <tr>
            <th style="width: 30px">Khoản khấu trừ</th>
            <th style="width: 30px; text-align: center">{{ number_format($total_deductions) }}</th>
        </tr>
        <tr>
            <th style="width: 30px">BHXH</th>
            <th style="width: 30px; text-align: center">{{ number_format(intval($user_payroll->bh->bhxh_user)) }}</th>
        </tr>
        <tr>
            <th style="width: 30px">BHYT</th>
            <th style="width: 30px; text-align: center">{{ number_format(intval($user_payroll->bh->bhyt_user)) }}</th>
        </tr>
        <tr>
            <th style="width: 30px">BHTN</th>
            <th style="width: 30px; text-align: center">{{ number_format(intval($user_payroll->bh->bhtn_user)) }}</th>
        </tr>
        <tr>
            <th style="width: 30px">Công Đoàn</th>
            <th style="width: 30px; text-align: center">{{ number_format(intval($user_payroll->bh->union_user)) }}</th>
        </tr>
        <tr>
            <th style="width: 30px"></th>
            <th style="width: 30px; text-align: center"></th>
        </tr>
        <tr>
            <th style="width: 30px">Giảm trừ gia cảnh</th>
            <th style="width: 30px; text-align: center">{{ number_format(intval($user_payroll->family_allowances)) }}</th>
        </tr>
        <tr>
            <th style="width: 30px">Thu nhập chịu thuế</th>
            <th style="width: 30px; text-align: center">{{ number_format(intval($user_payroll->income_taxes)) }}</th>
        </tr>
        <tr>
            <th style="width: 30px">Thu nhập tính thuế</th>
            <th style="width: 30px; text-align: center">{{ $user_payroll->taxable_income < 0 ? 0 : number_format(intval($user_payroll->taxable_income)) }}</th>
        </tr>
        <tr>
            <th style="width: 30px">Thuế thu nhập cá nhân</th>
            <th style="width: 30px; text-align: center">{{ $user_payroll->personal_income_tax < 0 ? 0 : number_format(intval($user_payroll->personal_income_tax)) }}</th>
        </tr>
        <tr>
            <th style="width: 30px"></th>
            <th style="width: 30px; text-align: center"></th>
        </tr>
        <tr>
            <th style="width: 30px">Tổng thu nhập</th>
            <th style="width: 30px; text-align: center">{{ number_format(intval($user_payroll->total_salary)) }}</th>
        </tr>
        <tr>
            <th style="width: 30px">Tổng lương thực lãnh</th>
            <th style="width: 30px; text-align: center">{{ number_format(intval($user_payroll->total_real_salary)) }}</th>
        </tr>
    </thead>
</table>