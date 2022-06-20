
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
       
    </style>
</head>
<body>
    <div class="box-body no-padding" style="overflow-x:auto; overflow-x:auto;">

        <?php $cate_allowances = \App\Models\AllowanceCategory::cateAllowance() ;?>
        <table class="table table-striped table-bordered">
            <thead>
                <tr></tr>
                <tr>
                    <th></th>
                    <th style="width: 50px;">BẢNG LƯƠNG THÁNG {{ $payroll->month . '/' . $payroll->year }}</th>
                    <th> {{ $payroll->company->shortened_name }}, {{ $payroll->department->name }}</th>  
                </tr>
                <tr></tr>
                <tr>
                    <th rowspan="2"  style="text-align: center; vertical-align: middle; background: #79CDCD; border: 1px solid black;">
                        <span class="uppercase">No.</span><br><br>
                        <span>STT</span><br><br>
                    </th>
                    <th  rowspan="2" class="sticky-col" style="text-align: center; vertical-align: middle; padding: 0 100px; width: 30px; background: #79CDCD; border: 1px solid black;">
                        <span class="uppercase">FULL NAME</span><br><br>
                        <span>Họ và tên</span>
                    </th>
                    
                    <th rowspan="2" style="text-align: center; vertical-align: middle; background: #79CDCD; border: 1px solid black;">
                        <span class="uppercase">CODE</span><br><br>
                        <span>Mã NV</span>
                    </th>
                    <th rowspan="2"  style="text-align: center; vertical-align: middle; width: 20px; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                        <span class="uppercase">FIXED WORKING DAYS </span> <br><br>
                        <span>Số ngày công theo tháng</span>
                    </th>
                    <th colspan="9"  style="text-align: center; vertical-align: middle; height: 50px; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                        <br>
                        <span class="uppercase" style="margin-top: 10px">ACTUAL WORKING DAYS</span><br>
                        <span>Ngày công thực tế</span><br>
                        {{-- <span>(làm việc ở công ty)</span> --}}
                    </th>
                    
    
                    {{-- <th rowspan="2"  style="text-align: center; vertical-align: middle;" class="tdbreak">
                        <span class="uppercase">ACTUAL
                            WORKING
                            DAYS
                            
                            </span><br><br>
                        <span>Ngày công thực tế</span><br>
                        <span>(công tác)</span>
                    </th> --}}
                    <th rowspan="2"  style="text-align: center; vertical-align: middle; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                        <span>NUMBER
                            OF SHIFT
                            MEAL
                            </span><br><br>
                        <span>Số bữa ăn chính</span>
                    </th>
                    <th rowspan="2"  style="text-align: center; vertical-align: middle; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                        <span>NUMBER OF EXTRA MEAL
                            </span><br><br>
                        <span>Số bữa phụ</span>
                    </th>
                    {{-- <th rowspan="2"  style="text-align: center; vertical-align: middle;" class="tdbreak">
                        <span class="uppercase">Night working day</span><br><br>
                        <span>Ngày công làm việc đêm</span>
                    </th> --}}
                    <th rowspan="2"  style="text-align: center; vertical-align: middle; width: 20px; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                        <span>SALARY PROBATION
                           </span><br><br>
                        <span>Lương thử việc</span>
                    </th>
                    <th rowspan="2"  style="text-align: center; vertical-align: middle; width: 20px; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                        <span>BASIC 
                            RATE
                           </span><br><br>
                        <span>Lương cơ bản</span>
                    </th>
                    <th  rowspan="2" style="text-align: center; vertical-align: middle; width: 20px; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                        <span>BASIC 
                            RATE
                           </span><br><br>
                        <span>Lương đóng BH</span>
                    </th>
                    <th  rowspan="2" style="text-align: center; vertical-align: middle; width: 20px; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                        <span class="uppercase">Actual Work. Day</span> <br><br>
                        <span>Lương làm việc thực tế</span>
                    </th>
                    <th  rowspan="2" style="text-align: center; vertical-align: middle; width: 20px; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                        <span class="uppercase">Actual night Work. Day</span><br><br>
                        <span>Lương trả khi làm đêm (30%)</span>
                    </th>
                    <th  colspan="2" style="text-align: center; vertical-align: middle; width: 20px; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                        <span class="uppercase"> Actual Work Salary 	
    
                        </span> <br><br>
                        <span>Lương làm thêm</span>
                    </th>
                    <th colspan="{{ count($cate_allowances) + 2 }}" style="text-align: center; vertical-align: middle; background: #79CDCD; border: 1px solid black;">
                        <span class="uppercase">ALLOWANCE</span><br><br>
                        <span>Phụ cấp</span>
                    </th>
                    <th rowspan="2" style="text-align: center; vertical-align: middle; width: 20px; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                        <span class="uppercase">Total income</span><br><br>
                        <span class="">Tổng thu nhập</span>
                    </th>
                    <th colspan="4" style="text-align: center; vertical-align: middle; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                        <span class="uppercase">DEDUCTIONS</span><br><br>
                        <span class="">Khấu trừ nhân viên</span>
                    </th>
                    
                    <th colspan="4" style="text-align: center; vertical-align: middle; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                        <span class="uppercase">COMPANY CONTRIBUTION</span><br><br>
                        <span class="">Đóng góp của CÔNG TY</span>
                    </th>
    
                    <th rowspan="2" style="text-align: center; vertical-align: middle; width: 20px; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                        <span class="uppercase">DEDUCTION PIT FINALIZE</span><br><br>
                        <span class="">Nộp theo quyết toán</span><br>
                        <span>TNCN năm 2022</span>
                    </th>
                    <th colspan="2" style="text-align: center; vertical-align: middle; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                        <span class="uppercase">DEDUCTION</span><br><br>
                        <span class="">Khoản giảm trừ khác</span>
                    </th>
                    <th colspan="2" style="text-align: center; vertical-align: middle; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                        <span class="uppercase">Increase</span><br><br>
                        <span class="">Khoản tăng</span>
                    </th>
    
                    <th rowspan="2" style="text-align: center; vertical-align: middle; width: 20px; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                        <span class="uppercase">Taxable
                            Income
                            </span><br><br>
                        <span class="">Thu nhập chịu thuế</span>
                    </th>
    
                    <th rowspan="2" style="text-align: center; vertical-align: middle; width: 20px; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                        <span class="uppercase">No of dependant
                            </span><br><br>
                        <span class="">Số người phụ thuộc</span>
                    </th>
    
                    <th rowspan="2" style="text-align: center; vertical-align: middle; width: 20px; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                        <span class="uppercase">Self relief and dependant relief
                            </span><br><br>
                        <span class="">Khấu trừ gia cảnh</span>
                    </th>
    
                    <th rowspan="2" style="text-align: center; vertical-align: middle; width: 20px; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                        <span class="uppercase">Assessable Income
                            </span><br><br>
                        <span class="">Thu nhập tính thuế</span>
                    </th>
    
                    <th rowspan="2" style="text-align: center; vertical-align: middle; width: 20px; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                        <span class="uppercase">Income tax
                            </span><br><br>
                        <span class="">Thuế TNCN</span>
                    </th>
                    <th rowspan="2" style="text-align: center; vertical-align: middle; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                        <span class="uppercase">Điểm KPI
                            </span><br><br>
                        <span>trong tháng</span>
                    </th>
                    <th rowspan="2" style="text-align: center; vertical-align: middle; width: 20px; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                        <span class="uppercase">
                            </span><br><br>
                        <span>Các khoản điều chỉnh khác</span>
                    </th>
                    <th rowspan="2" style="text-align: center; vertical-align: middle; width: 20px; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                        <span class="uppercase">TAKE HOME PAY
                            </span><br><br>
                        <span>Tổng thực lĩnh</span>
                    </th>
                    
    
    
                    <tr>
                        <th style="text-align: center; vertical-align: middle; width: 10px; background: #79CDCD; border: 1px solid black;" class="tdbreak">Số ngày <br>thử việc</th>
                        <th style="text-align: center; vertical-align: middle;  width: 10px; background: #79CDCD; border: 1px solid black;" class="tdbreak">Số ngày<br> hợp đồng</th>
                        <th style="text-align: center; vertical-align: middle; width: 10px; background: #79CDCD; border: 1px solid black;" class="tdbreak">Số đêm <br>thử việc</th>
                        <th style="text-align: center; vertical-align: middle; width: 10px; background: #79CDCD; border: 1px solid black;" class="tdbreak">Số đêm <br>hợp đồng</th>
                        <th style="text-align: center; vertical-align: middle; width: 10px; background: #79CDCD; border: 1px solid black;" class="tdbreak">Ngày <br>công tác</th>
                        <th style="text-align: center; vertical-align: middle; width: 10px; background: #79CDCD; border: 1px solid black;" class="tdbreak">Nghỉ <br>hưởng lương</th>
                        <th style="text-align: center; vertical-align: middle; width: 10px; background: #79CDCD; border: 1px solid black;" class="tdbreak">Nghỉ <br>đình chỉ</th>
                        <th style="text-align: center; vertical-align: middle; width: 10px; background: #79CDCD; border: 1px solid black;" class="tdbreak">Nghỉ không lương<br>đi muộn</th>
                        <th style="text-align: center; vertical-align: middle; background: #79CDCD; border: 1px solid black; width: 10px" class="tdbreak">Tổng</th>
                        
                        <th style="text-align: center; vertical-align: middle;  width: 20px; background: #79CDCD; border: 1px solid black;" class="tdbreak">Miễn thuế</th>
                        <th style="text-align: center; vertical-align: middle;  width: 20px; background: #79CDCD; border: 1px solid black;" class="tdbreak">Chịu thuế</th>
    
                        <th style="text-align: center; vertical-align: middle; width: 20px; background: #79CDCD; border: 1px solid black;" class="tdbreak">Phụ cấp ăn trưa,<br> ăn ca (NON-TAX)</th>
    
                        <th style="text-align: center; vertical-align: middle; width: 20px; background: #79CDCD; border: 1px solid black;" class="tdbreak">Phụ cấp ăn trưa, <br> ăn ca (TAX)</th>
    
                        @foreach ($cate_allowances as $k => $cate)
                            <th style="text-align: center; vertical-align: middle; width: 10px; background: #79CDCD; border: 1px solid black;" class="tdbreak">{{ $cate ?? '' }}</th>
                        @endforeach
                        
                        
                        <th style="text-align: center; vertical-align: middle; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                            <span class="uppercase">SOC INS</span><br><br>
                            <span class="uppercase">BHXH</span><br><br>
                            <span class="uppercase">8%</span>
                        </th>
                        <th style="text-align: center; vertical-align: middle; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                            <span class="uppercase">H INS </span><br><br>
                            <span class="uppercase">BHYT</span><br><br>
                            <span class="uppercase">1.5%</span>
                        </th>
                        <th style="text-align: center; vertical-align: middle; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                            <span class="uppercase">UNION</span><br><br>
                            <span class="uppercase">Công đoàn</span><br><br>
                            <span class="uppercase">1%</span>
                        </th>
                        <th style="text-align: center; vertical-align: middle; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                            <span class="uppercase">UN EM INS</span><br><br>
                            <span class="uppercase">BHTN</span><br><br>
                            <span class="uppercase">1%</span>
                        </th>
    
    
                        <th style="text-align: center; vertical-align: middle; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                            <span class="uppercase">SOC INS</span><br><br>
                            <span class="uppercase">BHXH</span><br><br>
                            <span class="uppercase">17%</span>
                        </th>
                        <th style="text-align: center; vertical-align: middle; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                            <span class="uppercase">H INS </span><br><br>
                            <span class="uppercase">BHYT</span><br><br>
                            <span class="uppercase">3%</span>
                        </th>
                        <th style="text-align: center; vertical-align: middle; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                            <span class="uppercase">UNION</span><br><br>
                            <span class="uppercase">Công đoàn</span><br><br>
                            <span class="uppercase">2%</span>
                        </th>
                        <th style="text-align: center; vertical-align: middle; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                            <span class="uppercase">UN EM INS</span><br><br>
                            <span class="uppercase">BHTN</span><br><br>
                            <span class="uppercase">1%</span>
                        </th>
                        
                        <th style="text-align: center; vertical-align: middle; width: 20px; background: #79CDCD; border: 1px solid black;" class="tdbreak">Miễn thuế</th>
                        <th style="text-align: center; vertical-align: middle; width: 20px; background: #79CDCD; border: 1px solid black;" class="tdbreak">Chịu thuế</th>
    
                        <th style="text-align: center; vertical-align: middle; width: 20px; background: #79CDCD; border: 1px solid black;" class="tdbreak">Miễn thuế</th>
                        <th style="text-align: center; vertical-align: middle; width: 20px; background: #79CDCD; border: 1px solid black;" class="tdbreak">Chịu thuế</th>
                        
                    </tr>
                   
                    
                </tr>
            </thead>
            <tbody>
                <tr></tr>
               @if (count($payroll_details) > 0)
               
               <?php $pc_di_lai = $pc_trach_nhiem = $pc_cong_hien = $pc_ns_cong_viec = $pc_dien_thoai = $pc_dac_thu = $pc_khac = $pc_chuyen_can = 0; $rowIndex = 1; ?>
                @foreach ($payroll_details as $k => $item)  
              
                    <?php 

                        $bhxh_user += $item->bh['bhxh_user'];
                        $bhyt_user += $item->bh['bhyt_user'];
                        $union_user += $item->bh['union_user'];
                        $bhtn_user += $item->bh['bhtn_user'];

                        $bhxh_company += $item->bh['bhxh_company'];
                        $bhyt_company += $item->bh['bhyt_company'];
                        $bhtn_company += $item->bh['bhtn_company'];
                        $union_company += $item->bh['union_company'];
                    ?>
                    <tr class="hover" data-index="{{ $k + 1 }}">
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">{{ $rowIndex++ }}</td>
                        <td class="sticky-col" style="text-align: center; vertical-align: middle; border: 1px solid black;">{!! $item->staff->fullname ?? '' !!}</td>
                        <td  style="text-align: center; vertical-align: middle; border: 1px solid black;">{!! $item->staff->code ?? '' !!}</td>
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                            {{ $item['total_day_request'] }}
                        </td>
                       
    
    
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                            {{ $item->tongHop['ngay_tv'] ?? 0 }}
                        </td>
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                            {{ $item->tongHop['ngay_hd'] ?? 0 }}
                        </td>
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                            {{  $item->tongHop['dem_tv'] ?? 0 }}
                        </td>
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                            {{ $item->tongHop['dem_hd'] ?? 0 }}
                        </td>
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                            {{ $item->tongHop['nghi_cong_tac'] ?? 0 }}
                        </td>
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                            <?php 
                                $nghiHuongLuong = $item->tongHop['nghi_phep'] + $item->tongHop['nghi_cuoi'] + $item->tongHop['nghi_hieu'] + $item->tongHop['nghi_le'] + $item->tongHop['lam_tai_nha'];
                            ?>
                            {{ $nghiHuongLuong ?? 0 }}
                        </td>
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                            {{ $item->tongHop['nghi_70_luong'] ?? 0 }}
                        </td>
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                            <?php 
                                $nghi_khong_luong = $item->tongHop['nghi_om'] + $item->tongHop['nghi_khong_luong'];
                            ?>
                            {{ $nghi_khong_luong }}
                        </td>
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                            {{ $item->tongHop['total'] ?? 0 }}
                        </td>
    
                        {{-- <td style="text-align: center; vertical-align: middle;">
                            <input type="text" value="{{ \App\StaffDayOff::countDayOffs($item->user_id, $payroll->month, $payroll->year, 'T') }}" data-name="total_work_department" data-user-id="{!! $item->user_id !!}" class="form-control ct">
                        </td> --}}
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                            {!! $item->tongHop['an_chinh'] ?? 0 !!}
                        </td>
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                            {!! $item->tongHop['an_phu'] ?? 0 !!}
                        </td>
                        {{-- <td style="text-align: center; vertical-align: middle;">
                            <input type="text" value="{!! $data['user_payroll']->ca_dem_hd +  $data['user_payroll']->ca_dem_tv !!}" data-name="ca_dem_hd" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct">
                        </td> --}}
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                            {!! number_format($item->basic_salary_tv) ?? '' !!}
                        </td>
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                            {!! number_format($item->basic_salary_hd) ?? '' !!}
                        </td>
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                            {!! number_format($item->salary_bh) ?? '' !!}
                        </td>
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                            {!! number_format($item->working_salary_tax )?? '' !!}
                        </td>
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                            {!! number_format($item->working_salary_non_tax )?? '' !!}
                        </td>
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                            {!! number_format($item->salary_ot_non_tax )?? '' !!}
                        </td>
    
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                            {!! number_format($item->salary_ot_tax )?? '' !!}
                        </td>
    
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                            {!! number_format($item->food_allowance_nonTax )?? '' !!}
                        </td>
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                            {!! number_format($item->food_allowance_tax )?? '' !!}
                        </td>
                        
    
                        @foreach ($cate_allowances as $k1 => $cate)

                            <?php 
                                $tt = intval($item->calculateAllowance[$k1]) ?? 0;
                                if ($k1 == 2) $pc_di_lai += $tt;
                                if ($k1 == 3) $pc_trach_nhiem += $tt;
                                if ($k1 == 4) $pc_cong_hien += $tt;
                                if ($k1 == 5) $pc_ns_cong_viec += $tt;
                                if ($k1 == 6) $pc_dien_thoai += $tt;
                                if ($k1 == 7) $pc_cong_viec += $tt;
                                if ($k1 == 8) $pc_dac_thu += $tt;
                                if ($k1 == 9) $pc_khac += $tt;
                                if ($k1 == 10) $pc_chuyen_can += $tt;
                            ?>
                            <td style="text-align: center; vertical-align: middle; border: 1px solid black;" class="tdbreak">
                                {!! $tt ?? '' !!}
                            </td>
                            
                        @endforeach
                        
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                            {!! number_format($item->total_salary )?? '' !!}
                        </td>
    
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                            {!! number_format($item->bh['bhxh_user'] )?? '' !!}
                        </td>
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                            {!! number_format($item->bh['bhyt_user'] )?? '' !!}
                        </td>
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                            {!! number_format($item->bh['union_user'] )?? '' !!}
                        </td>
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                            {!! number_format($item->bh['bhtn_user'])?? '' !!}
                        </td>
    
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                            {!! number_format($item->bh['bhxh_company'] )?? '' !!}
                        </td>
    
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                            {!! number_format($item->bh['bhyt_company'])?? '' !!}
                        </td>
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                            {!! number_format($item->bh['union_company'])?? '' !!}
                        </td>
    
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                            {!! number_format($item->bh['bhtn_company'])?? '' !!}
                        </td>
    
    
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                            0
                        </td>
    
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                            {{ number_format(($item->total_deduction - $item->total_deduction_tax)) }}
                        </td>
    
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                            {{ number_format($item->total_deduction_tax) }}
                        </td>
                       
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                            {{ number_format(($item->total_payoff - $item->total_payoff_tax)) }}
                        </td>
    
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                            {{ number_format($item->total_payoff_tax) }}
                        </td>
                       
    
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                            {!! number_format($item->income_taxes )?? '' !!}
                        </td>
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                            {{ \App\User::countUserRelationship($item->user_id) }}
                        </td>
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                            {!! number_format($item->family_allowances )?? '' !!}
                        </td>
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                            {!! number_format($item->taxable_income )?? '' !!}
                        </td>
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                            {!! number_format($item->personal_income_tax )?? '' !!}
                        </td>
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                            {{ $item->tongHop['total'] == 0 ? 0 : \App\Models\Payroll::getKpi($item->user_id, $payroll->month, $payroll->year) }}
                        </td>
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                            {!! number_format($item->total_impale )?? '' !!}
                        </td>
                        <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                            {!! number_format($item->total_real_salary )?? '' !!}
                        </td>
                        
                        {{-- <td>
                            <a href="{{ route('admin.payrolls.user-detail', $item->id) }}" class="btn btn-info btn-xs">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                        </td> --}}
                    </tr>
                    
                @endforeach
               @endif
                <tr>
                   
                    @for ($i = 1; $i <= 55; $i++)
                        @if ($i == 2)
                         <td style="background: #e5c3ce; border: 1px solid black; text-align: center">Tổng</td>
                         @elseif ($i == 16)
                            <td style="text-align: center; vertical-align: middle; background: #e5c3ce; border: 1px solid black;">
                                {!! number_format(array_sum(array_column($payroll_details->toArray(), 'basic_salary_tv')))?? '' !!}
                            </td>
                        @elseif ($i == 17)
                            <td style="text-align: center; vertical-align: middle; background: #e5c3ce; border: 1px solid black;">
                                {!! number_format(array_sum(array_column($payroll_details->toArray(), 'basic_salary_hd')))?? '' !!}
                            </td>
                        @elseif ($i == 18)
                            <td style="text-align: center; vertical-align: middle; background: #e5c3ce; border: 1px solid black;">
                                {!! number_format(array_sum(array_column($payroll_details->toArray(), 'salary_bh')))?? '' !!}
                            </td>
                        @elseif ($i == 19)
                            <td style="text-align: center; vertical-align: middle; background: #e5c3ce; border: 1px solid black;">
                                {!! number_format(array_sum(array_column($payroll_details->toArray(), 'working_salary_tax')))?? '' !!}
                            </td>
                        @elseif ($i == 20)
                            <td style="text-align: center; vertical-align: middle; background: #e5c3ce; border: 1px solid black;">
                                {!! number_format(array_sum(array_column($payroll_details->toArray(), 'working_salary_non_tax')))?? '' !!}
                            </td>

                        @elseif ($i == 21)
                            <td style="text-align: center; vertical-align: middle; background: #e5c3ce; border: 1px solid black;">
                                {!! number_format(array_sum(array_column($payroll_details->toArray(), 'salary_ot_non_tax')))?? '' !!}
                            </td>
                        @elseif ($i == 22)
                            <td style="text-align: center; vertical-align: middle; background: #e5c3ce; border: 1px solid black;">
                                {!! number_format(array_sum(array_column($payroll_details->toArray(), 'salary_ot_tax')))?? '' !!}
                            </td>
                        @elseif ($i == 23)
                            <td style="text-align: center; vertical-align: middle; background: #e5c3ce; border: 1px solid black;">
                                {!! number_format(array_sum(array_column($payroll_details->toArray(), 'food_allowance_nonTax')))?? '' !!}
                            </td>

                        @elseif ($i == 24)
                            <td style="text-align: center; vertical-align: middle; background: #e5c3ce; border: 1px solid black;">
                                {!! number_format(array_sum(array_column($payroll_details->toArray(), 'food_allowance_tax')))?? '' !!}
                            </td>
                        @elseif ($i == 25)
                            <td style="text-align: center; vertical-align: middle; background: #e5c3ce; border: 1px solid black;">
                                {!! number_format($pc_di_lai)?? '' !!}
                            </td>
                        @elseif ($i == 26)
                            <td style="text-align: center; vertical-align: middle; background: #e5c3ce; border: 1px solid black;">
                                {!! number_format($pc_trach_nhiem)?? '' !!}
                            </td>
                        @elseif ($i == 27)
                            <td style="text-align: center; vertical-align: middle; background: #e5c3ce; border: 1px solid black;">
                                {!! number_format($pc_cong_hien)?? '' !!}
                            </td>
                        @elseif ($i == 28)
                            <td style="text-align: center; vertical-align: middle; background: #e5c3ce; border: 1px solid black;">
                                {!! number_format($pc_ns_cong_viec)?? '' !!}
                            </td>
                        @elseif ($i == 29)
                            <td style="text-align: center; vertical-align: middle; background: #e5c3ce; border: 1px solid black;">
                                {!! number_format($pc_dien_thoai)?? '' !!}
                            </td>
                        @elseif ($i == 30)
                            <td style="text-align: center; vertical-align: middle; background: #e5c3ce; border: 1px solid black;">
                                {!! number_format($pc_cong_viec)?? '' !!}
                            </td>
                        @elseif ($i == 31)
                            <td style="text-align: center; vertical-align: middle; background: #e5c3ce; border: 1px solid black;">
                                {!! number_format($pc_dac_thu)?? '' !!}
                            </td>
                        @elseif ($i == 32)
                            <td style="text-align: center; vertical-align: middle; background: #e5c3ce; border: 1px solid black;">
                                {!! number_format($pc_khac)?? '' !!}
                            </td>
                        @elseif ($i == 33)
                            <td style="text-align: center; vertical-align: middle; background: #e5c3ce; border: 1px solid black;">
                                {!! number_format($pc_chuyen_can)?? '' !!}
                            </td>
                        @elseif ($i == 34)
                            <td style="text-align: center; vertical-align: middle; background: #e5c3ce; border: 1px solid black;">
                                {!! number_format(array_sum(array_column($payroll_details->toArray(), 'total_salary')))?? '' !!}
                            </td>
                        @elseif ($i == 35)
                            <td style="text-align: center; vertical-align: middle; background: #cca0ae; border: 1px solid black;">
                                {!! number_format($bhxh_user) ?? '' !!}
                            </td>
                        @elseif ($i == 36)
                            <td style="text-align: center; vertical-align: middle; background: #cca0ae; border: 1px solid black;">
                                {!! number_format($bhyt_user) ?? '' !!}
                            </td>
                        @elseif ($i == 37)
                            <td style="text-align: center; vertical-align: middle; background: #cca0ae; border: 1px solid black;">
                                {!! number_format($union_user) ?? '' !!}
                            </td>
                        @elseif ($i == 38)
                            <td style="text-align: center; vertical-align: middle; background: #cca0ae; border: 1px solid black;">
                                {!! number_format($bhtn_user) ?? '' !!}
                            </td>
                        @elseif ($i == 39)
                            <td style="text-align: center; vertical-align: middle; background: #cca0ae; border: 1px solid black;">
                                {!! number_format($bhxh_company) ?? '' !!}
                            </td>
                        @elseif ($i == 40)
                            <td style="text-align: center; vertical-align: middle; background: #cca0ae; border: 1px solid black;">
                                {!! number_format($bhyt_company) ?? '' !!}
                            </td>
                        @elseif ($i == 41)
                            <td style="text-align: center; vertical-align: middle; background: #cca0ae; border: 1px solid black;">
                                {!! number_format($union_company) ?? '' !!}
                            </td>
                        @elseif ($i == 42)
                            <td style="text-align: center; vertical-align: middle; background: #cca0ae; border: 1px solid black;">
                                {!! number_format($bhtn_company) ?? '' !!}
                            </td>
                        @elseif ($i == 43)
                            <td style="text-align: center; vertical-align: middle; background: #cca0ae; border: 1px solid black;">
                                0
                            </td>
                        @elseif ($i == 44)
                            <?php 
                                $total_deduction = array_sum(array_column($payroll_details->toArray(), 'total_deduction'));
                                $total_deduction_tax = array_sum(array_column($payroll_details->toArray(), 'total_deduction_tax'));
                            ?>
                            <td style="text-align: center; vertical-align: middle; background: #cca0ae; border: 1px solid black;">
                                {!! number_format($total_deduction - $total_deduction_tax)?? '' !!}
                            </td> 
                            
                        @elseif ($i == 45) 
                            <td style="text-align: center; vertical-align: middle; background: #cca0ae; border: 1px solid black;">
                                {!! number_format(array_sum(array_column($payroll_details->toArray(), 'total_deduction_tax')))?? '' !!}
                            </td>  
                        @elseif ($i == 46)
                            <?php 
                                $total_payoff = array_sum(array_column($payroll_details->toArray(), 'total_payoff'));
                                $total_payoff_tax = array_sum(array_column($payroll_details->toArray(), 'total_payoff_tax'));
                            ?>
                            <td style="text-align: center; vertical-align: middle; background: #cca0ae; border: 1px solid black;">
                                {!! number_format($total_payoff - $total_payoff_tax)?? '' !!}
                            </td> 
                        @elseif ($i == 47)
                            <td style="text-align: center; vertical-align: middle; background: #cca0ae; border: 1px solid black;">
                                {!! number_format(array_sum(array_column($payroll_details->toArray(), 'total_payoff_tax')))?? '' !!}
                            </td>  
                        @elseif ($i == 48)
                            <td style="text-align: center; vertical-align: middle; background: #cca0ae; border: 1px solid black;">
                                {!! number_format(array_sum(array_column($payroll_details->toArray(), 'income_taxes')))?? '' !!}
                            </td> 
                        
                        @elseif ($i == 50)
                            <td style="text-align: center; vertical-align: middle; background: #cca0ae; border: 1px solid black;">
                                {!! number_format(array_sum(array_column($payroll_details->toArray(), 'family_allowances')))?? '' !!}
                            </td> 
                        @elseif ($i == 51)
                            <td style="text-align: center; vertical-align: middle; background: #cca0ae; border: 1px solid black;">
                                {!! number_format(array_sum(array_column($payroll_details->toArray(), 'taxable_income')))?? '' !!}
                            </td>  
                        @elseif ($i == 52)
                            <td style="text-align: center; vertical-align: middle; background: #cca0ae; border: 1px solid black;">
                                {!! number_format(array_sum(array_column($payroll_details->toArray(), 'personal_income_tax')))?? '' !!}

                            </td>  
                        @elseif ($i == 55)
                            <td style="text-align: center; vertical-align: middle; background: #cca0ae; border: 1px solid black;">
                                {!! number_format(array_sum(array_column($payroll_details->toArray(), 'total_real_salary')))?? '' !!}
                            </td>  
                        @else   
                            <td style="background: #e5c3ce; border: 1px solid black;"></td>
                        @endif
                        
                    @endfor
                </tr>
                
            </tbody>
        </table>
    </div>
</body>
</html>

