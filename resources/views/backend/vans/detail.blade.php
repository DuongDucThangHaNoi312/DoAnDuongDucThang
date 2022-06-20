@extends('backend.master')
@section('title')
    {!! trans('system.action.list') !!} Chi tiết lương khoán
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
    <link rel="stylesheet" type="text/css"
          href="{!! asset('assets/backend/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') !!}"/>
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

        .modal-header {
            background-color: #3c8dbc;
            color: white;
            text-align: center;
        }

        .modal-footer {
            text-align: center;
        }
        .select2-container--default .select2-selection--single {
            height: 28px !important;
            border-radius: 3px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 24px !important;
            font-weight: normal;
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


        tbody tr th.th-job {
            position:sticky;
            left:0;
            z-index: 102;
            background:  white
        }
        
        /* .color {
            background: #c7f0f7;
        } */

    </style>
@stop
@section('content')
    <section class="content-header">
        <h1> 
            Lương khoán 1231123
            <small>Chi tiết</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.vans.index') !!}">Lương khoán</a></li>
        </ol>
    </section>
    <section class="content overlay">
        <div class="text-center">
            <h3>{{ $salary_van->title }}</h3>
            <h4>Công ty: {{ $salary_van->company->shortened_name }}</h4>
            <h4>Tháng: {{ $salary_van->month.'/'. $salary_van->title }}/{{ $salary_van->year }} Người tạo: {{ $salary_van->user_by->fullname }}</h4>
            {{-- @if ($payroll->status == 'APPROVED')
                <h4><span class="label label-success">Đã duyệt </span> Người duyệt: {{ $payroll->userApproved->fullname . ' - ' . date('d/m/Y H:i:s', strtotime($payroll->date_approved)) }}</h4>
            @endif --}}
        </div>
        
        <div class="box">
            <div class="box-body no-padding" style="overflow-x:auto; overflow-x:auto;">
                <table class="table table-striped table-bordered" id="tablePayrolls">
                    <thead>
                    <tr>
                        <th class="color" rowspan="3" style="text-align: center; vertical-align: middle;">{!! trans('system.no.') !!}</th>
                        <th class="color" rowspan="3"  style="text-align: center; vertical-align: middle; padding: 50px" class="">Họ và tên</th>
                        <th class="color" rowspan="3"  style="text-align: center; vertical-align: middle; padding: 50px" class="">Phòng ban</th>
                        <th class="color" rowspan="3"  style="text-align: center; vertical-align: middle; padding: 20px" class="">Mã NV</th>
                        <th class="color" rowspan="3"  style="text-align: center; vertical-align: middle; padding: 50px;">Biển số xe</th>
                        <th class="color" colspan="3" style="text-align: center; vertical-align: middle;">LƯƠNG KHOÁN</th>
                        <th class="color" colspan="3" style="text-align: center; vertical-align: middle;">CP CẦU ĐƯỜNG <br> BẾN BÃI</th>
                        <th class="color" rowspan="3" style="text-align: center; vertical-align: middle;">VÉ THÁNG</th>
                        <th class="color" rowspan="3" style="text-align: center; vertical-align: middle;">PHÍ GỬI XE</th>
                        <th class="color" rowspan="3" style="text-align: center; vertical-align: middle;">TIỀN ĂN <br>XE CONTAINER</th>
                        <th class="color" rowspan="3" style="text-align: center; vertical-align: middle;">TỔNG LƯƠNG <br> KHOÁN</th>
                        <th class="color" rowspan="3" style="text-align: center; vertical-align: middle;">TỔNG VÉ <br>CẦU ĐƯỜNG BẾN BÃI</th>
                        <th class="color" rowspan="3" style="text-align: center; vertical-align: middle;">TỔNG LƯƠNG <br>KHOÁN + VÉ</th>
                        
                    </tr>
                    <tr>
                        <th class="color" style="text-align: center; vertical-align: middle;">
                            Lương khoán <br> đợt 1<br>
                            {!! $salary_van->time_1 !!}
                        </th>
                        <th class="color" style="text-align: center; vertical-align: middle;">Lương khoán <br> đợt 2<br>
                            {!! $salary_van->time_2 !!}
                        </th>
                        <th class="color" style="text-align: center; vertical-align: middle;">Lương khoán <br>đợt 3<br>
                            {!! $salary_van->time_3 !!}
                        </th>
                        <th class="color" style="text-align: center; vertical-align: middle;">CP cầu đường, <br> Bến bãi đợt 1<br>
                            {!! $salary_van->cp_1 !!}
                        </th>
                        <th class="color" style="text-align: center; vertical-align: middle;">CP cầu đường,<br> Bến bãi đợt 2<br>
                            {!! $salary_van->cp_2 !!}
                        </th>
                        <th class="color" style="text-align: center; vertical-align: middle;">CP cầu đường, <br> Bến bãi đợt 3<br>
                            {!! $salary_van->cp_3 !!}
                        </th>

                    </tr>
                    
                    </thead>
                    <tbody>
                        @if (count($salary_vans) > 0)
                            @foreach ($salary_vans as $key => $item)
                                <tr>
                                    <td style="text-align: center; vertical-align: middle;">{!! $key + 1 !!}</td>
                                    <td style="vertical-align: middle;">{!! $item->user->fullname !!}</td>
                                    <td style="vertical-align: middle;">{!! $item->department->name !!}</td>
                                    <td style="vertical-align: middle;">{!! $item->user->code !!}</td>
                                    <td style="vertical-align: middle;">{!! $item->license_plates !!}</td>
                                    <td style="vertical-align: middle;">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn($item->contractual_wages_1) !!}</span>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn($item->contractual_wages_2) !!}</span>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn($item->contractual_wages_3) !!}</span>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn($item->wharf_1) !!}</span>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn($item->wharf_2) !!}</span>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn($item->wharf_3) !!}</span>
                                    </td>

                                    <td style="vertical-align: middle;">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn($item->monthly_ticket) !!}</span>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn($item->parking_fee) !!}</span>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn($item->meal_allowance) !!}</span>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn($item->total_contractual_wages) !!}</span>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn($item->total_wharf) !!}</span>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn($item->total) !!}</span>
                                    </td>
                                </tr>
                            @endforeach
                                <tr>
                                    <td></td>
                                <th style="font-weight: 700" class="th-job">TỔNG CỘNG</tg>
                                    <td></td>
                                    <td></td>
                                    <td></td>


                                    <td style="font-weight: 700">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn(array_sum(array_column($salary_vans->toArray(), 'contractual_wages_1'))) !!}</span>
                                    </td>
                                    <td style="font-weight: 700">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn(array_sum(array_column($salary_vans->toArray(), 'contractual_wages_2'))) !!}</span>
                                    </td>
                                    <td style="font-weight: 700">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn(array_sum(array_column($salary_vans->toArray(), 'contractual_wages_3'))) !!}</span>
                                    </td>
                                    <td style="font-weight: 700">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn(array_sum(array_column($salary_vans->toArray(), 'wharf_1'))) !!}</span>
                                    </td>
                                    <td style="font-weight: 700">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn(array_sum(array_column($salary_vans->toArray(), 'wharf_2'))) !!}</span>
                                    </td>
                                    <td style="font-weight: 700">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn(array_sum(array_column($salary_vans->toArray(), 'wharf_3'))) !!}</span>
                                    </td>
                                    <td style="font-weight: 700">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn(array_sum(array_column($salary_vans->toArray(), 'monthly_ticket'))) !!}</span>
                                    </td>
                                    <td style="font-weight: 700">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn(array_sum(array_column($salary_vans->toArray(), 'parking_fee'))) !!}</span>
                                    </td>
                                    <td style="font-weight: 700">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn(array_sum(array_column($salary_vans->toArray(), 'meal_allowance'))) !!}</span>
                                    </td>
                                    <td style="font-weight: 700">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn(array_sum(array_column($salary_vans->toArray(), 'total_contractual_wages'))) !!}</span>
                                    </td>
                                    <td style="font-weight: 700">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn(array_sum(array_column($salary_vans->toArray(), 'total_wharf'))) !!}</span>
                                    </td>
                                    <td style="font-weight: 700">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn(array_sum(array_column($salary_vans->toArray(), 'total'))) !!}</span>
                                    </td>
                                </tr>
                        @endif
                    </tbody>           
                </table>
                @if (count($salary_vans) == 0)
                <div class="text-center error">
                    <span class="text-size"><i class="fas fa-search"></i> {!! trans('timekeeping.no_data') !!}</span>
                </div>
                @endif
            </div>
        </div>
    </section>
@stop
@section('footer')
    <script src="{!! asset('assets/backend/plugins/iCheck/icheck.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/select2/select2.full.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/moment/min/moment-with-locales.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/input-mask/jquery.inputmask.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.vi.min.js') !!}"></script>
   
@stop