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

        /* tổng cộng */
        tbody tr th.th-job {
            position:sticky;
            left:0;
            z-index: 102;
            background:  white
        }

        /* job code */
        thead tr th.th-job {
            position:sticky;
            left:0;
            z-index: 102;
            background:  white
        }

        /* item->jobcode */
        td.td-job {
            position:sticky;
            left:0;
            z-index: 100;
            background: white
        }

        thead tr th.fixed-2{
            position: sticky;
            position: -webkit-sticky; /* Safari */
            top: 40px;
            z-index: 101;
        }
      
        /* .color {
            background: #c7f0f7;
        } */

    </style>
@stop
@section('content')
    <section class="content-header">
        <h1>
            Phân bổ chi phí lương theo xe
            <small>Chi tiết</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.vans.index') !!}">Lương khoán</a></li>
        </ol>
    </section>
    <section class="content overlay">
        <div class="text-center">
            <h3>{{ $insurance->title }}</h3>
            <h4>Tháng: {{ $insurance->month . '/' . $insurance->year }} Người tạo: {{ $insurance->user_by->fullname }} </h4>
           
        </div>
        
        <div class="box">
            <div class="box-body no-padding" style="overflow-x:auto; overflow-x:auto;">
                <table class="table table-striped table-bordered" id="tablePayrolls">
                    <thead>
                    <tr>
                        <th class="color " rowspan="3" style="text-align: center; vertical-align: middle;">{!! trans('system.no.') !!}</th>
                        <th class="color " rowspan="3"  style="text-align: center; vertical-align: middle; padding: 40px" class="">Biển số xe</th>
                        <th class="color " rowspan="3"  style="text-align: center; vertical-align: middle; padding: 20px" class="">Đơn vị sử dụng</th>
                        <th class="color th-job fixed-1" rowspan="3"  style="text-align: center; vertical-align: middle; padding: 20px" class="">Job code</th>
                        <th class="color " colspan="2"  style="text-align: center; vertical-align: middle; padding: 20px;">Số chính xác</th>
                        <th class="color " colspan="5" style="text-align: center; vertical-align: middle;">Phân bổ theo lương lái xe</th>
                        <th class="color " colspan="5" style="text-align: center; vertical-align: middle;">Phân bổ lương điều vận/bốc xếp</th>
                        
                    </tr>
                    <tr>
                        <th class="color fixed-2" style="text-align: center; vertical-align: middle;">
                            Tổng lương khoán <br> theo xe
                        </th>
                        <th class="color fixed-2" style="text-align: center; vertical-align: middle;">
                            Tổng vé cầu đường, <br> bến bãi
                        </th>

                        <th class="color fixed-2" style="text-align: center; vertical-align: middle; padding: 20px">
                            SOC INS <br> BHXH<br> 17%
                        </th>
                        <th class="color fixed-2" style="text-align: center; vertical-align: middle; padding: 20px">
                            H INS <br> BHYT<br> 3%
                        </th>
                        <th class="color fixed-2" style="text-align: center; vertical-align: middle; padding: 20px">
                            UNION <br> Công đoàn<br> 2%
                        </th>
                        <th class="color fixed-2" style="text-align: center; vertical-align: middle; padding: 20px">
                            UN EM INS <br> BHTN<br> 1%
                        </th>
                        <th class="color fixed-2" style="text-align: center; vertical-align: middle; padding: 20px">
                            LCB + PHỤ CẤP + <br> KHÁC
                        </th>

                        <th class="color fixed-2" style="text-align: center; vertical-align: middle; padding: 20px">
                            SOC INS <br> BHXH<br> 17%
                        </th>
                        <th class="color fixed-2" style="text-align: center; vertical-align: middle; padding: 20px">
                            H INS <br> BHYT<br> 3%
                        </th>
                        <th class="color fixed-2" style="text-align: center; vertical-align: middle; padding: 20px">
                            UNION <br> Công đoàn<br> 2%
                        </th>
                        <th class="color fixed-2" style="text-align: center; vertical-align: middle; padding: 20px">
                            UN EM INS <br> BHTN<br> 1%
                        </th>
                        <th class="color fixed-2" style="text-align: center; vertical-align: middle; padding: 20px">
                            TỔNG THU <br> NHẬP
                        </th>

                    </tr>
                    
                    </thead>
                    <tbody>
                        @if (count($insurance) > 0)
                            @foreach ($insurance->insurance_detail as $key => $item)
                                <tr>
                                    <td style="text-align: center; vertical-align: middle;">{!! $key + 1 !!}</td>
                                    <td style="vertical-align: middle;">{!! $item->license_plates !!}</td>
                                    <td style="vertical-align: middle;">{!! $item->company->shortened_name !!}</td>
                                    <td class="td-job" style="vertical-align: middle;">{!! $item->job_code !!}</td>
                                    <td style="vertical-align: middle;">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn($item->total_salary_vans) !!}</span>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn($item->total_wharf) !!}</span>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn($item->bhxh_drive) !!}</span>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn($item->bhyt_drive) !!}</span>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn($item->union_drive) !!}</span>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn($item->bhtn_drive) !!}</span>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn($item->basic_salary_allowance) !!}</span>
                                    </td>


                                    <td style="vertical-align: middle;">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn($item->bhxh_other) !!}</span>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn($item->bhyt_other) !!}</span>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn($item->union_other) !!}</span>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn($item->bhtn_other) !!}</span>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn($item->total_salary) !!}</span>
                                    </td>
                                </tr>
                            @endforeach
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th class="fixed-1 th-job" style="font-weight: 700">TỔNG CỘNG</td>

                                    <th style="font-weight: 700">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn(array_sum(array_column($insurance->insurance_detail->toArray(), 'total_salary_vans'))) !!}</span>
                                    </th>
                                    <th style="font-weight: 700">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn(array_sum(array_column($insurance->insurance_detail->toArray(), 'total_wharf'))) !!}</span>
                                    </th>
                                    <th style="font-weight: 700">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn(array_sum(array_column($insurance->insurance_detail->toArray(), 'bhxh_drive'))) !!}</span>
                                    </th>
                                    <th style="font-weight: 700">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn(array_sum(array_column($insurance->insurance_detail->toArray(), 'bhyt_drive'))) !!}</span>
                                    </th>
                                    <th style="font-weight: 700">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn(array_sum(array_column($insurance->insurance_detail->toArray(), 'union_drive'))) !!}</span>
                                    </th>
                                    <th style="font-weight: 700">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn(array_sum(array_column($insurance->insurance_detail->toArray(), 'bhtn_drive'))) !!}</span>
                                    </th>
                                    <th style="font-weight: 700">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn(array_sum(array_column($insurance->insurance_detail->toArray(), 'basic_salary_allowance'))) !!}</span>
                                    </th>
                                    <th style="font-weight: 700">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn(array_sum(array_column($insurance->insurance_detail->toArray(), 'bhxh_other'))) !!}</span>
                                    </th>
                                    <th style="font-weight: 700">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn(array_sum(array_column($insurance->insurance_detail->toArray(), 'bhyt_other'))) !!}</span>
                                    </th>
                                    <th style="font-weight: 700">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn(array_sum(array_column($insurance->insurance_detail->toArray(), 'union_other'))) !!}</span>
                                    </th>
                                    <th style="font-weight: 700">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn(array_sum(array_column($insurance->insurance_detail->toArray(), 'bhtn_other'))) !!}</span>
                                    </th>
                                    <th style="font-weight: 700">
                                        <span style="float: right">{!! \App\Helper\HString::currencyFormatVn(array_sum(array_column($insurance->insurance_detail->toArray(), 'total_salary'))) !!}</span>
                                    </th>
                                </tr>
                        @endif
                    </tbody>           
                </table>
                @if (count($insurance) == 0)
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