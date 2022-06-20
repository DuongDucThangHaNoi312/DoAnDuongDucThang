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

        .sticky-col1 {
            position: -webkit-sticky;
            position: sticky;
            background-color: white;
            left: 0;
            z-index: 300 !important;
        }

        .sticky-col {
            position: -webkit-sticky;
            position: sticky;
            background-color: white;
            left: 0;
            z-index: 200 !important;
        }

        .table-scroll {
            position: relative;
            width:100%;
            margin: auto;
            overflow: auto;
            max-height: 980px;
        }
        .table-scroll table {
            width: 100%;
            min-width: 1280px;
            margin: auto;
            border-collapse: separate;
        }
        .table-wrap {
            position: relative;
        }
        .table-scroll th,
        .table-scroll td {
            padding: 8px;
            vertical-align: top;
            border-right: 1px solid #D2D6DE;
            border-bottom: 1px solid #D2D6DE;
        }
        thead tr th.fixed-1 {
            background: #EBEEF4;
            color: #367FA9;
            z-index: 101;
            position: -webkit-sticky;
            position: sticky;
            top: 0;
            /*border-right: 1px solid #D2D6DE;*/
            /*border-top: 1px solid #D2D6DE;*/
        }

        thead tr th.fixed-2 {
            background: #EBEEF4;
            color: #367FA9;
            z-index: 101;
            position: -webkit-sticky;
            position: sticky;
            top: 58px;
            /*border-right: 1px solid #D2D6DE;*/
            /*border-top: 1px solid #D2D6DE;*/
        }

        
       
        /*th.col-fixed {*/

            
        /* .sticky-col {
            position: -webkit-sticky;
            position: sticky;
            left: 0;
            z-index: 8;
            background-color: white;
        } */
        
        .th-tc{
            min-width: 180px;
            text-align: center;
        }

        .tab {
            padding: 7px 0;
            margin-top: 5px;
        }
        .tab span {
            margin: 0 1px;
        }
        .tab span:first-child {
            margin-left: 0;
        }
        .tab span a {
            background-color: #c8d2e0;
            border-color: #c8d2e0;
            color: #FFFFFF;
            padding: 8px 9px;
        }
        .active-tab {
            background: #3c8dbc !important;
            border-color: #3c8dbc !important;
        }
    </style>
