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
            <h3>Chi tiết lương thưởng tở khai {!! $departmentGroupCode[$departmentGroup] !!}</h3>
            <h4>Tháng {!! $monthYear !!}</h4>
        </div>
        @php
            $totalPointTT = array_sum(array_column($total, 'POINT'));
            $totalNV = count(array_merge(...array_column($total, 'CREATED')));
            $overTarget = $rewardPoint = 0; 
            if ($totalPointTT > $totalNV*100) {
                $overTarget = round($totalPointTT - $totalNV*100, 2, PHP_ROUND_HALF_DOWN);
                $rewardPoint = ($totalPointTT - $totalNV*100)*40000;
            }
        @endphp
        <div class="box" style="overflow-x:auto;">
            <div class="box-body no-padding" style="overflow-x:auto;">
                <h4> &emsp; Số điểm thực tế : <b>{!! $totalPointTT !!}</b></h4>
                <h4> &emsp; Số điểm target :  <b>{!! $totalNV*100 !!}</b></h4>
                <h4> &emsp; Vượt target : <b>{!! $overTarget !!}</b></h4>
                <h4> &emsp; Điểm thưởng : <b>{!! \App\Helper\HString::currencyFormat($rewardPoint) !!}</b></h4>
                <table class="table table-striped table-bordered" style="width: 100%" id="tablePayrolls">
                    <thead>
                        <tr>
                            <th style="text-align: center; vertical-align: middle; width: 150px" >STT</th>
                            <th style="text-align: center; vertical-align: middle; width: 150px" >Công ty chi trả</th>
                            <th style="text-align: center; vertical-align: middle; width: 150px" >Điểm</th>
                            <th style="text-align: center; vertical-align: middle; width: 150px" >Tỉ lệ (%)</th>
                            <th style="text-align: center; vertical-align: middle; width: 150px" >Số tiền chi trả</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $c = 1; @endphp
                        @foreach ($company as  $com)
                            @php
                                $point = $total[$com]['POINT'];
                                $ratio = round(($point/ $totalPointTT) *100, 2, PHP_ROUND_HALF_DOWN);
                            @endphp
                            <tr>
                               <td style="text-align: center; vertical-align: middle;"> {!! $c !!} </td>
                               <td style="text-align: center; vertical-align: middle;"> {!! $companyCode[$com] !!} </td>
                               <td style="text-align: center; vertical-align: middle;"> {!! $point !!} </td>
                               <td style="text-align: center; vertical-align: middle;"> {!! $ratio !!} </td>
                               <td style="text-align: center; vertical-align: middle;"> {!! \App\Helper\HString::currencyFormat(($ratio/100)*$rewardPoint) !!} </td>
                       
                            </tr>
                            @php $c++; @endphp
                        @endforeach
                    </tbody>
                    <tr>
                        <th style="text-align: center; vertical-align: middle;">Tổng Số:</th>
                        <th style="text-align: center; vertical-align: middle;"></th>
                        <th style="text-align: center; vertical-align: middle;">{!! array_sum(array_column($total, 'POINT')) !!}</th>
                        <th style="text-align: center; vertical-align: middle;">100</th>
                        <th style="text-align: center; vertical-align: middle;">{!! \App\Helper\HString::currencyFormat($rewardPoint) !!}</th>
                    </tr>           
                </table>
            </div>
        </div>        
        <div class="box" style="overflow-x:auto;">
            <div class="box-body no-padding" style="overflow-x:auto;">
                <table class="table table-striped table-bordered" style="width: 100%" id="tablePayrolls">
                    <thead>
                        <tr>
                            <th style="text-align: center; vertical-align: middle;" colspan="2"></th>
                            <th style="text-align: center; vertical-align: middle; width: 150px" colspan="4">Tổng công ty</th>
                            <th style="text-align: center; vertical-align: middle; width: 100px" colspan="{!! count($company) !!}" >Chi tiết từng công ty </th>
                        </tr>
                        <tr>
                            <th style="text-align: center; vertical-align: middle;"> Loại hình tờ khai</th>
                            <th style="text-align: center; vertical-align: middle;"> Điểm</th>
                            <th style="text-align: center; vertical-align: middle;"> Tờ khai chính</th>
                            <th style="text-align: center; vertical-align: middle;"> Tờ khai nhánh</th>
                            <th style="text-align: center; vertical-align: middle;"> Tờ khai tự mở</th>
                            <th style="text-align: center; vertical-align: middle;"> Tính điểm</th>
                            @foreach (($company) as $item)
                                <th style="text-align: center; vertical-align: middle;"> {!! $companyCode[$item] !!}</th>                        
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $allTkChinh = $allTkNhanh = $allTkTuMo = $allPoint = 0;
                        @endphp
                        @foreach ($declarationWithPoints as $key1 => $item)
                        <tr>
                            @php
                                $tkChinh = !is_null($data[$key1]) ? array_sum(array_column($data[$key1], 'declaration_main')) : 0;
                                $tkNhanh = !is_null($data[$key1]) ? array_sum(array_column($data[$key1], 'declaration_branch')) : 0;
                                $tkTuMo = !is_null($data[$key1]) ? array_sum(array_column($data[$key1], 'declaration_self')) : 0;
                                $point = ($tkChinh + $tkNhanh*0.3 +  $tkTuMo*0.2)*$item;
                            @endphp
                               <td style="text-align: center; vertical-align: middle;"> {!! $key1 !!} </td>
                               <td style="text-align: center; vertical-align: middle;"> {!! $item !!} </td>
                               <td style="text-align: center; vertical-align: middle;"> {!! $tkChinh !!} </td>
                               <td style="text-align: center; vertical-align: middle;"> {!! $tkNhanh !!} </td>
                               <td style="text-align: center; vertical-align: middle;"> {!! $tkTuMo !!} </td>
                               <td style="text-align: center; vertical-align: middle;"> {!! $point !!} </td>
                                @foreach ($company as $value)
                                    @php
                                        $pointCom = (!is_null($data[$key1][$value])) ?  ($data[$key1][$value]['declaration_main']  + $data[$key1][$value]['declaration_branch']*0.3 +  $data[$key1][$value]['declaration_self']*0.2)*$item : 0;
                                    @endphp
                                     <td style="text-align: center; vertical-align: middle;"> {!! $pointCom !!}</td>                        
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                    <tr>
                        <th style="text-align: center; vertical-align: middle;">Tổng:</th>
                        <th style="text-align: center; vertical-align: middle;"></th>
                        <th style="text-align: center; vertical-align: middle;">{!! array_sum(array_column($total, 'MAIN')) !!}</th>
                        <th style="text-align: center; vertical-align: middle;">{!! array_sum(array_column($total, 'BRANCH')) !!}</th>
                        <th style="text-align: center; vertical-align: middle;">{!! array_sum(array_column($total, 'SELF')) !!}</th>
                        <th style="text-align: center; vertical-align: middle;">{!! array_sum(array_column($total, 'POINT')) !!}</th>
                        @foreach ($company as $value)
                            <th style="text-align: center; vertical-align: middle;">{!! $total[$value]['POINT'] !!}</th>                        
                        @endforeach
                    </tr>           
                </table>
            </div>
        </div>
    </section>
@stop
@section('footer')
<script src="{!! asset('assets/backend/plugins/input-mask/jquery.inputmask.min.js') !!}"></script>
@stop