@extends('backend.master')
@section('title')
    {!! trans('payrolls.detail') !!} {!! trans('payrolls.label') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">

    <style>
        .error {
            width: 100%;
            height: 100px;
            line-height: 100px;
        }

        .text-size {
            font-size: 16px;
        }

        tr td {
            text-align: center;
        }

        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type=number] {
            -moz-appearance: textfield;
        }

        b, strong {
            font-weight: 500;
        }

        table {
            border: 1px solid #bbb;
            border-collapse: collapse;
            border-spacing: 0;
            
        }

        .tdbreak {
            word-break: break-all
        }
        
        thead tr th {
            white-space: nowrap;
            text-overflow: clip;
        }

        .uppercase {
            text-transform: uppercase;
        }

        .sticky-col {
            position: -webkit-sticky;
            position: sticky;
            background-color: white;
            left: 0;
        }
        tbody tr th.th-job {
            position:sticky;
            left:0;
            z-index: 102;
            background:  white
        }
    </style>
@stop
@section('content')
    <section class="content-header">
        {{-- <h1>
            {!! trans('payrolls.detail') !!}
            <small>{!! trans('payrolls.label') !!}</small>
        </h1> --}}
        <h4>
            {{ $payroll->title }} tháng
            {{ $payroll->month }}/{{ $payroll->year }} {{ $payroll->company->shortened_name }}:
                Tạo bởi: {{ $payroll->user_by->fullname }}
          
        </h4>

        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.drivers.index') !!}">Lương lái xe</a></li>
        </ol>
    </section>
    <section class="content overlay">
        {{-- <div class="text-center">
            <h3>{{ $payroll->title }}</h3>
            <h4>Công ty: {{ $payroll->company->shortened_name }}</h4>
            <h4>Người tạo: {{ $payroll->user_by->fullname }}</h4>
        </div> --}}
        
        <div class="box">
            <div class="box-body no-padding" style="overflow-x:auto; overflow-x:auto;">

                <?php $cate_allowances = \App\Models\AllowanceCategory::cateAllowance() ;?>
                <table class="table table-striped table-bordered">
                    <thead>
                        
                        <tr>
                            <th rowspan="2"  style="text-align: center; vertical-align: middle;">
                                <span class="uppercase">No.</span><br><br>
                                <span>STT</span><br><br>
                            </th>
                            <th rowspan="2" class="sticky-col" style="text-align: center; vertical-align: middle; padding: 0 100px">
                                <span class="uppercase">full name</span><br><br>
                                <span>Họ và tên</span>
                            </th>
                            <th rowspan="2" class="" style="text-align: center; vertical-align: middle; padding: 0 50px">
                                <span class="uppercase">Department</span><br><br>
                                <span>Phòng ban</span>
                            </th>

                            <th rowspan="2" style="text-align: center; vertical-align: middle;">
                                <span class="uppercase">Code</span><br><br>
                                <span>Mã NV</span>
                            </th>
                            <th rowspan="2"  style="text-align: center; vertical-align: middle;" class="tdbreak">
                                <span class="uppercase">FIXED WORKING DAYS </span> <br><br>
                                <span>Số ngày công theo tháng</span>
                            </th>
                            <th colspan="9"  style="text-align: center; vertical-align: middle;" class="tdbreak">
                                <span class="uppercase">ACTUAL WORKING DAYS</span><br><br>
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
                            <th rowspan="2"  style="text-align: center; vertical-align: middle;" class="tdbreak">
                                <span>NUMBER
                                    OF SHIFT
                                    MEAL
                                    </span><br><br>
                                <span>Số bữa ăn chính</span>
                            </th>
                            <th rowspan="2"  style="text-align: center; vertical-align: middle;" class="tdbreak">
                                <span>NUMBER OF EXTRA MEAL
                                    </span><br><br>
                                <span>Số bữa phụ</span>
                            </th>
                            {{-- <th rowspan="2"  style="text-align: center; vertical-align: middle;" class="tdbreak">
                                <span class="uppercase">Night working day</span><br><br>
                                <span>Ngày công làm việc đêm</span>
                            </th> --}}
                            <th rowspan="2"  style="text-align: center; vertical-align: middle;" class="tdbreak">
                                <span>SALARY PROBATION
                                   </span><br><br>
                                <span>Lương thử việc</span>
                            </th>
                            <th rowspan="2"  style="text-align: center; vertical-align: middle;" class="tdbreak">
                                <span>BASIC 
                                    RATE
                                   </span><br><br>
                                <span>Lương cơ bản</span>
                            </th>
                            <th  rowspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak">
                                <span>BASIC 
                                    RATE
                                   </span><br><br>
                                <span>Lương đóng BH</span>
                            </th>
                            <th  rowspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak">
                                <span class="uppercase">Actual Work. Day</span> <br><br>
                                <span>Lương làm việc thực tế</span>
                            </th>
                            <th  rowspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak">
                                <span class="uppercase">Actual night Work. Day</span><br><br>
                                <span>Lương trả khi làm đêm (30%)</span>
                            </th>
                            <th  colspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak">
                                <span class="uppercase"> Actual Work Salary 	
	
                                </span> <br><br>
                                <span>Lương làm thêm</span>
                            </th>
                            <th colspan="{{ count($cate_allowances) + 2 }}" style="text-align: center; vertical-align: middle;">
                                <span class="uppercase">allowance</span><br><br>
                                <span>Phụ cấp</span>
                            </th>
                            <th rowspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak">
                                <span class="uppercase">Total income</span><br><br>
                                <span class="">Tổng thu nhập</span>
                            </th>
                            <th colspan="4" style="text-align: center; vertical-align: middle;" class="tdbreak">
                                <span class="uppercase">DEDUCTIONS</span><br><br>
                                <span class="">Khấu trừ nhân viên</span>
                            </th>
                            
                            <th colspan="4" style="text-align: center; vertical-align: middle;" class="tdbreak">
                                <span class="uppercase">COMPANY CONTRIBUTION</span><br><br>
                                <span class="">Đóng góp của CÔNG TY</span>
                            </th>

                            <th rowspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak">
                                <span class="uppercase">DEDUCTION PIT FINALIZE</span><br><br>
                                <span class="">Nộp theo quyết toán</span><br>
                                <span>TNCN năm 2022</span>
                            </th>
                            <th colspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak">
                                <span class="uppercase">DEDUCTION</span><br><br>
                                <span class="">Khoản giảm trừ khác</span>
                            </th>
                            <th colspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak">
                                <span class="uppercase">increase</span><br><br>
                                <span class="">Khoản tăng</span>
                            </th>

                            <th rowspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak">
                                <span class="uppercase">Taxable
                                    Income
                                    </span><br><br>
                                <span class="">Thu nhập chịu thuế</span>
                            </th>

                            <th rowspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak">
                                <span class="uppercase">No of dependant
                                    </span><br><br>
                                <span class="">Số người phụ thuộc</span>
                            </th>

                            <th rowspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak">
                                <span class="uppercase">Self relief and dependant relief
                                    </span><br><br>
                                <span class="">Khấu trừ gia cảnh</span>
                            </th>

                            <th rowspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak">
                                <span class="uppercase">Assessable Income
                                    </span><br><br>
                                <span class="">Thu nhập tính thuế</span>
                            </th>

                            <th rowspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak">
                                <span class="uppercase">Income tax
                                    </span><br><br>
                                <span class="">Thuế TNCN</span>
                            </th>
                            <th rowspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak">
                                <span class="uppercase">Điểm KPI
                                    </span><br><br>
                                <span>trong tháng</span>
                            </th>
                            <th rowspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak">
                                <span class="uppercase">
                                    </span><br><br>
                                <span>Các khoản điều chỉnh khác</span>
                            </th>
                            <th rowspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak">
                                <span class="uppercase">TAKE HOME PAY
                                    </span><br><br>
                                <span>Tổng thực lĩnh</span>
                            </th>
                            


                            <tr>
                                <th style="text-align: center; vertical-align: middle;" class="tdbreak">Số ngày thử việc</th>
                                <th style="text-align: center; vertical-align: middle;" class="tdbreak">Số ngày hợp đồng</th>
                                <th style="text-align: center; vertical-align: middle;" class="tdbreak">Số đêm thử việc</th>
                                <th style="text-align: center; vertical-align: middle;" class="tdbreak">Số đêm hợp đồng</th>
                                <th style="text-align: center; vertical-align: middle;" class="tdbreak">Ngày công tác</th>
                                <th style="text-align: center; vertical-align: middle;" class="tdbreak">Nghỉ hưởng lương</th>
                                <th style="text-align: center; vertical-align: middle;" class="tdbreak">Nghỉ đình chỉ</th>
                                <th style="text-align: center; vertical-align: middle;" class="tdbreak">Nghỉ không lương<br>đi muộn</th>
                                <th style="text-align: center; vertical-align: middle;" class="tdbreak">Tổng</th>
                                
                                <th style="text-align: center; vertical-align: middle;" class="tdbreak">Miễn thuế</th>
                                <th style="text-align: center; vertical-align: middle;" class="tdbreak">Chịu thuế</th>

                                <th style="text-align: center; vertical-align: middle;" class="tdbreak">Phụ cấp ăn trưa, ăn ca (NON-TAX)</th>

                                <th style="text-align: center; vertical-align: middle;" class="tdbreak">Phụ cấp ăn trưa, ăn ca (TAX)</th>

                                @foreach ($cate_allowances as $k => $cate)
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak">{{ $cate ?? '' }}</th>
                                @endforeach
                                
                                
                                <th style="text-align: center; vertical-align: middle;" class="tdbreak">
                                    <span class="uppercase">SOC INS</span><br><br>
                                    <span class="uppercase">BHXH</span><br><br>
                                    <span class="uppercase">8%</span>
                                </th>
                                <th style="text-align: center; vertical-align: middle;" class="tdbreak">
                                    <span class="uppercase">H INS </span><br><br>
                                    <span class="uppercase">BHYT</span><br><br>
                                    <span class="uppercase">1.5%</span>
                                </th>
                                <th style="text-align: center; vertical-align: middle;" class="tdbreak">
                                    <span class="uppercase">UNION</span><br><br>
                                    <span class="uppercase">Công đoàn</span><br><br>
                                    <span class="uppercase">1%</span>
                                </th>
                                <th style="text-align: center; vertical-align: middle;" class="tdbreak">
                                    <span class="uppercase">UN EM INS</span><br><br>
                                    <span class="uppercase">BHTN</span><br><br>
                                    <span class="uppercase">0%</span>
                                </th>


                                <th style="text-align: center; vertical-align: middle;" class="tdbreak">
                                    <span class="uppercase">SOC INS</span><br><br>
                                    <span class="uppercase">BHXH</span><br><br>
                                    <span class="uppercase">17%</span>
                                </th>
                                <th style="text-align: center; vertical-align: middle;" class="tdbreak">
                                    <span class="uppercase">H INS </span><br><br>
                                    <span class="uppercase">BHYT</span><br><br>
                                    <span class="uppercase">3%</span>
                                </th>
                                <th style="text-align: center; vertical-align: middle;" class="tdbreak">
                                    <span class="uppercase">UNION</span><br><br>
                                    <span class="uppercase">Công đoàn</span><br><br>
                                    <span class="uppercase">2%</span>
                                </th>
                                <th style="text-align: center; vertical-align: middle;" class="tdbreak">
                                    <span class="uppercase">UN EM INS</span><br><br>
                                    <span class="uppercase">BHTN</span><br><br>
                                    <span class="uppercase">1%</span>
                                </th>
                                
                                <th style="text-align: center; vertical-align: middle;" class="tdbreak">Miễn thuế</th>
                                <th style="text-align: center; vertical-align: middle;" class="tdbreak">Chịu thuế</th>

                                <th style="text-align: center; vertical-align: middle;" class="tdbreak">Miễn thuế</th>
                                <th style="text-align: center; vertical-align: middle;" class="tdbreak">Chịu thuế</th>
                                
                            </tr>
                           
                            
                        </tr>
                    </thead>
                    <tbody>
                       @if (count($payroll_detail) > 0)
                        @foreach ($payroll_detail as $k => $item)
                            <?php $bh = json_decode($item->bh, true); ?>
                            <tr class="hover" data-index="{{ $k + 1 }}">
                                <td style="text-align: center; vertical-align: middle;">{{ $k + 1 }}</td>
                                <td class="sticky-col" style="text-align: center; vertical-align: middle;">{!! $item->user->fullname ?? '' !!}</td>
                                <td class="" style="text-align: center; vertical-align: middle;">{!! $item->department->name ?? '' !!}</td>
                                <td  style="text-align: center; vertical-align: middle;">{!! $item->user->code ?? '' !!}</td>
                                <td style="text-align: center; vertical-align: middle;">
                                    {!! $item->total_day_request !!}
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    {!! $item->ca_ngay_tv !!}
                                    {{-- <input type="text" value="{{ $data['user_payroll']->ca_ngay_tv ?? 0 }}" data-name="default" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct"> --}}
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    {!! $item->ca_ngay_hd !!}

                                    {{-- <input type="text" value="{{ $data['user_payroll']->ca_ngay_hd ?? 0 }}" data-name="default" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct"> --}}
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    {!! $item->ca_dem_tv !!}
                                    {{-- <input type="text" value="{{ $data['user_payroll']->ca_dem_tv ?? 0 }}" data-name="default" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct"> --}}
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    {!! $item->ca_dem_hd !!}
                                    {{-- <input type="text" value="{{ $data['user_payroll']->ca_dem_hd ?? 0 }}" data-name="default" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct"> --}}
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    {!! $item->cong_tac !!}

                                    {{-- <input type="text" value="{{ \App\StaffDayOff::countDayOffs($item->user_id, $payroll->month, $payroll->year, 'T') }}" data-name="default" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct"> --}}
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    {!! $item->nghi_huong_luong !!}

                                    {{-- <input type="text" value="{{ \App\Models\Payroll::countTotalInMonthForTimeKeeping($item->user_id, $payroll->month, $payroll->year, 'T') }}" data-name="default" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct"> --}}
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    {!! $item->nghi_dinh_chi !!}
                                    {{-- <input type="text" value="{{ \App\StaffDayOff::countDayOffs($item->user_id, $payroll->month, $payroll->year, 'C') }}" data-name="default" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct"> --}}
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    {!! $item->muon_k_luong !!}

                                    {{-- <input type="text" value="{{ \App\Models\Payroll::nghiKhongLuong($item->user_id, $payroll->month, $payroll->year, 'O') }}" data-name="default" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct"> --}}
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    {!! $item->total_work !!}
                                    {{-- <input type="text" style="width: 70px;" value="{{ $totalWorkDepartment ?? '' }}" data-name="total_work_department" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct"> --}}
                                </td>

                                {{-- <td style="text-align: center; vertical-align: middle;">
                                    <input type="text" value="{{ \App\StaffDayOff::countDayOffs($item->user_id, $payroll->month, $payroll->year, 'T') }}" data-name="total_work_department" data-user-id="{!! $item->user_id !!}" class="form-control ct">
                                </td> --}}
                                <td style="text-align: center; vertical-align: middle;">
                                    {!! $item->an_chinh !!}

                                    {{-- <input type="text" value="{!! $data['user_payroll']->an_chinh ?? '' !!}" data-name="an_chinh" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct"> --}}
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    {!! $item->an_phu !!}
                                    {{-- <input type="text" value="{!! $data['user_payroll']->an_phu ?? '' !!}" data-name="an_phu" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct"> --}}
                                </td>
                                {{-- <td style="text-align: center; vertical-align: middle;">
                                    <input type="text" value="{!! $data['user_payroll']->ca_dem_hd +  $data['user_payroll']->ca_dem_tv !!}" data-name="ca_dem_hd" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct">
                                </td> --}}

                                <td style="text-align: center; vertical-align: middle; text-align: right">
                                    {{ \App\Helper\HString::currencyFormatVn($item->basic_salary_tv) }}
                                    {{-- <input style="width: 100px" type="text" value="{!! number_format($item->basic_salary_tv, 0, ',', '.') ?? '' !!}" data-name="basic_salary_hd" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct"> --}}
                                </td>
                                <td style="text-align: center; vertical-align: middle; text-align: right">
                                    {{ \App\Helper\HString::currencyFormatVn($item->basic_salary_hd) }}
                                    {{-- <input style="width: 100px" type="text" value="{!! number_format($item->basic_salary_hd, 0, ',', '.') ?? '' !!}" data-name="basic_salary_hd" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct"> --}}
                                </td>
                                <td style="text-align: center; vertical-align: middle; text-align: right">
                                    {{ \App\Helper\HString::currencyFormatVn($item->salary_bh) }}
                                    {{-- <input style="width: 150px" type="text" value="{!! number_format($item->salary_bh, 0, ',', '.') ?? '' !!}" data-name="salary_bh" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct"> --}}
                                </td>
                                <td style="text-align: center; vertical-align: middle; text-align: right">
                                    {{ \App\Helper\HString::currencyFormatVn($item->working_salary_tax) }}
                                    {{-- <input style="width: 150px" type="text" value="{!! number_format($item->working_salary_tax, 0, ',', '.' )?? '' !!}" data-name="working_salary_tax" data-user-id="{!! $item->user_id !!}" data-concurrent="{{ $item->salary_concurrent }}"
                                        name="" id="" class="form-control ct"> --}}
                                </td>
                                <td style="text-align: center; vertical-align: middle; text-align: right">
                                    {{ \App\Helper\HString::currencyFormatVn($item->working_salary_non_tax) }}

                                    {{-- <input style="width: 150px" type="text" value="{!! number_format($item->working_salary_non_tax, 0, ',', '.' )?? '' !!}" data-name="working_salary_non_tax" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct"> --}}
                                </td>
                                <td style="text-align: center; vertical-align: middle; text-align: right">
                                    {{ \App\Helper\HString::currencyFormatVn($item->salary_ot_non_tax) }}
                                    {{-- <input style="width: 150px" type="text" value="{!! number_format($item->salary_ot_non_tax, 0, ',', '.' )?? '' !!}" data-name="default" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct"> --}}
                                </td>

                                <td style="text-align: center; vertical-align: middle; text-align: right">
                                    {{ \App\Helper\HString::currencyFormatVn($item->salary_ot_tax) }}
                                    {{-- <input style="width: 150px" type="text" value="{!! number_format($item->salary_ot_tax, 0, ',', '.' )?? '' !!}" data-name="default" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct"> --}}
                                </td>

                                <td style="text-align: center; vertical-align: middle; text-align: right">
                                    {{ \App\Helper\HString::currencyFormatVn($item->an_trua_non_tax) }}

                                    {{-- <input style="width: 150px" type="text" value="{!! number_format($item->food_allowance_nonTax, 0, ',', '.' )?? '' !!}" name="" id="" data-name="food_allowance_nonTax" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct"> --}}
                                </td>
                                <td style="text-align: center; vertical-align: middle; text-align: right">
                                    {{ \App\Helper\HString::currencyFormatVn($item->an_trua_tax) }}
                                    {{-- <input style="width: 150px" type="text" value="{!! number_format($item->food_allowance_tax, 0, ',', '.' )?? '' !!}" data-name="food_allowance_tax" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct"> --}}
                                </td>
                                
                                <td style="text-align: center; vertical-align: middle; text-align: right">
                                    {{ \App\Helper\HString::currencyFormatVn($item->di_lai) }}
                                </td>
                                <td style="text-align: center; vertical-align: middle; text-align: right">
                                    {{ \App\Helper\HString::currencyFormatVn($item->trach_nhiem) }}
                                </td>
                                <td style="text-align: center; vertical-align: middle; text-align: right">
                                    {{ \App\Helper\HString::currencyFormatVn($item->cong_hien) }}
                                </td>
                                <td style="text-align: center; vertical-align: middle; text-align: right">
                                    {{ \App\Helper\HString::currencyFormatVn($item->nang_suat) }}
                                </td>
                                <td style="text-align: center; vertical-align: middle; text-align: right">
                                    {{ \App\Helper\HString::currencyFormatVn($item->dien_thoai) }}
                                </td>
                                <td style="text-align: center; vertical-align: middle; text-align: right">
                                    {{ \App\Helper\HString::currencyFormatVn($item->cong_viec) }}
                                </td>
                                <td style="text-align: center; vertical-align: middle; text-align: right">
                                    {{ \App\Helper\HString::currencyFormatVn($item->dac_thu) }}
                                </td>
                                <td style="text-align: center; vertical-align: middle; text-align: right">
                                    {{ \App\Helper\HString::currencyFormatVn($item->khac) }}
                                </td>
                                <td style="text-align: center; vertical-align: middle; text-align: right">
                                    {{ \App\Helper\HString::currencyFormatVn($item->chuyen_can) }}
                                </td>

                                <td style="text-align: center; vertical-align: middle; text-align: right">
                                    {{ \App\Helper\HString::currencyFormatVn($item->total_salary) }}

                                    {{-- <input style="width: 150px" type="text" value="{!! number_format($item->total_salary, 0, ',', '.' )?? '' !!}" data-name="total_salary" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct"> --}}
                                </td>

                                <td style="text-align: center; vertical-align: middle; text-align: right">
                                    {{ \App\Helper\HString::currencyFormatVn($bh['bhxh_user']) }}
                                    {{-- <input style="width: 150px" type="text" value="{!! number_format($data['user_payroll']->bh->bhxh_user, 0, ',', '.' )?? '' !!}"  data-name="bhxh_user" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct"> --}}
                                </td>

                                <td style="text-align: center; vertical-align: middle; text-align: right">
                                    {{ \App\Helper\HString::currencyFormatVn($bh['bhyt_user']) }}

                                    {{-- <input style="width: 150px" type="text" value="{!! number_format($data['user_payroll']->bh->bhyt_user, 0, ',', '.' )?? '' !!}" data-name="bhyt_user" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct"> --}}
                                </td>
                                <td style="text-align: center; vertical-align: middle; text-align: right">
                                    {{ \App\Helper\HString::currencyFormatVn($bh['union_user']) }}
                                    {{-- <input style="width: 150px" type="text" value="{!! number_format($data['user_payroll']->bh->union_user, 0, ',', '.' )?? '' !!}" data-name="union_user" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct"> --}}
                                </td>
                                <td style="text-align: center; vertical-align: middle; text-align: right">
                                    {{ \App\Helper\HString::currencyFormatVn($bh['bhtn_user']) }}
                                    {{-- <input style="width: 150px" type="text" value="{!! number_format($data['user_payroll']->bh->bhtn_user, 0, ',', '.' )?? '' !!}" data-name="bhtn_user" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct"> --}}
                                </td>

                                <td style="text-align: center; vertical-align: middle; text-align: right">
                                    {{ \App\Helper\HString::currencyFormatVn($bh['bhxh_company']) }}
                                    {{-- <input style="width: 150px" type="text" value="{!! number_format($data['user_payroll']->bh->bhxh_company, 0, ',', '.' )?? '' !!}" data-name="bhxh_company" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct"> --}}
                                </td>

                                <td style="text-align: center; vertical-align: middle; text-align: right">
                                    {{ \App\Helper\HString::currencyFormatVn($bh['bhyt_company']) }}

                                    {{-- <input style="width: 150px" type="text" value="{!! number_format($data['user_payroll']->bh->bhyt_company, 0, ',', '.' )?? '' !!}" data-name="bhyt_company" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct"> --}}
                                </td>
                                <td style="text-align: center; vertical-align: middle; text-align: right">
                                    {{ \App\Helper\HString::currencyFormatVn($bh['union_company']) }}
                                    {{-- <input style="width: 150px" type="text" value="{!! number_format($data['user_payroll']->bh->union_company, 0, ',', '.' )?? '' !!}" data-name="union_company" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct"> --}}
                                </td>

                                <td style="text-align: center; vertical-align: middle; text-align: right">
                                    {{ \App\Helper\HString::currencyFormatVn($bh['bhtn_company']) }}
                                    {{-- <input style="width: 150px" type="text" value="{!! number_format($data['user_payroll']->bh->bhtn_company, 0, ',', '.' )?? '' !!}" data-name="bhtn_company" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct"> --}}
                                </td>


                                <td style="text-align: center; vertical-align: middle; text-align: right">
                                    {{ \App\Helper\HString::currencyFormatVn($item->quyet_toan) }}

                                    {{-- <input style="width: 150px" type="text" value="0" data-name="ttcn_2020" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct"> --}}
                                </td>

                                <td style="text-align: center; vertical-align: middle; text-align: right">
                                    {{ \App\Helper\HString::currencyFormatVn($item->deduction_non_tax) }}
                                    {{-- <input style="width: 150px" type="text" value="{{ number_format(($item->total_deduction - $item->total_deduction_tax), 0, ',', '.') }}" data-name="total_deductions" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct"> --}}
                                </td>

                                <td style="text-align: center; vertical-align: middle; text-align: right">
                                    {{ \App\Helper\HString::currencyFormatVn($item->deduction_tax) }}
                                    {{-- <input style="width: 150px" type="text" value="{{ number_format($item->total_deduction_tax, 0, ',', '.') }}" data-name="total_deductions" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct"> --}}
                                </td>
                               
                                <td style="text-align: center; vertical-align: middle; text-align: right">
                                    {{ \App\Helper\HString::currencyFormatVn($item->increase_non_tax) }}
                                    {{-- <input style="width: 150px" type="text" value="{{ number_format(($item->total_payoff - $item->total_payoff_tax), 0, ',', '.') }}" data-name="total_deductions" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct"> --}}
                                </td>

                                <td style="text-align: center; vertical-align: middle; text-align: right">
                                    {{ \App\Helper\HString::currencyFormatVn($item->increase_tax) }}
                                    {{-- <input style="width: 150px" type="text" value="{{ number_format($item->total_payoff_tax, 0, ',', '.') }}" data-name="total_deductions" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct"> --}}
                                </td>
                               

                                <td style="text-align: center; vertical-align: middle; text-align: right">
                                    {{ \App\Helper\HString::currencyFormatVn($item->income_taxes) }}
                                    {{-- <input style="width: 150px" type="text" value="{!! number_format($item->income_taxes, 0, ',', '.' )?? '' !!}" data-name="income_taxes" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct"> --}}
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    {{ $item->dependent_person }}
                                    {{-- <input style="width: 150px" type="text" value="{{ \App\User::countUserRelationship($item->user_id) }}" data-name="ng_phu_thuoc" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct"> --}}
                                </td>
                                <td style="text-align: center; vertical-align: middle; text-align: right">
                                    {{ \App\Helper\HString::currencyFormatVn($item->family_allowances) }}

                                    {{-- <input style="width: 150px" type="text" value="{!! number_format($item->family_allowances, 0, ',', '.' )?? '' !!}" data-name="gia_canh" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct"> --}}
                                </td>
                                <td style="text-align: center; vertical-align: middle; text-align: right">
                                    {{ \App\Helper\HString::currencyFormatVn($item->taxable_income) }}

                                    {{-- <input style="width: 150px" type="text" value="{!! number_format($item->taxable_income, 0, ',', '.' )?? '' !!}"  data-name="tntt" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct"> --}}
                                </td>
                                <td style="text-align: center; vertical-align: middle; text-align: right">
                                    {{ \App\Helper\HString::currencyFormatVn($item->personal_income_tax) }}

                                    {{-- <input style="width: 150px" type="text" value="{!! number_format($item->personal_income_tax, 0, ',', '.' )?? '' !!}" data-name="personal_income_tax" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct"> --}}
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    {{ $item->kpi }}
                                    {{-- <input style="width: 150px" type="text" value="{{ \App\Models\Payroll::getKpi($item->user_id, $payroll->month, $payroll->year) ?? '0' }}" data-name="kpi" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct"> --}}
                                </td>
                                <td style="text-align: center; vertical-align: middle; text-align: right">
                                    {{ \App\Helper\HString::currencyFormatVn($item->dieu_chinh_khac) }}

                                    {{-- <input style="width: 150px" type="text" value="{!! number_format($item->total_impale, 0, ',', '.' )?? '' !!}" data-name="kpi" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct"> --}}
                                </td>
                                <td style="text-align: center; vertical-align: middle; text-align: right">
                                    {{ \App\Helper\HString::currencyFormatVn($item->total_real_salary) }}

                                    {{-- <input style="width: 150px" type="text" value="{!! number_format($item->total_real_salary, 0, ',', '.' )?? '' !!}" data-name="total_real_salary" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct"> --}}
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
                           <th colspan="2" style="text-align: center" class="th-job">Tổng : </th>
                           <th style="text-align: center"></th>
                           <th style="text-align: center"></th>
                           <th style="text-align: center"></th>
                           <th style="text-align: center"></th>
                           <th style="text-align: center"></th>
                           <th style="text-align: center"></th>
                           <th style="text-align: center"></th>
                           <th style="text-align: center"></th>
                           <th style="text-align: center"></th>
                           <th style="text-align: center"></th>
                           <th style="text-align: center"></th>
                           <th style="text-align: center"></th>
                           <th style="text-align: center"></th>
                           <th style="text-align: center"></th>
                           {{-- <th style="text-align: center">{{ ($total['total_day_request']) }}</th>
                           <th style="text-align: center">{{ ($total['ca_ngay_tv']) }}</th>
                           <th style="text-align: center">{{ \App\Helper\HString::currencyFormatVn($total['ca_ngay_hd']) }}</th>
                           <th style="text-align: center">{{ ($total['ca_dem_tv']) }}</th>
                           <th style="text-align: center">{{ ($total['ca_dem_hd']) }}</th>
                           <th style="text-align: center">{{ ($total['cong_tac']) }}</th>
                           <th style="text-align: center">{{ ($total['nghi_huong_luong']) }}</th>
                           <th style="text-align: center">{{ ($total['nghi_dinh_chi']) }}</th>
                           <th style="text-align: center">{{ ($total['muon_k_luong']) }}</th>
                           <th style="text-align: center">{{ ($total['total_work']) }}</th>
                           <th style="text-align: center">{{ ($total['an_chinh']) }}</th>
                           <th style="text-align: center">{{ ($total['an_phu']) }}</th> --}}
                           <th style="text-align: right">{{ \App\Helper\HString::currencyFormatVn($total['basic_salary_tv']) }}</th>
                           <th style="text-align: right">{{ \App\Helper\HString::currencyFormatVn($total['basic_salary_hd']) }}</th>
                           <th style="text-align: right">{{ \App\Helper\HString::currencyFormatVn($total['salary_bh']) }}</th>  
                           <th style="text-align: right">{{ \App\Helper\HString::currencyFormatVn($total['working_salary_tax']) }}</th>  
                           <th style="text-align: right">{{ \App\Helper\HString::currencyFormatVn($total['working_salary_non_tax']) }}</th>  
                           {{-- <th style="text-align: right">100</th>   --}}
                           <th style="text-align: right">{{ \App\Helper\HString::currencyFormatVn($total['salary_ot_non_tax']) }}</th>  
                           <th style="text-align: right">{{ \App\Helper\HString::currencyFormatVn($total['salary_ot_tax']) }}</th>  
                           <th style="text-align: right">{{ \App\Helper\HString::currencyFormatVn($total['an_trua_non_tax']) }}</th>  
                           <th style="text-align: right">{{ \App\Helper\HString::currencyFormatVn($total['an_trua_tax']) }}</th>  
                           <th style="text-align: right">{{ \App\Helper\HString::currencyFormatVn($total['di_lai']) }}</th>  
                           <th style="text-align: right">{{ \App\Helper\HString::currencyFormatVn($total['trach_nhiem']) }}</th>  
                           <th style="text-align: right">{{ \App\Helper\HString::currencyFormatVn($total['cong_hien']) }}</th>  
                           <th style="text-align: right">{{ \App\Helper\HString::currencyFormatVn($total['nang_suat']) }}</th>  
                           <th style="text-align: right">{{ \App\Helper\HString::currencyFormatVn($total['dien_thoai']) }}</th>  
                           <th style="text-align: right">{{ \App\Helper\HString::currencyFormatVn($total['cong_viec']) }}</th>  
                           <th style="text-align: right">{{ \App\Helper\HString::currencyFormatVn($total['dac_thu']) }}</th>  
                           <th style="text-align: right">{{ \App\Helper\HString::currencyFormatVn($total['khac']) }}</th>  
                           <th style="text-align: right">{{ \App\Helper\HString::currencyFormatVn($total['chuyen_can']) }}</th>  
                           <th style="text-align: right">{{ \App\Helper\HString::currencyFormatVn($total['total_salary']) }}</th> 
                           <th style="text-align: right" >{{ \App\Helper\HString::currencyFormatVn($total_bh['bhxh_user']) }}</th> 
                           <th style="text-align: right" >{{ \App\Helper\HString::currencyFormatVn($total_bh['bhyt_user']) }}</th> 
                           <th style="text-align: right" >{{ \App\Helper\HString::currencyFormatVn($total_bh['union_user']) }}</th> 
                           <th style="text-align: right" >{{ \App\Helper\HString::currencyFormatVn($total_bh['bhtn_user']) }}</th> 
                           <th style="text-align: right" >{{ \App\Helper\HString::currencyFormatVn($total_bh['bhxh_company']) }}</th> 
                           <th style="text-align: right" >{{ \App\Helper\HString::currencyFormatVn($total_bh['bhyt_company']) }}</th> 
                           <th style="text-align: right" >{{ \App\Helper\HString::currencyFormatVn($total_bh['union_company']) }}</th> 
                           <th style="text-align: right" >{{ \App\Helper\HString::currencyFormatVn($total_bh['bhtn_company']) }}</th> 
                           <th style="text-align: right">{{ \App\Helper\HString::currencyFormatVn($total['quyet_toan']) }}</th>  
                           <th style="text-align: right">{{ \App\Helper\HString::currencyFormatVn($total['deduction_non_tax']) }}</th>  
                           <th style="text-align: right">{{ \App\Helper\HString::currencyFormatVn($total['deduction_tax']) }}</th>  
                           <th style="text-align: right">{{ \App\Helper\HString::currencyFormatVn($total['increase_non_tax']) }}</th>  
                           <th style="text-align: right">{{ \App\Helper\HString::currencyFormatVn($total['increase_tax']) }}</th>  
                           <th style="text-align: right">{{ \App\Helper\HString::currencyFormatVn($total['income_taxes']) }}</th>  
                           {{-- <th style="text-align: center">{{ \App\Helper\HString::currencyFormatVn($total['dependent_person']) }}</th>   --}}
                           <th style="text-align: center"></th>  
                           <th style="text-align: right">{{ \App\Helper\HString::currencyFormatVn($total['family_allowances']) }}</th>  
                           <th style="text-align: right">{{ \App\Helper\HString::currencyFormatVn($total['taxable_income']) }}</th>  
                           <th style="text-align: right">{{ \App\Helper\HString::currencyFormatVn($total['personal_income_tax']) }}</th>  
                           <th style="text-align: center"></th>  
                           {{-- <th style="text-align: center">{{ \App\Helper\HString::currencyFormatVn($total['kpi']) }}</th>   --}}
                           <th style="text-align: right">{{ \App\Helper\HString::currencyFormatVn($total['dieu_chinh_khac']) }}</th>  
                           <th style="text-align: right">{{ \App\Helper\HString::currencyFormatVn($total['total_real_salary']) }}</th>  
                     
                       </tr>
                        
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@stop
@section('footer')
<script src="{!! asset('assets/backend/plugins/input-mask/jquery.inputmask.min.js') !!}"></script>

<script>
    $(document).ready(function () {
        $(".hover").hover(function(){
                 $(this).css("background-color", "#9ad0ff");
                 $(this).find('.sticky-col').css("background-color", "#9ad0ff");

             }, function(){
                 $(this).css("background-color", "white");
                 $(this).find('.sticky-col').css("background-color", "white");

         });

    });

    
             $('#text_bhxh_user').val(parseInt(getTotal('input[name="bhxh_user[]"]')));  
             $('#text_bhyt_user').val(parseInt(getTotal('input[name="bhyt_user[]"]')));  
             $('#text_union_user').val(parseInt(getTotal('input[name="union_user[]"]')));  
             $('#text_bhtn_user').val(parseInt(getTotal('input[name="bhtn_user[]"]')));  
             $('#text_bhxh_company').val(parseInt(getTotal('input[name="bhxh_company[]"]')));  
             $('#text_bhyt_company').val(parseInt(getTotal('input[name="bhyt_company[]"]')));  
             $('#text_union_company').val(parseInt(getTotal('input[name="union_company[]"]')));  
             $('#text_bhtn_company').val(parseInt(getTotal('input[name="bhtn_company[]"]')));  
</script>
   
   
    
@stop