@stop
@section('content')
    <section class="content-header">
        <h1>
            <small style="font-weight: 600">
                Chi tiết lương NV: {{ Auth::user()->fullname }}
            </small>
        </h1>
        <div class="tab">
            <span class="all">
                 <a href="{!! route('admin.payrolls1.salary_user') !!}" class="hc-tab"
                    data-toggle="tooltip" data-placement="top" title="Lương"
                    style="outline: none;">
                    Lương
                 </a>
            </span>
            <span class="all">
                <a href="{{ route('admin.payroll.payoff') }}" class="shift-tab" target="_blank"
                   data-toggle="tooltip" data-placement="top" title="khoản tăng"
                   style="outline: none;">
                   Khoản tăng
                </a>
           </span>
        </div>
    </section>
    <section class="content overlay">
        
        <div class="box">
            
            <div class="box-body no-padding" style="overflow-x:auto; overflow-x:auto;">
                
                <div id="table-scroll" class="table-scroll">
                    <?php $cate_allowances = \App\Models\AllowanceCategory::cateAllowance() ;?>

                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                
                                <th rowspan="2" class="sticky-col1 fixed-1" style="text-align: center; vertical-align: middle; padding: 0 100px">
                                    <span class="uppercase">Thông tin</span><br>
                                    <span class="uppercase">nhân viên</span>
                                </th>
                                
                                <th rowspan="2"  style="text-align: center; vertical-align: middle;" class="tdbreak fixed-1">
                                    <span class="uppercase">FIXED WORKING DAYS </span> <br>
                                    <span>Số ngày công theo tháng</span>
                                </th>
                                <th  colspan="9"  style="text-align: center; vertical-align: middle;" class="tdbreak fixed-1">
                                    <span class="uppercase">ACTUAL WORKING DAYS</span><br>
                                    <span>Ngày công thực tế</span><br>
                                    {{-- <span>(làm việc ở công ty)</span> --}}
                                </th>
                                
    
                                {{-- <th rowspan="2"  style="text-align: center; vertical-align: middle;" class="tdbreak">
                                    <span class="uppercase">ACTUAL
                                        WORKING
                                        DAYS
                                        
                                        </span><br>
                                    <span>Ngày công thực tế</span><br>
                                    <span>(công tác)</span>
                                </th> --}}
                                <th rowspan="2"  style="text-align: center; vertical-align: middle;" class="tdbreak fixed-1">
                                    <span>NUMBER
                                        OF SHIFT
                                        MEAL
                                        </span><br>
                                    <span>Số bữa ăn chính</span>
                                </th>
                                <th rowspan="2"  style="text-align: center; vertical-align: middle;" class="tdbreak fixed-1">
                                    <span>NUMBER OF EXTRA MEAL
                                        </span><br>
                                    <span>Số bữa phụ</span>
                                </th>
                                {{-- <th rowspan="2"  style="text-align: center; vertical-align: middle;" class="tdbreak">
                                    <span class="uppercase">Night working day</span><br>
                                    <span>Ngày công làm việc đêm</span>
                                </th> --}}
                                <th rowspan="2"  style="text-align: center; vertical-align: middle;" class="tdbreak fixed-1">
                                    <span>SALARY PROBATION
                                       </span><br>
                                    <span>Lương thử việc</span>
                                </th>
                                <th rowspan="2"  style="text-align: center; vertical-align: middle;" class="tdbreak fixed-1">
                                    <span>BASIC 
                                        RATE
                                       </span><br>
                                    <span>Lương cơ bản</span>
                                </th>
                                <th  rowspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak fixed-1">
                                    <span>BASIC 
                                        RATE
                                       </span><br>
                                    <span>Lương đóng BH</span>
                                </th>
                                <th  rowspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak fixed-1">
                                    <span class="uppercase">Actual Work. Day</span> <br>
                                    <span>Lương làm việc thực tế</span>
                                </th>
                                <th  rowspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak fixed-1">
                                    <span class="uppercase">Actual night Work. Day</span><br>
                                    <span>Lương trả khi làm đêm (30%)</span>
                                </th>
                                <th  colspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak fixed-1">
                                    <span class="uppercase"> Actual Work Salary 	
        
                                    </span> <br>
                                    <span>Lương làm thêm</span>
                                </th>
                                <th class="fixed-1" colspan="{{ count($cate_allowances) + 2 }}" style="text-align: center; vertical-align: middle;">
                                    <span class="uppercase">allowance</span><br>
                                    <span>Phụ cấp</span>
                                </th>
                                <th rowspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak fixed-1">
                                    <span class="uppercase">Total income</span><br>
                                    <span class="">Tổng thu nhập</span>
                                </th>
                                <th colspan="4" style="text-align: center; vertical-align: middle;" class="tdbreak fixed-1">
                                    <span class="uppercase">DEDUCTIONS</span><br>
                                    <span class="">Khấu trừ nhân viên</span>
                                </th>
                                
                                <th colspan="4" style="text-align: center; vertical-align: middle;" class="tdbreak fixed-1">
                                    <span class="uppercase">COMPANY CONTRIBUTION</span><br>
                                    <span class="">Đóng góp của CÔNG TY</span>
                                </th>
    
                                <th rowspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak fixed-1">
                                    <span class="uppercase">DEDUCTION PIT FINALIZE</span><br>
                                    <span class="">Nộp theo quyết toán</span><br>
                                    <span>TNCN năm 2022</span>
                                </th>
                                <th colspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak fixed-1">
                                    <span class="uppercase">DEDUCTION</span><br>
                                    <span class="">Khoản giảm trừ khác</span>
                                </th>
                                <th colspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak fixed-1">
                                    <span class="uppercase">increase</span><br>
                                    <span class="">Khoản tăng</span>
                                </th>
    
                                <th rowspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak fixed-1">
                                    <span class="uppercase">Taxable
                                        Income
                                        </span><br>
                                    <span class="">Thu nhập chịu thuế</span>
                                </th>
    
                                <th rowspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak fixed-1">
                                    <span class="uppercase">No of dependant
                                        </span><br>
                                    <span class="">Số người phụ thuộc</span>
                                </th>
    
                                <th rowspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak fixed-1">
                                    <span class="uppercase">Self relief and dependant relief
                                        </span><br>
                                    <span class="">Khấu trừ gia cảnh</span>
                                </th>
    
                                <th rowspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak fixed-1">
                                    <span class="uppercase">Assessable Income
                                        </span><br>
                                    <span class="">Thu nhập tính thuế</span>
                                </th>
    
                                <th rowspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak fixed-1">
                                    <span class="uppercase">Income tax
                                        </span><br>
                                    <span class="">Thuế TNCN</span>
                                </th>
                                <th rowspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak fixed-1">
                                    <span class="uppercase">Điểm KPI
                                        </span><br>
                                    <span>trong tháng</span>
                                </th>
                                <th rowspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak fixed-1">
                                    <span class="uppercase">
                                        </span><br>
                                    <span>Các khoản điều <br> chỉnh khác</span>
                                </th>
                                <th rowspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak fixed-1">
                                    <span class="uppercase">TAKE HOME PAY
                                        </span><br>
                                    <span>Tổng thực lĩnh</span>
                                </th>
    
                                <tr>
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">Số ngày thử việc</th>
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">Số ngày hợp đồng</th>
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">Số đêm thử việc</th>
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">Số đêm hợp đồng</th>
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">Ngày công tác</th>
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">Nghỉ hưởng lương</th>
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">Nghỉ đình chỉ</th>
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">Nghỉ không lương<br>đi muộn</th>
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">Tổng</th>
                                    
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">Miễn thuế</th>
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">Chịu thuế</th>
    
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">Phụ cấp ăn trưa, ăn ca (NON-TAX)</th>
    
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">Phụ cấp ăn trưa, ăn ca (TAX)</th>
    
                                    @foreach ($cate_allowances as $k => $cate)
                                        <th style="text-align: center; vertical-align: middle; " class="th-tc tdbreak fixed-2">{{ $cate ?? '' }}</th>
                                    @endforeach
                                    
                                    
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">
                                        <span class="uppercase">SOC INS</span><br>
                                        <span class="uppercase">BHXH</span><br>
                                        <span class="uppercase">8%</span>
                                    </th>
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">
                                        <span class="uppercase">H INS </span><br>
                                        <span class="uppercase">BHYT</span><br>
                                        <span class="uppercase">1.5%</span>
                                    </th>
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">
                                        <span class="uppercase">UNION</span><br>
                                        <span class="uppercase">Công đoàn</span><br>
                                        <span class="uppercase">1%</span>
                                    </th>
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">
                                        <span class="uppercase">UN EM INS</span><br>
                                        <span class="uppercase">BHTN</span><br>
                                        <span class="uppercase">1%</span>
                                    </th>
    
    
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">
                                        <span class="uppercase">SOC INS</span><br>
                                        <span class="uppercase">BHXH</span><br>
                                        <span class="uppercase">17%</span>
                                    </th>
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">
                                        <span class="uppercase">H INS </span><br>
                                        <span class="uppercase">BHYT</span><br>
                                        <span class="uppercase">3%</span>
                                    </th>
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">
                                        <span class="uppercase">UNION</span><br>
                                        <span class="uppercase">Công đoàn</span><br>
                                        <span class="uppercase">2%</span>
                                    </th>
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">
                                        <span class="uppercase">UN EM INS</span><br>
                                        <span class="uppercase">BHTN</span><br>
                                        <span class="uppercase">0%</span>
                                    </th>
                                    
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">Miễn thuế</th>
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">Chịu thuế</th>
    
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">Miễn thuế</th>
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">Chịu thuế</th>
                                    
                                </tr>
                               
                                
                            </tr>
                        </thead>
                        <tbody>
                           @if (count($payroll_detail) > 0)
                                <?php $i = 1; ?>
                                @foreach ($payroll_detail as $k => $item)
                                    @if ($item->timekeepingDetail->timekeeping->version == 1)
                                        <?php 
                                            $dep_timekeeping = $item->timekeepingDetail->timekeeping->department_id;
                                            $typeDepartment = $item->timekeepingDetail->timekeeping->department->type;
                                            $total_day_request = \App\Models\OverTimes::totalWorkingInMonth($item->payroll->month, $item->payroll->year, $dep_timekeeping);
                                            $tongHop = \App\Models\TimeKeeping::tongHop($item->timekeeping_id, $typeDepartment, $total_day_request);
                                            $calculateAllowance = json_decode($item->calculateAllowance, true);
                                            $bh = json_decode($item->bh, true);
                                        ?>
                                        <tr class="hover" data-index="{{ $k + 1 }}">
                                            <td class="sticky-col" style="text-align: center; vertical-align: middle;">
                                                <span class="">Lương tháng: {{ $item->payroll->month.'/'.$item->payroll->year }}</span><br>
                                                <span>{{ $item->payroll->company->shortened_name }} - {{ $item->payroll->department->name }} </span><br>
                                                @if ($item->salary_concurrent > 0)
                                                    <span class="label label-success">Kiêm nhiệm</span>
                                                @endif

                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <input name="total_day_request[]" style="text-align: center;" type="text" value="{{ $total_day_request ?? '' }}" data-name="total_day_request" data-user-id="{!! $item->user_id !!}" class="form-control ct">
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <input name="ca_ngay_tv[]" style="text-align: center;" type="text" value="{{ $tongHop['ngay_tv'] ?? 0 }}" data-name="default" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct">
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <input name="ca_ngay_hd[]" style="text-align: center;" type="text" value="{{ $tongHop['ngay_hd'] ?? 0 }}" data-name="default" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct">
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <input name="ca_dem_tv[]" style="text-align: center;" type="text" value="{{ $tongHop['dem_tv'] ?? 0 }}" data-name="default" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct">
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <input name="ca_dem_hd[]" style="text-align: center;" type="text" value="{{ $tongHop['dem_hd'] ?? 0 }}" data-name="default" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct">
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <input name="count_day_offs[]" style="text-align: center;" type="text" value="{{ $tongHop['nghi_cong_tac'] ?? 0 }}" data-name="default" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct">
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <?php 
                                                    $nghiHuongLuong = $tongHop['nghi_phep'] + $tongHop['nghi_cuoi'] + $tongHop['nghi_hieu'] + $tongHop['nghi_le'] + $tongHop['lam_tai_nha'];
                                                ?>
                                                <input name = "nghi_huong_luong[]" style="text-align: center;" type="text" value="{{ $nghiHuongLuong ?? 0 }}" data-name="default" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct">
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <input name="ngaydinhchi[]" style="text-align: center;" type="text" value="{{ $tongHop['nghi_70_luong'] ?? 0 }}" data-name="default" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct">
                                            </td>
                                            <td  style="text-align: center; vertical-align: middle;">
                                                <?php 
                                                    $nghi_khong_luong = $item->tongHop['nghi_om'] + $item->tongHop['nghi_khong_luong'];
                                                ?>
                                                <input name="nghikhongluong[]" style="text-align: center;" type="text" value="{{ $nghi_khong_luong ?? 0 }}" data-name="default" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct">
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <input name="tong[]" type="text" style="width: 70px;text-align: center;" value="{{ $tongHop['total'] ?? 0 }}" data-name="total_work_department" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct">
                                            </td>

                                            
                                            <td style="text-align: center; vertical-align: middle;">
                                                <input name="an_chinh[]" style="text-align: center;" type="text" value="{!! $tongHop['an_chinh'] ?? 0 !!}" data-name="an_chinh" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct">
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <input name="an_phu[]" style="text-align: center;" type="text" value="{!! $tongHop['an_phu'] ?? 0 !!}" data-name="an_phu" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct">
                                            </td>
                                            
                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="basic_salary_tv[]" style="text-align: right;" type="text" value="{!! intval($item->basic_salary_tv) ?? '' !!}" 
                                                data-name="basic_salary_tv" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                            </td>
                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="basic_salary_hd[]" style="width: 100px;text-align: right;" type="text" value="{!! intval($item->basic_salary_hd) ?? '' !!}" 
                                                data-name="basic_salary_hd" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                            </td>
                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="salary_bh[]" style="width: 150px;text-align: right;" type="text" value="{!! intval($item->salary_bh)  !!}"  
                                                data-name="salary_bh" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                            </td>
                                            <td style="text-align: right; vertical-align: middle;">
                                                <input style="width: 150px;text-align: right;" type="text" value="{!! intval($item->working_salary_tax)?? '' !!}" 
                                                data-name="working_salary_tax" data-user-id="{!! $item->user_id !!}" data-concurrent="{{ $item->salary_concurrent }}"
                                                    name="working_salary_tax[]" id="" class="form-control ct currency">
                                            </td>
                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="working_salary_non_tax[]" style="text-align: right;" type="text" value="{!! intval($item->working_salary_non_tax)?? '' !!}" 
                                                data-name="working_salary_non_tax" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                            </td>
                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="salary_ot_non_tax[]" style="width: 150px;text-align: right;" type="text" value="{!! intval($item->salary_ot_non_tax)?? '' !!}" 
                                                data-name="default" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                            </td>

                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="salary_ot_tax[]" style="width: 150px;text-align: right;" type="text" value="{!! intval($item->salary_ot_tax)?? '' !!}" 
                                                data-name="default" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                            </td>

                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="food_allowance_nonTax[]" style="text-align: right;" type="text" value="{!! intval($item->food_allowance_nonTax)?? '' !!}" 
                                                name="" id="" data-name="food_allowance_nonTax" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency" data-concurrent="{{ $item->salary_concurrent }}">
                                            </td>
                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="food_allowance_tax[]" style="text-align: right;" type="text" value="{!! intval($item->food_allowance_tax)?? '' !!}" 
                                                data-name="food_allowance_tax" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency" data-concurrent="{{ $item->salary_concurrent }}">
                                            </td>
                                            

                                            @foreach ($cate_allowances as $k1 => $cate)
                                                <td style="text-align: center; vertical-align: middle;" class="tdbreak">
                                                    {{ $salary_concurrent }}
                                                    <input name="trocap_{!! $k1 !!}[]" style="text-align: right;width:100%" type="text" value="{!! intval($calculateAllowance[$k1]) ?? 0 !!}" data-name="allowance" 
                                                        data-user-id="{!! $item->user_id !!}" data-allowance="{!! $k1 !!}" name="" id="" class="form-control ct currency trocap_{!! $k1 !!}" data-concurrent="{{ $item->salary_concurrent }}">
                                                </td>
                                            @endforeach

                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="total_salary[]" style="width: 150px;" type="text" value="{!! intval($item->total_salary) !!}" 
                                                data-name="total_salary" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                            </td>

                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="bhxh_user[]" style="width: 150px ;text-align: right;" type="text" value="{!! intval($bh['bhxh_user'])?? '' !!}"  
                                                data-name="bhxh_user" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct bhxh_user currency">
                                            </td>
                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="bhyt_user[]" style="width: 150px;text-align: right;" type="text" value="{!! intval($bh['bhyt_user'])?? '' !!}" 
                                                data-name="bhyt_user" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct bhyt_user currency">
                                            </td>
                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="union_user[]" style="width: 150px;text-align: right;" type="text" value="{!! intval($bh['union_user'])?? '' !!}" 
                                                data-name="union_user" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct union_user currency">
                                            </td>
                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="bhtn_user[]" style="width: 150px;text-align: right;" type="text" value="{!! intval($bh['bhtn_user'])?? '' !!}" 
                                                data-name="bhtn_user" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct bhtn_user currency">
                                            </td>

                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="bhxh_company[]" style="width: 150px;text-align: right;" type="text" value="{!! intval($bh['bhxh_company'])?? '' !!}" 
                                                data-name="bhxh_company" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct bhxh_company currency" >
                                            </td>

                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="bhyt_company[]" style="width: 150px;text-align: right;" type="text" value="{!! intval($bh['bhyt_company'])?? '' !!}" 
                                                data-name="bhyt_company" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct bhyt_company currency">
                                            </td>
                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="union_company[]" style="width: 150px;text-align: right;" type="text" value="{!! intval($bh['union_company'])?? '' !!}" 
                                                data-name="union_company" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct union_company currency">
                                            </td>

                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="bhtn_company[]" style="width: 150px;text-align: right;" type="text" value="{!! intval($bh['bhtn_company$'])?? '' !!}" data-name="bhtn_company" 
                                                data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct bhtn_company currency">
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <input name="ttcn_2020[]" style="width: 150px;text-align: right;" type="text" value="0" data-name="ttcn_2020" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                            </td>

                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="deduction_not_tax[]" style="width: 150px;text-align: right;" type="text" value="{{ intval(($item->total_deduction - $item->total_deduction_tax)) }}" data-name="total_deductions" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                            </td>

                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="deduction_tax[]" style="width: 150px;text-align: right;" type="text" value="{{ intval($item->total_deduction_tax) }}" data-name="total_deductions" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                            </td>
                                        
                                            <td style="text-align: right; vertical-align: middle;">
                                                <input  name="payoff_not_tax[]" style="width: 150px;text-align: right;" type="text" value="{{ intval(($item->total_payoff - $item->total_payoff_tax)) }}" data-name="total_deductions" data-user-id="{!! $item->user_id !!}" name="" id="" class=" currency form-control ct">
                                            </td>

                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="payoff_tax[]" style="width: 150px;text-align: right;" type="text" value="{{ intval($item->total_payoff_tax) }}" data-name="total_deductions" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                            </td>
                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="income_taxes[]" style="width: 150px;text-align: right;" type="text" value="{!! intval($item->income_taxes)?? '' !!}" data-name="income_taxes" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency income_taxes">
                                            </td>
                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="ng_phu_thuoc[]" style="text-align: center;" type="text" value="{{ \App\User::countUserRelationship($item->user_id) }}" data-name="ng_phu_thuoc" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct">
                                            </td>
                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="family_allowances[]" style="text-align: right;" type="text" value="{!! intval($item->family_allowances)?? '' !!}" data-name="gia_canh" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                            </td>
                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="taxable_income[]" style="width: 150px;text-align: right;" type="text" value="{!! intval($item->taxable_income)?? '' !!}"  data-name="tntt" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                            </td>
                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="personal_income_tax[]" style="width: 150px;text-align: right;" type="text" value="{!! intval($item->personal_income_tax) ?? '' !!}" data-name="personal_income_tax" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                            </td>
                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="get_kpi[]" style="text-align: center;" type="text" value="{{ $totalWorkDepartment == 0 ? 0 : \App\Models\Payroll::getKpi($item->user_id, $item->payroll->month, $item->payroll->year) }}" data-name="kpi" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct ">
                                            </td>
                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="total_impale[]" style="text-align: right;" value="{!! intval($item->total_impale) !!}"  type="text"  data-name="kpi" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                            </td>
                                            <td  style="text-align: right; vertical-align: middle;">
                                                <input  name="total_real_salary[]" style="width: 150px;text-align: right;" type="text" value="{!! intval($item->total_real_salary)  ??  0 !!}" data-name="total_real_salary" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                            </td>
                                            
                                        </tr>
                                    @else   
                                        <?php $data = \App\Models\Payroll::userPayrollDetail($item->id); 
                                            $totalDayRequest = \App\Models\Payroll::totalDayRequest($item->user_id, $item->payroll->month, $item->payroll->year);
                                            $totalWorkDepartment = \App\Models\Payroll::totalWorkDepartment($item->user_id, $item->payroll->month, $item->payroll->year, $item->payroll->department_id);
                                        ?>
                                        <tr class="hover" data-index="{{ $k + 1 }}">
                                            <td class="sticky-col" style="text-align: center; vertical-align: middle;">
                                                <span class="">Lương tháng: {{ $item->payroll->month.'/'.$item->payroll->year }}</span><br>
                                                <span>{{ $item->payroll->company->shortened_name }} - {{ $item->payroll->department->name }} </span><br>
                                                @if ($item->salary_concurrent > 0)
                                                    <span class="label label-success">Kiêm nhiệm</span>
                                                @endif

                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <input name="total_day_request[]" style="text-align: center;" type="text" value="{{ $totalDayRequest ?? '' }}" data-name="total_day_request" data-user-id="{!! $item->user_id !!}" class="form-control ct">
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <input name="ca_ngay_tv[]" style="text-align: center;" type="text" value="{{ $data['user_payroll']->ca_ngay_tv ?? 0 }}" data-name="default" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct">
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <input name="ca_ngay_hd[]" style="text-align: center;" type="text" value="{{ $data['user_payroll']->ca_ngay_hd ?? 0 }}" data-name="default" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct">
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <input name="ca_dem_tv[]" style="text-align: center;" type="text" value="{{ $data['user_payroll']->ca_dem_tv ?? 0 }}" data-name="default" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct">
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <input name="ca_dem_hd[]" style="text-align: center;" type="text" value="{{ $data['user_payroll']->ca_dem_hd ?? 0 }}" data-name="default" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct">
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <input name="count_day_offs[]" style="text-align: center;" type="text" value="{{ \App\StaffDayOff::countDayOffs($item->user_id, $item->payroll->month, $item->payroll->year, 'T', $item->payroll->department_id) }}" data-name="default" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct">
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <input name = "nghi_huong_luong[]" style="text-align: center;" type="text" value="{{ \App\Models\Payroll::countTotalInMonthForTimeKeeping($item->user_id, $item->payroll->month, $item->payroll->year, 'T', $item->payroll->department_id) }}" data-name="default" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct">
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <input name="ngaydinhchi[]" style="text-align: center;" type="text" value="{{ \App\StaffDayOff::countDayOffs($item->user_id, $item->payroll->month, $item->payroll->year, 'C', $item->payroll->department_id) }}" data-name="default" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct">
                                            </td>
                                            <td  style="text-align: center; vertical-align: middle;">
                                                <input name="nghikhongluong[]" style="text-align: center;" type="text" value="{{ \App\Models\Payroll::nghiKhongLuong($item->user_id, $item->payroll->month, $item->payroll->year, 'O', $item->payroll->department_id) }}" data-name="default" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct">
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <input name="tong[]" type="text" style="width: 70px;text-align: center;" value="{{ $totalWorkDepartment ?? '' }}" data-name="total_work_department" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct">
                                            </td>

                                            {{-- <td style="text-align: center; vertical-align: middle;">
                                                <input type="text" value="{{ \App\StaffDayOff::countDayOffs($item->user_id, $item->payroll->month, $item->payroll->year, 'T') }}" data-name="total_work_department" data-user-id="{!! $item->user_id !!}" class="form-control ct">
                                            </td> --}}
                                            <td style="text-align: center; vertical-align: middle;">
                                                <input name="an_chinh[]" style="text-align: center;" type="text" value="{!! $data['user_payroll']->an_chinh ?? '' !!}" data-name="an_chinh" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct">
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <input name="an_phu[]" style="text-align: center;" type="text" value="{!! $data['user_payroll']->an_phu ?? '' !!}" data-name="an_phu" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct">
                                            </td>
                                            {{-- <td style="text-align: center; vertical-align: middle;">
                                                <input type="text" value="{!! $data['user_payroll']->ca_dem_hd +  $data['user_payroll']->ca_dem_tv !!}" data-name="ca_dem_hd" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct">
                                            </td> --}}
                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="basic_salary_tv[]" style="text-align: right;" type="text" value="{!! intval($item->basic_salary_tv) ?? '' !!}" 
                                                data-name="basic_salary_tv" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                            </td>
                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="basic_salary_hd[]" style="width: 100px;text-align: right;" type="text" value="{!! intval($item->basic_salary_hd) ?? '' !!}" 
                                                data-name="basic_salary_hd" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                            </td>
                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="salary_bh[]" style="width: 150px;text-align: right;" type="text" value="{!! intval($item->salary_bh)  !!}"  
                                                data-name="salary_bh" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                            </td>
                                            <td style="text-align: right; vertical-align: middle;">
                                                <input style="width: 150px;text-align: right;" type="text" value="{!! intval($item->working_salary_tax)?? '' !!}" 
                                                data-name="working_salary_tax" data-user-id="{!! $item->user_id !!}" data-concurrent="{{ $item->salary_concurrent }}"
                                                    name="working_salary_tax[]" id="" class="form-control ct currency">
                                            </td>
                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="working_salary_non_tax[]" style="text-align: right;" type="text" value="{!! intval($item->working_salary_non_tax)?? '' !!}" 
                                                data-name="working_salary_non_tax" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                            </td>
                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="salary_ot_non_tax[]" style="width: 150px;text-align: right;" type="text" value="{!! intval($item->salary_ot_non_tax)?? '' !!}" 
                                                data-name="default" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                            </td>

                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="salary_ot_tax[]" style="width: 150px;text-align: right;" type="text" value="{!! intval($item->salary_ot_tax)?? '' !!}" 
                                                data-name="default" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                            </td>

                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="food_allowance_nonTax[]" style="text-align: right;" type="text" value="{!! intval($item->food_allowance_nonTax)?? '' !!}" 
                                                name="" id="" data-name="food_allowance_nonTax" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency" data-concurrent="{{ $item->salary_concurrent }}">
                                            </td>
                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="food_allowance_tax[]" style="text-align: right;" type="text" value="{!! intval($item->food_allowance_tax)?? '' !!}" 
                                                data-name="food_allowance_tax" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency" data-concurrent="{{ $item->salary_concurrent }}">
                                            </td>
                                            

                                            @foreach ($cate_allowances as $k1 => $cate)
                                                <td style="text-align: center; vertical-align: middle;" class="tdbreak">
                                                    {{ $salary_concurrent }}
                                                    <input name="trocap_{!! $k1 !!}[]" style="text-align: right;width:100%" type="text" value="{!! intval(str_replace(".","", (String)\App\Models\Payroll::calculateAllowance($item->user_id, $k1, $totalDayRequest, $totalWorkDepartment, $item->payroll->month, $item->payroll->year, $item->salary_concurrent, '', $item->payroll->department_id))) ?? '' !!}" data-name="allowance" 
                                                        data-user-id="{!! $item->user_id !!}" data-allowance="{!! $k1 !!}" name="" id="" class="form-control ct currency trocap_{!! $k1 !!}" data-concurrent="{{ $item->salary_concurrent }}">
                                                </td>
                                            @endforeach

                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="total_salary[]" style="width: 150px;" type="text" value="{!! intval($item->total_salary) !!}" 
                                                data-name="total_salary" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                            </td>

                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="bhxh_user[]" style="width: 150px ;text-align: right;" type="text" value="{!! intval($data['user_payroll']->bh->bhxh_user)?? '' !!}"  
                                                data-name="bhxh_user" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct bhxh_user currency">
                                            </td>
                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="bhyt_user[]" style="width: 150px;text-align: right;" type="text" value="{!! intval($data['user_payroll']->bh->bhyt_user)?? '' !!}" 
                                                data-name="bhyt_user" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct bhyt_user currency">
                                            </td>
                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="union_user[]" style="width: 150px;text-align: right;" type="text" value="{!! intval($data['user_payroll']->bh->union_user)?? '' !!}" 
                                                data-name="union_user" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct union_user currency">
                                            </td>
                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="bhtn_user[]" style="width: 150px;text-align: right;" type="text" value="{!! intval($data['user_payroll']->bh->bhtn_user)?? '' !!}" 
                                                data-name="bhtn_user" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct bhtn_user currency">
                                            </td>

                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="bhxh_company[]" style="width: 150px;text-align: right;" type="text" value="{!! intval($data['user_payroll']->bh->bhxh_company)?? '' !!}" 
                                                data-name="bhxh_company" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct bhxh_company currency" >
                                            </td>

                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="bhyt_company[]" style="width: 150px;text-align: right;" type="text" value="{!! intval($data['user_payroll']->bh->bhyt_company)?? '' !!}" 
                                                data-name="bhyt_company" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct bhyt_company currency">
                                            </td>
                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="union_company[]" style="width: 150px;text-align: right;" type="text" value="{!! intval($data['user_payroll']->bh->union_company)?? '' !!}" 
                                                data-name="union_company" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct union_company currency">
                                            </td>

                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="bhtn_company[]" style="width: 150px;text-align: right;" type="text" value="{!! intval($data['user_payroll']->bh->bhtn_company)?? '' !!}" data-name="bhtn_company" 
                                                data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct bhtn_company currency">
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <input name="ttcn_2020[]" style="width: 150px;text-align: right;" type="text" value="0" data-name="ttcn_2020" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                            </td>

                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="deduction_not_tax[]" style="width: 150px;text-align: right;" type="text" value="{{ intval(($item->total_deduction - $item->total_deduction_tax)) }}" data-name="total_deductions" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                            </td>

                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="deduction_tax[]" style="width: 150px;text-align: right;" type="text" value="{{ intval($item->total_deduction_tax) }}" data-name="total_deductions" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                            </td>
                                        
                                            <td style="text-align: right; vertical-align: middle;">
                                                <input  name="payoff_not_tax[]" style="width: 150px;text-align: right;" type="text" value="{{ intval(($item->total_payoff - $item->total_payoff_tax)) }}" data-name="total_deductions" data-user-id="{!! $item->user_id !!}" name="" id="" class=" currency form-control ct">
                                            </td>

                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="payoff_tax[]" style="width: 150px;text-align: right;" type="text" value="{{ intval($item->total_payoff_tax) }}" data-name="total_deductions" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                            </td>
                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="income_taxes[]" style="width: 150px;text-align: right;" type="text" value="{!! intval($item->income_taxes)?? '' !!}" data-name="income_taxes" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency income_taxes">
                                            </td>
                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="ng_phu_thuoc[]" style="text-align: center;" type="text" value="{{ \App\User::countUserRelationship($item->user_id) }}" data-name="ng_phu_thuoc" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct">
                                            </td>
                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="family_allowances[]" style="text-align: right;" type="text" value="{!! intval($item->family_allowances)?? '' !!}" data-name="gia_canh" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                            </td>
                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="taxable_income[]" style="width: 150px;text-align: right;" type="text" value="{!! intval($item->taxable_income)?? '' !!}"  data-name="tntt" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                            </td>
                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="personal_income_tax[]" style="width: 150px;text-align: right;" type="text" value="{!! intval($item->personal_income_tax) ?? '' !!}" data-name="personal_income_tax" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                            </td>
                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="get_kpi[]" style="text-align: center;" type="text" value="{{ $totalWorkDepartment == 0 ? 0 : \App\Models\Payroll::getKpi($item->user_id, $item->payroll->month, $item->payroll->year) }}" data-name="kpi" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct ">
                                            </td>
                                            <td style="text-align: right; vertical-align: middle;">
                                                <input name="total_impale[]" style="text-align: right;" value="{!! intval($item->total_impale) !!}"  type="text"  data-name="kpi" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                            </td>
                                            <td  style="text-align: right; vertical-align: middle;">
                                                <input  name="total_real_salary[]" style="width: 150px;text-align: right;" type="text" value="{!! intval($item->total_real_salary)  ??  0 !!}" data-name="total_real_salary" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                            </td>
                                            
                                        </tr>
                                    @endif
                                
                                @endforeach
                           @endif
                           @if (count($salary_drives) > 0) 
                                @foreach ($salary_drives as $item)
                                    <?php 
                                        $bh = json_decode($item->bh, true);
                                    ?>
                                    <tr class="hover" data-index="{{ $k + 1 }}">
                                        <td class="sticky-col" style="text-align: center; vertical-align: middle;">
                                            <span class="">Lương tháng: {{ $item->salaryDrive->month.'/'.$item->salaryDrive->year }}</span><br>
                                        </td>
                                        <td style="text-align: center; vertical-align: middle;">
                                            <input name="total_day_request[]" style="text-align: center;" type="text" value="{{ $item->total_day_request }}" data-name="total_day_request" data-user-id="{!! $item->user_id !!}" class="form-control ct">
                                        </td>
                                        <td style="text-align: center; vertical-align: middle;">
                                            <input name="ca_ngay_tv[]" style="text-align: center;" type="text" value="{{ $item->ca_ngay_tv ?? 0 }}" data-name="default" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct">
                                        </td>
                                        <td style="text-align: center; vertical-align: middle;">
                                            <input name="ca_ngay_hd[]" style="text-align: center;" type="text" value="{{ $item->ca_ngay_hd ?? 0 }}" data-name="default" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct">
                                        </td>
                                        <td style="text-align: center; vertical-align: middle;">
                                            <input name="ca_dem_tv[]" style="text-align: center;" type="text" value="{{ $item->ca_dem_tv ?? 0 }}" data-name="default" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct">
                                        </td>
                                        <td style="text-align: center; vertical-align: middle;">
                                            <input name="ca_dem_hd[]" style="text-align: center;" type="text" value="{{ $item->ca_dem_hd ?? 0 }}" data-name="default" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct">
                                        </td>
                                        <td style="text-align: center; vertical-align: middle;">
                                            <input name="count_day_offs[]" style="text-align: center;" type="text" value="{{ $item->cong_tac ?? 0 }}" data-name="default" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct">
                                        </td>
                                        <td style="text-align: center; vertical-align: middle;">
                                            
                                            <input name = "nghi_huong_luong[]" style="text-align: center;" type="text" value="{{ $item->nghi_huong_luong ?? 0 }}" data-name="default" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct">
                                        </td>
                                        <td style="text-align: center; vertical-align: middle;">
                                            <input name="ngaydinhchi[]" style="text-align: center;" type="text" value="{{ $item->nghi_dinh_chi ?? 0 }}" data-name="default" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct">
                                        </td>
                                        <td  style="text-align: center; vertical-align: middle;">
                                            <input name="nghikhongluong[]" style="text-align: center;" type="text" value="{{ $item->muon_k_luong ?? 0 }}" data-name="default" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct">
                                        </td>
                                        <td style="text-align: center; vertical-align: middle;">
                                            <input name="tong[]" type="text" style="width: 70px;text-align: center;" value="{{ $item->total_work ?? 0 }}" data-name="total_work_department" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct">
                                        </td>

                                        
                                        <td style="text-align: center; vertical-align: middle;">
                                            <input name="an_chinh[]" style="text-align: center;" type="text" value="{!! $item->an_chinh ?? 0 !!}" data-name="an_chinh" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct">
                                        </td>
                                        <td style="text-align: center; vertical-align: middle;">
                                            <input name="an_phu[]" style="text-align: center;" type="text" value="{!! $item->an_phu ?? 0 !!}" data-name="an_phu" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct">
                                        </td>
                                        
                                        <td style="text-align: right; vertical-align: middle;">
                                            <input name="basic_salary_tv[]" style="text-align: right;" type="text" value="{!! intval($item->basic_salary_tv) ?? '' !!}" 
                                            data-name="basic_salary_tv" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                        </td>
                                        <td style="text-align: right; vertical-align: middle;">
                                            <input name="basic_salary_hd[]" style="width: 100px;text-align: right;" type="text" value="{!! intval($item->basic_salary_hd) ?? '' !!}" 
                                            data-name="basic_salary_hd" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                        </td>
                                        <td style="text-align: right; vertical-align: middle;">
                                            <input name="salary_bh[]" style="width: 150px;text-align: right;" type="text" value="{!! intval($item->salary_bh)  !!}"  
                                            data-name="salary_bh" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                        </td>
                                        <td style="text-align: right; vertical-align: middle;">
                                            <input style="width: 150px;text-align: right;" type="text" value="{!! intval($item->working_salary_tax)?? '' !!}" 
                                            data-name="working_salary_tax" data-user-id="{!! $item->user_id !!}" data-concurrent="{{ $item->salary_concurrent }}"
                                                name="working_salary_tax[]" id="" class="form-control ct currency">
                                        </td>
                                        <td style="text-align: right; vertical-align: middle;">
                                            <input name="working_salary_non_tax[]" style="text-align: right;" type="text" value="{!! intval($item->working_salary_non_tax)?? '' !!}" 
                                            data-name="working_salary_non_tax" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                        </td>
                                        <td style="text-align: right; vertical-align: middle;">
                                            <input name="salary_ot_non_tax[]" style="width: 150px;text-align: right;" type="text" value="{!! intval($item->salary_ot_non_tax)?? '' !!}" 
                                            data-name="default" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                        </td>

                                        <td style="text-align: right; vertical-align: middle;">
                                            <input name="salary_ot_tax[]" style="width: 150px;text-align: right;" type="text" value="{!! intval($item->salary_ot_tax)?? '' !!}" 
                                            data-name="default" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                        </td>

                                        <td style="text-align: right; vertical-align: middle;">
                                            <input name="food_allowance_nonTax[]" style="text-align: right;" type="text" value="{!! intval($item->an_trua_non_tax)?? '' !!}" 
                                            name="" id="" data-name="food_allowance_nonTax" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency" data-concurrent="{{ $item->salary_concurrent }}">
                                        </td>
                                        <td style="text-align: right; vertical-align: middle;">
                                            <input name="food_allowance_tax[]" style="text-align: right;" type="text" value="{!! intval($item->an_trua_tax)?? '' !!}" 
                                            data-name="food_allowance_tax" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency" data-concurrent="{{ $item->salary_concurrent }}">
                                        </td>
                                        <td style="text-align: right; vertical-align: middle;">
                                            <input name="trocap_1[]" style="text-align: right;" type="text" value="{!! intval($item->di_lai)?? '' !!}" 
                                            data-name="food_allowance_tax" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency" data-concurrent="{{ $item->salary_concurrent }}">
                                        </td>
                                        <td style="text-align: right; vertical-align: middle;">
                                            <input name="trocap_3[]" style="text-align: right;" type="text" value="{!! intval($item->trach_nhiem)?? '' !!}" 
                                            data-name="food_allowance_tax" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency" data-concurrent="{{ $item->salary_concurrent }}">
                                        </td>
                                        <td style="text-align: right; vertical-align: middle;">
                                            <input name="trocap_4[]" style="text-align: right;" type="text" value="{!! intval($item->cong_hien)?? '' !!}" 
                                            data-name="food_allowance_tax" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency" data-concurrent="{{ $item->salary_concurrent }}">
                                        </td>
                                        <td style="text-align: right; vertical-align: middle;">
                                            <input name="trocap_5[]" style="text-align: right;" type="text" value="{!! intval($item->nang_suat)?? '' !!}" 
                                            data-name="food_allowance_tax" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency" data-concurrent="{{ $item->salary_concurrent }}">
                                        </td>
                                        <td style="text-align: right; vertical-align: middle;">
                                            <input name="trocap_6[]" style="text-align: right;" type="text" value="{!! intval($item->dien_thoai)?? '' !!}" 
                                            data-name="food_allowance_tax" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency" data-concurrent="{{ $item->salary_concurrent }}">
                                        </td>
                                        <td style="text-align: right; vertical-align: middle;">
                                            <input name="trocap_7[]" style="text-align: right;" type="text" value="{!! intval($item->cong_viec)?? '' !!}" 
                                            data-name="food_allowance_tax" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency" data-concurrent="{{ $item->salary_concurrent }}">
                                        </td>
                                        <td style="text-align: right; vertical-align: middle;">
                                            <input name="trocap_8[]" style="text-align: right;" type="text" value="{!! intval($item->dac_thu)?? '' !!}" 
                                            data-name="food_allowance_tax" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency" data-concurrent="{{ $item->salary_concurrent }}">
                                        </td>
                                        <td style="text-align: right; vertical-align: middle;">
                                            <input name="trocap_9[]" style="text-align: right;" type="text" value="{!! intval($item->khac)?? '' !!}" 
                                            data-name="food_allowance_tax" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency" data-concurrent="{{ $item->salary_concurrent }}">
                                        </td>

                                        <td style="text-align: right; vertical-align: middle;">
                                            <input name="trocap_10[]" style="text-align: right;" type="text" value="{!! intval($item->chuyen_can)?? '' !!}" 
                                            data-name="food_allowance_tax" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency" data-concurrent="{{ $item->salary_concurrent }}">
                                        </td>

                                        <td style="text-align: right; vertical-align: middle;">
                                            <input name="total_salary[]" style="width: 150px;" type="text" value="{!! intval($item->total_salary) !!}" 
                                            data-name="total_salary" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                        </td>

                                        <td style="text-align: right; vertical-align: middle;">
                                            <input name="bhxh_user[]" style="width: 150px ;text-align: right;" type="text" value="{!! intval($bh['bhxh_user'])?? '' !!}"  
                                            data-name="bhxh_user" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct bhxh_user currency">
                                        </td>
                                        <td style="text-align: right; vertical-align: middle;">
                                            <input name="bhyt_user[]" style="width: 150px;text-align: right;" type="text" value="{!! intval($bh['bhyt_user'])?? '' !!}" 
                                            data-name="bhyt_user" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct bhyt_user currency">
                                        </td>
                                        <td style="text-align: right; vertical-align: middle;">
                                            <input name="union_user[]" style="width: 150px;text-align: right;" type="text" value="{!! intval($bh['union_user'])?? '' !!}" 
                                            data-name="union_user" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct union_user currency">
                                        </td>
                                        <td style="text-align: right; vertical-align: middle;">
                                            <input name="bhtn_user[]" style="width: 150px;text-align: right;" type="text" value="{!! intval($bh['bhtn_user'])?? '' !!}" 
                                            data-name="bhtn_user" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct bhtn_user currency">
                                        </td>

                                        <td style="text-align: right; vertical-align: middle;">
                                            <input name="bhxh_company[]" style="width: 150px;text-align: right;" type="text" value="{!! intval($bh['bhxh_company'])?? '' !!}" 
                                            data-name="bhxh_company" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct bhxh_company currency" >
                                        </td>

                                        <td style="text-align: right; vertical-align: middle;">
                                            <input name="bhyt_company[]" style="width: 150px;text-align: right;" type="text" value="{!! intval($bh['bhyt_company'])?? '' !!}" 
                                            data-name="bhyt_company" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct bhyt_company currency">
                                        </td>
                                        <td style="text-align: right; vertical-align: middle;">
                                            <input name="union_company[]" style="width: 150px;text-align: right;" type="text" value="{!! intval($bh['union_company'])?? '' !!}" 
                                            data-name="union_company" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct union_company currency">
                                        </td>

                                        <td style="text-align: right; vertical-align: middle;">
                                            <input name="bhtn_company[]" style="width: 150px;text-align: right;" type="text" value="{!! intval($bh['bhtn_company$'])?? '' !!}" data-name="bhtn_company" 
                                            data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct bhtn_company currency">
                                        </td>
                                        <td style="text-align: center; vertical-align: middle;">
                                            <input name="ttcn_2020[]" style="width: 150px;text-align: right;" type="text" value="0" data-name="ttcn_2020" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                        </td>

                                        <td style="text-align: right; vertical-align: middle;">
                                            <input name="deduction_not_tax[]" style="width: 150px;text-align: right;" type="text" value="{{ intval(($item->total_deduction - $item->total_deduction_tax)) }}" data-name="total_deductions" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                        </td>

                                        <td style="text-align: right; vertical-align: middle;">
                                            <input name="deduction_tax[]" style="width: 150px;text-align: right;" type="text" value="{{ intval($item->total_deduction_tax) }}" data-name="total_deductions" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                        </td>
                                    
                                        <td style="text-align: right; vertical-align: middle;">
                                            <input  name="payoff_not_tax[]" style="width: 150px;text-align: right;" type="text" value="{{ intval(($item->total_payoff - $item->total_payoff_tax)) }}" data-name="total_deductions" data-user-id="{!! $item->user_id !!}" name="" id="" class=" currency form-control ct">
                                        </td>

                                        <td style="text-align: right; vertical-align: middle;">
                                            <input name="payoff_tax[]" style="width: 150px;text-align: right;" type="text" value="{{ intval($item->total_payoff_tax) }}" data-name="total_deductions" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                        </td>
                                        <td style="text-align: right; vertical-align: middle;">
                                            <input name="income_taxes[]" style="width: 150px;text-align: right;" type="text" value="{!! intval($item->income_taxes)?? '' !!}" data-name="income_taxes" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency income_taxes">
                                        </td>
                                        <td style="text-align: right; vertical-align: middle;">
                                            <input name="ng_phu_thuoc[]" style="text-align: center;" type="text" value="{{ \App\User::countUserRelationship($item->user_id) }}" data-name="ng_phu_thuoc" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct">
                                        </td>
                                        <td style="text-align: right; vertical-align: middle;">
                                            <input name="family_allowances[]" style="text-align: right;" type="text" value="{!! intval($item->family_allowances)?? '' !!}" data-name="gia_canh" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                        </td>
                                        <td style="text-align: right; vertical-align: middle;">
                                            <input name="taxable_income[]" style="width: 150px;text-align: right;" type="text" value="{!! intval($item->taxable_income)?? '' !!}"  data-name="tntt" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                        </td>
                                        <td style="text-align: right; vertical-align: middle;">
                                            <input name="personal_income_tax[]" style="width: 150px;text-align: right;" type="text" value="{!! intval($item->personal_income_tax) ?? '' !!}" data-name="personal_income_tax" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                        </td>
                                        <td style="text-align: right; vertical-align: middle;">
                                            <input name="get_kpi[]" style="text-align: center;" type="text" value="{{ $totalWorkDepartment == 0 ? 0 : \App\Models\Payroll::getKpi($item->user_id, $item->payroll->month, $item->payroll->year) }}" data-name="kpi" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct ">
                                        </td>
                                        <td style="text-align: right; vertical-align: middle;">
                                            <input name="total_impale[]" style="text-align: right;" value="{!! intval($item->total_impale) !!}"  type="text"  data-name="kpi" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                        </td>
                                        <td  style="text-align: right; vertical-align: middle;">
                                            <input  name="total_real_salary[]" style="width: 150px;text-align: right;" type="text" value="{!! intval($item->total_real_salary)  ??  0 !!}" data-name="total_real_salary" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                        </td>
                                        
                                    </tr>
                                @endforeach
                           @endif
                           <tr>
                                <th colspan="1" class="sticky-col1" style="text-align: center">Tổng :</th>
                                <th  >
                                    {{-- <input id="text_total_day_request" class="form-control ct" style="text-align: center">  --}}
                                </th>
                                <th  >
                                    {{-- <input id="text_ca_ngay_tv" class="form-control ct" style="text-align: center">  --}}
                                </th>
                                <th  >
                                    {{-- <input id="text_ca_ngay_hd" class="form-control ct" style="text-align: center">  --}}
                                </th>
                                <th  >
                                    {{-- <input id="text_ca_dem_tv" class="form-control ct" style="text-align: center">  --}}
                                </th>
                                <th  >
                                    {{-- <input id="text_ca_dem_hd" class="form-control ct" style="text-align: center">  --}}
                                </th>
                                <th  >
                                    {{-- <input id="text_count_day_offs" class="form-control ct" style="text-align: center">  --}}
                                </th>
                                <th  >
                                    {{-- <input id="text_nghi_huong_luong" class="form-control ct" style="text-align: center">  --}}
                                </th>
                                <th  >
                                    {{-- <input id="text_ngaydinhchi" class="form-control ct" style="text-align: center">  --}}
                                </th>
                                <th  >
                                    {{-- <input id="text_nghikhongluong" class="form-control ct" style="text-align: center">  --}}
                                </th>
                                <th  >
                                    {{-- <input id="text_tong" class="form-control ct" style="text-align: center">  --}}
                                </th>
                                <th  >
                                    {{-- <input id="text_an_chinh" class="form-control ct" style="text-align: center">  --}}
                                </th>
                                <th  >
                                    {{-- <input id="text_an_phu" class="form-control ct" style="text-align: center">  --}}
                                </th>
                                <th  >
                                    <input id="text_basic_salary_tv" class="form-control ct currency basic_salary_tv ">
                                </th>
                                <th  >
                                    <input id="text_basic_salary_hd" class="form-control ct currency basic_salary_hd ">
                                </th>
                                <th  >
                                    <input id="text_salary_bh" class="form-control ct currency salary_bh ">
                                </th>
                                <th  >
                                    <input id="text_working_salary_tax" class="form-control ct currency working_salary_tax ">
                                </th>
                                <th  >
                                    <input id="text_working_salary_non_tax" class="form-control ct currency working_salary_non_tax ">
                                </th>
                                <th  >
                                    <input id="text_salary_ot_non_tax" class="form-control ct currency salary_ot_non_tax ">
                                </th>
                                <th  >
                                    <input id="text_salary_ot_tax" class="form-control ct currency salary_ot_tax ">
                                </th>
                                <th  >
                                    <input id="text_food_allowance_nonTax" class="form-control ct currency food_allowance_nonTax ">
                                </th>
                                <th  >
                                    <input id="text_food_allowance_tax" class="form-control ct currency food_allowance_tax ">
                                </th>

                                <th  >
                                    <input id="text_trocap_2" class="form-control ct currency  ">
                                </th>
                                <th  >
                                    <input id="text_trocap_3" class="form-control ct currency  ">
                                </th>
                                <th  >
                                    <input id="text_trocap_4" class="form-control ct currency  ">
                                </th>
                                <th  >
                                    <input id="text_trocap_5" class="form-control ct currency  ">
                                </th>
                                <th  >
                                    <input id="text_trocap_6" class="form-control ct currency  ">
                                </th>
                                <th  >
                                    <input id="text_trocap_7" class="form-control ct currency  ">
                                </th>
                                <th  >
                                    <input id="text_trocap_8" class="form-control ct currency  ">
                                </th>
                                <th  >
                                    <input id="text_trocap_9" class="form-control ct currency  ">
                                </th>
                                <th  >
                                    <input id="text_trocap_10" class="form-control ct currency  ">
                                </th>
            
                                <th  >
                                    <input id="text_total_salary" class="form-control ct currency  ">
                                </th>
                                <th >
                                    <input  id="text_bhxh_user" class="form-control ct currency  ">
                                </th>
                                <th >
                                    <input  id="text_bhyt_user" class="form-control ct currency  ">
                                </th>
                                <th >
                                    <input  id="text_union_user" class="form-control ct currency  ">
                                </th>
                                <th >
                                    <input  id="text_bhtn_user" class="form-control ct currency  ">
                                </th>
                                <th >
                                    <input  id="text_bhxh_company" class="form-control ct currency  ">
                                </th>
                                <th >
                                    <input  id="text_bhyt_company" class="form-control ct currency  ">
                                </th>
                                <th >
                                    <input  id="text_union_company" class="form-control ct currency  ">
                                </th>
                                <th >
                                    <input  id="text_bhtn_company" class="form-control ct currency  ">
                                </th>
                                <th >
                                    <input value="{!! intval(0) !!}" class="form-control ct currency  ">
                                </th>
                                <th >
                                    <input  id="text_deduction_not_tax" class="form-control ct currency  ">
                                </th>
                                <th >
                                    <input  id="text_deduction_tax" class="form-control ct currency  ">
                                </th>
                                <th >
                                    <input  id="text_payoff_not_tax" class="form-control ct currency  ">
                                </th>
                                <th >
                                    <input  id="text_payoff_tax" class="form-control ct currency  ">
                                </th>
                                <th >
                                    <input  id="text_income_taxes" class="form-control ct currency  ">
                                </th>
                                <th  >
                                    {{-- <input id="text_person_depend" class="form-control ct " style="text-align: center"> --}}
                                </th>
                                <th >
                                    <input  id="text_family_allowances" class="form-control ct currency  ">
                                </th>
                                <th >
                                    <input  id="text_taxable_income" class="form-control ct currency  ">
                                </th>
                                <th >
                                    <input  id="text_personal_income_tax" class="form-control ct currency  ">
                                </th>
                                <th  >
                                    {{-- <input  id="textgetKpi" class="form-control ct " style="text-align: center"> --}}
                                </th>
                                <th >
                                    <input  id="text_total_impale" class="form-control ct currency  ">
                                </th>
                                <th >
                                    <input  id="text_total_real_salary" class="form-control ct currency  ">
                                </th>

                            </tr>
                        </tbody>
                    </table>
                </div>
                
            </div>
        </div>
    </section>

    <div class="modal fade" id="check" tabindex="-1" role="diacheck" aria-labelledby="checkLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document" style="width: 900px">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #3c8dbc;">
                    <h4 class="modal-title" style="color: white;margin-left: 85px" id="logLabel">Chỉnh sửa bảo hiểm</h4>
                    <span class="text-bh"></span>
                </div>
                <div class="modal-body body-log" style="">

                    <div class="bh-html">

                    </div>
                    
                </div>
                <div class="modal-footer" style="text-align: center">
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary btn-sm btn-bh-save">Lưu lại</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="check-thue" tabindex="-1" role="diacheck" aria-labelledby="checkLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #3c8dbc;">
                    {{-- <h4 class="modal-title" style="color: white;" id="logLabel">Chỉnh sửa thu nhập chịu thuế</h4> --}}
                    <span class="text-thue"></span>
                </div>
                <div class="modal-body body-log" style="">

                    <div class="thue-html">

                    </div>
                    
                </div>
                <div class="modal-footer" style="text-align: center">
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary btn-sm btn-thue-save">Lưu lại</button>
                </div>
            </div>
        </div>
    </div>
