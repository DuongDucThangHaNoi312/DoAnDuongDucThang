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
            z-index: 220 !important;
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
            max-height: 500px;
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
            top: 36px;
            /*border-right: 1px solid #D2D6DE;*/
            /*border-top: 1px solid #D2D6DE;*/
        }

        thead tr th.fixed-3 {
            background: #EBEEF4;
            color: #367FA9;
            z-index: 101;
            position: -webkit-sticky;
            position: sticky;
            top: 92px;
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
    </style>
@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('payrolls.detail') !!}
            <small>{!! trans('payrolls.label') !!} {{ $payroll->month }}/{{ $payroll->year }} {{ $payroll->department->name }} - {{ $payroll->company->shortened_name }}
            
                @if ($payroll->status == 'APPROVED')
                <span class="label label-success">Đã duyệt </span> Người duyệt: {{ $payroll->userApproved->fullname . ' - ' . date('d/m/Y H:i:s', strtotime($payroll->date_approved)) }}
                @endif
            </small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.timekeeping.index') !!}">{!! trans('payrolls.detail') !!} {!! trans('payrolls.label') !!}</a></li>
        </ol>
    </section>
    <section class="content overlay">
        <div class="text-center">
            {{-- <h3>{!! trans('payrolls.label_month') !!} {{ $payroll->month }}/{{ $payroll->year }}</h3> --}}
            {{-- <h4>{{ $payroll->department->name }} - {{ $payroll->company->shortened_name }}</h4> --}}
            {{-- @if ($payroll->status == 'APPROVED')
                <h4><span class="label label-success">Đã duyệt </span> Người duyệt: {{ $payroll->userApproved->fullname . ' - ' . date('d/m/Y H:i:s', strtotime($payroll->date_approved)) }}</h4>
            @endif --}}
        </div>
        <div class="export">
            @permission('payrolls.create')
                @if ($payroll->status != 'APPROVED')
                    <button type="button" class="btn btn-primary btn-flat btn-creat" data-toggle="modal" data-target="#exampleModalCenter">
                        <span class="fas fa-sync"></span>&nbsp;Tính lại
                    </button>    
                @endif
            
                {{-- <button class="btn btn-primary btn-flat action" data-toggle="modal" data-target="#approved"><i class="fas fa-check-circle"></i> Duyệt lương</button> --}}
                
                <!-- Modal -->
                <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        {!! Form::open(['id' => 'tinh_lai', 'url' => route('admin.payrolls.store1'), 'method' => 'POST', 'target' => "_blank"]) !!}
                            <input type="hidden" name="company_id" value="{!! $payroll->company_id !!}">
                            <input type="hidden" name="department_id" value="{!! $payroll->department_id !!}">
                            <input type="hidden" name="month" value="{!! $payroll->month !!}">
                            <input type="hidden" name="year" value="{!! $payroll->year !!}">
                            <input type="hidden" name="id" value="{!! $payroll->id !!}">
                            <div class="modal-header" style="background-color: #3c8dbc; color: white; text-align: center">
                                <h4 class="modal-title" id="exampleModalLongTitle">Xác nhận</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                Bạn có muốn tính lại bảng lương 
                            </div>
                            <div class="modal-footer" style="text-align: center">
                                <button type="button" class="btn btn-danger btn-flat" data-dismiss="modal">Đóng</button>
                                <button type="button" class="btn btn-primary btn-flat btn-tinh-lai">Tính lại</button>
                            </div>
                        {!! Form::close() !!}
                        
                    </div>
                    </div>
                </div>
                <a href="{{ route('admin.payrolls.exportExcel', $payroll->id) }}" class="btn btn-success">
                    <span class="far fa-file-excel fa-fw"></span>&nbsp;{{ trans('timekeeping.export_excel') }}
                </a>
                {{-- <a href="{{ route('admin.ot.detail', $payroll->department_id) }}" class="btn btn-success" target="_blank">
                    &nbsp;Bảng làm thêm
                </a> --}}
            @endpermission
            

            @if ($payroll->status != 'APPROVED')
                @permission('payrolls.approved')
                    <button type="button" class="btn btn-primary btn-flat btn-creat" data-toggle="modal" data-target="#approved">
                        <span class="fas fa-check"></span>&nbsp;Duyệt lương
                    </button>
                    <div class="modal fade" id="approved" tabindex="-1" role="dialog" aria-labelledby="approvedLabel"  aria-hidden="true" data-backdrop="static" data-keyboard="false">
                        <div class="modal-dialog" role="document" style="text-align: left">
                            <form action="{!! route('admin.payrolls.approved', $payroll->id) !!}" method="POST">
                                @csrf
                                <div class="modal-content">
                                    <div class="modal-header" style="background-color: #3c8dbc; color: white; text-align: center">
                                        <h3 class="modal-title" id="approvedLabel">Duyệt lương tháng {{ $payroll->month.'/'.$payroll->year }} {{ $payroll->company->shortened_name }} - {{ $payroll->department->name }}</h3>
                                    </div>
                                    <div class="modal-body text-center">
                                        <h4 style="color: red">Lưu ý bảng lương đã được duyệt không thể thêm mới và tính lại</h4>

                                    </div>
                                    <div class="modal-footer" style="text-align: center">
                                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Đóng</button>
                                        <button type="submit" class="btn btn-primary btn-sm">Xác nhận</button>
                                    </div>
                                </div>
                            </form>
                            
                        </div>
                    </div>
                @endpermission
            @endif
            
           
        </div>
        <div class="modal fade" id="approved" tabindex="-1" role="dialog" aria-labelledby="approvedLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="text-align: center; background-color: #3c8dbc; color: white">
                        <h4 class="modal-title" id="approvedLabel">Xác nhận</h4>
                    </div>
                    <div class="modal-body">
                        Bạn chắc chắn duyệt lương nhân viên
                    </div>
                    <div class="modal-footer" style="text-align: center">
                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Đóng</button>
                        <button type="button" class="btn btn-approved-many btn-primary btn-approved btn-sm">Duyệt</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" style="margin-top: 10px; margin-bottom: 10px;">
            <div class="col-md-12">
                <input type="text" name="" id="" class="form-control text">
            </div>
        </div>
        <div class="box">
            <div class="box-body no-padding" style="overflow-x:auto; overflow-x:auto;">
                <div id="table-scroll" class="table-scroll">
                    <?php $cate_allowances = \App\Models\AllowanceCategory::cateAllowance() ;?>

                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr class="">
                                <?php $characters = \App\Models\Payroll::characterPayroll(); ?>
                                @foreach ($characters as $key => $item)
                                    <th  class="fixed-1 {{ $key == 'B' ? 'sticky-col1' : '' }}" style="text-align: center; vertical-align: middle;">{{ $key }}</th>
                                @endforeach
                                
                            </tr>
                            <tr>
                                <th class="fixed-2 sticky-col" rowspan="2"  style="text-align: center; vertical-align: middle;">
                                    <span class="uppercase">No.</span><br>
                                    <span>STT</span><br>
                                </th>
                                <th rowspan="2" class="sticky-col1 fixed-2" style="text-align: center; vertical-align: middle; padding: 0 100px">
                                    <span class="uppercase">full name</span><br>
                                    <span>Họ và tên</span>
                                </th>
                                
                                <th class="fixed-2" rowspan="2" style="text-align: center; vertical-align: middle;">
                                    <span class="uppercase">Code</span><br>
                                    <span>Mã NV</span>
                                </th>
                                <th rowspan="2"  style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">
                                    <span class="uppercase">FIXED WORKING DAYS </span> <br>
                                    <span>Số ngày công theo tháng</span>
                                </th>
                                <th  colspan="9"  style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">
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
                                <th rowspan="2"  style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">
                                    <span>NUMBER
                                        OF SHIFT
                                        MEAL
                                        </span><br>
                                    <span>Số bữa ăn chính</span>
                                </th>
                                <th rowspan="2"  style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">
                                    <span>NUMBER OF EXTRA MEAL
                                        </span><br>
                                    <span>Số bữa phụ</span>
                                </th>
                                {{-- <th rowspan="2"  style="text-align: center; vertical-align: middle;" class="tdbreak">
                                    <span class="uppercase">Night working day</span><br>
                                    <span>Ngày công làm việc đêm</span>
                                </th> --}}
                                <th rowspan="2"  style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">
                                    <span>SALARY PROBATION
                                       </span><br>
                                    <span>Lương thử việc</span>
                                </th>
                                <th rowspan="2"  style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">
                                    <span>BASIC 
                                        RATE
                                       </span><br>
                                    <span>Lương cơ bản</span>
                                </th>
                                <th  rowspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">
                                    <span>BASIC 
                                        RATE
                                       </span><br>
                                    <span>Lương đóng BH</span>
                                </th>
                                <th  rowspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">
                                    <span class="uppercase">Actual Work. Day</span> <br>
                                    <span>Lương làm việc thực tế</span>
                                </th>
                                <th  rowspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">
                                    <span class="uppercase">Actual night Work. Day</span><br>
                                    <span>Lương trả khi làm đêm (30%)</span>
                                </th>
                                <th  colspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">
                                    <span class="uppercase"> Actual Work Salary 	
        
                                    </span> <br>
                                    <span>Lương làm thêm</span>
                                </th>
                                <th class="fixed-2" colspan="{{ count($cate_allowances) + 2 }}" style="text-align: center; vertical-align: middle;">
                                    <span class="uppercase">allowance</span><br>
                                    <span>Phụ cấp</span>
                                </th>
                                <th rowspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">
                                    <span class="uppercase">Total income</span><br>
                                    <span class="">Tổng thu nhập</span>
                                </th>
                                <th colspan="4" style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">
                                    <span class="uppercase">DEDUCTIONS</span><br>
                                    <span class="">Khấu trừ nhân viên</span>
                                </th>
                                
                                <th colspan="4" style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">
                                    <span class="uppercase">COMPANY CONTRIBUTION</span><br>
                                    <span class="">Đóng góp của CÔNG TY</span>
                                </th>
    
                                <th rowspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">
                                    <span class="uppercase">DEDUCTION PIT FINALIZE</span><br>
                                    <span class="">Nộp theo quyết toán</span><br>
                                    <span>TNCN năm 2022</span>
                                </th>
                                <th colspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">
                                    <span class="uppercase">DEDUCTION</span><br>
                                    <span class="">Khoản giảm trừ khác</span>
                                </th>
                                <th colspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">
                                    <span class="uppercase">increase</span><br>
                                    <span class="">Khoản tăng</span>
                                </th>
    
                                <th rowspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">
                                    <span class="uppercase">Taxable
                                        Income
                                        </span><br>
                                    <span class="">Thu nhập chịu thuế</span>
                                </th>
    
                                <th rowspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">
                                    <span class="uppercase">No of dependant
                                        </span><br>
                                    <span class="">Số người phụ thuộc</span>
                                </th>
    
                                <th rowspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">
                                    <span class="uppercase">Self relief and dependant relief
                                        </span><br>
                                    <span class="">Khấu trừ gia cảnh</span>
                                </th>
    
                                <th rowspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">
                                    <span class="uppercase">Assessable Income
                                        </span><br>
                                    <span class="">Thu nhập tính thuế</span>
                                </th>
    
                                <th rowspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">
                                    <span class="uppercase">Income tax
                                        </span><br>
                                    <span class="">Thuế TNCN</span>
                                </th>
                                <th rowspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">
                                    <span class="uppercase">Điểm KPI
                                        </span><br>
                                    <span>trong tháng</span>
                                </th>
                                <th rowspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">
                                    <span class="uppercase">
                                        </span><br>
                                    <span>Các khoản điều chỉnh khác</span>
                                </th>
                                <th rowspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">
                                    <span class="uppercase">TAKE HOME PAY
                                        </span><br>
                                    <span>Tổng thực lĩnh</span>
                                </th>
                                <th rowspan="2" style="text-align: center; vertical-align: middle;" class="tdbreak fixed-2">
                                    <span>Thao tác</span>
                                </th>
    
    
                                <tr>
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-3">Số ngày thử việc</th>
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-3">Số ngày hợp đồng</th>
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-3">Số đêm thử việc</th>
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-3">Số đêm hợp đồng</th>
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-3">Ngày công tác</th>
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-3">Nghỉ hưởng lương</th>
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-3">Nghỉ đình chỉ</th>
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-3">Nghỉ không lương<br>đi muộn</th>
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-3">Tổng</th>
                                    
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-3">Miễn thuế</th>
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-3">Chịu thuế</th>
    
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-3">Phụ cấp ăn trưa, ăn ca (NON-TAX)</th>
    
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-3">Phụ cấp ăn trưa, ăn ca (TAX)</th>
    
                                    @foreach ($cate_allowances as $k => $cate)
                                        <th style="text-align: center; vertical-align: middle; " class="th-tc tdbreak fixed-3">{{ $cate ?? '' }}</th>
                                    @endforeach
                                    
                                    
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-3">
                                        <span class="uppercase">SOC INS</span><br>
                                        <span class="uppercase">BHXH</span><br>
                                        <span class="uppercase">8%</span>
                                    </th>
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-3">
                                        <span class="uppercase">H INS </span><br>
                                        <span class="uppercase">BHYT</span><br>
                                        <span class="uppercase">1.5%</span>
                                    </th>
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-3">
                                        <span class="uppercase">UNION</span><br>
                                        <span class="uppercase">Công đoàn</span><br>
                                        <span class="uppercase">1%</span>
                                    </th>
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-3">
                                        <span class="uppercase">UN EM INS</span><br>
                                        <span class="uppercase">BHTN</span><br>
                                        <span class="uppercase">1%</span>
                                    </th>
    
    
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-3">
                                        <span class="uppercase">SOC INS</span><br>
                                        <span class="uppercase">BHXH</span><br>
                                        <span class="uppercase">17%</span>
                                    </th>
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-3">
                                        <span class="uppercase">H INS </span><br>
                                        <span class="uppercase">BHYT</span><br>
                                        <span class="uppercase">3%</span>
                                    </th>
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-3">
                                        <span class="uppercase">UNION</span><br>
                                        <span class="uppercase">Công đoàn</span><br>
                                        <span class="uppercase">2%</span>
                                    </th>
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-3">
                                        <span class="uppercase">UN EM INS</span><br>
                                        <span class="uppercase">BHTN</span><br>
                                        <span class="uppercase">0%</span>
                                    </th>
                                    
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-3">Miễn thuế</th>
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-3">Chịu thuế</th>
    
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-3">Miễn thuế</th>
                                    <th style="text-align: center; vertical-align: middle;" class="tdbreak fixed-3">Chịu thuế</th>
                                    
                                </tr>
                               
                                
                            </tr>
                        </thead>
                        <tbody>
                           @if (count($payroll_detail) > 0)
                           <?php $i = 1; ?>
                            @foreach ($payroll_detail as $k => $item)
                                <?php $data = \App\Models\Payroll::userPayrollDetail($item->id); 
                                    $totalDayRequest = \App\Models\Payroll::totalDayRequest($item->user_id, $payroll->month, $payroll->year);
                                    $totalWorkDepartment = \App\Models\Payroll::totalWorkDepartment($item->user_id, $payroll->month, $payroll->year, $item->timekeepingDetail->timekeeping->department_id);
                                ?>
                                <tr class="hover" data-index="{{ $k + 1 }}">
                                    <td style="text-align: center; vertical-align: middle;">{{ $i++ }}</td>
                                    <td class="sticky-col" style="text-align: center; vertical-align: middle;">{!! $item->staff->fullname ?? '' !!}</td>
                                    <td  style="text-align: center; vertical-align: middle;">{!! $item->staff->code ?? '' !!}</td>
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
                                        <input name="count_day_offs[]" style="text-align: center;" type="text" value="{{ \App\StaffDayOff::countDayOffs($item->user_id, $payroll->month, $payroll->year, 'T', $item->timekeepingDetail->timekeeping->department_id) }}" data-name="default" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct">
                                    </td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        <input name = "nghi_huong_luong[]" style="text-align: center;" type="text" value="{{ \App\Models\Payroll::countTotalInMonthForTimeKeeping($item->user_id, $payroll->month, $payroll->year, $item->timekeepingDetail->timekeeping->department_id) }}" data-name="default" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct">
                                    </td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        <input name="ngaydinhchi[]" style="text-align: center;" type="text" value="{{ \App\StaffDayOff::countDayOffs($item->user_id, $payroll->month, $payroll->year, 'C', $item->timekeepingDetail->timekeeping->department_id) }}" data-name="default" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct">
                                    </td>
                                    <td  style="text-align: center; vertical-align: middle;">
                                        <?php 
                                            $nghi_khong_luong = \App\StaffDayOff::countDayOffs($item->user_id, $payroll->month, $payroll->year, 'S', $item->timekeepingDetail->timekeeping->department_id) + \App\StaffDayOff::countDayOffs($item->user_id, $payroll->month, $payroll->year, 'O', $item->timekeepingDetail->timekeeping->department_id);
                                        ?>
                                        <input name="nghikhongluong[]" style="text-align: center;" type="text" value="{{ $nghi_khong_luong }}" data-name="default" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct">
                                    </td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        <input name="tong[]" type="text" style="width: 70px;text-align: center;" value="{{ $totalWorkDepartment ?? '' }}" data-name="total_work_department" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct">
                                    </td>
    
                                    {{-- <td style="text-align: center; vertical-align: middle;">
                                        <input type="text" value="{{ \App\StaffDayOff::countDayOffs($item->user_id, $payroll->month, $payroll->year, 'T') }}" data-name="total_work_department" data-user-id="{!! $item->user_id !!}" class="form-control ct">
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
                                            {{-- {{$item->timekeepingDetail->timekeeping->department_id }} --}}
                                            <input name="trocap_{!! $k1 !!}[]" style="text-align: right;width:100%" type="text" value="{!! intval(str_replace(".","", (String)\App\Models\Payroll::calculateAllowance($item->user_id, $k1, $totalDayRequest, $totalWorkDepartment, $payroll->month, $payroll->year, $item->salary_concurrent,'', $payroll->department_id))) ?? '' !!}" data-name="allowance" 
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
                                        <input name="get_kpi[]" style="text-align: center;" type="text" value="{{ $totalWorkDepartment == 0 ? 0 : \App\Models\Payroll::getKpi($item->user_id, $payroll->month, $payroll->year) }}" data-name="kpi" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct ">
                                    </td>
                                    <td style="text-align: right; vertical-align: middle;">
                                        <input name="total_impale[]" style="text-align: right;" value="{!! intval($item->total_impale) !!}"  type="text"  data-name="kpi" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                    </td>
                                    <td  style="text-align: right; vertical-align: middle;">
                                        <input  name="total_real_salary[]" style="width: 150px;text-align: right;" type="text" value="{!! intval($item->total_real_salary)  ??  0 !!}" data-name="total_real_salary" data-user-id="{!! $item->user_id !!}" name="" id="" class="form-control ct currency">
                                    </td>
                                    
                                    <td>
                                        @if ($payroll->status != 'APPROVED' && $item->salary_concurrent == 0)
                                            <button data-bh="{{ route('admin.payrolls.bh', $item->id) }}" type="button" class="btn btn-xs btn-primary btn-check" data-toggle="modal" data-target="#check" data-fullname="{{ $item->staff->fullname }}" data-code="{{ $item->staff->code }}">
                                                <i class="far fa-edit"></i> Bảo hiểm
                                            </button>
                                        @endif
                                        @if ($payroll->status != 'APPROVED')
                                            <button data-thue="{{ route('admin.payrolls.thue', $item->id) }}" type="button" class="btn btn-xs btn-primary btn-check-thue" data-toggle="modal" data-target="#check-thue" data-fullname="{{ $item->staff->fullname }}" data-code="{{ $item->staff->code }}">
                                                <i class="far fa-edit"></i> Thuế
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                           @endif
                           
                           <tr>
                                    <th colspan="1" class="sticky-col" style="text-align: center"></th>
                                    <th colspan="1" class="sticky-col" style="text-align: center">Tổng :</th>
                                    <th colspan="1" class="sticky-col" style="text-align: center"></th>
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
             $(".hover").on('click', function(){
                     $('.hover').css("background-color", "white");
                     $('.hover').find('.sticky-col').css("background-color", "white");
                     $(this).find('.sticky-col').css("background-color", "#9ad0ff");
                     $(this).css("background-color", "#9ad0ff");

                //  }, function(){
                //      $(this).css("background-color", "white");
                //      $(this).find('.sticky-col').css("background-color", "white");

             });

            //  $('.onChange').on('click', function() {
            //     $('.text').val('E + G/H').change();
            //  })
            //  $('.onChange1').on('click', function() {
            //     $('.text').val('E + H').change();
            //  })

            $('.ct').on('click', function() {
                let userId = $(this).data('user-id');
                let value = $(this).val();
                let name = $(this).data('name');
                let allowance = $(this).data('allowance');
                let index = $(this).closest('tr').data('index');
                let salary_concurrent = $(this).data('concurrent');


                $.ajax({
                    type: "POST",
                    url: "{!! route('admin.payroll.ct') !!}",
                    data: {
                        'userId': userId,
                        'value': value,
                        'name': name,
                        'allowance': allowance,
                        'index': index,
                        'salary_concurrent': salary_concurrent
                    },
                    dataType: "json",
                    headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},

                    success: function (response) {
                        if (response.status == 200) {
                            $('.text').val(response.data).change();
                        }
                    }
                });
            })

            $('.ct').on('keyup', function(e) {
                if (e.which == 9) {
                    let userId = $(this).data('user-id');
                    let value = $(this).val();
                    let name = $(this).data('name');
                    let allowance = $(this).data('allowance');
                    let index = $(this).closest('tr').data('index');
                    let salary_concurrent = $(this).data('concurrent');

                    $.ajax({
                        type: "POST",
                        url: "{!! route('admin.payroll.ct') !!}",
                        data: {
                            'userId': userId,
                            'value': value,
                            'name': name,
                            'allowance': allowance,
                            'index': index,
                            'salary_concurrent': salary_concurrent
                        },
                        dataType: "json",
                        headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},

                        success: function (response) {
                            if (response.status == 200) {
                                $('.text').val(response.data).change();
                            }
                        }
                    });
                }
                
            })
         });
     </script>

    <script>
        $(document).ready(function () {

            var bhxh_user = '';
            var bhyt_user = '';
            var union_user = '';
            var bhtn_user = '';

            var bhxh_company = '';
            var bhyt_company = '';
            var union_company = '';
            var bhtn_company = '';
            var url_bh = '';

            $('.btn-check').on('click', function() {
                
                $('.fullname-code').remove();
                $('.remove').remove();

                url_bh = $(this).data('bh');
                let fullname = $(this).data('fullname');
                let code = $(this).data('code');

                 bhxh_user = $(this).closest('tr').find('td .bhxh_user').val().replace(/\D/g, '');
                 bhyt_user = $(this).closest('tr').find('td .bhyt_user').val().replace(/\D/g, '');
                 union_user = $(this).closest('tr').find('td .union_user').val().replace(/\D/g, '');
                 bhtn_user = $(this).closest('tr').find('td .bhtn_user').val().replace(/\D/g, '');


                 bhxh_company = $(this).closest('tr').find('td .bhxh_company').val().replace(/\D/g, '');
                 bhyt_company = $(this).closest('tr').find('td .bhyt_company').val().replace(/\D/g, '');
                 union_company = $(this).closest('tr').find('td .union_company').val().replace(/\D/g, '');
                 bhtn_company = $(this).closest('tr').find('td .bhtn_company').val().replace(/\D/g, '');

                let html = `
                    <h4 style="color: white; margin-left: 85px" class="fullname-code">${fullname} - ${code}</h4>
                `;

                let html_bh = `
                        <div class="remove">
                            <h4 style="margin-left: 85px">Khấu trừ nhân viên</h4>
                            <table class="table table-striped table-bordered" style="width: 700px; margin: 0 auto">
                                <tr>
                                <th style="width: 50%;">Bảo hiểm xã hội</th>
                                <td>
                                    <input style="border: none; background: none" disabled type="text" name="" class="form-control currency currency-money" value="${bhxh_user}">
                                </td>
                                </tr>
                                <tr style="width: 50%">
                                    <th>Bảo hiểm y tế</th>
                                    <td>
                                        <input style="border: none; background: none" disabled type="text" name="" class="form-control currency currency-money" value="${bhyt_user}">

                                    </td>
                                </tr>
                                <tr style="width: 50%">
                                    <th>Bảo hiểm thất nghiệp</th>
                                    <td>
                                        <input style="border: none; background: none" disabled type="text" name="" class="form-control currency currency-money" value="${bhtn_user}">

                                    </td>
                                </tr>
                                <tr style="width: 50%">
                                    <th>Công đoàn</th>
                                    <td>
                                        <input style="border: none; background: none" disabled type="text" name="" class="form-control currency currency-money" value="${union_user}">
                                    </td>
                                </tr>
                            </table>

                            <br>
                            <h4 style="margin-left: 85px">Đóng góp công ty</h4>
                            <table class="table table-striped table-bordered" style="width: 700px; margin: 0 auto">
                                <tr>
                                <th style="width: 50%;">Bảo hiểm xã hội</th>
                                <td>
                                    <input style="border: none; background: none" disabled type="text" name="" class="form-control currency currency-money" value="${bhxh_company}">

                                </td>
                                </tr>
                                <tr style="width: 50%">
                                    <th>Bảo hiểm y tế</th>
                                    <td>
                                        <input style="border: none; background: none" disabled type="text" name="" class="form-control currency currency-money" value="${bhyt_company}">
                                    </td>
                                </tr>
                                <tr style="width: 50%">
                                    <th>Bảo hiểm thất nghiệp</th>
                                    <td>
                                        <input style="border: none; background: none" disabled type="text" name="" class="form-control currency currency-money" value="${bhtn_company}">
                                    </td>
                                </tr>
                                <tr style="width: 50%">
                                    <th>Công đoàn</th>
                                    <td>
                                        <input style="border: none; background: none" disabled type="text" name="" class="form-control currency currency-money" value="${union_company}">
                                    </td>
                                </tr>
                            </table>
                            
                        </div>
                            
                        `;

                $('.text-bh').append(html);
                $('.bh-html').append(html_bh);

                $(".currency-money").inputmask({'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'max': 999999999.99, 'removeMaskOnSubmit': true,'placeholder': "0"});

            })

            $('.btn-bh-save').on('click', function () {
                $('#check').modal('hide');

                $.ajax({
                    type: "POST",
                    url: url_bh,
                    data: {
                        'bhxh_user': bhxh_user,
                        'bhyt_user': bhyt_user,
                        'union_user': union_user,
                        'bhtn_user': bhtn_user,

                        'bhxh_company': bhxh_company,
                        'bhyt_company': bhyt_company,
                        'union_company': union_company,
                        'bhtn_company': bhtn_company
                    },
                    dataType: "json",
                    headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                    success: function (response) {

                        if (response.status == 200) {
                            toastr.success(response.message);
                            location.reload();
                        } else {
                            toastr.error(response.message);
                        }
                    }
                });
            })

            $('.btn-tinh-lai').on('click', function () {
                $(this).attr('disabled', 'disabled');

                // $('#exampleModalCenter').modal('hide');
                $('#tinh_lai').submit();
            })

            var income_taxes = '';
            var url_thue = '';
            $('.btn-check-thue').on('click', function() {
                
                $('.fullname-code1').remove();
                $('.remove1').remove();

                url_thue = $(this).data('thue');
                let fullname = $(this).data('fullname');
                let code = $(this).data('code');

                income_taxes = $(this).closest('tr').find('td .income_taxes').val().replace(/\D/g, '');

                let html = `
                    <h4 style="color: white;" class="fullname-code1">${fullname} - ${code}</h4>
                `;

                let html_thue = `
                        <div class="remove1">
                            <table class="table table-striped table-bordered" style="width: 700px; margin: 0 auto">
                                <tr>
                                    <th style="width: 50%;">Thu nhập chịu thuế</th>
                                    <td>
                                        <input style="border: none; background: none" disabled type="text" name="" class="form-control currency currency-money" value="${income_taxes}">
                                    </td>
                                </tr>
                                
                            </table>
                            
                        </div>
                    `;

                $('.text-thue').append(html);
                $('.thue-html').append(html_thue);

                $(".currency-money").inputmask({'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'max': 999999999.99, 'removeMaskOnSubmit': true,'placeholder': "0"});

            })

            $('.btn-thue-save').on('click', function () {

                $.ajax({
                    type: "POST",
                    url: url_thue,
                    data: {
                        'income_taxes': income_taxes
                    },
                    dataType: "json",
                    headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                    success: function (response) {
                        $('#check-thue').modal('hide');

                        if (response.status == 200) {
                            toastr.success(response.message);
                            location.reload();
                        } else {
                            toastr.error(response.message);
                        }
                    }
                });
            })
        });

        
    </script>
@stop