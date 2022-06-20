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
    <div class="text-center">
        <h3> Chi tiết số lượng tờ khai {!! $departmentGroupCode[$departmentGroup] !!} - {!! $companyCode[$company] !!} </h3>
        <h4>Tháng: {!! $monthYear !!}</h4>
    </div>
    <section class="content overlay">
        <div class="text-center">
           
        </div>
        <div class="text-center">
           
        </div>
        <div class="text-center">
           
        </div>
        <div class="box">
            <div class="box-body no-padding" style="overflow-x:auto; overflow-x:auto;">
                <div id="table-scroll" class="table-scroll">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th style="text-align: center; vertical-align: middle;">
                                    <span>STT</span><br>
                                </th>
                                <th style="text-align: center; vertical-align: middle; padding: 0 100px">
                                    <span>Loại hình tờ khai</span>
                                </th>
                                <th style="text-align: center; vertical-align: middle;">
                                    <span>Thang điểm</span>
                                </th>
                                <th style="text-align: center; vertical-align: middle;">
                                    <span>Số tờ khai chính</span>
                                </th>
                                <th  style="text-align: center; vertical-align: middle;">
                                    <span class="">Số tờ khai nhánh</span>
                                </th>
                                <th  style="text-align: center; vertical-align: middle;">
                                    <span class="">Số tờ khai tự mở</span>
                                </th>
                                <th  style="text-align: center; vertical-align: middle;">
                                    <span class="">Tính điểm</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                                @foreach ($declarationWithPoints as $name  => $point)
                                    <tr >
                                        <td style="text-align: center; vertical-align: middle;">{!! $i !!}</td>
                                        <td style="text-align: center; vertical-align: middle;">{!! $name !!}</td>
                                        <td style="text-align: center; vertical-align: middle;">{!! $point !!}</td>
                                        <td class="sticky-col" style="text-align: center; vertical-align: middle;">{!! is_null($data[$name]) ? 0 : $data[$name]['MAIN'] !!}</td>
                                        <td class="sticky-col" style="text-align: center; vertical-align: middle;">{!! is_null($data[$name]) ? 0 : $data[$name]['BRANCH'] !!}</td>
                                        <td class="sticky-col" style="text-align: center; vertical-align: middle;">{!! is_null($data[$name]) ? 0 : $data[$name]['SELF'] !!}</td>
                                        <td style="text-align: center; vertical-align: middle;">{!! is_null($data[$name]) ? 0 : $data[$name]['POINT'] !!}</td>
                                    </tr>
                                    @php
                                        $i ++;
                                    @endphp
                                @endforeach
                                {{-- @dd(array_sum(array_column($data, 'MAIN'))) --}}
                                <tr>
                                    <th class="sticky-col" style="text-align: center"></th>
                                    <th class="sticky-col" style="text-align: center"></th>
                                    <th class="sticky-col" style="text-align: center">Tổng :</th>
                                    <th class="sticky-col" style="text-align: center">{!! array_sum(array_column($data, 'MAIN')) !!}</th>
                                    <th class="sticky-col" style="text-align: center">{!! array_sum(array_column($data, 'BRANCH')) !!}</th>
                                    <th class="sticky-col" style="text-align: center">{!! array_sum(array_column($data, 'SELF')) !!}</th>
                                    <th class="sticky-col" style="text-align: center">{!! array_sum(array_column($data, 'POINT')) !!}</th>
                                    <th class="sticky-col" style="text-align: center"></th>
                                </tr>
                        </tbody>
                    </table>
                </div>
                
            </div>
        </div>
    </section>
@stop
@section('footer')
<script src="{!! asset('assets/backend/plugins/input-mask/jquery.inputmask.min.js') !!}"></script>
@stop