@stop
@section('footer')
<script src="{!! asset('assets/backend/plugins/input-mask/jquery.inputmask.min.js') !!}"></script>

    <script type="text/javascript" charset="utf8"
            src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>

            

    <script>
        !function ($) {
            $(function () {
            var isNV =  @json($isNV);
            if (isNV) {
                $('#table-scroll :input').prop('readonly', true);
            }

            function getTotal(element) {
            return $(element).map(function() {
            if(isNaN(parseInt(($(this).val()).replaceAll('.','').replaceAll('.','')))){
                 return 0;
            }
             return parseInt(($(this).val()).replaceAll('.','').replaceAll('.',''));
             }).get().reduce((a, b) => parseInt(a) + parseInt(b), 0);
            }
            
          
            function getTotal1(element) {
            return $(element).map(function() {
            if(isNaN(parseFloat($(this).val()))){
                 return 0;
            }
             return parseFloat($(this).val());
             }).get().reduce((a, b) => parseFloat(a) + parseFloat(b), 0);
            }

         
             $('#textgetKpi').val(parseInt(getTotal('input[name="get_kpi[]"]')));  
             $('#text_person_depend').val(parseInt(getTotal('input[name="ng_phu_thuoc[]"]')));  

             $('#text_bhxh_user').val(parseInt(getTotal('input[name="bhxh_user[]"]')));  
             $('#text_bhyt_user').val(parseInt(getTotal('input[name="bhyt_user[]"]')));  
             $('#text_union_user').val(parseInt(getTotal('input[name="union_user[]"]')));  
             $('#text_bhtn_user').val(parseInt(getTotal('input[name="bhtn_user[]"]')));  
             $('#text_bhxh_company').val(parseInt(getTotal('input[name="bhxh_company[]"]')));  
             $('#text_bhyt_company').val(parseInt(getTotal('input[name="bhyt_company[]"]')));  
             $('#text_union_company').val(parseInt(getTotal('input[name="union_company[]"]')));  
             $('#text_bhtn_company').val(parseInt(getTotal('input[name="bhtn_company[]"]')));  
                       
             $("#text_trocap_2").val(parseInt(getTotal('input[name="trocap_2[]"]')));  
             $("#text_trocap_3").val(parseInt(getTotal('input[name="trocap_3[]"]')));  
             $("#text_trocap_4").val(parseInt(getTotal('input[name="trocap_4[]"]')));  
             $("#text_trocap_5").val(parseInt(getTotal('input[name="trocap_5[]"]')));  
             $("#text_trocap_6").val(parseInt(getTotal('input[name="trocap_6[]"]')));  
             $("#text_trocap_7").val(parseInt(getTotal('input[name="trocap_7[]"]')));  
             $("#text_trocap_8").val(parseInt(getTotal('input[name="trocap_8[]"]')));  
             $("#text_trocap_9").val(parseInt(getTotal('input[name="trocap_9[]"]')));  
             $("#text_trocap_10").val(parseInt(getTotal('input[name="trocap_10[]"]')));  
           

             $("#text_total_day_request").val(parseFloat(getTotal1('input[name="total_day_request[]"]')));  
             $("#text_ca_ngay_tv").val(parseFloat(getTotal1('input[name="ca_ngay_tv[]"]')));  
             $("#text_ca_ngay_hd").val(parseFloat(getTotal1('input[name="ca_ngay_hd[]"]')));  
             $("#text_ca_dem_tv").val(parseFloat(getTotal1('input[name="ca_dem_tv[]"]')));  
             $("#text_ca_dem_hd").val(parseFloat(getTotal1('input[name="ca_dem_hd[]"]')));  
             $("#text_count_day_offs").val(parseFloat(getTotal1('input[name="count_day_offs[]"]')));  
             $("#text_nghi_huong_luong").val(parseFloat(getTotal1('input[name="nghi_huong_luong[]"]')));  
             $("#text_ngaydinhchi").val(parseFloat(getTotal1('input[name="ngaydinhchi[]"]')));  
             $("#text_nghikhongluong").val(parseFloat(getTotal1('input[name="nghikhongluong[]"]')));  
             $("#text_tong").val(parseFloat(getTotal1('input[name="tong[]"]')));  
             $("#text_an_chinh").val(parseFloat(getTotal1('input[name="an_chinh[]"]')));  
             $("#text_an_phu").val(parseFloat(getTotal1('input[name="an_phu[]"]')));  



             $("#text_basic_salary_tv").val(parseInt(getTotal('input[name="basic_salary_tv[]"]')));  
             $("#text_basic_salary_hd").val(parseInt(getTotal('input[name="basic_salary_hd[]"]')));  
             $("#text_salary_bh").val(parseInt(getTotal('input[name="salary_bh[]"]')));  
             $("#text_working_salary_tax").val(parseInt(getTotal('input[name="working_salary_tax[]"]')));  
             $("#text_working_salary_non_tax").val(parseInt(getTotal('input[name="working_salary_non_tax[]"]')));  
             $("#text_salary_ot_non_tax").val(parseInt(getTotal('input[name="salary_ot_non_tax[]"]')));  
             $("#text_salary_ot_tax").val(parseInt(getTotal('input[name="salary_ot_tax[]"]')));  
             $("#text_food_allowance_nonTax").val(parseInt(getTotal('input[name="food_allowance_nonTax[]"]')));  
             $("#text_food_allowance_tax").val(parseInt(getTotal('input[name="food_allowance_tax[]"]')));  
             $("#text_total_salary").val(parseInt(getTotal('input[name="total_salary[]"]')));  
             $("#text_deduction_not_tax").val(parseInt(getTotal('input[name="deduction_not_tax[]"]')));  
             $("#text_deduction_tax").val(parseInt(getTotal('input[name="deduction_tax[]"]')));  
             $("#text_payoff_not_tax").val(parseInt(getTotal('input[name="payoff_not_tax[]"]')));  
             $("#text_payoff_tax").val(parseInt(getTotal('input[name="payoff_tax[]"]')));  
             $("#text_income_taxes").val(parseInt(getTotal('input[name="income_taxes[]"]')));  
             $("#text_family_allowances").val(parseInt(getTotal('input[name="family_allowances[]"]')));  
             $("#text_taxable_income").val(parseInt(getTotal('input[name="taxable_income[]"]')));  
             $("#text_personal_income_tax").val(parseInt(getTotal('input[name="personal_income_tax[]"]')));  
             $("#text_total_impale").val(parseInt(getTotal('input[name="total_impale[]"]')));  
             $("#text_total_real_salary").val(parseInt(getTotal('input[name="total_real_salary[]"]')));


             
           });
        }(window.jQuery);
    </script>
                        
    <script>
        !function ($) {
            $(function () {
                
                $(".currency").inputmask({'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'max': 999999999.99, 'removeMaskOnSubmit': true,'placeholder': "0"});
              
                $('#tablePallRolesDetail thead tr').clone(true).appendTo('#tablePallRolesDetail thead');
                $('#tablePallRolesDetail thead tr:eq(1) th').each(function (i) {
                   if (i !=0 && i !=1  && i != 13) {
                        $(this).html('<input type="text" class="search-form input-text" autocomplete="off" />');
                    } else {
                        $(this).html('');
                    }
                    $('.month_filter').datepicker({
                        format: "mm/yyyy",
                        viewMode: "months",
                        minViewMode: "months",
                        clearBtn: true,
                        autoclose: true,
                        language: 'vi'
                    });

                    $('input', this).on('keyup change', function () {
                        if (table.column(i).search() !== this.value) {
                            table
                                .column(i)
                                .search(this.value)
                                .draw();
                        }
                    });
                });

                var table = $('#tablePallRolesDetail').DataTable({
                    orderCellsTop: true,
                    fixedHeader: true,
                    pageLength: 20,
                    lengthChange: false,
                    ordering: false,
                    pagingType: "full_numbers",
                    language: {
                        "info": "Hiển thị _START_ - _END_ của _TOTAL_ kết quả",
                        "paginate": {
                            "first": "«",
                            "last": "»",
                            "next": "→",
                            "previous": "←"
                        },
                        "infoFiltered": " ( trong tổng số _MAX_ kết quả)",
                    },
                    dom: '<"top "i>rt<"bottom"flp>'

                });

            });
        }(window.jQuery);
    </script>
    <script>
        $(function() {
            $('.action').hide();
            var ids = [];
            var count = {{ count($payroll_detail) }};

            $('.check-all').on('click', function() {
                $(this).attr('checked', 'checked');
                if (this.checked == true) {
                    $('.type-check').attr('checked', 'checked');
                    ids = [{{ implode(',', array_pluck($payroll_detail, 'id')) }}];
                    $('.action').show();
                } else {
                    $('.type-check').removeAttr('checked', 'checked');
                    $('.action').hide();
                }
            });
            $('.type-check').on('click', function() {
                let id = $(this).data('id');
                if (this.checked == true) {
                    ids.push(id);
                    if (ids.length == count) $('.check-all').attr('checked', 'checked');

                } else {
                    $('.check-all').removeAttr('checked', 'checked');
                    ids = jQuery.grep(ids, function(value) {
                        return value != id;
                    });
                }
                if (ids.length > 0) $('.action').show();
                if (ids.length == 0) $('.action').hide();
            });
            
            $('.btn-approved-many').on('click', function(){
                let url = `{{ route('admin.payrolls.approved-many') }}`;
                $.ajax({
                    url: url,
                    type: "POST",
                    headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                    data: {
                        ids: ids
                    },
                    success:function(response) {
                        if (response.status == 'FAIL') {
                            toastr.error(response.message);
                        } else if (response.status == 'SUCCESS') {
                            toastr.success(response.message);
                            location.reload();
                        }
                    },
                });
            });
        });
    </script>
    <script>
        $(document).ready(function(){
            var url_href = '{{ route("admin.payrolls1.salary_user") }}';
                if (url_href == window.location) {
                    $('.hc-tab').addClass('active-tab');
                }
                
             $(".hover").on('click', function(){
                $('.hover').css("background-color", "white");
                $('.hover').find('.sticky-col').css("background-color", "white");
                $(this).find('.sticky-col').css("background-color", "#9ad0ff");
                $(this).css("background-color", "#9ad0ff");
             });
         });
    </script>

@stop