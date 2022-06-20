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
@php
$user = Auth::user();
@endphp
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
            <h3>{!! trans('salary_cont.label_title') !!}  {!! trans('salary_cont.label_month') !!} {!! $salaryChooseCont->month !!} {!! trans('salary_cont.label_year') !!} {!! $salaryChooseCont->year !!}</h3>
            <h4>Công ty: {!! $salaryChooseCont->company->shortened_name !!}</h4>
            <h4>Phòng ban: {!! $salaryChooseCont->deparment->name !!}</h4>
        </div>
        <div>
            @if (($user->hasRole('TGD') || $user->hasRole('system') || $user->hasRole('TP') || in_array($user->qualification_id, \App\Defines\User::KT)) && (is_null($salaryChooseCont->kt_approved_by)))
                <button type="button" class="btn btn-primary btn-flat btn-creat" data-toggle="modal" data-target="#exampleModalCenter">
                    <span class="fas fa-sync"></span>&nbsp;Tính lại
                </button>   
            @endif    
            <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    {!! Form::open(['id' => 'tinh_lai', 'url' => route('admin.salary-choose-containers.restart'), 'method' => 'POST']) !!}
                        <input type="hidden" name="company_id" value="{!! $salaryChooseCont->company_id !!}">
                        <input type="hidden" name="department_id" value="{!! $salaryChooseCont->department_id !!}">
                        <input type="hidden" name="month" value="{!! $salaryChooseCont->month !!}">
                        <input type="hidden" name="year" value="{!! $salaryChooseCont->year !!}">
                        <div class="modal-header" style="background-color: #3c8dbc; color: white; text-align: center">
                            <h4 class="modal-title" id="exampleModalLongTitle">Xác nhận</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            Bạn có muốn tính lại khoán chọn vỏ
                        </div>
                        <div class="modal-footer" style="text-align: center">
                            <button type="button" class="btn btn-danger btn-flat" data-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-primary btn-flat btn-tinh-lai">Tính lại</button>
                        </div>
                    {!! Form::close() !!}
                    
                </div>
                </div>
            </div>

            @if (($user->hasRole('TGD') && is_null($salaryChooseCont->kt_approved_by)) || ($user->hasRole('system') && is_null($salaryChooseCont->kt_approved_by)) || ($user->hasRole('TP') && is_null($salaryChooseCont->kt_approved_by) && is_null($salaryChooseCont->tp_approved_by)) || (in_array($user->qualification_id, \App\Defines\User::KT) && is_null($salaryChooseCont->tp_approved_by)))
                <button type="button" class="btn btn-primary btn-flat btn-creat" data-toggle="modal" data-target="#approved">
                    <span class="fas fa-check"></span>&nbsp;Duyệt lương
                </button>
            @endif    
            <div class="modal fade" id="approved" tabindex="-1" role="dialog" aria-labelledby="approvedLabel"  aria-hidden="true" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog" role="document" style="text-align: left">
                    <form action="{!! route('admin.salary-choose-containers.approved') !!}" method="POST">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header" style="background-color: #3c8dbc; color: white; text-align: center">
                                <h4 class="modal-title" id="approvedLabel">Duyệt lương tháng {!! $salaryChooseCont->month !!} năm {!! $salaryChooseCont->year !!} </h4> 
                                <h4>Công ty: {!! $salaryChooseCont->company->shortened_name !!}</h4>
                                <h4>Phòng ban: {!! $salaryChooseCont->deparment->name !!}</h4>
                            </div>
                            <div class="modal-body text-center">
                                <h4 style="color: red">Lưu ý bảng lương đã được duyệt không thể thêm mới và tính lại</h4>
                            </div>
                            <input type="hidden" name="month" value="{!! $salaryChooseCont->month !!}">
                            <input type="hidden" name="year" value="{!! $salaryChooseCont->year !!}">
                            <input type="hidden" name="company" value="{!! $salaryChooseCont->company->id !!}">
                            <input type="hidden" name="department" value="{!! $salaryChooseCont->deparment->id !!}">
                            <div class="modal-footer" style="text-align: center">
                                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Đóng</button>
                                <button type="submit" class="btn btn-primary btn-sm">Xác nhận</button>
                            </div>
                        </div>
                    </form>
                    
                </div>
            </div>
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
                                    <span>Họ và tên</span>
                                </th>
                                <th style="text-align: center; vertical-align: middle;">
                                    <span>Mã NV</span>
                                </th>
                                <th style="text-align: center; vertical-align: middle;">
                                    <span>Lương Khoán</span>
                                </th>
                                <th  style="text-align: center; vertical-align: middle;">
                                    <span class="">Thao tác</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($salaryChooseContDetails) > 0)
                            <?php $i = 1; $totalMoney = 0 ?>
                                @foreach ($salaryChooseContDetails as $key  => $salaryChooseContDetail)
                                @php
                                    $money = intval(array_sum(array_column($salaryChooseContDetail, 'money')));
                                    $salaryChooseContDetailUse = $salaryChooseContDetail['0'] ;
                                @endphp
                                    <tr >
                                        <td style="text-align: center; vertical-align: middle;">{!! $i !!}</td>
                                        <td class="sticky-col" style="text-align: center; vertical-align: middle;">{!! $salaryChooseContDetailUse['user']['fullname'] !!}</td>
                                        <td class="sticky-col" style="text-align: center; vertical-align: middle;">{!! $salaryChooseContDetailUse['user']['code'] !!}</td>
                                        <td class="sticky-col" style="text-align: right; vertical-align: middle;">{!! \App\Helper\HString::currencyFormatVn($money) !!}</td>
                                        <td>
                                            <a href="{{ route('admin.salary-choose-containers.detailDep') }}?user_id={!! $salaryChooseContDetailUse['user_id'] !!}&department_id={!! $salaryChooseContDetailUse['department_id'] !!}&company_id={!! $salaryChooseContDetailUse['company_id'] !!}&month={!! $salaryChooseCont->month !!}&year={!! $salaryChooseCont->year !!}" class="btn btn-info btn-xs">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            {{-- @if (is_null($salaryChooseContDetailUse['kt_approved_by']))
                                                <a href="route"  class="btn-confirm-del btn btn-default btn-xs">
                                                    <i class="text-danger glyphicon glyphicon-remove"></i>
                                                </a>
                                            @endif --}}
                                        </td>
                                    </tr>
                                    @php
                                        $i ++;
                                        $totalMoney += $money;
                                    @endphp
                                @endforeach
                                <tr>
                                    <th class="sticky-col" style="text-align: center"></th>
                                    <th class="sticky-col" style="text-align: center">Tổng :</th>
                                    <th class="sticky-col" style="text-align: center"></th>
                                    <th class="sticky-col" style="text-align: right">{!! \App\Helper\HString::currencyFormatVn($totalMoney) !!}</th>
                                    <th class="sticky-col" style="text-align: center"></th>
                                </tr>
                            @endif
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