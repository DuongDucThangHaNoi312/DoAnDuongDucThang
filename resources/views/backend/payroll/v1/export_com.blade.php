
    <?php $cate_allowances = \App\Models\AllowanceCategory::cateAllowance();
        $keyAllowanceById = \App\Models\AllowanceCategory::keyAllowanceById();
        $styleAmount = 'text-align: right; vertical-align: middle; border: 1px solid black; width: 15px;';
        $styleLeftText = 'text-align: left; vertical-align: middle; border: 1px solid black;';
        $styleAmountCenter = 'text-align: center; vertical-align: middle; border: 1px solid black;';
        $styleTotalAmount = $styleAmount . ' background: #e5c3ce; font-weight: bold; font-size: 12px;';
        $styleAllTotalAmount = $styleAmount . ' background: #ffc33e; font-weight: bold;  font-size: 12px';
        $styleHeader = "text-align: center; vertical-align: center; border: 1px solid black; background: #79CDCD;";
        $h = 1; $countColumn = 55;
    ?>
    <table>
        <thead>
            <tr>
                <th colspan="{!! $countColumn !!}" style="text-align: left; vertical-align: middle; font-size: 13px;">{!! $companyData->name. '-' .$companyData->shortened_name !!}
                </th>
            </tr>
            <tr><th colspan="{!! $countColumn !!}" style="text-align: left; vertical-align: middle; font-size: 13px;">{!! $companyData->address !!}</th>
            </tr>
            <tr>
                <th colspan="{!! $countColumn !!}" style="text-align: left; vertical-align: middle; font-weight:bold; font-size: 14px;">
                    BẢNG LƯƠNG THÁNG {{ $payrolls[0]->month }}/{{ $payrolls[0]->year }}
                </th>
            <tr>
                <th rowspan="2" style="{!! $styleHeader !!}"><span class="uppercase">NO.</span><br><br>
                    <span>STT</span><br><br>
                </th>
                <th rowspan="2" class="sticky-col" style="{!! $styleHeader !!} padding: 0 100px; width: 30px;">
                    <span class="uppercase">FULL NAME</span><br><br>
                    <span>Họ và tên</span>
                </th>
                <th rowspan="2" style="{!! $styleHeader !!}">
                    <span class="uppercase">CODE</span><br><br>
                    <span>Mã NV</span>
                </th>
                <th rowspan="2" style="{!! $styleHeader !!} width: 15px;" class="tdbreak">
                    <span class="uppercase">FIXED WORKING DAYS </span> <br><br>
                    <span>Số ngày công theo tháng</span>
                </th>
                <th colspan="9"  style="{!! $styleHeader !!} height: 50px; " class="tdbreak">
                    <br>
                    <span class="uppercase" style="margin-top: 10px">ACTUAL WORKING DAYS</span><br>
                    <span>Ngày công thực tế</span><br>
                </th>
                <th rowspan="2" style="{!! $styleHeader !!}" class="tdbreak">
                    <span>NUMBER
                        OF SHIFT
                        MEAL
                        </span><br><br>
                    <span>Số bữa ăn chính</span>
                </th>
                <th rowspan="2"  style="{!! $styleHeader !!}" class="tdbreak">
                    <span>NUMBER OF EXTRA MEAL
                        </span><br><br>
                    <span>Số bữa phụ</span>
                </th>
                <th rowspan="2"  style="{!! $styleHeader !!} width: 15px; " class="tdbreak">
                    <span>SALARY PROBATION
                       </span><br><br>
                    <span>Lương thử việc</span>
                </th>
                <th rowspan="2"  style="{!! $styleHeader !!} width: 15px; " class="tdbreak">
                    <span>BASIC 
                        RATE
                       </span><br><br>
                    <span>Lương cơ bản</span>
                </th>
                <th  rowspan="2" style="{!! $styleHeader !!} width: 15px; " class="tdbreak">
                    <span>BASIC 
                        RATE
                       </span><br><br>
                    <span>Lương đóng BH</span>
                </th>
                <th  rowspan="2" style="{!! $styleHeader !!} width: 15px; " class="tdbreak">
                    <span class="uppercase">Actual Work. Day</span> <br><br>
                    <span>Lương làm việc thực tế</span>
                </th>
                <th  rowspan="2" style="{!! $styleHeader !!} width: 15px; " class="tdbreak">
                    <span class="uppercase">Actual night Work. Day</span><br><br>
                    <span>Lương trả khi làm đêm (30%)</span>
                </th>
                <th  colspan="2" style="{!! $styleHeader !!} width: 15px; " class="tdbreak">
                    <span class="uppercase"> Actual Work Salary
                    </span> <br><br>
                    <span>Lương làm thêm</span>
                </th>
                <th colspan="{{ count($cate_allowances) + 2 }}" style="{!! $styleHeader !!}">
                    <span class="uppercase">ALLOWANCE</span><br><br>
                    <span>Phụ cấp</span>
                </th>
                <th rowspan="2" style="{!! $styleHeader !!} width: 15px; " class="tdbreak">
                    <span class="uppercase">Total income</span><br><br>
                    <span class="">Tổng thu nhập</span>
                </th>
                <th colspan="4" style="{!! $styleHeader !!}" class="tdbreak">
                    <span class="uppercase">DEDUCTIONS</span><br><br>
                    <span class="">Khấu trừ nhân viên</span>
                </th>
                <th colspan="4" style="{!! $styleHeader !!}" class="tdbreak">
                    <span class="uppercase">COMPANY CONTRIBUTION</span><br><br>
                    <span class="">Đóng góp của CÔNG TY</span>
                </th>
                <th rowspan="2" style="{!! $styleHeader !!} width: 15px; " class="tdbreak">
                    <span class="uppercase">DEDUCTION PIT FINALIZE</span><br><br>
                    <span class="">Nộp theo quyết toán</span><br>
                    <span>TNCN năm 2022</span>
                </th>
                <th colspan="2" style="{!! $styleHeader !!}" class="tdbreak">
                    <span class="uppercase">DEDUCTION</span><br><br>
                    <span class="">Khoản giảm trừ khác</span>
                </th>
                <th colspan="2" style="{!! $styleHeader !!}" class="tdbreak">
                    <span class="uppercase">Increase</span><br><br>
                    <span class="">Khoản tăng</span>
                </th>
                <th rowspan="2" style="{!! $styleHeader !!} width: 15px; " class="tdbreak">
                    <span class="uppercase">Taxable
                        Income
                        </span><br><br>
                    <span class="">Thu nhập chịu thuế</span>
                </th>
                <th rowspan="2" style="{!! $styleHeader !!}" class="tdbreak">
                    <span class="uppercase">No of dependant
                        </span><br><br>
                    <span class="">Số người phụ thuộc</span>
                </th>
                <th rowspan="2" style="{!! $styleHeader !!} width: 15px;" class="tdbreak">
                    <span class="uppercase">Self relief and dependant relief
                        </span><br><br>
                    <span class="">Khấu trừ gia cảnh</span>
                </th>
                <th rowspan="2" style="{!! $styleHeader !!} width: 15px;" class="tdbreak">
                    <span class="uppercase">Assessable Income
                        </span><br><br>
                    <span class="">Thu nhập tính thuế</span>
                </th>
                <th rowspan="2" style="{!! $styleHeader !!}" class="tdbreak">
                    <span class="uppercase">Income tax
                        </span><br><br>
                    <span class="">Thuế TNCN</span>
                </th>
                <th rowspan="2" style="{!! $styleHeader !!}" class="tdbreak">
                    <span class="uppercase">Điểm KPI
                        </span><br><br>
                    <span>trong tháng</span>
                </th>
                <th rowspan="2" style="{!! $styleHeader !!}" class="tdbreak">
                    <span class="uppercase">
                        </span><br><br>
                    <span>Các khoản điều chỉnh khác</span>
                </th>
                <th rowspan="2" style="{!! $styleHeader !!} width: 15px;" class="tdbreak">
                    <span class="uppercase">TAKE HOME PAY
                        </span><br><br>
                    <span>Tổng thực lĩnh</span>
                </th>
            </tr>
            <tr>
                <th style="{!! $styleHeader !!} width: 10px; " class="tdbreak">Số ngày <br>thử việc</th>
                <th style="{!! $styleHeader !!}  width: 10px; " class="tdbreak">Số ngày<br> hợp đồng</th>
                <th style="{!! $styleHeader !!} width: 10px; " class="tdbreak">Số đêm <br>thử việc</th>
                <th style="{!! $styleHeader !!} width: 10px; " class="tdbreak">Số đêm <br>hợp đồng</th>
                <th style="{!! $styleHeader !!} width: 10px; " class="tdbreak">Ngày <br>công tác</th>
                <th style="{!! $styleHeader !!} width: 10px; " class="tdbreak">Nghỉ <br>hưởng lương</th>
                <th style="{!! $styleHeader !!} width: 10px; " class="tdbreak">Nghỉ <br>đình chỉ</th>
                <th style="{!! $styleHeader !!} width: 10px; " class="tdbreak">Nghỉ không lương<br>đi muộn</th>
                <th style="{!! $styleHeader !!} width: 10px" class="tdbreak">Tổng</th>
                <th style="{!! $styleHeader !!}  width: 15px; " class="tdbreak">Miễn thuế</th>
                <th style="{!! $styleHeader !!}  width: 15px; " class="tdbreak">Chịu thuế</th>
                <th style="{!! $styleHeader !!}" class="tdbreak">Phụ cấp ăn trưa,<br> ăn ca (NON-TAX)</th>
                <th style="{!! $styleHeader !!}" class="tdbreak">Phụ cấp ăn trưa, <br> ăn ca (TAX)</th>
                @foreach ($cate_allowances as $k => $cate)
                    <th style="{!! $styleHeader !!} width: 10px; " class="tdbreak">{{ $cate ?? '' }}</th>
                @endforeach
                <th style="{!! $styleHeader !!}" class="tdbreak">
                    <span class="uppercase">SOC INS</span><br><br>
                    <span class="uppercase">BHXH</span><br><br>
                    <span class="uppercase">8%</span>
                </th>
                <th style="{!! $styleHeader !!}" class="tdbreak">
                    <span class="uppercase">H INS </span><br><br>
                    <span class="uppercase">BHYT</span><br><br>
                    <span class="uppercase">1.5%</span>
                </th>
                <th style="{!! $styleHeader !!}" class="tdbreak">
                    <span class="uppercase">UNION</span><br><br>
                    <span class="uppercase">Công đoàn</span><br><br>
                    <span class="uppercase">1%</span>
                </th>
                <th style="{!! $styleHeader !!}" class="tdbreak">
                    <span class="uppercase">UN EM INS</span><br><br>
                    <span class="uppercase">BHTN</span><br><br>
                    <span class="uppercase">1%</span>
                </th>
                <th style="{!! $styleHeader !!}" class="tdbreak">
                    <span class="uppercase">SOC INS</span><br><br>
                    <span class="uppercase">BHXH</span><br><br>
                    <span class="uppercase">17%</span>
                </th>
                <th style="{!! $styleHeader !!}" class="tdbreak">
                    <span class="uppercase">H INS </span><br><br>
                    <span class="uppercase">BHYT</span><br><br>
                    <span class="uppercase">3%</span>
                </th>
                <th style="{!! $styleHeader !!}" class="tdbreak">
                    <span class="uppercase">UNION</span><br><br>
                    <span class="uppercase">Công đoàn</span><br><br>
                    <span class="uppercase">2%</span>
                </th>
                <th style="{!! $styleHeader !!}" class="tdbreak">
                    <span class="uppercase">UN EM INS</span><br><br>
                    <span class="uppercase">BHTN</span><br><br>
                    <span class="uppercase">1%</span>
                </th>
                <th style="{!! $styleHeader !!}" class="tdbreak">Miễn thuế</th>
                <th style="{!! $styleHeader !!}" class="tdbreak">Chịu thuế</th>
                <th style="{!! $styleHeader !!}" class="tdbreak">Miễn thuế</th>
                <th style="{!! $styleHeader !!}" class="tdbreak">Chịu thuế</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $totalDept = [];
            ?>
            @if (count($payrolls) > 0)
                @foreach ($payrolls as $items)
                    @if (count($items->payroll_details))
                        <?php $deptId = $items->department_id; $userExcels = []; $i = 0;?>
                        <tr><td colspan="{!! $countColumn !!}" style="font-weight: 700; background: #f7bf90">{{ $deptData[$deptId] }}</td></tr>
                        <!-- Nếu pb này có nv up lương lái xe, thì lấy lương nv này, bỏ lương nv ở bảng lương chính -->
                        @if($salaryDrivers && $salaryDrivers['detail'][$deptId])
                            @foreach ($salaryDrivers['detail'][$deptId] as $k => $item)
                                <?php $bh = json_decode($item->bh, true); $userExcels[] = $item->user_id; ?>
                                <tr class="hover" data-index="{{ $k + 1 }}">
                                    <td style="{!! $styleAmountCenter !!}">{{ $h++ }}</td>
                                    <td class="sticky-col" style="{!! $styleLeftText !!}">{!! $item->user ? $item->user->fullname : '' !!}</td>
                                    <td  style="{!! $styleAmountCenter !!}">{!! $item->user ? $item->user->code : '' !!}</td>
                                    <td style="{!! $styleAmountCenter !!}">
                                        {!! $item->total_day_request !!}
                                    </td>
                                    <td style="{!! $styleAmountCenter !!}">
                                        {!! $item->ca_ngay_tv !!}
                                    </td>
                                    <td style="{!! $styleAmountCenter !!}">
                                        {!! $item->ca_ngay_hd !!}
                                    </td>
                                    <td style="{!! $styleAmountCenter !!}">
                                        {!! $item->ca_dem_tv !!}
                                    </td>
                                    <td style="{!! $styleAmountCenter !!}">
                                        {!! $item->ca_dem_hd !!}
                                    </td>
                                    <td style="{!! $styleAmountCenter !!}">
                                        {!! $item->cong_tac !!}
                                    </td>
                                    <td style="{!! $styleAmountCenter !!}">
                                        {!! $item->nghi_huong_luong !!}
                                    </td>
                                    <td style="{!! $styleAmountCenter !!}">
                                        {!! $item->nghi_dinh_chi !!}
                                    </td>
                                    <td style="{!! $styleAmountCenter !!}">
                                        {!! $item->muon_k_luong !!}
                                    </td>
                                    <td style="{!! $styleAmountCenter !!}">
                                        {!! $item->total_work !!}
                                    </td>
                                    <td style="{!! $styleAmountCenter !!}">
                                        {!! $item->an_chinh !!}
                                    </td>
                                    <td style="{!! $styleAmountCenter !!}">
                                        {!! $item->an_phu !!}
                                    </td>
                                    <td style="{!! $styleAmount !!}">
                                        {{ \App\Helper\HString::currencyFormatVn($item->basic_salary_tv) }}
                                    </td>
                                    <td style="{!! $styleAmount !!}">
                                        {{ \App\Helper\HString::currencyFormatVn($item->basic_salary_hd) }}
                                    </td>
                                    <td style="{!! $styleAmount !!}">
                                        {{ \App\Helper\HString::currencyFormatVn($item->salary_bh) }}
                                    </td>
                                    <td style="{!! $styleAmount !!}">
                                        {{ \App\Helper\HString::currencyFormatVn($item->working_salary_tax) }}
                                    </td>
                                    <td style="{!! $styleAmount !!}">
                                        {{ \App\Helper\HString::currencyFormatVn($item->working_salary_non_tax) }}
                                    </td>
                                    <td style="{!! $styleAmount !!}">
                                        {{ \App\Helper\HString::currencyFormatVn($item->salary_ot_non_tax) }}
                                    </td>
                                    <td style="{!! $styleAmount !!}">
                                        {{ \App\Helper\HString::currencyFormatVn($item->salary_ot_tax) }}
                                    </td>
                                    <td style="{!! $styleAmount !!}">
                                        {{ \App\Helper\HString::currencyFormatVn($item->an_trua_non_tax) }}
                                    </td>
                                    <td style="{!! $styleAmount !!}">
                                        {{ \App\Helper\HString::currencyFormatVn($item->an_trua_tax) }}
                                    </td>
                                    <td style="{!! $styleAmount !!}">
                                        {{ \App\Helper\HString::currencyFormatVn($item->di_lai) }}
                                    </td>
                                    <td style="{!! $styleAmount !!}">
                                        {{ \App\Helper\HString::currencyFormatVn($item->trach_nhiem) }}
                                    </td>
                                    <td style="{!! $styleAmount !!}">
                                        {{ \App\Helper\HString::currencyFormatVn($item->cong_hien) }}
                                    </td>
                                    <td style="{!! $styleAmount !!}">
                                        {{ \App\Helper\HString::currencyFormatVn($item->nang_suat) }}
                                    </td>
                                    <td style="{!! $styleAmount !!}">
                                        {{ \App\Helper\HString::currencyFormatVn($item->dien_thoai) }}
                                    </td>
                                    <td style="{!! $styleAmount !!}">
                                        {{ \App\Helper\HString::currencyFormatVn($item->cong_viec) }}
                                    </td>
                                    <td style="{!! $styleAmount !!}">
                                        {{ \App\Helper\HString::currencyFormatVn($item->dac_thu) }}
                                    </td>
                                    <td style="{!! $styleAmount !!}">
                                        {{ \App\Helper\HString::currencyFormatVn($item->khac) }}
                                    </td>
                                    <td style="{!! $styleAmount !!}">
                                        {{ \App\Helper\HString::currencyFormatVn($item->chuyen_can) }}
                                    </td>
                                    <td style="{!! $styleAmount !!}">
                                        {{ \App\Helper\HString::currencyFormatVn($item->total_salary) }}
                                    </td>
                                    @include('backend.payroll.v1._td_bh', ['bh' => $bh])
                                    <td style="{!! $styleAmount !!}">
                                        {{ \App\Helper\HString::currencyFormatVn($item->quyet_toan) }}
                                    </td>
                                    <td style="{!! $styleAmount !!}">
                                        {{ \App\Helper\HString::currencyFormatVn($item->deduction_non_tax) }}
                                    </td>
                                    <td style="{!! $styleAmount !!}">
                                        {{ \App\Helper\HString::currencyFormatVn($item->deduction_tax) }}
                                    </td>
                                    <td style="{!! $styleAmount !!}">
                                        {{ \App\Helper\HString::currencyFormatVn($item->increase_non_tax) }}
                                    </td>
                                    <td style="{!! $styleAmount !!}">
                                        {{ \App\Helper\HString::currencyFormatVn($item->increase_tax) }}
                                    </td>
                                    <td style="{!! $styleAmount !!}">
                                        {{ \App\Helper\HString::currencyFormatVn($item->income_taxes) }}
                                    </td>
                                    <td style="{!! $styleAmountCenter !!}">
                                        {{ $item->dependent_person }}
                                    </td>
                                    <td style="{!! $styleAmount !!}">
                                        {{ \App\Helper\HString::currencyFormatVn($item->family_allowances) }}
                                    </td>
                                    <td style="{!! $styleAmount !!}">
                                        {{ \App\Helper\HString::currencyFormatVn($item->taxable_income) }}
                                    </td>
                                    <td style="{!! $styleAmount !!}">
                                        {{ \App\Helper\HString::currencyFormatVn($item->personal_income_tax) }}
                                    </td>
                                    <td style="{!! $styleAmountCenter !!}">
                                        {{ $item->kpi }}
                                    </td>
                                    <td style="{!! $styleAmount !!}">
                                        {{ \App\Helper\HString::currencyFormatVn($item->dieu_chinh_khac) }}
                                    </td>
                                    <td style="{!! $styleAmount !!}">
                                        {{ \App\Helper\HString::currencyFormatVn($item->total_real_salary) }}
                                    </td>
                                </tr>
                                <?php
                                    $totalDept[$deptId]['total_day_request'] += $item->total_day_request;
                                    $totalDept[$deptId]['ca_ngay_tv'] += $item->ca_ngay_tv;
                                    $totalDept[$deptId]['ca_ngay_hd'] += $item->ca_ngay_hd;
                                    $totalDept[$deptId]['ca_dem_tv'] += $item->ca_dem_tv;
                                    $totalDept[$deptId]['ca_dem_hd'] += $item->ca_dem_hd;
                                    $totalDept[$deptId]['cong_tac'] += $item->cong_tac;
                                    $totalDept[$deptId]['nghi_huong_luong'] += $item->nghi_huong_luong;
                                    $totalDept[$deptId]['nghi_dinh_chi'] += $item->nghi_dinh_chi;
                                    $totalDept[$deptId]['muon_k_luong'] += $item->muon_k_luong;
                                    $totalDept[$deptId]['total_work'] += $item->total_work;
                                    $totalDept[$deptId]['an_chinh'] += $item->an_chinh;
                                    $totalDept[$deptId]['an_phu'] += $item->an_phu;
                                    $totalDept[$deptId]['basic_salary_tv'] += $item->basic_salary_tv;
                                    $totalDept[$deptId]['basic_salary_hd'] += $item->basic_salary_hd;
                                    $totalDept[$deptId]['salary_bh'] += $item->salary_bh;
                                    $totalDept[$deptId]['working_salary_tax'] += $item->working_salary_tax;
                                    $totalDept[$deptId]['working_salary_non_tax'] += $item->working_salary_non_tax;
                                    $totalDept[$deptId]['salary_ot_non_tax'] += $item->salary_ot_non_tax;
                                    $totalDept[$deptId]['salary_ot_tax'] += $item->salary_ot_tax;
                                    $totalDept[$deptId]['an_trua_non_tax'] += $item->an_trua_non_tax;
                                    $totalDept[$deptId]['an_trua_tax'] += $item->an_trua_tax;
                                    $totalDept[$deptId]['di_lai'] += $item->di_lai;
                                    $totalDept[$deptId]['cong_hien'] += $item->cong_hien;
                                    $totalDept[$deptId]['trach_nhiem'] += $item->trach_nhiem;
                                    $totalDept[$deptId]['nang_suat'] += $item->nang_suat;
                                    $totalDept[$deptId]['dien_thoai'] += $item->dien_thoai;
                                    $totalDept[$deptId]['cong_viec'] += $item->cong_viec;
                                    $totalDept[$deptId]['khac'] += $item->khac;
                                    $totalDept[$deptId]['chuyen_can'] += $item->chuyen_can;
                                    $totalDept[$deptId]['dac_thu'] += $item->dac_thu;
                                    $totalDept[$deptId]['total_salary'] += $item->total_salary;
                                    $totalDept[$deptId]['bhxh_user'] += $bh['bhxh_user'];
                                    $totalDept[$deptId]['bhyt_user'] += $bh['bhyt_user'];
                                    $totalDept[$deptId]['union_user'] += $bh['union_user'];
                                    $totalDept[$deptId]['bhtn_user'] += $bh['bhtn_user'];
                                    $totalDept[$deptId]['bhxh_company'] += $bh['bhxh_company'];
                                    $totalDept[$deptId]['bhyt_company'] += $bh['bhyt_company'];
                                    $totalDept[$deptId]['union_company'] += $bh['union_company'];
                                    $totalDept[$deptId]['bhtn_company'] += $bh['bhtn_company'];
                                    $totalDept[$deptId]['quyet_toan'] += $item->quyet_toan;
                                    $totalDept[$deptId]['deduction_non_tax'] += $item->deduction_non_tax;
                                    $totalDept[$deptId]['deduction_tax'] += $item->deduction_tax;
                                    $totalDept[$deptId]['increase_non_tax'] += $item->increase_non_tax;
                                    $totalDept[$deptId]['increase_tax'] += $item->increase_tax;
                                    $totalDept[$deptId]['income_taxes'] += $item->income_taxes;
                                    $totalDept[$deptId]['family_allowances'] += $item->family_allowances;
                                    $totalDept[$deptId]['taxable_income'] += $item->taxable_income;
                                    $totalDept[$deptId]['personal_income_tax'] += $item->personal_income_tax;
                                    $totalDept[$deptId]['dieu_chinh_khac'] += $item->dieu_chinh_khac;
                                    $totalDept[$deptId]['total_real_salary'] += $item->total_real_salary;
                                ?>
                            @endforeach
                            <?php unset($salaryDrivers['detail'][$deptId]); ?>
                        @endif
                        @foreach ($items->payroll_details as $k => $item)
                            @if(in_array($item->user_id, $userExcels)) @continue @endif
                            <?php
                                $bh = json_decode($item->bh, true);
                            ?>
                            <tr class="hover" data-index="{{ $i++ }}">
                                <td style="text-align: center; vertical-align: middle; border: 1px solid black;">{{ $h++ }}</td>
                                <td class="sticky-col" style="vertical-align: middle; border: 1px solid black;">{!! $item->staff->fullname ?? '' !!}</td>
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
                                <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                                    {!! $item->tongHop['an_chinh'] ?? 0 !!}
                                </td>
                                <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                                    {!! $item->tongHop['an_phu'] ?? 0 !!}
                                </td>
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
                                        $tt = intval($item->calculateAllowance[$k1]) ?? 0;
                                        if ($keyAllowanceById[$k1])
                                            $totalDept[$deptId][$keyAllowanceById[$k1]] += $tt;
                                    ?>
                                    <td style="text-align: right; vertical-align: middle; border: 1px solid black;" class="tdbreak">
                                        {!! number_format($tt) ?? '' !!}
                                    </td>
                                @endforeach
                                <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                                    {!! number_format($item->total_salary )?? '' !!}
                                </td>
                                @include('backend.payroll.v1._td_bh', ['bh' => $bh])
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
                                    {{ $item->tongHop['total'] == 0 ? 0 : (\App\Target::getKpiUserByMonthYear($item->user_id, $items->month, $items->year) ?? 100) }}
                                </td>
                                <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                                    {!! number_format($item->total_impale )?? '' !!}
                                </td>
                                <td style="text-align: right; vertical-align: middle; border: 1px solid black;">
                                    {!! number_format($item->total_real_salary )?? '' !!}
                                </td>
                            </tr>
                            <?php
                                $totalDept[$deptId]['total_day_request'] += $item->total_day_request;
                                $totalDept[$deptId]['ca_ngay_tv'] += $item->tongHop['ngay_tv'];
                                $totalDept[$deptId]['ca_ngay_hd'] += $item->tongHop['ngay_hd'];
                                $totalDept[$deptId]['ca_dem_tv'] += $item->tongHop['dem_tv'];
                                $totalDept[$deptId]['ca_dem_hd'] += $item->tongHop['dem_hd'];
                                $totalDept[$deptId]['cong_tac'] += $item->tongHop['nghi_cong_tac'];
                                $totalDept[$deptId]['nghi_huong_luong'] += $nghiHuongLuong;
                                $totalDept[$deptId]['nghi_dinh_chi'] += $item->tongHop['nghi_70_luong'];
                                $totalDept[$deptId]['muon_k_luong'] += $nghi_khong_luong;
                                $totalDept[$deptId]['total_work'] += $item->tongHop['total'];
                                $totalDept[$deptId]['an_chinh'] += $item->tongHop['an_chinh'];
                                $totalDept[$deptId]['an_phu'] += $item->tongHop['an_phu'];
                                $totalDept[$deptId]['basic_salary_tv'] += $item->basic_salary_tv;
                                $totalDept[$deptId]['basic_salary_hd'] += $item->basic_salary_hd;
                                $totalDept[$deptId]['salary_bh'] += $item->salary_bh;
                                $totalDept[$deptId]['working_salary_tax'] += $item->working_salary_tax;
                                $totalDept[$deptId]['working_salary_non_tax'] += $item->working_salary_non_tax;
                                $totalDept[$deptId]['salary_ot_non_tax'] += $item->salary_ot_non_tax;
                                $totalDept[$deptId]['salary_ot_tax'] += $item->salary_ot_tax;
                                $totalDept[$deptId]['an_trua_non_tax'] += $item->food_allowance_nonTax;
                                $totalDept[$deptId]['an_trua_tax'] += $item->food_allowance_tax;
                                $totalDept[$deptId]['total_salary'] += $item->total_salary;
                                $totalDept[$deptId]['bhxh_user'] += $bh['bhxh_user'];
                                $totalDept[$deptId]['bhyt_user'] += $bh['bhyt_user'];
                                $totalDept[$deptId]['union_user'] += $bh['union_user'];
                                $totalDept[$deptId]['bhtn_user'] += $bh['bhtn_user'];
                                $totalDept[$deptId]['bhxh_company'] += $bh['bhxh_company'];
                                $totalDept[$deptId]['bhyt_company'] += $bh['bhyt_company'];
                                $totalDept[$deptId]['union_company'] += $bh['union_company'];
                                $totalDept[$deptId]['bhtn_company'] += $bh['bhtn_company'];
                                $totalDept[$deptId]['quyet_toan'] += 0;
                                $totalDept[$deptId]['deduction_non_tax'] += ($item->total_deduction - $item->total_deduction_tax);
                                $totalDept[$deptId]['deduction_tax'] += $item->total_deduction_tax;
                                $totalDept[$deptId]['increase_non_tax'] += $item->total_payoff - $item->total_payoff_tax;
                                $totalDept[$deptId]['increase_tax'] += $item->total_payoff_tax;
                                $totalDept[$deptId]['income_taxes'] += $item->income_taxes;
                                $totalDept[$deptId]['family_allowances'] += $item->family_allowances;
                                $totalDept[$deptId]['taxable_income'] += $item->taxable_income;
                                $totalDept[$deptId]['personal_income_tax'] += $item->personal_income_tax;
                                $totalDept[$deptId]['dieu_chinh_khac'] += $item->total_impale;
                                $totalDept[$deptId]['total_real_salary'] += $item->total_real_salary;
                            ?>
                        @endforeach
                    @endif
                    <tr>
                        @include('backend.payroll.v1._tr_total', ['totalData' => $totalDept[$deptId]])
                    </tr>
                @endforeach
            @endif
            @if($salaryDrivers)
                @foreach($salaryDrivers['detail'] as $deptId => $payroll_detail)
                    <tr><td colspan="{!! $countColumn !!}" style="font-weight: 700; background: #f7bf90">{!! $deptData[$deptId] !!}</td></tr>
                    @foreach ($payroll_detail as $k => $item)
                        <?php $bh = json_decode($item->bh, true); ?>
                        <tr class="hover" data-index="{{ $k + 1 }}">
                            <td style="{!! $styleAmountCenter !!}">{{ $h++ }}</td>
                            <td class="sticky-col" style="{!! $styleLeftText !!}">{!! $item->user ? $item->user->fullname : '' !!}</td>
                            <td  style="{!! $styleAmountCenter !!}">{!! $item->user ? $item->user->code : '' !!}</td>
                            <td style="{!! $styleAmountCenter !!}">
                                {!! $item->total_day_request !!}
                            </td>
                            <td style="{!! $styleAmountCenter !!}">
                                {!! $item->ca_ngay_tv !!}
                            </td>
                            <td style="{!! $styleAmountCenter !!}">
                                {!! $item->ca_ngay_hd !!}
                            </td>
                            <td style="{!! $styleAmountCenter !!}">
                                {!! $item->ca_dem_tv !!}
                            </td>
                            <td style="{!! $styleAmountCenter !!}">
                                {!! $item->ca_dem_hd !!}
                            </td>
                            <td style="{!! $styleAmountCenter !!}">
                                {!! $item->cong_tac !!}
                            </td>
                            <td style="{!! $styleAmountCenter !!}">
                                {!! $item->nghi_huong_luong !!}
                            </td>
                            <td style="{!! $styleAmountCenter !!}">
                                {!! $item->nghi_dinh_chi !!}
                            </td>
                            <td style="{!! $styleAmountCenter !!}">
                                {!! $item->muon_k_luong !!}
                            </td>
                            <td style="{!! $styleAmountCenter !!}">
                                {!! $item->total_work !!}
                            </td>
                            <td style="{!! $styleAmountCenter !!}">
                                {!! $item->an_chinh !!}
                            </td>
                            <td style="{!! $styleAmountCenter !!}">
                                {!! $item->an_phu !!}
                            </td>
                            <td style="{!! $styleAmount !!}">
                                {{ \App\Helper\HString::currencyFormatVn($item->basic_salary_tv) }}
                            </td>
                            <td style="{!! $styleAmount !!}">
                                {{ \App\Helper\HString::currencyFormatVn($item->basic_salary_hd) }}
                            </td>
                            <td style="{!! $styleAmount !!}">
                                {{ \App\Helper\HString::currencyFormatVn($item->salary_bh) }}
                            </td>
                            <td style="{!! $styleAmount !!}">
                                {{ \App\Helper\HString::currencyFormatVn($item->working_salary_tax) }}
                            </td>
                            <td style="{!! $styleAmount !!}">
                                {{ \App\Helper\HString::currencyFormatVn($item->working_salary_non_tax) }}
                            </td>
                            <td style="{!! $styleAmount !!}">
                                {{ \App\Helper\HString::currencyFormatVn($item->salary_ot_non_tax) }}
                            </td>
                            <td style="{!! $styleAmount !!}">
                                {{ \App\Helper\HString::currencyFormatVn($item->salary_ot_tax) }}
                            </td>
                            <td style="{!! $styleAmount !!}">
                                {{ \App\Helper\HString::currencyFormatVn($item->an_trua_non_tax) }}
                            </td>
                            <td style="{!! $styleAmount !!}">
                                {{ \App\Helper\HString::currencyFormatVn($item->an_trua_tax) }}
                            </td>
                            <td style="{!! $styleAmount !!}">
                                {{ \App\Helper\HString::currencyFormatVn($item->di_lai) }}
                            </td>
                            <td style="{!! $styleAmount !!}">
                                {{ \App\Helper\HString::currencyFormatVn($item->trach_nhiem) }}
                            </td>
                            <td style="{!! $styleAmount !!}">
                                {{ \App\Helper\HString::currencyFormatVn($item->cong_hien) }}
                            </td>
                            <td style="{!! $styleAmount !!}">
                                {{ \App\Helper\HString::currencyFormatVn($item->nang_suat) }}
                            </td>
                            <td style="{!! $styleAmount !!}">
                                {{ \App\Helper\HString::currencyFormatVn($item->dien_thoai) }}
                            </td>
                            <td style="{!! $styleAmount !!}">
                                {{ \App\Helper\HString::currencyFormatVn($item->cong_viec) }}
                            </td>
                            <td style="{!! $styleAmount !!}">
                                {{ \App\Helper\HString::currencyFormatVn($item->dac_thu) }}
                            </td>
                            <td style="{!! $styleAmount !!}">
                                {{ \App\Helper\HString::currencyFormatVn($item->khac) }}
                            </td>
                            <td style="{!! $styleAmount !!}">
                                {{ \App\Helper\HString::currencyFormatVn($item->chuyen_can) }}
                            </td>
                            <td style="{!! $styleAmount !!}">
                                {{ \App\Helper\HString::currencyFormatVn($item->total_salary) }}
                            </td>
                            @include('backend.payroll.v1._td_bh', ['bh' => $bh])
                            <td style="{!! $styleAmount !!}">
                                {{ \App\Helper\HString::currencyFormatVn($item->quyet_toan) }}
                            </td>
                            <td style="{!! $styleAmount !!}">
                                {{ \App\Helper\HString::currencyFormatVn($item->deduction_non_tax) }}
                            </td>
                            <td style="{!! $styleAmount !!}">
                                {{ \App\Helper\HString::currencyFormatVn($item->deduction_tax) }}
                            </td>
                            <td style="{!! $styleAmount !!}">
                                {{ \App\Helper\HString::currencyFormatVn($item->increase_non_tax) }}
                            </td>
                            <td style="{!! $styleAmount !!}">
                                {{ \App\Helper\HString::currencyFormatVn($item->increase_tax) }}
                            </td>
                            <td style="{!! $styleAmount !!}">
                                {{ \App\Helper\HString::currencyFormatVn($item->income_taxes) }}
                            </td>
                            <td style="{!! $styleAmountCenter !!}">
                                {{ $item->dependent_person }}
                            </td>
                            <td style="{!! $styleAmount !!}">
                                {{ \App\Helper\HString::currencyFormatVn($item->family_allowances) }}
                            </td>
                            <td style="{!! $styleAmount !!}">
                                {{ \App\Helper\HString::currencyFormatVn($item->taxable_income) }}
                            </td>
                            <td style="{!! $styleAmount !!}">
                                {{ \App\Helper\HString::currencyFormatVn($item->personal_income_tax) }}
                            </td>
                            <td style="{!! $styleAmountCenter !!}">
                                {{ $item->kpi }}
                            </td>
                            <td style="{!! $styleAmount !!}">
                                {{ \App\Helper\HString::currencyFormatVn($item->dieu_chinh_khac) }}
                            </td>
                            <td style="{!! $styleAmount !!}">
                                {{ \App\Helper\HString::currencyFormatVn($item->total_real_salary) }}
                            </td>
                        </tr>
                        <?php
                        $totalDept[$deptId]['total_day_request'] += $item->total_day_request;
                        $totalDept[$deptId]['ca_ngay_tv'] += $item->ca_ngay_tv;
                        $totalDept[$deptId]['ca_ngay_hd'] += $item->ca_ngay_hd;
                        $totalDept[$deptId]['ca_dem_tv'] += $item->ca_dem_tv;
                        $totalDept[$deptId]['ca_dem_hd'] += $item->ca_dem_hd;
                        $totalDept[$deptId]['cong_tac'] += $item->cong_tac;
                        $totalDept[$deptId]['nghi_huong_luong'] += $item->nghi_huong_luong;
                        $totalDept[$deptId]['nghi_dinh_chi'] += $item->nghi_dinh_chi;
                        $totalDept[$deptId]['muon_k_luong'] += $item->muon_k_luong;
                        $totalDept[$deptId]['total_work'] += $item->total_work;
                        $totalDept[$deptId]['an_chinh'] += $item->an_chinh;
                        $totalDept[$deptId]['an_phu'] += $item->an_phu;
                        $totalDept[$deptId]['basic_salary_tv'] += $item->basic_salary_tv;
                        $totalDept[$deptId]['basic_salary_hd'] += $item->basic_salary_hd;
                        $totalDept[$deptId]['salary_bh'] += $item->salary_bh;
                        $totalDept[$deptId]['working_salary_tax'] += $item->working_salary_tax;
                        $totalDept[$deptId]['working_salary_non_tax'] += $item->working_salary_non_tax;
                        $totalDept[$deptId]['salary_ot_non_tax'] += $item->salary_ot_non_tax;
                        $totalDept[$deptId]['salary_ot_tax'] += $item->salary_ot_tax;
                        $totalDept[$deptId]['an_trua_non_tax'] += $item->an_trua_non_tax;
                        $totalDept[$deptId]['an_trua_tax'] += $item->an_trua_tax;
                        $totalDept[$deptId]['di_lai'] += $item->di_lai;
                        $totalDept[$deptId]['cong_hien'] += $item->cong_hien;
                        $totalDept[$deptId]['trach_nhiem'] += $item->trach_nhiem;
                        $totalDept[$deptId]['nang_suat'] += $item->nang_suat;
                        $totalDept[$deptId]['dien_thoai'] += $item->dien_thoai;
                        $totalDept[$deptId]['cong_viec'] += $item->cong_viec;
                        $totalDept[$deptId]['khac'] += $item->khac;
                        $totalDept[$deptId]['chuyen_can'] += $item->chuyen_can;
                        $totalDept[$deptId]['dac_thu'] += $item->dac_thu;
                        $totalDept[$deptId]['total_salary'] += $item->total_salary;
                        $totalDept[$deptId]['bhxh_user'] += $bh['bhxh_user'];
                        $totalDept[$deptId]['bhyt_user'] += $bh['bhyt_user'];
                        $totalDept[$deptId]['union_user'] += $bh['union_user'];
                        $totalDept[$deptId]['bhtn_user'] += $bh['bhtn_user'];
                        $totalDept[$deptId]['bhxh_company'] += $bh['bhxh_company'];
                        $totalDept[$deptId]['bhyt_company'] += $bh['bhyt_company'];
                        $totalDept[$deptId]['union_company'] += $bh['union_company'];
                        $totalDept[$deptId]['bhtn_company'] += $bh['bhtn_company'];
                        $totalDept[$deptId]['quyet_toan'] += $item->quyet_toan;
                        $totalDept[$deptId]['deduction_non_tax'] += $item->deduction_non_tax;
                        $totalDept[$deptId]['deduction_tax'] += $item->deduction_tax;
                        $totalDept[$deptId]['increase_non_tax'] += $item->increase_non_tax;
                        $totalDept[$deptId]['increase_tax'] += $item->increase_tax;
                        $totalDept[$deptId]['income_taxes'] += $item->income_taxes;
                        $totalDept[$deptId]['family_allowances'] += $item->family_allowances;
                        $totalDept[$deptId]['taxable_income'] += $item->taxable_income;
                        $totalDept[$deptId]['personal_income_tax'] += $item->personal_income_tax;
                        $totalDept[$deptId]['dieu_chinh_khac'] += $item->dieu_chinh_khac;
                        $totalDept[$deptId]['total_real_salary'] += $item->total_real_salary;
                        ?>
                    @endforeach
                    <tr>
                        @include('backend.payroll.v1._tr_total', ['totalData' => $totalDept[$deptId]])
                    </tr>
                @endforeach
            @endif
            <?php
                $totalAll = [];
                foreach ($totalDept as $deptId => $deptTotal) {
                     foreach ($deptTotal as $key => $v) {
                         $totalAll[$key] += $v;
                    }
                }
            ?>
            <tr>
                @include('backend.payroll.v1._tr_total', ['totalData' => $totalAll, 'styleTotalAmount' => $styleAllTotalAmount, 'labelTong' => 'TỔNG CÔNG TY'])
            </tr>
        </tbody>
    </table>