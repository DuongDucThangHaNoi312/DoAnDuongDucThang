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
            {!! trans('salary_cont.label_table') !!}
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.salary-choose-containers.index') !!}">{!! trans('payrolls.detail') !!} {!! trans('payrolls.label') !!}</a></li>
        </ol>
    </section>
    <section class="content overlay">
        <div class="text-center">
            <h3>Danh sách tính số lượng tờ khai từng công ty {!! $departmentGroupCode[$departmentGroup] !!} </h3>
            <h3>Tháng {!! $monthYear !!}</h3>
        </div>
        <div class="box" style="overflow-x:auto;">
            <div class="box-body no-padding" style="overflow-x:auto;">
                <table class="table table-striped table-bordered" style="width: 100%" id="tablePayrolls">
                    <thead>
                        <tr>
                            <th style="text-align: center; vertical-align: middle;"> STT </th>
                            <th style="text-align: center; vertical-align: middle;"> Công ty </th>
                            <th style="text-align: center; vertical-align: middle;"> Sô tờ khai chính</th>
                            <th style="text-align: center; vertical-align: middle;"> Số tờ khai nhánh</th>
                            <th style="text-align: center; vertical-align: middle;"> Số tờ khai tự mở</th>
                            <th style="text-align: center; vertical-align: middle;"> Tổng điểm</th>
                            <th style="text-align: center; vertical-align: middle;"> Số lượng nhân viên </th>
                            <th style="text-align: center; vertical-align: middle;"> Thao tác </th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $i = 1; $totalNV = count(array_merge(...array_column($data, "NV"))); $totalPoint = array_sum(array_column($data, 'POINT')); @endphp
                        @foreach ($data as $key => $item)
                            <tr>
                               <td style="text-align: center; vertical-align: middle;"> {!! $i !!} </td>
                               <td style="text-align: center; vertical-align: middle;"> {!! $companyCode[$key] !!} </td>
                               <td style="text-align: center; vertical-align: middle;"> {!! $item['MAIN'] !!} </td>
                               <td style="text-align: center; vertical-align: middle;"> {!! $item['BRANCH'] !!} </td>
                               <td style="text-align: center; vertical-align: middle;"> {!! $item['SELF'] !!} </td>
                               <td style="text-align: center; vertical-align: middle;"> {!! round($item['POINT'], 2, PHP_ROUND_HALF_DOWN) !!} </td>
                               <td style="text-align: center; vertical-align: middle;"> {!! count($item['NV']) !!} </td>
                               <td style="border: none">
                                <div class="row">
                                    <a href="{{ route('admin.salary-declarations.detailUser') }}?month_year={!!  $monthYear !!}&department_group_id={!! $departmentGroup !!}&company_id={!! $key !!}" class="btn btn-default btn-xs">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.salary-declarations.createSalaryUser') }}?month_year={!!  $monthYear !!}&department_group_id={!! $departmentGroup !!}&company_id={!! $key !!}&total_nv={!! $totalNV !!}&total_point={!! $totalPoint !!}&point={!! round($item['POINT'], 2, PHP_ROUND_HALF_DOWN) !!}" class="btn btn-default btn-xs">
                                        <i class="fa fa-list"></i>
                                    </a>
                                </div>
                            </td>
                            </tr>
                        @php $i++; @endphp    
                        @endforeach
                    </tbody>
                    <tr>
                        <th style="text-align: center; vertical-align: middle;">Tổng:</th>
                        <th style="text-align: center; vertical-align: middle;"></th>
                        <th style="text-align: center; vertical-align: middle;">{!! array_sum(array_column($data, 'MAIN')) !!}</th>
                        <th style="text-align: center; vertical-align: middle;">{!! array_sum(array_column($data, 'BRANCH')) !!}</th>
                        <th style="text-align: center; vertical-align: middle;">{!! array_sum(array_column($data, 'SELF')) !!}</th>
                        <th style="text-align: center; vertical-align: middle;">{!! round($totalPoint, 2, PHP_ROUND_HALF_DOWN) !!}</th>
                        <th style="text-align: center; vertical-align: middle;">{!! $totalNV !!}</th>
                        <th style="text-align: center; vertical-align: middle;"></th>
                    </tr>           
                </table>
            </div>
        </div>
    </section>
@stop
@section('footer')
<script src="{!! asset('assets/backend/plugins/input-mask/jquery.inputmask.min.js') !!}"></script>
@stop