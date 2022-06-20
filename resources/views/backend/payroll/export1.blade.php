<div margin-bottom="20">
    <h3>Bảng lương tháng {{ $payrolls[0]->month }}/{{ $payrolls[0]->year }} - {{ $payrolls[0]->company->name }}</h3>
</div>
    <?php $cate_allowances = \App\Models\AllowanceCategory::cateAllowance() ;?>
    <table>
        <thead>
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
                <th rowspan="2"  style="text-align: center; vertical-align: middle; width: 15px; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                    <span class="uppercase">FIXED WORKING DAYS </span> <br><br>
                    <span>Số ngày công theo tháng</span>
                </th>
                <th colspan="9"  style="text-align: center; vertical-align: middle; height: 50px; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                    <br>
                    <span class="uppercase" style="margin-top: 10px">ACTUAL WORKING DAYS</span><br>
                    <span>Ngày công thực tế</span><br>
                    {{-- <span>(làm việc ở công ty)</span> --}}
                </th>
                

                {{-- <th rowspan="2"  style="text-align: right; vertical-align: middle;" class="tdbreak">
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
                {{-- <th rowspan="2"  style="text-align: right; vertical-align: middle;" class="tdbreak">
                    <span class="uppercase">Night working day</span><br><br>
                    <span>Ngày công làm việc đêm</span>
                </th> --}}
                <th rowspan="2"  style="text-align: center; vertical-align: middle; width: 15px; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                    <span>SALARY PROBATION
                       </span><br><br>
                    <span>Lương thử việc</span>
                </th>
                <th rowspan="2"  style="text-align: center; vertical-align: middle; width: 15px; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                    <span>BASIC 
                        RATE
                       </span><br><br>
                    <span>Lương cơ bản</span>
                </th>
                <th  rowspan="2" style="text-align: center; vertical-align: middle; width: 15px; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                    <span>BASIC 
                        RATE
                       </span><br><br>
                    <span>Lương đóng BH</span>
                </th>
                <th  rowspan="2" style="text-align: center; vertical-align: middle; width: 15px; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                    <span class="uppercase">Actual Work. Day</span> <br><br>
                    <span>Lương làm việc thực tế</span>
                </th>
                <th  rowspan="2" style="text-align: center; vertical-align: middle; width: 15px; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                    <span class="uppercase">Actual night Work. Day</span><br><br>
                    <span>Lương trả khi làm đêm (30%)</span>
                </th>
                <th  colspan="2" style="text-align: center; vertical-align: middle; width: 15px; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                    <span class="uppercase"> Actual Work Salary 	

                    </span> <br><br>
                    <span>Lương làm thêm</span>
                </th>
                <th colspan="{{ count($cate_allowances) + 2 }}" style="text-align: center; vertical-align: middle; background: #79CDCD; border: 1px solid black;">
                    <span class="uppercase">ALLOWANCE</span><br><br>
                    <span>Phụ cấp</span>
                </th>
                <th rowspan="2" style="text-align: center; vertical-align: middle; width: 15px; background: #79CDCD; border: 1px solid black;" class="tdbreak">
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

                <th rowspan="2" style="text-align: center; vertical-align: middle; width: 15px; background: #79CDCD; border: 1px solid black;" class="tdbreak">
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

                <th rowspan="2" style="text-align: center; vertical-align: middle; width: 15px; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                    <span class="uppercase">Taxable
                        Income
                        </span><br><br>
                    <span class="">Thu nhập chịu thuế</span>
                </th>

                <th rowspan="2" style="text-align: center; vertical-align: middle; width: 15px; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                    <span class="uppercase">No of dependant
                        </span><br><br>
                    <span class="">Số người phụ thuộc</span>
                </th>

                <th rowspan="2" style="text-align: center; vertical-align: middle; width: 15px; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                    <span class="uppercase">Self relief and dependant relief
                        </span><br><br>
                    <span class="">Khấu trừ gia cảnh</span>
                </th>

                <th rowspan="2" style="text-align: center; vertical-align: middle; width: 15px; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                    <span class="uppercase">Assessable Income
                        </span><br><br>
                    <span class="">Thu nhập tính thuế</span>
                </th>

                <th rowspan="2" style="text-align: center; vertical-align: middle; width: 15px; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                    <span class="uppercase">Income tax
                        </span><br><br>
                    <span class="">Thuế TNCN</span>
                </th>
                <th rowspan="2" style="text-align: center; vertical-align: middle; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                    <span class="uppercase">Điểm KPI
                        </span><br><br>
                    <span>trong tháng</span>
                </th>
                <th rowspan="2" style="text-align: center; vertical-align: middle; width: 15px; background: #79CDCD; border: 1px solid black;" class="tdbreak">
                    <span class="uppercase">
                        </span><br><br>
                    <span>Các khoản điều chỉnh khác</span>
                </th>
                <th rowspan="2" style="text-align: center; vertical-align: middle; width: 15px; background: #79CDCD; border: 1px solid black;" class="tdbreak">
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
                    
                    <th style="text-align: center; vertical-align: middle;  width: 15px; background: #79CDCD; border: 1px solid black;" class="tdbreak">Miễn thuế</th>
                    <th style="text-align: center; vertical-align: middle;  width: 15px; background: #79CDCD; border: 1px solid black;" class="tdbreak">Chịu thuế</th>

                    <th style="text-align: center; vertical-align: middle; width: 15px; background: #79CDCD; border: 1px solid black;" class="tdbreak">Phụ cấp ăn trưa,<br> ăn ca (NON-TAX)</th>

                    <th style="text-align: center; vertical-align: middle; width: 15px; background: #79CDCD; border: 1px solid black;" class="tdbreak">Phụ cấp ăn trưa, <br> ăn ca (TAX)</th>

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
                    
                    <th style="text-align: center; vertical-align: middle; width: 15px; background: #79CDCD; border: 1px solid black;" class="tdbreak">Miễn thuế</th>
                    <th style="text-align: center; vertical-align: middle; width: 15px; background: #79CDCD; border: 1px solid black;" class="tdbreak">Chịu thuế</th>

                    <th style="text-align: center; vertical-align: middle; width: 15px; background: #79CDCD; border: 1px solid black;" class="tdbreak">Miễn thuế</th>
                    <th style="text-align: center; vertical-align: middle; width: 15px; background: #79CDCD; border: 1px solid black;" class="tdbreak">Chịu thuế</th>
                    
                </tr>
               
                
            </tr>
        </thead>
        <tbody>
            @if (count($payrolls) > 0)
                <?php $total_basic_salary_tv = $total_basic_salary_hd = $total_salary_bh = $total_working_salary_tax = 0; 
                        $total_working_salary_non_tax = $total_salary_ot_non_tax = $total_salary_ot_tax = $total_food_allowance_nonTax = $total_food_allowance_tax = 0;
                        $total_pc_di_lai = $total_pc_trach_nhiem = $total_pc_cong_hien = $total_pc_ns_cong_viec = $total_pc_dien_thoai = $total_pc_dac_thu = $total_pc_khac = $total_pc_chuyen_can = 0;
                        $total_total_salary = $total_bhxh_user = $total_bhyt_user = $total_union_user = $total_bhtn_user = $total_bhxh_company = $total_bhyt_company = $total_union_company = $total_bhtn_company = 0;
                        $total_deduction_non_tax = $total_deduction_tax = $total_payoff_non_tax = $total_payoff_tax = $total_income_taxes = $total_family_allowances = $total_taxable_income = 0;
                        $total_personal_income_tax = $total_total_real_salary = 0;
                ?>
                @foreach ($payrolls as $items)
                    @if (count($items->userPayroll))
                        <tr style="background: #f7bf90"><td style="font-weight: 700">{{ $items->department->name }}</td></tr>
                        <?php $pc_di_lai = $pc_trach_nhiem = $pc_cong_hien = $pc_ns_cong_viec = $pc_dien_thoai = $pc_dac_thu = $pc_khac = $pc_chuyen_can = 0; ?>
                        <?php $i = 0; ?>

                        @foreach ($items->userPayroll as $k => $item)  
                            <?php $data = \App\Models\Payroll::userPayrollDetail($item->id); 
                                $totalDayRequest = \App\Models\Payroll::totalDayRequest($item->user_id, $payrolls[0]->month, $payrolls[0]->year);
                                $totalWorkDepartment = \App\Models\Payroll::totalWorkDepartment($item->user_id, $payrolls[0]->month, $payrolls[0]->year, $item->staff->department_id);

                                $bhxh_user += json_decode(json_encode($data['user_payroll']->bh->bhxh_user), true);
                                $bhyt_user += json_decode(json_encode($data['user_payroll']->bh->bhyt_user), true);
                                $union_user += json_decode(json_encode($data['user_payroll']->bh->union_user), true);
                                $bhtn_user += json_decode(json_encode($data['user_payroll']->bh->bhtn_user), true);


                                $bhxh_company += json_decode(json_encode($data['user_payroll']->bh->bhxh_company), true);
                                $bhyt_company += json_decode(json_encode($data['user_payroll']->bh->bhyt_company), true);
                                $bhtn_company += json_decode(json_encode($data['user_payroll']->bh->bhtn_company), true);
                                $union_company += json_decode(json_encode($data['user_payroll']->bh->union_company), true);
                            ?>
                            <tr class="hover" data-index="{{ $i++ }}">
                                <td style="text-align: center; vertical-align: middle; border: 1px solid black;">{{ $k + 1 }}</td>
                                <td class="sticky-col" style="vertical-align: middle; border: 1px solid black;">{!! $item->staff->fullname ?? '' !!}</td>
                                <td  style="text-align: center; vertical-align: middle; border: 1px solid black;">{!! $item->staff->code ?? '' !!}</td>
                                <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                                    {{ $totalDayRequest ?? '' }}
                                </td>
                            
            
            
                                <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                                    {{ $data['user_payroll']->ca_ngay_tv ?? 0 }}
                                </td>
                                <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                                    {{ $data['user_payroll']->ca_ngay_hd ?? 0 }}
                                </td>
                                <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                                    {{ $data['user_payroll']->ca_dem_tv ?? 0 }}
                                </td>
                                <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                                    {{ $data['user_payroll']->ca_dem_hd ?? 0 }}
                                </td>
                                <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                                    {{ \App\StaffDayOff::countDayOffs($item->user_id, $payrolls[0]->month, $payrolls[0]->year, 'T', $item->staff->department_id) }}
                                </td>
                                <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                                    {{ \App\Models\Payroll::countTotalInMonthForTimeKeeping($item->user_id, $payrolls[0]->month, $payrolls[0]->year, $item->staff->department_id) }}
                                </td>
                                <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                                    {{ \App\StaffDayOff::countDayOffs($item->user_id, $payrolls[0]->month, $payrolls[0]->year, 'C', $item->staff->department_id) }}
                                </td>
                                <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                                    {{ \App\Models\Payroll::nghiKhongLuong($item->user_id, $payrolls[0]->month, $payrolls[0]->year, 'O', $item->staff->department_id) }}
                                </td>
                                <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                                    {{ $totalWorkDepartment ?? '' }}
                                </td>
            
                                {{-- <td style="text-align: right; vertical-align: middle;">
                                    <input type="text" value="{{ \App\StaffDayOff::countDayOffs($item->user_id, $payrolls[0]->month, $payrolls[0]->year, 'T') }}" data-name="total_work_department" data-user-id="{!! $item->user_id !!}" class="form-control ct">
                                </td> --}}
                                <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                                    {!! $data['user_payroll']->an_chinh ?? '' !!}
                                </td>
                                <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                                    {!! $data['user_payroll']->an_phu ?? '' !!}
                                </td>
                                {{-- <td style="text-align: right; vertical-align: middle;">
                                    <input type="text" value="{!! $data['user_payroll']->ca_dem_hd +  $data['user_payroll']->ca_dem_tv !!}" data-name="ca_dem_hd" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct">
                                </td> --}}
                                <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                                    {!! number_format($item->basic_salary_tv) ?? '' !!}
                                </td>
                                <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                                    {!! number_format($item->basic_salary_hd) ?? '' !!}
                                </td>
                                <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                                    {!! number_format($item->salary_bh) ?? '' !!}
                                </td>
                                <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                                    {!! number_format($item->working_salary_tax )?? '' !!}
                                </td>
                                <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                                    {!! number_format($item->working_salary_non_tax )?? '' !!}
                                </td>
                                <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                                    {!! number_format($item->salary_ot_non_tax )?? '' !!}
                                </td>
            
                                <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                                    {!! number_format($item->salary_ot_tax )?? '' !!}
                                </td>
            
                                <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                                    {!! number_format($item->food_allowance_nonTax )?? '' !!}
                                </td>
                                <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                                    {!! number_format($item->food_allowance_tax )?? '' !!}
                                </td>
                                
            
                                @foreach ($cate_allowances as $k1 => $cate)

                                    <?php 
                                        $tt = intval(str_replace(".","", (String)\App\Models\Payroll::calculateAllowance($item->user_id, $k1, $totalDayRequest, $totalWorkDepartment, $payrolls[0]->month, $payrolls[0]->year, $item->salary_concurrent,'', $item->payroll->department_id)));
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
                                    <td style="text-align: right; vertical-align: middle; border: 1px solid black;" class="tdbreak">
                                        {!! number_format($tt) ?? '' !!}
                                    </td>
                                    
                                @endforeach
                                
                                <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                                    {!! number_format($item->total_salary )?? '' !!}
                                </td>
            
                                <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                                    {!! number_format($data['user_payroll']->bh->bhxh_user )?? '' !!}
                                </td>
                                <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                                    {!! number_format($data['user_payroll']->bh->bhyt_user )?? '' !!}
                                </td>
                                <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                                    {!! number_format($data['user_payroll']->bh->union_user )?? '' !!}
                                </td>
                                <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                                    {!! number_format($data['user_payroll']->bh->bhtn_user )?? '' !!}
                                </td>
            
                                <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                                    {!! number_format($data['user_payroll']->bh->bhxh_company )?? '' !!}
                                </td>
            
                                <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                                    {!! number_format($data['user_payroll']->bh->bhyt_company )?? '' !!}
                                </td>
                                <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                                    {!! number_format($data['user_payroll']->bh->union_company )?? '' !!}
                                </td>
            
                                <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                                    {!! number_format($data['user_payroll']->bh->bhtn_company )?? '' !!}
                                </td>
            
            
                                <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                                    0
                                </td>
            
                                <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                                    {{ number_format(($item->total_deduction - $item->total_deduction_tax)) }}
                                </td>
            
                                <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                                    {{ number_format($item->total_deduction_tax) }}
                                </td>
                            
                                <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                                    {{ number_format(($item->total_payoff - $item->total_payoff_tax)) }}
                                </td>
            
                                <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                                    {{ number_format($item->total_payoff_tax) }}
                                </td>
                            
            
                                <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                                    {!! number_format($item->income_taxes )?? '' !!}
                                </td>
                                <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                                    {{ \App\User::countUserRelationship($item->user_id) }}
                                </td>
                                <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                                    {!! number_format($item->family_allowances )?? '' !!}
                                </td>
                                <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                                    {!! number_format($item->taxable_income )?? '' !!}
                                </td>
                                <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                                    {!! number_format($item->personal_income_tax )?? '' !!}
                                </td>
                                <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                                    {{ \App\Models\Payroll::getKpi($item->user_id, $payrolls[0]->month, $payrolls[0]->year) ?? '0' }}
                                </td>
                                <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                                    {!! number_format($item->total_impale )?? '' !!}
                                </td>
                                <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                                    {!! number_format($item->total_real_salary )?? '' !!}
                                </td>
                                
                                {{-- <td>
                                    <a href="{{ route('admin.payrolls.user-detail', $item->id) }}" class="btn btn-info btn-xs">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                </td> --}}
                            </tr>
                            
                        @endforeach

                        <?php 
                            $basic_salary_tv = array_sum(array_column($items->userPayroll->toArray(), 'basic_salary_tv')); 
                            $basic_salary_hd = array_sum(array_column($items->userPayroll->toArray(), 'basic_salary_hd')); 
                            $salary_bh = array_sum(array_column($items->userPayroll->toArray(), 'salary_bh')); 
                            $working_salary_tax = array_sum(array_column($items->userPayroll->toArray(), 'working_salary_tax')); 
                            $working_salary_non_tax = array_sum(array_column($items->userPayroll->toArray(), 'working_salary_non_tax')); 
                            $salary_ot_non_tax = array_sum(array_column($items->userPayroll->toArray(), 'salary_ot_non_tax')); 
                            $salary_ot_tax = array_sum(array_column($items->userPayroll->toArray(), 'salary_ot_tax')); 
                            $food_allowance_nonTax = array_sum(array_column($items->userPayroll->toArray(), 'food_allowance_nonTax')); 
                            $food_allowance_tax = array_sum(array_column($items->userPayroll->toArray(), 'food_allowance_tax')); 
                            $total_salary = array_sum(array_column($items->userPayroll->toArray(), 'total_salary')); 


                            $total_basic_salary_tv += $basic_salary_tv;
                            $total_basic_salary_hd += $basic_salary_hd;
                            $total_salary_bh += $salary_bh;
                            $total_working_salary_tax += $working_salary_tax;
                            $total_working_salary_non_tax += $working_salary_non_tax;
                            $total_salary_ot_non_tax += $salary_ot_non_tax;
                            $total_salary_ot_tax += $salary_ot_tax;
                            $total_food_allowance_nonTax += $food_allowance_nonTax;
                            $total_food_allowance_tax += $food_allowance_tax;
                            $total_total_salary += $total_salary;

                            $total_pc_di_lai += $pc_di_lai;
                            $total_pc_trach_nhiem += $pc_trach_nhiem;
                            $total_pc_trach_nhiem += $pc_trach_nhiem;
                            $total_pc_cong_hien += $pc_cong_hien;
                            $total_pc_ns_cong_viec += $pc_ns_cong_viec;
                            $total_pc_dien_thoai += $pc_dien_thoai;
                            $total_pc_dac_thu += $pc_dac_thu;
                            $total_pc_khac += $pc_khac;
                            $total_pc_chuyen_can += $pc_chuyen_can;

                            $total_bhxh_user += $bhxh_user;
                            $total_bhyt_user += $bhyt_user;
                            $total_union_user += $union_user;
                            $total_bhtn_user += $bhtn_user;

                            $total_bhxh_company += $bhxh_company;
                            $total_bhyt_company += $bhyt_company;
                            $total_union_company += $union_company;
                            $total_bhtn_company += $bhtn_company;

                            $total_deduction = array_sum(array_column($items->userPayroll->toArray(), 'total_deduction'));
                            $deduction_tax = array_sum(array_column($items->userPayroll->toArray(), 'total_deduction_tax'));
                            $deduction_non_tax = $total_deduction - $deduction_tax;

                            $total_deduction_non_tax += $deduction_non_tax; 
                            $total_deduction_tax += $deduction_tax; 

                            $total_payoff = array_sum(array_column($items->userPayroll->toArray(), 'total_payoff'));
                            $payoff_tax = array_sum(array_column($items->userPayroll->toArray(), 'total_payoff_tax'));
                            $payoff_non_tax = $total_payoff  - $payoff_tax;

                            $total_payoff_tax += $payoff_tax;
                            $total_payoff_non_tax += $payoff_non_tax;

                            $income_taxes = array_sum(array_column($items->userPayroll->toArray(), 'income_taxes'));
                            $family_allowances = array_sum(array_column($items->userPayroll->toArray(), 'family_allowances'));
                            $taxable_income = array_sum(array_column($items->userPayroll->toArray(), 'taxable_income'));
                            $personal_income_tax = array_sum(array_column($items->userPayroll->toArray(), 'personal_income_tax'));
                            $total_real_salary = array_sum(array_column($items->userPayroll->toArray(), 'total_real_salary'));

                            $total_income_taxes += $income_taxes;
                            $total_family_allowances += $family_allowances;
                            $total_taxable_income += $taxable_income;
                            $total_personal_income_tax += $personal_income_tax;
                            $total_total_real_salary += $total_real_salary;
                        ?>

                        <tr>
                            @for ($i = 1; $i <= 55; $i++)
                                @if ($i == 2)
                                 <td style="background: #e5c3ce; border: 1px solid black; text-align: center">Tổng</td>
                                 @elseif ($i == 16)
                                    <td style="text-align: right; vertical-align: middle; background: #e5c3ce; border: 1px solid black;">
                                        {{-- {!! number_format(array_sum(array_column($items->userPayroll->toArray(), 'basic_salary_tv')))?? '' !!} --}}
                                        {!! number_format($basic_salary_tv)?? '' !!}
                                    </td>
                                @elseif ($i == 17)
                                    <td style="text-align: right; vertical-align: middle; background: #e5c3ce; border: 1px solid black;">
                                        {!! number_format($basic_salary_hd)?? '' !!}
                                    </td>
                                @elseif ($i == 18)
                                    <td style="text-align: right; vertical-align: middle; background: #e5c3ce; border: 1px solid black;">
                                        {!! number_format($salary_bh)?? '' !!}
                                        {{-- {!! number_format(array_sum(array_column($items->userPayroll->toArray(), 'salary_bh')))?? '' !!} --}}
                                    </td>
                                @elseif ($i == 19)
                                    <td style="text-align: right; vertical-align: middle; background: #e5c3ce; border: 1px solid black;">
                                        {!! number_format($working_salary_tax)?? '' !!}

                                        {{-- {!! number_format(array_sum(array_column($items->userPayroll->toArray(), 'working_salary_tax')))?? '' !!} --}}
                                    </td>
                                @elseif ($i == 20)
                                    <td style="text-align: right; vertical-align: middle; background: #e5c3ce; border: 1px solid black;">
                                        {!! number_format($working_salary_non_tax)?? '' !!}

                                        {{-- {!! number_format(array_sum(array_column($items->userPayroll->toArray(), 'working_salary_non_tax')))?? '' !!} --}}
                                    </td>
        
                                @elseif ($i == 21)
                                    <td style="text-align: right; vertical-align: middle; background: #e5c3ce; border: 1px solid black;">
                                        {!! number_format($salary_ot_non_tax)?? '' !!}

                                        {{-- {!! number_format(array_sum(array_column($items->userPayroll->toArray(), 'salary_ot_non_tax')))?? '' !!} --}}
                                    </td>
                                @elseif ($i == 22)
                                    <td style="text-align: right; vertical-align: middle; background: #e5c3ce; border: 1px solid black;">
                                        {!! number_format($salary_ot_tax)?? '' !!}

                                        {{-- {!! number_format(array_sum(array_column($items->userPayroll->toArray(), 'salary_ot_tax')))?? '' !!} --}}
                                    </td>
                                @elseif ($i == 23)
                                    <td style="text-align: right; vertical-align: middle; background: #e5c3ce; border: 1px solid black;">
                                        {!! number_format($food_allowance_nonTax)?? '' !!}

                                        {{-- {!! number_format(array_sum(array_column($items->userPayroll->toArray(), 'food_allowance_nonTax')))?? '' !!} --}}
                                    </td>
        
                                @elseif ($i == 24)
                                    <td style="text-align: right; vertical-align: middle; background: #e5c3ce; border: 1px solid black;">
                                        {!! number_format($food_allowance_tax)?? '' !!}

                                        {{-- {!! number_format(array_sum(array_column($items->userPayroll->toArray(), 'food_allowance_tax')))?? '' !!} --}}
                                    </td>
                                @elseif ($i == 25)
                                    <td style="text-align: right; vertical-align: middle; background: #e5c3ce; border: 1px solid black;">
                                        {!! number_format($pc_di_lai)?? '' !!}
                                    </td>
                                @elseif ($i == 26)
                                    <td style="text-align: right; vertical-align: middle; background: #e5c3ce; border: 1px solid black;">
                                        {!! number_format($pc_trach_nhiem)?? '' !!}
                                    </td>
                                @elseif ($i == 27)
                                    <td style="text-align: right; vertical-align: middle; background: #e5c3ce; border: 1px solid black;">
                                        {!! number_format($pc_cong_hien)?? '' !!}
                                    </td>
                                @elseif ($i == 28)
                                    <td style="text-align: right; vertical-align: middle; background: #e5c3ce; border: 1px solid black;">
                                        {!! number_format($pc_ns_cong_viec)?? '' !!}
                                    </td>
                                @elseif ($i == 29)
                                    <td style="text-align: right; vertical-align: middle; background: #e5c3ce; border: 1px solid black;">
                                        {!! number_format($pc_dien_thoai)?? '' !!}
                                    </td>
                                @elseif ($i == 30)
                                    <td style="text-align: right; vertical-align: middle; background: #e5c3ce; border: 1px solid black;">
                                        {!! number_format($pc_cong_viec)?? '' !!}
                                    </td>
                                @elseif ($i == 31)
                                    <td style="text-align: right; vertical-align: middle; background: #e5c3ce; border: 1px solid black;">
                                        {!! number_format($pc_dac_thu)?? '' !!}
                                    </td>
                                @elseif ($i == 32)
                                    <td style="text-align: right; vertical-align: middle; background: #e5c3ce; border: 1px solid black;">
                                        {!! number_format($pc_khac)?? '' !!}
                                    </td>
                                @elseif ($i == 33)
                                    <td style="text-align: right; vertical-align: middle; background: #e5c3ce; border: 1px solid black;">
                                        {!! number_format($pc_chuyen_can)?? '' !!}
                                    </td>
                                @elseif ($i == 34)
                                    <td style="text-align: right; vertical-align: middle; background: #e5c3ce; border: 1px solid black;">
                                        {!! number_format(array_sum(array_column($items->userPayroll->toArray(), 'total_salary')))?? '' !!}
                                    </td>
                                @elseif ($i == 35)
                                    <td style="text-align: right; vertical-align: middle; background: #cca0ae; border: 1px solid black;">
                                        {!! number_format($bhxh_user) ?? '' !!}
                                    </td>
                                @elseif ($i == 36)
                                    <td style="text-align: right; vertical-align: middle; background: #cca0ae; border: 1px solid black;">
                                        {!! number_format($bhyt_user) ?? '' !!}
                                    </td>
                                @elseif ($i == 37)
                                    <td style="text-align: right; vertical-align: middle; background: #cca0ae; border: 1px solid black;">
                                        {!! number_format($union_user) ?? '' !!}
                                    </td>
                                @elseif ($i == 38)
                                    <td style="text-align: right; vertical-align: middle; background: #cca0ae; border: 1px solid black;">
                                        {!! number_format($bhtn_user) ?? '' !!}
                                    </td>
                                @elseif ($i == 39)
                                    <td style="text-align: right; vertical-align: middle; background: #cca0ae; border: 1px solid black;">
                                        {!! number_format($bhxh_company) ?? '' !!}
                                    </td>
                                @elseif ($i == 40)
                                    <td style="text-align: right; vertical-align: middle; background: #cca0ae; border: 1px solid black;">
                                        {!! number_format($bhyt_company) ?? '' !!}
                                    </td>
                                @elseif ($i == 41)
                                    <td style="text-align: right; vertical-align: middle; background: #cca0ae; border: 1px solid black;">
                                        {!! number_format($union_company) ?? '' !!}
                                    </td>
                                @elseif ($i == 42)
                                    <td style="text-align: right; vertical-align: middle; background: #cca0ae; border: 1px solid black;">
                                        {!! number_format($bhtn_company) ?? '' !!}
                                    </td>
                                @elseif ($i == 43)
                                    <td style="text-align: right; vertical-align: middle; background: #cca0ae; border: 1px solid black;">
                                        0
                                    </td>
                                @elseif ($i == 44)
                                   
                                    <td style="text-align: right; vertical-align: middle; background: #cca0ae; border: 1px solid black;">
                                        {!! number_format($deduction_non_tax)?? '' !!}
                                    </td> 
                                    
                                @elseif ($i == 45) 
                                    <td style="text-align: right; vertical-align: middle; background: #cca0ae; border: 1px solid black;">
                                        {!! number_format($deduction_tax)?? '' !!}

                                        {{-- {!! number_format(array_sum(array_column($items->userPayroll->toArray(), 'total_deduction_tax')))?? '' !!} --}}
                                    </td>  
                                @elseif ($i == 46)
                                   
                                    <td style="text-align: right; vertical-align: middle; background: #cca0ae; border: 1px solid black;">
                                        {!! number_format($payoff_non_tax)?? '' !!}
                                    </td> 
                                @elseif ($i == 47)
                                    <td style="text-align: right; vertical-align: middle; background: #cca0ae; border: 1px solid black;">
                                        {!! number_format($payoff_tax)?? '' !!}
                                        {{-- {!! number_format(array_sum(array_column($items->userPayroll->toArray(), 'total_payoff_tax')))?? '' !!} --}}
                                    </td>  
                                @elseif ($i == 48)
                                    <td style="text-align: right; vertical-align: middle; background: #cca0ae; border: 1px solid black;">
                                        {!! number_format($income_taxes)?? '' !!}

                                        {{-- {!! number_format(array_sum(array_column($items->userPayroll->toArray(), 'income_taxes')))?? '' !!} --}}
                                    </td> 
                                
                                @elseif ($i == 50)
                                    <td style="text-align: right; vertical-align: middle; background: #cca0ae; border: 1px solid black;">
                                        {!! number_format($family_allowances)?? '' !!}
                                        {{-- {!! number_format(array_sum(array_column($items->userPayroll->toArray(), 'family_allowances')))?? '' !!} --}}
                                    </td> 
                                @elseif ($i == 51)
                                    <td style="text-align: right; vertical-align: middle; background: #cca0ae; border: 1px solid black;">
                                        {!! number_format($taxable_income)?? '' !!}
                                        {{-- {!! number_format(array_sum(array_column($items->userPayroll->toArray(), 'taxable_income')))?? '' !!} --}}
                                    </td>  
                                @elseif ($i == 52)
                                    <td style="text-align: right; vertical-align: middle; background: #cca0ae; border: 1px solid black;">
                                        {!! number_format($personal_income_tax)?? '' !!}
                                        {{-- {!! number_format(array_sum(array_column($items->userPayroll->toArray(), 'personal_income_tax')))?? '' !!} --}}
        
                                    </td>  
                                @elseif ($i == 55)
                                    <td style="text-align: right; vertical-align: middle; background: #cca0ae; border: 1px solid black;">
                                        {!! number_format($total_real_salary)?? '' !!}
                                        {{-- {!! number_format(array_sum(array_column($items->userPayroll->toArray(), 'total_real_salary')))?? '' !!} --}}
                                    </td>  
                                @else   
                                    <td style="background: #e5c3ce; border: 1px solid black;"></td>
                                @endif
                                
                            @endfor
                        </tr>
                    @endif
                   
                @endforeach    
                <tr>
                    @for ($i = 1; $i <= 55; $i++)
                        @if ($i == 2)
                         <td style="background: #ffc33e; border: 1px solid black; text-align: center">TỔNG THEO CÔNG TY</td>
                        @elseif ($i == 16)
                            <td style="text-align: right; vertical-align: middle; background: #ffc33e; border: 1px solid black;">
                                {!! number_format($total_basic_salary_tv)?? '' !!}

                            </td>
                        @elseif ($i == 17)
                            <td style="text-align: right; vertical-align: middle; background: #ffc33e; border: 1px solid black;">
                                {!! number_format($total_basic_salary_hd)?? '' !!}
                            </td>
                        @elseif ($i == 18)
                            <td style="text-align: right; vertical-align: middle; background: #ffc33e; border: 1px solid black;">
                                {!! number_format($total_salary_bh)?? '' !!}
                                {{-- {!! number_format(array_sum(array_column($items->userPayroll->toArray(), 'salary_bh')))?? '' !!} --}}
                            </td>
                        @elseif ($i == 19)
                            <td style="text-align: right; vertical-align: middle; background: #ffc33e; border: 1px solid black;">
                                {!! number_format($total_working_salary_tax)?? '' !!}
                                {{-- {!! number_format(array_sum(array_column($items->userPayroll->toArray(), 'working_salary_tax')))?? '' !!} --}}
                            </td>
                        @elseif ($i == 20)
                            <td style="text-align: right; vertical-align: middle; background: #ffc33e; border: 1px solid black;">
                                {!! number_format($total_working_salary_non_tax)?? '' !!}
                                {{-- {!! number_format(array_sum(array_column($items->userPayroll->toArray(), 'working_salary_non_tax')))?? '' !!} --}}
                            </td>

                        @elseif ($i == 21)
                            <td style="text-align: right; vertical-align: middle; background: #ffc33e; border: 1px solid black;">
                                {!! number_format($total_salary_ot_non_tax)?? '' !!}
                                {{-- {!! number_format(array_sum(array_column($items->userPayroll->toArray(), 'salary_ot_non_tax')))?? '' !!} --}}
                            </td>
                        @elseif ($i == 22)
                            <td style="text-align: right; vertical-align: middle; background: #ffc33e; border: 1px solid black;">
                                {!! number_format($total_salary_ot_tax)?? '' !!}
                                {{-- {!! number_format(array_sum(array_column($items->userPayroll->toArray(), 'salary_ot_tax')))?? '' !!} --}}
                            </td>
                        @elseif ($i == 23)
                            <td style="text-align: right; vertical-align: middle; background: #ffc33e; border: 1px solid black;">
                                {!! number_format($total_food_allowance_nonTax)?? '' !!}
                                {{-- {!! number_format(array_sum(array_column($items->userPayroll->toArray(), 'food_allowance_nonTax')))?? '' !!} --}}
                            </td>

                        @elseif ($i == 24)
                            <td style="text-align: right; vertical-align: middle; background: #ffc33e; border: 1px solid black;">
                                {!! number_format($total_food_allowance_tax)?? '' !!}
                                {{-- {!! number_format(array_sum(array_column($items->userPayroll->toArray(), 'food_allowance_tax')))?? '' !!} --}}
                            </td>
                        @elseif ($i == 25)
                            <td style="text-align: right; vertical-align: middle; background: #ffc33e; border: 1px solid black; width: 15px">
                                {!! number_format($total_pc_di_lai)?? '' !!}
                            </td>
                        @elseif ($i == 26)
                            <td style="text-align: right; vertical-align: middle; background: #ffc33e; border: 1px solid black; width: 15px">
                                {!! number_format($total_pc_trach_nhiem)?? '' !!}
                            </td>
                        @elseif ($i == 27)
                            <td style="text-align: right; vertical-align: middle; background: #ffc33e; border: 1px solid black; width: 15px">
                                {!! number_format($total_pc_cong_hien)?? '' !!}
                            </td>
                        @elseif ($i == 28)
                            <td style="text-align: right; vertical-align: middle; background: #ffc33e; border: 1px solid black; width: 15px">
                                {!! number_format($total_pc_ns_cong_viec)?? '' !!}
                            </td>
                        @elseif ($i == 29)
                            <td style="text-align: right; vertical-align: middle; background: #ffc33e; border: 1px solid black; width: 15px">
                                {!! number_format($total_pc_dien_thoai)?? '' !!}
                            </td>
                        @elseif ($i == 30)
                            <td style="text-align: right; vertical-align: middle; background: #ffc33e; border: 1px solid black; width: 15px">
                                {!! number_format($total_pc_cong_viec)?? '' !!}
                            </td>
                        @elseif ($i == 31)
                            <td style="text-align: right; vertical-align: middle; background: #ffc33e; border: 1px solid black; width: 15px">
                                {!! number_format($total_pc_dac_thu)?? '' !!}
                            </td>
                        @elseif ($i == 32)
                            <td style="text-align: right; vertical-align: middle; background: #ffc33e; border: 1px solid black; width: 15px">
                                {!! number_format($total_pc_khac)?? '' !!}
                            </td>
                        @elseif ($i == 33)
                            <td style="text-align: right; vertical-align: middle; background: #ffc33e; border: 1px solid black; width: 15px">
                                {!! number_format($total_pc_chuyen_can)?? '' !!}
                            </td>
                        @elseif ($i == 34)
                            <td style="text-align: right; vertical-align: middle; background: #ffc33e; border: 1px solid black;">
                                {!! number_format($total_total_salary)?? '' !!}
                                {{-- {!! number_format(array_sum(array_column($items->userPayroll->toArray(), 'total_salary')))?? '' !!} --}}
                            </td>
                        @elseif ($i == 35)
                            <td style="text-align: right; vertical-align: middle; background: #ffc33e; border: 1px solid black;  width: 15px">
                                {!! number_format($total_bhxh_user) ?? '' !!}
                            </td>
                        @elseif ($i == 36)
                            <td style="text-align: right; vertical-align: middle; background: #ffc33e; border: 1px solid black; width: 15px">
                                {!! number_format($total_bhyt_user) ?? '' !!}
                            </td>
                        @elseif ($i == 37)
                            <td style="text-align: right; vertical-align: middle; background: #ffc33e; border: 1px solid black; width: 15px">
                                {!! number_format($total_union_user) ?? '' !!}
                            </td>
                        @elseif ($i == 38)
                            <td style="text-align: right; vertical-align: middle; background: #ffc33e; border: 1px solid black; width: 15px">
                                {!! number_format($total_bhtn_user) ?? '' !!}
                            </td>
                        @elseif ($i == 39)
                            <td style="text-align: right; vertical-align: middle; background: #ffc33e; border: 1px solid black; width: 15px">
                                {!! number_format($total_bhxh_company) ?? '' !!}
                            </td>
                        @elseif ($i == 40)
                            <td style="text-align: right; vertical-align: middle; background: #ffc33e; border: 1px solid black; width: 15px">
                                {!! number_format($total_bhyt_company) ?? '' !!}
                            </td>
                        @elseif ($i == 41)
                            <td style="text-align: right; vertical-align: middle; background: #ffc33e; border: 1px solid black; width: 15px">
                                {!! number_format($total_union_company) ?? '' !!}
                            </td>
                        @elseif ($i == 42)
                            <td style="text-align: right; vertical-align: middle; background: #ffc33e; border: 1px solid black; width: 15px">
                                {!! number_format($total_bhtn_company) ?? '' !!}
                            </td>
                        @elseif ($i == 43)
                            <td style="text-align: right; vertical-align: middle; background: #ffc33e; border: 1px solid black;">
                                0
                            </td>
                        @elseif ($i == 44)
                          
                            <td style="text-align: right; vertical-align: middle; background: #ffc33e; border: 1px solid black;">
                                {!! number_format($total_deduction_non_tax)?? '' !!}
                            </td> 
                            
                        @elseif ($i == 45) 
                            <td style="text-align: right; vertical-align: middle; background: #ffc33e; border: 1px solid black;">
                                {!! number_format($total_deduction_tax)?? '' !!}
                                {{-- {!! number_format(array_sum(array_column($items->userPayroll->toArray(), 'total_deduction_tax')))?? '' !!} --}}
                            </td>  
                        @elseif ($i == 46)
                            
                            <td style="text-align: right; vertical-align: middle; background: #ffc33e; border: 1px solid black;">
                                {!! number_format($total_payoff_non_tax)?? '' !!}
                            </td> 
                        @elseif ($i == 47)
                            <td style="text-align: right; vertical-align: middle; background: #ffc33e; border: 1px solid black;">
                                {!! number_format($total_payoff_tax)?? '' !!}
                                {{-- {!! number_format(array_sum(array_column($items->userPayroll->toArray(), 'total_payoff_tax')))?? '' !!} --}}
                            </td>  
                        @elseif ($i == 48)
                            <td style="text-align: right; vertical-align: middle; background: #ffc33e; border: 1px solid black;">
                                {!! number_format($total_income_taxes)?? '' !!}
                                {{-- {!! number_format(array_sum(array_column($items->userPayroll->toArray(), 'income_taxes')))?? '' !!} --}}
                            </td> 
                        
                        @elseif ($i == 50)
                            <td style="text-align: right; vertical-align: middle; background: #ffc33e; border: 1px solid black;">
                                {!! number_format($total_family_allowances)?? '' !!}
                                {{-- {!! number_format(array_sum(array_column($items->userPayroll->toArray(), 'family_allowances')))?? '' !!} --}}
                            </td> 
                        @elseif ($i == 51)
                            <td style="text-align: right; vertical-align: middle; background: #ffc33e; border: 1px solid black;">
                                {!! number_format($total_taxable_income)?? '' !!}
                                {{-- {!! number_format(array_sum(array_column($items->userPayroll->toArray(), 'taxable_income')))?? '' !!} --}}
                            </td>  
                        @elseif ($i == 52)
                            <td style="text-align: right; vertical-align: middle; background: #ffc33e; border: 1px solid black;">
                                {!! number_format($total_personal_income_tax)?? '' !!}
                                {{-- {!! number_format(array_sum(array_column($items->userPayroll->toArray(), 'personal_income_tax')))?? '' !!} --}}

                            </td>  
                        @elseif ($i == 55)
                            <td style="text-align: right; vertical-align: middle; background: #ffc33e; border: 1px solid black;">
                                {!! number_format($total_total_real_salary)?? '' !!}
                                {{-- {!! number_format(array_sum(array_column($ items->userPayroll->toArray(), 'total_real_salary')))?? '' !!} --}}
                            </td>  
                        @else   
                            <td style="background: #ffc33e; border: 1px solid black;"></td>
                        @endif
                        
                    @endfor
                </tr>
            @endif
            
        </tbody>
    </table>