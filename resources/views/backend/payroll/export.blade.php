<div style="margin-bottom: 20px">
    <h3>Bảng lương tháng {{ $payroll->month }}/{{ $payroll->year }}</h3>
    <h4>{{ $payroll->department->name }} - {{ $payroll->company->name }}</h4>
</div>
<table>
    <thead>
        <tr>
            <th rowspan="3"  style="border: 5px solid black;">{!! trans('system.no.') !!}</th>
            <th rowspan="3"  style="border: 5px solid black;" width="20">{!! trans('timekeeping.staff') !!}</th>
            <th rowspan="3"  style="border: 5px solid black;">{!! trans('timekeeping.code') !!}</th>
            <th colspan="6"  style="background-color: #7fabc5; border: 5px solid black; text-align: center">Ngày công thực tế</th>
            <th colspan="2"  style="background-color: #dd4b39; border: 5px solid black; text-align: center">Số bữa ăn</th>
            <th colspan="2"  style="background-color: #dd4b39; border: 5px solid black; text-align: center">Phụ cấp ăn</th>
            <th colspan="2"  style="background-color: #bfbcf9; border: 5px solid black; text-align: center">Lương cơ bản</th>
            <th colspan="2"  style="background-color: #bfbcf9; border: 5px solid black; text-align: center;" width="20">Lương làm việc thực tế</th>
            <th colspan="2"  style="background-color: #bfbcf9; border: 5px solid black; text-align: center;">Lương làm thêm</th>
            @if (count($allowance_categories))
                @foreach ($allowance_categories as $item)
                <th rowspan="3"  style="background-color: #ec9494; border: 5px solid black; text-align: center;" width="10">{{ $item->name }}</th>
                @endforeach
            @endif
            <th rowspan="3"  style="background-color: #ec9494; border: 5px solid black; text-align: center;" width="10">Tổng các khoản phụ cấp</th>
            <th rowspan="3"  style="background-color: #c6c775; border: 5px solid black; text-align: center;" width="10">Các khoản điều chỉnh</th>
            <th rowspan="3"  style="background-color: #c6c775; border: 5px solid black; text-align: center;" width="10">Các khoản khấu trừ</th>
            <th rowspan="3"  style="background-color: #c6c775; border: 5px solid black; text-align: center;" width="10">Giảm trừ gia cảnh</th>
            <th rowspan="3"  style="background-color: #c6c775; border: 5px solid black; text-align: center;" width="10">Thu nhập chịu thuế</th>
            <th rowspan="3"  style="background-color: #c6c775; border: 5px solid black; text-align: center;" width="10">Thu nhập tính thuế</th>
            <th rowspan="3"  style="background-color: #c6c775; border: 5px solid black; text-align: center;" width="10">Thuế thu nhập cá nhân</th>
            <th colspan="8"  style="background-color: #9cb3e4; border: 5px solid black; text-align: center;">Bảo hiểm</th>
            <th rowspan="3"  style="border: 5px solid black; text-align: center;" width="10">Thực lãnh</th>
        </tr>
        <tr>
            <th style="background-color: #7fabc5; border: 5px solid black; text-align: center" colspan="2">Thử việc</th>
            <th style="background-color: #7fabc5; border: 5px solid black; text-align: center" colspan="2">Hợp đồng</th>
            <th style="background-color: #7fabc5; border: 5px solid black; text-align: center" rowspan="2" width="15">Nghỉ nguyên lương</th>
            <th style="background-color: #7fabc5; border: 5px solid black; text-align: center" rowspan="2" width="8">Đình chỉ</th>
            <th style="background-color: #dd4b39; border: 5px solid black; text-align: center" rowspan="2" width="8">Chính</th>
            <th style="background-color: #dd4b39; border: 5px solid black; text-align: center" rowspan="2" width="8">Phụ</th>
            <th style="background-color: #dd4b39; border: 5px solid black; text-align: center" rowspan="2" width="10">Miễn thuế</th>
            <th style="background-color: #dd4b39; border: 5px solid black; text-align: center" rowspan="2" width="10">Chịu thuế</th>
            <th style="background-color: #bfbcf9; border: 5px solid black; text-align: center" rowspan="2" width="10">Thử việc</th>
            <th style="background-color: #bfbcf9; border: 5px solid black; text-align: center" rowspan="2" width="10">Hợp đồng</th>
            <th style="background-color: #bfbcf9; border: 5px solid black; text-align: center" rowspan="2" width="10">Chịu thuế</th>
            <th style="background-color: #bfbcf9; border: 5px solid black; text-align: center" rowspan="2" width="10">Miễn thuế</th>
            <th style="background-color: #bfbcf9; border: 5px solid black; text-align: center" rowspan="2" width="10">Chịu thuế</th>
            <th style="background-color: #bfbcf9; border: 5px solid black; text-align: center" rowspan="2" width="10">Miễn thuế</th>
            <th style="background-color: #9cb3e4; border: 5px solid black; text-align: center" colspan="4">Khoản khấu trừ nhân viên</th>
            <th style="background-color: #9cb3e4; border: 5px solid black; text-align: center" colspan="4">Đóng góp của Công ty</th>
        </tr>
        <tr>
            <th style="background-color: #7fabc5; border: 5px solid black; text-align: center" width="8">Ngày</th>
            <th style="background-color: #7fabc5; border: 5px solid black; text-align: center" width="8">Đêm</th>
            <th style="background-color: #7fabc5; border: 5px solid black; text-align: center" width="8">Ngày</th>
            <th style="background-color: #7fabc5; border: 5px solid black; text-align: center" width="8">Đêm</th>
            <th style="background-color: #9cb3e4; border: 5px solid black; text-align: center"  width="10">BHXH (8%)</th>
            <th style="background-color: #9cb3e4; border: 5px solid black; text-align: center" width="10">BHYT (1.5%)</th>
            <th style="background-color: #9cb3e4; border: 5px solid black; text-align: center" width="10">BHTN (1%)</th>
            <th style="background-color: #9cb3e4; border: 5px solid black; text-align: center" width="10">Công đoàn (1%)</th>
            <th style="background-color: #9cb3e4; border: 5px solid black; text-align: center" width="10">BHXH (17.5%)</th>
            <th style="background-color: #9cb3e4; border: 5px solid black; text-align: center" width="10">BHYT (3%)</th>
            <th style="background-color: #9cb3e4; border: 5px solid black; text-align: center" width="10">BHTN (2%)</th>
            <th style="background-color: #9cb3e4; border: 5px solid black; text-align: center" width="10">Công đoàn (%)</th>
        </tr>
    </thead>
    <tbody>
        @if (count($data > 0))
            @foreach ($data as $key => $item)
                <tr>
                    <td align="center" class="">{{ $key + 1 }}</td>
                    <td align="center">{{ $item['user_payroll']->staff->fullname }}</td>
                    <td align="center">{{ $item['user_payroll']->staff->code }}</td>
                    <td align="center" style="background-color: #7fabc5; border: 5px solid black;">{{ $item['user_payroll']->ca_ngay_tv }}</td>
                    <td align="center" style="background-color: #7fabc5; border: 5px solid black;">{{ $item['user_payroll']->ca_dem_tv }}</td>
                    <td align="center" style="background-color: #7fabc5; border: 5px solid black;">{{ $item['user_payroll']->ca_ngay_hd }}</td>
                    <td align="center" style="background-color: #7fabc5; border: 5px solid black;">{{ $item['user_payroll']->ca_dem_hd }}</td>
                    <td align="center" style="background-color: #7fabc5; border: 5px solid black;">{{ !empty($item['user_payroll']->day_off_70_salary) ? $item['user_payroll']->day_off_70_salary : '0' }}</td>
                    <td align="center" style="background-color: #7fabc5; border: 5px solid black;">{{ $item['user_payroll']->day_off_luong }}</td>
                    <td align="center" style="background-color: #dd4b39; border: 5px solid black;">{{ $item['user_payroll']->an_chinh }}</td>
                    <td align="center" style="background-color: #dd4b39; border: 5px solid black;">{{ $item['user_payroll']->an_phu }}</td>
                    <td align="center" style="background-color: #dd4b39; border: 5px solid black;">{{ number_format($item['user_payroll']->food_allowance_nonTax) }}</td>
                    <td align="center" style="background-color: #dd4b39; border: 5px solid black;">{{ number_format($item['user_payroll']->food_allowance_tax) }}</td>
                    <td align="center" style="background-color: #bfbcf9; border: 5px solid black;">{{ number_format($item['user_payroll']->basic_salary_tv) }}</td>
                    <td align="center" style="background-color: #bfbcf9; border: 5px solid black;">{{ number_format($item['user_payroll']->basic_salary_hd) }}</td>
                    <td align="center" style="background-color: #bfbcf9; border: 5px solid black;">{{ number_format($item['user_payroll']->working_salary_tax) }}</td>
                    <td align="center" style="background-color: #bfbcf9; border: 5px solid black;">{{ number_format($item['user_payroll']->working_salary_non_tax) }}</td>
                    <td align="center" style="background-color: #bfbcf9; border: 5px solid black;">{{ number_format($item['user_payroll']->salary_ot_tax) }}</td>
                    <td align="center" style="background-color: #bfbcf9; border: 5px solid black;">{{ number_format($item['user_payroll']->salary_ot_non_tax) }}</td>

                    @if (count($allowance_categories))
                        @foreach ($allowance_categories as $c => $category)
                        <td style="background-color: #ec9494; border: 5px solid black; text-align: center;" width="15">
                            @if (!empty($allowances[$category->id]))
                                {{ number_format($allowances[$category->id]['money']) }}
                            @endif

                            @if (!empty($allowances1[$category->id]))
                            {{ number_format($allowances1[$category->id]['money']) }}
                            @endif

                        </td>
                        @endforeach
                    @endif

                    <td align="center" style="background-color: #ec9494; border: 5px solid black;">{{ number_format($item['user_payroll']->total_allowances) }}</td>
                    <td align="center" style="background-color: #c6c775; border: 5px solid black;">{{ number_format($item['user_payroll']->total_other_amounts) }}</td>
                    <td align="center" style="background-color: #c6c775; border: 5px solid black;">{{ number_format($item['user_payroll']->total_deductions) }}</td>
                    <td align="center" style="background-color: #c6c775; border: 5px solid black;">{{ number_format($item['user_payroll']->family_allowances) }}</td>
                    <td align="center" style="background-color: #c6c775; border: 5px solid black;">{{ number_format($item['user_payroll']->income_taxes) }}</td>
                    <td align="center" style="background-color: #c6c775; border: 5px solid black;">{{ $item['user_payroll']->taxable_income < 0 ? 0 : number_format($item['user_payroll']->taxable_income) }}</td>
                    <td align="center" style="background-color: #c6c775; border: 5px solid black;">{{ $item['user_payroll']->personal_income_tax < 0 ? 0 : number_format($item['user_payroll']->personal_income_tax) }}</td>   

                    <td align="center" style="background-color: #9cb3e4; border: 5px solid black;">{{ number_format(intval($item['user_payroll']->bh->bhxh_user)) }}</td>
                    <td align="center" style="background-color: #9cb3e4; border: 5px solid black;">{{ number_format(intval($item['user_payroll']->bh->bhyt_user)) }}</td>
                    <td align="center" style="background-color: #9cb3e4; border: 5px solid black;">{{ number_format(intval($item['user_payroll']->bh->bhtn_user)) }}</td>
                    <td align="center" style="background-color: #9cb3e4; border: 5px solid black;">{{ number_format(intval($item['user_payroll']->bh->union_user)) }}</td>


                    <td align="center" style="background-color: #9cb3e4; border: 5px solid black;">{{ number_format(intval($item['user_payroll']->bh->bhxh_company)) }}</td>
                    <td align="center" style="background-color: #9cb3e4; border: 5px solid black;">{{ number_format(intval($item['user_payroll']->bh->bhyt_company)) }}</td>
                    <td align="center" style="background-color: #9cb3e4; border: 5px solid black;">{{ number_format(intval($item['user_payroll']->bh->bhtn_company)) }}</td>
                    <td align="center" style="background-color: #9cb3e4; border: 5px solid black;">{{ number_format(intval($item['user_payroll']->bh->union_company)) }}</td>

                    <td align="center">{{ number_format($item['user_payroll']->total_real_salary) }}</td>   
                </tr>
            @endforeach
        @endif
    </tbody>           
</table>