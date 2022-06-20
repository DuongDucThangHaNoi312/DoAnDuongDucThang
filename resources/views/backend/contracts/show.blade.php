@extends('backend.master')
@section('title')
    {!! trans('system.action.detail') !!} - {!! trans('contracts.label') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}" />
    <link rel="stylesheet" type="text/css"
          href="{!! asset('assets/backend/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>

    <style>
        .contract-content .row {
            margin: 15px -15px;
        }
        .contract-content label {
            text-align: right;
        }
        label {
            margin-top: 5px;
        }
        td, th {
            text-align: center;
            vertical-align: middle;
        }
        div.col-appendix-allowance label {
            /*margin-right: -40px;*/
        }
        .setBorderColor {
            border-color: #dd4b39 !important;
        }
        button:focus {
            border: none !important;
            outline: none !important;
        }
        #concurrent-contract-form table {
            border-collapse: collapse;
            border-spacing: 0;
            border: 1px solid #ddd;
        }
        #concurrent-contract-form thead tr th {
            white-space: nowrap;
            text-overflow: clip;
        }
        #concurrent-contract-form table thead th:not(:first-child):not(:last-child) {
            min-width: 150px;
        }
    </style>
@stop

@section('content')
    <section class="content-header">
        <h1>
            {!! trans('contracts.label') !!}
            <small>{!! trans('system.action.detail') !!}</small>
            @if ($contract->status == 0)
                @if($contract->check_valid == \App\Defines\Contract::NOT_YET_VALID)
                    <span class="label label-default">Chưa áp dụng</span>
                @else
                    <span class="label label-danger">{!! trans('contracts.type_status.' . $contract->type_status) !!}</span>
                @endif
            @else
                @if($contract->check_valid == \App\Defines\Contract::NOT_VALID)
                    <span class="label label-danger">{!! trans('system.status.notvalid') !!}</span>
                @elseif($contract->check_valid == \App\Defines\Contract::NOT_YET_VALID)
                    <span class="label label-default">{!! trans('system.status.notyetvalid') !!}</span>
                @else
                    <span class="label label-success">{!! trans('contracts.type_status.' . $contract->type_status) !!}</span>
                @endif
            @endif
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.contracts.index') !!}">{!! trans('contracts.label') !!}</a></li>
        </ol>
    </section>
    <div class="container col-md-12 contract-content">
        <div class="row">
            <label class="col col-md-2">{!! trans('contracts.staff_id') !!}</label>
            <div class="col col-md-4">
                {!! Form::select('user_id', [$contract->user_id => $contract->user->code . ' - ' .$contract->user->fullname], old('user_id', $contract->user_id), ['id' => 'userSelect', 'class' => 'form-control select2 staffSelect', 'required', 'disabled']) !!}
            </div>
            <label class="col col-md-2">{!! trans('contracts.company_id') !!}</label>
            <div class="col col-md-4">
                {!! Form::select('company_id', \App\Helpers\GetOption::getCompaniesForOption(), old('company_id', $contract->company_id), ['class' => 'form-control select2 companySelect', 'required', 'disabled']) !!}
            </div>
        </div>
        <div class="row">
            <label class="col col-md-2">{!! trans('contracts.code') !!}</label>
            <div class="col col-md-4">
                {!! Form::text('code', old('code', $contract->code), ['class' => 'form-control', 'maxlength' => 50, 'required', 'disabled']) !!}
            </div>
            <label class="col col-md-2">{!! trans('contracts.department_id') !!}</label>
            <div class="col col-md-4">
                {!! Form::select('department_id', $departmentOption, old('department_id', $contract->department_id), ['id' => 'departmentSelect', 'class' => 'form-control select2', 'required', 'disabled']) !!}
            </div>
        </div>
        <div class="row">
            <label class="col col-md-2">{!! trans('contracts.position_id') !!}</label>
            <div class="col col-md-4">
                {!! Form::select('position_id', \App\Helpers\GetOption::getStaffPositionsForOption(), old('position_id', $contract->position_id), ['class' => 'form-control select2 positionSelect', 'required', 'disabled']) !!}
            </div>
            <label class="col col-md-2">{!! trans('contracts.title_id') !!}</label>
            <div class="col col-md-4">
                {!! Form::select('qualification_id', \App\Helpers\GetOption::getStaffTitlesForOption(), old('qualification_id', $contract->qualification_id), ['class' => 'form-control select2', 'required', 'disabled']) !!}
            </div>
        </div>
        <div class="row">
            <label class="col col-md-2">{!! trans('contracts.desc_qualification') !!}</label>
            <div class="col-md-4">
                {!! Form::textarea('desc_qualification', old('desc_qualification', $contract->desc_qualification), ['class' => 'form-control desc_qualification', 'rows' => 1, 'disabled']) !!}
            </div>
            <label class="col col-md-2">{!! trans('contracts.is_main') !!}</label>
            <div class="col col-md-4">
                {!! Form::select('is_main', ['' => trans('system.dropdown_choice')] + \App\Defines\Staff::getStatusForOptionContract(), old('is_main', $contract->is_main), ['class' => 'form-control select2 is-main', 'required', 'disabled' ]) !!}
            </div>
        </div>
        <div class="row">

            <label class="col col-md-2">{!! trans('contracts.type') !!}</label>
            <div class="col col-md-4">
                {!! Form::select('type', ['' => trans('system.dropdown_choice')] + App\Defines\Contract::getTypesForOption(), old('type', $contract->type), ['class' => 'form-control select2 type', 'disabled']) !!}
            </div>
            <label class="col col-md-2">{!! trans('contracts.valid_from') !!}</label>
            <div class="col col-md-4">
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    {!! Form::text('valid_from', old('valid_from', $contract->valid_from->format('d/m/Y')), ['class' => 'form-control datepicker datepicker-from', 'required', 'autocomplete' => 'off', 'disabled']) !!}
                </div>
            </div>
        </div>
        <div class="row">
            <label class="col col-md-2">{!! trans('contracts.basic_salary') !!}</label>
            <div class="col col-md-4">
                <div class="input-group" style="display: inline-flex; width: 100%">
                    <div class="div_select_currency" @if(!$contract->currency_code) style="display: none" @endif>
                        {!! Form::select('currency_code', \App\Defines\Contract::getCurrencyOptions(), old('currency_code', $contract->currency_code ?? \App\Defines\Contract::VND), ['class' => 'form-control select2 currency_code', 'disabled']) !!}
                    </div>
                    <div style="width: 100%">
                        {!! Form::text('basic_salary', old('basic_salary', $contract->basic_salary), ["class" => "form-control currency", 'autocomplete' => 'off', 'disabled']) !!}
                    </div>
                </div>
            </div>
            <label class="col col-md-2">{!! trans('contracts.valid_to') !!}</label>
            <div class="col col-md-4">
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    {!! Form::text('valid_to', old('valid_to', $contract->valid_to ? $contract->valid_to->format('d/m/Y') : null), ['readonly'=>true, 'class' => 'form-control datepicker datepicker-to', 'autocomplete' => 'off', 'disabled']) !!}
                </div>
            </div>
        </div>
        <div class="row">
            <label class="col col-md-2" style="margin-top: 2px">Tệp đính kèm</label>
            <div class="col col-md-4">
                {!! Form::file('file[]', ['id' => 'files', 'multiple' => true, 'disabled' => true]) !!}
            </div>
        </div>
        <div class="row">
            <div class="col-md-offset-2 col-md-10 file-show">
                @foreach($contract->contractFiles as $file)
                    <li class="pip">
                        <span>{!! $file->name !!}</span>
{{--                        <span class="btn btn-default btn-xs remove" title="Xóa"><i class="text-danger fa fa-times"></i></span>--}}
                    </li>
                @endforeach
            </div>
        </div>
    </div>

    <div class="row">
        <label class="col col-md-offset-3 col-md-2">{!! trans('system.status.label') !!}</label>
        <div class="col col-md-2">
            {!! Form::select('type_status', App\Defines\Contract::getTypeStatusForOption(), old('type_status', $contract->type_status), ['class' => 'form-control select2 type_status', 'disabled']) !!}
        </div>
    </div>
    <div class="additional-date">
        <div class="row staff-submit-date-row">
            <label class="col col-md-offset-3 col-md-2">{!! trans('contracts.staff_submit_date') !!}</label>
            <div class="col col-md-2">
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    {!! Form::text('staff_submit_date', old('staff_submit_date', $contract->staff_submit_date ? date('d/m/Y', strtotime($contract->staff_submit_date)) : null), ['class' => 'form-control datepicker staff-submit-date', 'autocomplete' => 'off', 'disabled']) !!}
                </div>
            </div>
        </div>
        <div class="row set-notvalid-date-row">
            <label class="col col-md-offset-3 col-md-2">{!! trans('contracts.notvalid_date') !!}</label>
            <div class="col col-md-2">
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    {!! Form::text('set_notvalid_date', old('set_notvalid_date', $contract->set_notvalid_on ? date('d/m/Y', strtotime($contract->set_notvalid_on)) : null), ['class' => 'form-control datepicker set-notvalid-date', 'autocomplete' => 'off', 'disabled']) !!}
                </div>
            </div>
        </div>
        <div class="row report-valid-row">
            <label class="col col-md-offset-3 col-md-2">{!! trans('contracts.report_valid') !!}</label>
            <div class="col col-md-2">
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    {!! Form::text('report_valid', old('report_valid', $contract->report_valid ? date('d/m/Y', strtotime($contract->report_valid)) : null), ['class' => 'form-control datepicker report-valid', 'autocomplete' => 'off', 'disabled']) !!}
                </div>
            </div>
        </div>
    </div>

    {{--Contract Allowance--}}
    <div class="row" style="margin: 0">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-header with-border">
                    <i class="fas fa-money-bill-alt"></i>
                    <h3 class="box-title">{!! trans('contracts.table_allowances') !!}</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered expense">
                                    <?php $allowance_cat = old('allowance_cat', $allowancesOptionIdCurrent); $totalAmount = $totalAdvanced = 0; $totalAllowance = 0; ?>
                                    @if (count($allowance_cat))
                                        <thead style="background: #3C8DBC;color: white;">
                                        <tr>
                                            <th style="text-align: center; vertical-align: middle; ">{!! trans('system.no.') !!}</th>
                                            <th style="text-align: center; vertical-align: middle; min-width: 270px;">{!! trans('contracts.allowance_cat') !!}</th>
                                            <th style="text-align: center; vertical-align: middle; min-width: 270px;">{!! trans('contracts.allowance_cost') !!}</th>
                                            <th style="text-align: center; vertical-align: middle; min-width: 270px;">{!! trans('system.desc') !!}</th>
                                            <th style="text-align: center; vertical-align: middle; min-width: 70px;">{!! trans('system.action.label') !!}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $allowances = $contract->allowances;
                                        $desc = $allowances->pluck('desc');
                                        $allowance_cost = old('allowance_cost', $allowances->pluck('expense'));
                                        $totalAmount = array_sum($allowance_cost);
                                        ?>
                                        @for ($i = 0; $i < count($allowance_cat); $i++)
                                            <tr>
                                                <td style="text-align: center; vertical-align: middle;">{!! $i+1 !!}</td>
                                                <td >
                                                    {!! Form::select("allowance_cat[$i]", ['' => trans('system.dropdown_choice')] + $allowancesOption, old("allowance_cat[$i]", $allowance_cat[$i]), ['class' => 'form-control select2', 'disabled' ]) !!}
                                                </td>

                                                <td style="text-align: center; vertical-align: middle;">
                                                    {!! Form::text("allowance_cost[$i]", old("allowance_cost[$i]", $allowance_cost[$i]), ['class' => 'form-control currency', 'disabled' ]) !!}
                                                    <?php $totalAllowance += $allowance_cost[$i]; ?>
                                                </td>
                                                <td style="vertical-align: middle;">
                                                    {!! Form::text("desc[$i]", old("desc[$i]", $desc[$i]), ['class' => 'form-control currency', 'disabled']) !!}
                                                </td>
                                                <td style="text-align: center; vertical-align: middle;">
                                                    @if ($i > 0)
                                                        <a href="javascript:void(0);" class="btn btn-xs btn-default remove-expense" disabled>
                                                            <i class="text-danger fa fa-minus"></i>
                                                        </a>
                                                    @else
                                                        <a href="javascript:void(0);" class="btn btn-xs btn-default add-expense" disabled>
                                                            <i class="text-success fa fa-plus"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endfor
                                        @else
                                            <tr>Hợp đồng chưa có nội dung phụ cấp</tr>
                                        @endif
                                        </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <table class="table table-total" style="width: 40%">
                        <tr style="font-size: 17px;">
                            <th style="width: 5%"></th>
                            <td style="text-align: left; font-weight: 500;">Tổng tiền phụ cấp: </td> &nbsp;&nbsp;&nbsp;
                            <th class="total-allowance-cost text-right">{!! (!$contract->currency_code || $contract->currency_code == \App\Defines\Contract::VND) ? (\App\Helper\HString::currencyFormat($totalAllowance) ?? 0) : (\App\Helper\HString::decimalFormat($totalAllowance) ?? 0) !!}</th>
                        </tr>
                        <tr style="font-size: 17px;">
                            <th style="width: 5%"></th>
                            <td style="text-align: left; font-weight: 500;">Tổng tiền lương + phụ cấp:&nbsp;&nbsp;&nbsp; </td>
                            <th class="total-amount text-right">{!! (!$contract->currency_code || $contract->currency_code == \App\Defines\Contract::VND) ? (\App\Helper\HString::currencyFormat($contract->basic_salary+$totalAllowance) ?? 0) : (\App\Helper\HString::decimalFormat($contract->basic_salary+$totalAllowance) ?? 0) !!}</th>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{--Appendix--}}
    <div class="row"  style="margin: 0">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-header with-border">
                    <i class="fas fa-money-bill-alt" style="margin-right: 5px"></i>
                    <h3 class="box-title">Các phụ lục đính kèm</h3>
                </div>
                <div class="box-body">
                    <div class="row" id="row-appendix">

                        {{--Appendix Allowance--}}
                        <div class="col-md-12 col-appendix-allowance" style="margin: 10px 0;">
                            <div class="" >
                                <span style="font-size: 20px; font-weight: 600;"> {!! trans('appendixes.list_appendix_allowance') !!}</span>
                                @permission('contracts.update')
                                @if ($contract->check_valid != 1)
                                    <span class="btn btn-sm btn-primary add-appendix-allowance" style="float: right;">
                                    <i class="glyphicon glyphicon-plus" style="margin-right: 3px"></i>
                                    {!! trans('appendixes.add_appendix') !!}
                                </span>
                                @endif
                                @endpermission
                            </div>
                            @if (count($appendixAllowances))
                                @foreach ($appendixAllowances as $key => $value)
                                    {!! Form::open(['url' => route('admin.appendix-allowances.ajaxUpdate'), 'role' => 'form', 'id' => 'update-appendix-allowance-form-' . $key]) !!}
                                    <?php $j = $loop->index ?>
                                    <hr width = "99%" style="border-top: 1px dashed #f81111;">
                                    <?php $today = \Carbon\Carbon::today();?>
                                    <div style=" margin-bottom: 10px">
                                        <span style="font-size: 26px;">
                                            @if ($contract->status == 0)
                                                <label class="label label-danger">
                                                    {!! trans('system.status.noactive') !!}
                                                </label>
                                            @else
                                                @if(strtotime($today) > strtotime($value[0]['valid_to']))
                                                    <label class="label label-default">
                                                    {!! trans('system.status.notvalid') !!}
                                                </label>
                                                @elseif (strtotime($today) < strtotime($value[0]['valid_from']))
                                                    <label class="label label-default">
                                                    {!! trans('system.status.notyetvalid') !!}
                                                </label>
                                                @else
                                                    <label class="label label-success">
                                                    {!! trans('system.status.valid') !!}
                                                </label>
                                                @endif
                                            @endif
                                        </span>
                                        @permission('contracts.update')
                                        <span style="float: right">
                                            @if ($contract->check_valid != \App\Defines\Contract::NOT_VALID)
                                                @if (strtotime($today) < strtotime($value[0]['valid_from']))
                                                    <button class="btn btn-warning btn-xs edit-appendix-allowance" data-id="{{$key}}">
                                                        <i class="glyphicon glyphicon-edit" style="padding-right: 3px"></i>
                                                        Sửa
                                                    </button>
                                                @endif
                                                @if (strtotime($today) > strtotime($value[0]['valid_to']) || strtotime($today) < strtotime($value[0]['valid_from']))
                                                    <button class="btn btn-danger btn-xs delete-appendix-allowance" data-id="{{$key}}">
                                                    <i class="glyphicon glyphicon-remove" style="padding-right: 3px"></i>
                                                    Xóa
                                                    </button>
                                                @endif
                                            @endif
                                            <a href="{!! route('admin.contracts.export-appendix', ['contract' => $contract->id,'code' => $key]) !!}"
                                               class="btn btn-xs btn-success"
                                               data-toggle="tooltip" data-placement="top" title="Tải xuống bản word"
                                               style="">
                                                <i class="glyphicon glyphicon-download-alt"></i>
                                                Word
                                            </a>
                                        </span>
                                        @endpermission
                                    </div>

                                    <div class="row" style="margin-bottom: 10px">
                                        <label class="col col-md-2 text-center">{!! trans('appendixes.code') !!}</label>
                                        <div class="col-md-4">
                                            {!! Form::text("code[$j]", old("code[$j]", $value[0]['code_global']), ['info' => trans('appendixes.code'), "class" => "form-control code_global", 'disabled']) !!}
                                        </div>
                                        <label class="col col-md-2">{!! trans('contracts.basic_salary') !!}</label>
                                        <div class="col-md-4">
                                            {!! Form::text("salary[$j]", old("salary[$j]", $value[0]['salary']), ['info' => trans('contracts.basic_salary'), "class" => "form-control currency salary_global", 'disabled']) !!}
                                        </div>
                                        </div>
                                    </div>
                                    <div class="row" style="margin-bottom: 10px">
                                        <label class="col col-md-2">{!! trans('contracts.valid_from') !!}</label>
                                        <div class="col col-md-4">
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </div>
                                                {!! Form::text("valid_from[$j]", old("valid_from[$j]", $value[0]['valid_from']->format('d/m/Y')), ['info' => trans('contracts.valid_from'), 'class' => 'form-control datepicker datepicker-from-appendix', 'autocomplete' => 'off', 'disabled']) !!}
                                            </div>
                                        </div>
                                        <label class="col col-md-2">{!! trans('contracts.valid_to') !!}</label>
                                        <div class="col col-md-4">
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </div>
                                                {!! Form::text("valid_to[$j]", old("valid_to[$j]",  $value[0]['valid_to']->format('d/m/Y')), ['info' => trans('contracts.valid_from'), 'class' => 'form-control datepicker datepicker-to-appendix', 'autocomplete' => 'off', 'disabled']) !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered appendix-allowance-{{$j}}">
                                            <thead style="background: #3C8DBC;color: white;">
                                            <tr>
                                                <th style="text-align: center; vertical-align: middle; ">{!! trans('system.no.') !!}</th>
                                                <th style="text-align: center; vertical-align: middle; min-width: 270px;">{!! trans('contracts.allowance_cat') !!}</th>
                                                <th style="text-align: center; vertical-align: middle; min-width: 270px;">{!! trans('contracts.allowance_cost') !!}</th>
                                                <th style="text-align: center; vertical-align: middle; min-width: 270px;">{!! trans('system.desc') !!}</th>
                                                <th style="text-align: center; vertical-align: middle; min-width: 70px;">{!! trans('system.action.label') !!}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $appendix_allowance_cat[$j] = old('appendix_allowance_cat', $value->pluck('allowance_id')->toArray()); ?>
                                            <?php
                                            $appendix_allowance_cost[$j] = old('appendix_allowance_cost', $value->pluck('expense')->toArray());
                                            $appendix_allowance_desc[$j] = old('appendix_allowance_desc', $value->pluck('desc')->toArray() ?? []);
                                            ?>
                                            @for ($i = 0; $i < count($appendix_allowance_cat[$j]); $i++)
                                                <tr>
                                                    <td style="text-align: center; vertical-align: middle;">{!! $i+1 !!}</td>
                                                    <td >
                                                        {!! Form::select("appendix_allowance_cat[$j][$i]", ['' => trans('system.dropdown_choice')] + $allowancesOption, old("appendix_allowance_cat[$j][$i]", $appendix_allowance_cat[$j][$i]), ['class' => 'form-control select2 allowance_cat_global', 'disabled' ]) !!}
                                                    </td>

                                                    <td style="text-align: center; vertical-align: middle;">
                                                        {!! Form::text("appendix_allowance_cost[$j][$i]", old("appendix_allowance_cost[$j][$i]", $appendix_allowance_cost[$j][$i]), ['class' => 'form-control currency allowance_cost_global', 'disabled']) !!}
                                                    </td>
                                                    <td style="vertical-align: middle;">
                                                        {!! Form::text("appendix_allowance_desc[$j][$i]", old("appendix_allowance_desc[$j][$i]", $appendix_allowance_desc[$j][$i]), ['class' => 'form-control desc_global', 'disabled']) !!}
                                                    </td>
                                                    <td style="text-align: center; vertical-align: middle;">
                                                        @if ($i > 0)
                                                            <a href="javascript:void(0);" class="btn btn-xs btn-default remove-allowance disabled" data-id="{{$j}} ">
                                                                <i class="text-danger fa fa-minus"></i>
                                                            </a>
                                                        @else
                                                            <a href="javascript:void(0);" class="btn btn-xs btn-default add-allowance disabled" data-id="{{$j}}">
                                                                <i class="text-success fa fa-plus"></i>
                                                            </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endfor
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-md-offset-3 text-center action-update-{{$key}}" data-id="{{$key}}" style="display: none">
                                            <button class="btn btn-danger btn-xs cancel-update-appendix-allowance" data-id="{{$key}}">
                                                <i class="glyphicon glyphicon-remove" style="padding-right: 3px"></i>
                                                Hủy
                                            </button>
                                            <button class="btn btn-primary btn-xs update-appendix-allowance" type="submit" data-id="{{$key}}" >
                                                <i class="glyphicon glyphicon-floppy-save" ></i>
                                                Lưu
                                            </button>
                                        </div>
                                    </div>

                                    {!! Form::hidden("contract_id", $contract->id) !!}
                                    {!! Form::hidden("old_code", $key) !!}
                                    {!! Form::close() !!}
                                @endforeach
                            @else
                                <span class="notify" >Hợp đồng chưa có phụ lục bổ sung nào!</span>
                            @endif
                            <div class="new-appendix-allowance" style="display: block">
                                {!! Form::open(['url' => route('admin.appendix-allowances.ajaxStore'), 'role' => 'form', 'id' => 'appendix-allowance-form', 'style'=> 'display:none']) !!}
                                @php
                                    $code = old('code_new', '');
                                    $salary = old('salary_new', $lastAppendixAllowance[0]['salary']);
                                    $valid_from = old('valid_from',$lastAppendixAllowance[0]['valid_from'] ?? '');
                                    $valid_to = old('valid_to',$lastAppendixAllowance[0]['valid_to'] ?? '');
                                    $appendix_allowance_cat_new = old('appendix_allowance_cat_new', ($lastAppendixAllowance ? $lastAppendixAllowance->pluck('allowance_id')->toArray() : $allowancesOptionIdCurrent) ?? [] );
                                    $appendix_allowance_cost_new = old('appendix_allowance_cost_new', ($lastAppendixAllowance ? $lastAppendixAllowance->pluck('expense')->toArray() : $allowance_cost) ?? [] );
                                    $appendix_allowance_desc_new = old('appendix_allowance_desc', ($lastAppendixAllowance ? $lastAppendixAllowance->pluck('desc')->toArray() : $desc) ?? [] );
                                @endphp
                                <hr width = "95%" style="border-top: 1px dashed #f81111;">
                                {!! Form::hidden('contract_id', $contract->id) !!}
                                <div class="row" style="margin-bottom: 10px">
                                    <label class="col col-md-2">{!! trans('appendixes.code') !!}</label>
                                    <div class="col-md-4">
                                        {!! Form::text("code_new", old("code_new", $code), ['info'=> trans('appendixes.code'), "class" => "form-control new-appendix-allowance-code code_global", 'required' ]) !!}
                                    </div>
                                    <label class="col col-md-2">{!! trans('contracts.basic_salary') !!}</label>
                                    <div class="col-md-4">
                                        {!! Form::text("salary_new", old("salary_new", $salary), ['info' => trans('contracts.basic_salary'), "class" => "form-control currency salary_global"]) !!}
                                    </div>
                                </div>
                                <div class="row" style="margin-bottom: 10px">
                                    <label class="col col-md-2">{!! trans('contracts.valid_from') !!}</label>
                                    <div class="col col-md-4">
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            {!! Form::text("valid_from", old("valid_from", $valid_from ? $valid_from->format('d/m/Y') : ''), ['info' => trans('contracts.valid_from'), 'class' => 'form-control datepicker datepicker-from-appendix ', 'autocomplete' => 'off', 'required']) !!}
                                        </div>
                                    </div>
                                    <label class="col col-md-2">{!! trans('contracts.valid_to') !!}</label>
                                    <div class="col col-md-4">
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            {!! Form::text("valid_to", old("valid_to",  $valid_to ? $valid_to->format('d/m/Y') : ''), ['info' => trans('contracts.valid_to'), 'class' => 'form-control datepicker datepicker-to-appendix ', 'autocomplete' => 'off', 'required']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered appendix-allowance-new">
                                        <thead style="background: #3C8DBC;color: white;">
                                        <tr>
                                            <th style="text-align: center; vertical-align: middle; ">{!! trans('system.no.') !!}</th>
                                            <th style="text-align: center; vertical-align: middle; min-width: 270px;">{!! trans('contracts.allowance_cat') !!}</th>
                                            <th style="text-align: center; vertical-align: middle; min-width: 270px;">{!! trans('contracts.allowance_cost') !!}</th>
                                            <th style="text-align: center; vertical-align: middle; min-width: 270px;">{!! trans('system.desc') !!}</th>
                                            <th style="text-align: center; vertical-align: middle; min-width: 70px;">{!! trans('system.action.label') !!}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if (count($appendix_allowance_cat_new))
                                            @for ($i = 0; $i < count($appendix_allowance_cat_new); $i++)
                                                <tr>
                                                    <td style="text-align: center; vertical-align: middle;">{!! $i+1 !!}</td>
                                                    <td >
                                                        {!! Form::select("appendix_allowance_cat_new[$i]", ['' => trans('system.dropdown_choice')] + $allowancesOption, old("appendix_allowance_cat_new[$i]", $appendix_allowance_cat_new[$i]), ['class' => 'form-control select2 appendix_allowance_cat_new allowance_cat_global', 'required']) !!}
                                                    </td>
                                                    <td style="text-align: center; vertical-align: middle;">
                                                        {!! Form::text("appendix_allowance_cost_new[$i]", old("appendix_allowance_cost_new[$i]", $appendix_allowance_cost_new[$i]), ['class' => 'form-control currency appendix_allowance_cost_new allowance_cost_global', 'required']) !!}
                                                    </td>
                                                    <td style="vertical-align: middle;">
                                                        {!! Form::text("appendix_allowance_desc[$i]", old("appendix_allowance_desc[$i]", $appendix_allowance_desc_new[$i]), ['class' => 'form-control desc_global',]) !!}
                                                    </td>
                                                    <td style="text-align: center; vertical-align: middle;">
                                                        @if ($i > 0)
                                                            <a href="javascript:void(0);" class="btn btn-xs btn-default remove-allowance" data-id="new">
                                                                <i class="text-danger fa fa-minus"></i>
                                                            </a>
                                                        @else
                                                            <a href="javascript:void(0);" class="btn btn-xs btn-default add-allowance" data-id="new">
                                                                <i class="text-success fa fa-plus"></i>
                                                            </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endfor
                                        @else
                                            <tr>
                                                <td style="text-align: center; vertical-align: middle;">1</td>
                                                <td >
                                                    {!! Form::select("appendix_allowance_cat_new[]", ['' => trans('system.dropdown_choice')] + $allowancesOption, old("appendix_allowance_cat_new[]"), ['class' => 'form-control select2 appendix_allowance_cat_new', 'required']) !!}
                                                </td>

                                                <td style="text-align: center; vertical-align: middle;">
                                                    {!! Form::text("appendix_allowance_cost_new[]", old("appendix_allowance_cost_new[]"), ['class' => 'form-control currency appendix_allowance_cost_new', 'required']) !!}
                                                </td>
                                                <td style="vertical-align: middle;">
                                                    {!! Form::text("appendix_allowance_desc[]", old("appendix_allowance_desc[]"), ['class' => 'form-control currency appendix_weight_kpi_new', 'readonly' => true  ]) !!}
                                                </td>
                                                <td style="text-align: center; vertical-align: middle;">
                                                    <a href="javascript:void(0);" class="btn btn-xs btn-default add-allowance" data-id="new">
                                                        <i class="text-success fa fa-plus"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-md-offset-3 text-center">
                                        <button class="btn btn-danger btn-xs cancel-add-appendix-allowance" >
                                            <i class="glyphicon glyphicon-remove" style="padding-right: 3px"></i>
                                            Hủy
                                        </button>
                                        <button class="btn btn-primary btn-xs save-appendix-allowance" type="submit" >
                                            <i class="glyphicon glyphicon-floppy-save" style="padding-right: 3px"></i>
                                            Lưu
                                        </button>
                                    </div>
                                </div>
                                {!! Form::close() !!}
                            </div>
                        </div>

                        <hr width = "98%" style="border-top: 3px solid #a94442;">

                        {{--Concurrent Contrac--}}
                        <div class="col-md-12 col-contract-part-time" style="display: block">
                            <div class="row" style="margin-bottom: 10px">
                                <div class="col-md-12">
                                    <span style="font-size: 20px; font-weight: 600;">{!! trans('appendixes.list_part_time_contract') !!}</span>
                                    | <i>Chú giải: </i>&nbsp;&nbsp;
                                    <span class="btn btn-xs btn-default"><i class="text-success fa fa-plus" style="font-size: 10px;"></i></span>{!! trans('system.action.create') !!}
                                    <span class="btn btn-xs btn-default" style="margin-left: 5px"><i class="text-danger fa fa-minus" style="font-size: 10px;"></i></span>{!! trans('system.cancel_create') !!}
                                    <span class="text-warning btn btn-xs btn-default" style="margin-left: 5px"><i class="text-warning glyphicon glyphicon-edit"></i></span>{!! trans('system.action.edit') !!}
                                    <span class="text-danger btn btn-xs btn-default" style="margin-left: 5px"><i class="text-danger glyphicon glyphicon-remove" ></i></span>{!! trans('system.action.delete') !!}
                                </div>
                            </div>
                            <?php $appendix_company_id = old('appendix_company_id', $contract->concurrentContracts->pluck('company_id')->toArray());
                            $signed_company_id = old('appendix_company_id', $contract->concurrentContracts->where('status', 1)->pluck('company_id')->toArray());
                            array_push($signed_company_id, $contract->company_id);
                            ?>
                            @unless (count($appendix_company_id))
                                <span>Chưa có hợp đồng kiêm nhiệm!</span>
                            @endunless

                            <hr width = "95%" style="border-top: 1px solid #cccccc;">

                            {!! Form::open(['id' => 'concurrent-contract-form', 'role' => 'form']) !!}

                            <div class="table-responsive" style="overflow-x:auto;">
                                <table class="table table-bordered part-time-contract">
                                    <thead style="background: #3C8DBC;color: white;">
                                    <tr>
                                        <th style="text-align: center; vertical-align: middle; ">{!! trans('contracts.company_id') !!}</th>
                                        <th style="text-align: center; vertical-align: middle; ">{!! trans('contracts.department_id') !!}</th>
                                        <th style="text-align: center; vertical-align: middle; ">{!! trans('contracts.position_id') !!}</th>
                                        <th style="text-align: center; vertical-align: middle; ">{!! trans('contracts.title_id') !!}</th>
                                        <th style="text-align: center; vertical-align: middle; ">{!! trans('appendixes.concurrent_salary') !!}</th>
                                        <th style="text-align: center; vertical-align: middle; ">{!! trans('contracts.valid_from') !!}</th>
                                        <th style="text-align: center; vertical-align: middle; ">{!! trans('contracts.valid_to') !!}</th>
                                        <th style="text-align: center; vertical-align: middle; ">{!! trans('system.action.label') !!}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if (count($appendix_company_id))
                                        <?php
                                        $concurrentContracts = $contract->concurrentContracts;
                                        $appendix_id = $concurrentContracts->pluck('id')->toArray();
                                        $salary = old('salary', $concurrentContracts->pluck('salary')->toArray());
                                        $appendix_department_id = old('appendix_department_id', $concurrentContracts->pluck('department_id')->toArray());
                                        $appendix_position_id = old('appendix_position_id', $concurrentContracts->pluck('position_id')->toArray());
                                        $appendix_qualification_id = old('appendix_qualification_id', $concurrentContracts->pluck('qualification_id')->toArray());
                                        $concurrentValidFrom = old('appendix_valid_from', $concurrentContracts->pluck('valid_from')->toArray());
                                        $concurrentValidTo = old('appendix_valid_to', $concurrentContracts->pluck('valid_to')->toArray());
                                        $status = old('salary', $concurrentContracts->pluck('status')->toArray());
                                        ?>
                                        @for ($i = 0; $i < count($appendix_company_id); $i++)
                                            <tr class="col-concurrent-{{$appendix_id[$i]}}">
                                                <td style="vertical-align: middle;">
                                                    {!! Form::select("appendix_company_id[$i]", ['' => trans('system.dropdown_choice')] + \App\Helpers\GetOption::getCompaniesForOption([$contract->company_id]), old("appendix_company_id[$i]", $appendix_company_id[$i]), ['info' => trans('contracts.company_id'), 'class' => 'form-control select2 appendix_company_id', 'disabled' ]) !!}
                                                </td>
                                                <td style="text-align: center; vertical-align: middle">
                                                    {!! Form::select("appendix_department_id[$i]", ['' => trans('system.dropdown_choice')] + \App\Helpers\GetOption::getDepartmentsForOption($appendix_company_id[$i]), old("appendix_company_id[$i]", $appendix_department_id[$i]), ['info' => trans('contracts.department_id'), 'class' => 'form-control select2 appendix_department_id', 'disabled' ]) !!}
                                                </td>
                                                <td style="text-align: center; vertical-align: middle">
                                                    {!! Form::select("appendix_position_id[$i]", ['' => trans('system.dropdown_choice')] + \App\Helpers\GetOption::getStaffPositionsForOption(), old("appendix_position_id[$i]", $appendix_position_id[$i]), ['info' => trans('contracts.position_id'), 'class' => 'form-control select2', 'disabled' ]) !!}
                                                </td>
                                                <td style="text-align: center; vertical-align: middle">
                                                    {!! Form::select("appendix_qualification_id[$i]", ['' => trans('system.dropdown_choice')] + \App\Helpers\GetOption::getStaffTitlesForOption(), old("appendix_qualification_id[$i]", $appendix_qualification_id[$i]), ['info' => trans('contracts.qualification_id'), 'class' => 'form-control select2', 'disabled' ]) !!}
                                                </td>
                                                <td style="text-align: center; vertical-align: middle">
                                                    {!! Form::text("salary[$id]", old("salary[$i]", $salary[$i]), ['info' => trans('appendixes.concurrent_salary'), "class" => "form-control currency salary-parttime", 'disabled', ]) !!}
                                                </td>
                                                <td style="text-align: center; vertical-align: middle">
                                                    {!! Form::text("appendix_valid_from[$i]", old("appendix_valid_from[$i]", $concurrentValidFrom[$i]->format('d/m/Y')), ['info' => trans('contracts.valid_from'), 'class' => 'text-center form-control datepicker datepicker-from-appendix', 'autocomplete' => 'off', 'disabled']) !!}
                                                </td>
                                                <td style="text-align: center; vertical-align: middle">
                                                    {!! Form::text("appendix_valid_to[$i]", old("appendix_valid_to[$i]", $concurrentValidTo[$i]->format('d/m/Y')), ['info' => trans('contracts.valid_to'), 'class' => 'text-center form-control datepicker datepicker-to-appendix', 'autocomplete' => 'off', 'disabled']) !!}
                                                </td>
                                                <td style="text-align: center; vertical-align: middle; min-width: 125px">
                                                    @permission('contracts.update')
                                                    @if ($i < 1)
                                                        <a href="javascript:void(0);" class="btn btn-xs btn-default add-part-time-contract" >
                                                            <i class="text-success fa fa-plus"></i>
                                                        </a>
                                                    @endif

                                                    @if ($status[$i] == 1)
                                                        <button class="btn btn-xs btn-default edit-concurrent-contract" data-id="{{$appendix_id[$i]}}" >
                                                            <i class="text-warning glyphicon glyphicon-edit"></i>
                                                        </button>
                                                        <a href="{!! route('admin.contracts.export-concurrent', ['contract' => $contract->id,'id' => $appendix_id[$i]]) !!}"
                                                        class="btn btn-xs btn-default"
                                                        data-toggle="tooltip" data-placement="top" title="{!! trans('contracts.download') !!}"
                                                        data-id="{!! $appendix_id[$i] !!}" style="outline: none;">
                                                            <i class="text-success glyphicon glyphicon-download-alt"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-xs btn-default btn-cancel-concurrent" data-toggle="modal" data-target="#exampleModalCenter" data-url="{{ route('admin.contracts.cancel-concurrent') }}" data-id="{{ $appendix_id[$i] }}">
                                                            <i class="fas fa-power-off"></i>
                                                        </button>
                                                        
                                                        <button class="btn btn-xs btn-default delete-concurrent-contract" data-id="{{$appendix_id[$i]}}" >
                                                            <i class="text-danger glyphicon glyphicon-remove"></i>
                                                        </button>
                                                    @else  
                                                        <br>
                                                        <span class="label label-default">Không hoạt động</span>
                                                    @endif
                                                    
                                                    @endpermission
                                                </td>
                                                {!! Form::hidden("id[$i]", $appendix_id[$i], ['disabled']) !!}
                                                {!! Form::hidden("contract_id", $contract->id) !!}
                                            </tr>
                                        @endfor
                                    @else
                                        <tr>
                                            <td style="text-align: center; vertical-align: middle">
                                                {!! Form::select("appendix_company_id[]", ['' => trans('system.dropdown_choice')] + \App\Helpers\GetOption::getCompaniesForOption($signed_company_id), old("appendix_company_id[]"), ['info' => trans('contracts.company_id'), 'class' => 'form-control select2 appendix_company_id' ]) !!}
                                            </td>
                                            <td style="text-align: center; vertical-align: middle">
                                                {!! Form::select("appendix_department_id[]", ['' => trans('system.dropdown_choice')] , old("appendix_company_id[]"), ['info' => trans('contracts.department_id'), 'class' => 'form-control select2 appendix_department_id', ]) !!}
                                            </td>
                                            <td style="text-align: center; vertical-align: middle">
                                                {!! Form::select("appendix_position_id[]", ['' => trans('system.dropdown_choice')] + \App\Helpers\GetOption::getStaffPositionsForOption(), old("appendix_position_id[]"), ['info' => trans('contracts.position_id'), 'class' => 'form-control select2', ]) !!}
                                            </td>
                                            <td style="text-align: center; vertical-align: middle">
                                                {!! Form::select("appendix_qualification_id[]", ['' => trans('system.dropdown_choice')] + \App\Helpers\GetOption::getStaffTitlesForOption(), old("appendix_qualification_id[]"), ['info' => trans('contracts.qualification_id'), 'class' => 'form-control select2', ]) !!}
                                            </td>
                                            <td style="text-align: center; vertical-align: middle">
                                                {!! Form::text('salary[]', old('salary[]'), ['info' => trans('appendixes.concurrent_salary'), "class" => "form-control currency salary-parttime"]) !!}
                                            </td>
                                            <td style="text-align: center; vertical-align: middle">
                                                <div class="input-group">
                                                    {!! Form::text('appendix_valid_from[]', old('appendix_valid_from[]'), ['info' => trans('contracts.valid_from'), 'class' => 'form-control datepicker datepicker-from-appendix', 'autocomplete' => 'off']) !!}
                                                </div>
                                            </td>
                                            <td style="text-align: center; vertical-align: middle">
                                                <div class="input-group">
                                                    {!! Form::text('appendix_valid_to[]', old('appendix_valid_to[]'), ['info' => trans('contracts.valid_to'), 'class' => 'form-control datepicker datepicker-to-appendix', 'autocomplete' => 'off']) !!}
                                                </div>
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                @permission('contracts.update')
                                                <a href="javascript:void(0);" class="btn btn-xs btn-default add-part-time-contract">
                                                    <i class="text-success fa fa-plus"></i>
                                                </a>
                                                @endpermission
                                            </td>
                                            {!! Form::hidden("id[]") !!}
                                            {!! Form::hidden("contract_id", $contract->id) !!}
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                            @permission('contracts.update')
                            @if ($contract->check_valid != 1)
                                <div class="col-md-1 col-md-offset-11" style="margin-top: 10px; float: right">
                                    <button class="btn btn-primary btn-sm save-concurrent-contract" type="submit">
                                        <i class="glyphicon glyphicon-floppy-save" style="padding-right: 3px"></i>
                                        Lưu
                                    </button>
                                </div>
                            @endif
                            @endpermission
                            {!! Form::close() !!}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="box box-default">
        <div class="box-body">
            <div class="container col-xs-12">
                <div class="row">
                    <div class="col-md-6 col-md-offset-3 text-center">
                        {!! HTML::link(route( 'admin.contracts.index' ), trans('system.action.return'), ['class' => 'return btn btn-danger btn-flat']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLongTitle">Dừng hoạt động</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              Bạn có chắc chắn dừng hoạt động lương kiêm nhiệm của phòng ban này
            </div>
            <div class="modal-footer" style="text-align: center" >
              <button type="button" class="btn btn-danger btn-flat" data-dismiss="modal">Hủy</button>
              <button type="button" class="btn btn-primary btn-flat save-cancel-concurrent">Lưu lại</button>
            </div>
          </div>
        </div>
    </div>

@stop
@section('footer')
    <script src="{!! asset('assets/backend/plugins/select2/select2.full.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/input-mask/jquery.inputmask.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/iCheck/icheck.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.vi.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/moment/locale/vi.js') !!}"></script>
    <script src="{!! asset('assets/backend/js/contract.js') !!}?v=25-02-2022"></script>

    <script>
        !function ($) {

            $(function(){
                const active = {!! \App\Defines\Contract::ACTIVE !!};
                const transfer = {!! \App\Defines\Contract::TRANSFER !!};
                const appoint = {!! \App\Defines\Contract::APPOINT !!};
                const leaveWork = {!! \App\Defines\Contract::LEAVE_WORK !!};
                const dismissal = {!! \App\Defines\Contract::DISMISSAL !!};
                const endPartTime = {!! \App\Defines\Contract::END_PART_TIME !!};
                const expired = {!! \App\Defines\Contract::EXPIRED !!};
                const future = {!! \App\Defines\Contract::FUTURE !!};
                function showHideAdditionalDate() {
                    let typeStatus = $('select[name="type_status"]').val()
                    if (typeStatus == active) {
                        $('.additional-date').hide()
                    } else if (typeStatus == appoint) {
                        $('.additional-date').show()
                        $('.staff-submit-date-row').hide()
                        $('.report-valid-row').show()
                    } else if (typeStatus == transfer) {
                        $('.additional-date').show()
                        $('.staff-submit-date-row').hide()
                        $('.report-valid-row').show()
                    } else if (typeStatus == dismissal) {
                        $('.additional-date').show()
                        $('.staff-submit-date-row').hide()
                    } else if (typeStatus == leaveWork) {
                        $('.additional-date').show()
                        $('.staff-submit-date-row').show()
                        $('.report-valid-row').show()
                    } else if (typeStatus == endPartTime) {
                        $('.additional-date').show()
                        $('.staff-submit-date-row').hide()
                        $('.report-valid-row').hide()
                    } else if (typeStatus == expired || typeStatus == future) {
                        $('input[name="status"]').val(0)
                        $('.report-valid-row').hide()
                        $('.additional-date').hide()
                        $('.staff-submit-date-row').hide()
                    }
                }

                showHideAdditionalDate()

                $('.col-appendix-allowance').find('label').each(function () {
                    $(this).addClass('text-center')
                })
                var status = {!! $contract->status !!};
                if (status == 0) {
                    $('.btn').attr('disabled', true)
                    $('.return').attr('disabled', false)
                    $('input').attr('disabled', true)
                    $('select').attr('disabled', true)
                }
                $('button.edit-concurrent-contract').on('click', function (e) {
                    e.preventDefault()
                    let dataId = $(this).attr('data-id')
                    let tagTr = $('tr.col-concurrent-' + dataId)
                    tagTr.find('select').attr('disabled', false)
                    tagTr.find('input').attr('disabled', false)
                    $('tr.action-update-area').show()
                    // setDepartmentOption()
                })

                $('button.cancel-update-appendix-allowance').on('click', function (e) {
                    e.preventDefault()
                    let dataId = $(this).attr('data-id')
                    let tagForm = $('#update-appendix-allowance-form-' + dataId)
                    tagForm.trigger('reset')
                    tagForm.find('input').attr('disabled', true)
                    tagForm.find('select').attr('disabled', true)
                    $('div.action-update-' + dataId).slideUp("slow");
                })
                $('.code_global').keypress(function( e ) {
                    if(e.which === 32)
                        return false;
                });
                $('.edit-appendix-allowance').on('click', function (e) {
                    e.preventDefault()
                    let dataId = $(this).attr('data-id')
                    $('button.update-appendix-allowance[data-id="' + dataId + '"]').show()
                    let tagForm = $('#update-appendix-allowance-form-' + dataId)
                    tagForm.find('input').attr('disabled', false)
                    tagForm.find('select').attr('disabled', false)
                    $('div.action-update-' + dataId).slideDown("slow");
                    tagForm.find('input.code_global').focus()
                })
                $('button.delete-appendix-allowance').on('click', function (e) {
                    e.preventDefault()
                    let r = confirm("Bạn có chắc chắn muốn xóa phụ lục này?");
                    if (r == true) {
                        let dataId = $(this).attr('data-id')
                        $.ajax({
                            url: "{!! route('admin.appendix-allowances.ajaxDestroy') !!}",
                            type: 'POST',
                            data: {code:dataId},
                            headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                            success:function(res) {
                                if(res.data) {
                                    $('#update-appendix-allowance-form-' + dataId).remove()
                                    toastr.success(res.message, "{!! trans('system.info') !!}")
                                } else {
                                    toastr.warning(res.message, "{!! trans('system.info') !!}")
                                }
                            },
                        });
                    } else {
                        return false
                    }
                })
                var allowanceCategoryKpi = {!! $allowanceCategoryKpi !!};
                var targetKpi = 100
                function setKpi() {
                    $('input.appendix_allowance_cost_new').each(function () {
                        $(this).on('keyup', function () {
                            let allowanceCost = $(this).val()
                            if (allowanceCost) {
                                allowanceCost = allowanceCost.replaceAll(',', '')
                                console.log(allowanceCost)
                                let tagCat = $(this).parent().parent().find('td select.appendix_allowance_cat_new').val()
                                console.log(tagCat)
                                if (Object.values(allowanceCategoryKpi).indexOf(parseInt(tagCat)) > -1) {
                                    let tagKpi = $(this).parent().parent().find('td input.appendix_weight_kpi_new')
                                    tagKpi.val(parseInt( allowanceCost)/targetKpi)
                                }
                            }
                        })
                    });
                    $('select.appendix_allowance_cat_new').each(function () {
                        $(this).on('change', function () {
                            let allowanceId = $(this).val()
                            let tagKpi = $(this).parent().parent().find('td input.appendix_weight_kpi_new')
                            if (Object.values(allowanceCategoryKpi).indexOf(parseInt(allowanceId)) > -1) {
                                let allowanceCost = $(this).parent().parent().find('td input.appendix_allowance_cost_new').val()
                                if (allowanceCost) {
                                    tagKpi.val(parseInt(allowanceCost.replaceAll(',', ''))/targetKpi)
                                }
                            } else {
                                tagKpi.val('')
                            }
                        })
                    });
                }
                function setDepartmentOption() {
                    $('select.appendix_company_id').each(function () {
                        $(this).change(function () {
                            console.log($(this).val())
                            let tagDepartment = $(this).parent().parent().find('td select.appendix_department_id')
                            let companyId = $(this).val()
                            $.ajax({
                                url: "{!! route('admin.contracts.setDepartmentOption') !!}",
                                data: {companyId: companyId},
                                type: 'POST',
                                headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                                success: function (res) {
                                    // console.log(res)
                                    $(tagDepartment).find('option').remove()
                                    $(tagDepartment).append('<option value="">'+ '{!! trans('system.dropdown_choice') !!}'  + '</option>')
                                    $.each(res, function (index, value) {
                                        $(tagDepartment).append('<option value="' + index + '">' + value + '</option>')
                                    })
                                },
                                error: function (data) {
                                    console.log(data)
                                }
                            })
                        });
                    });
                }
                setDepartmentOption()
                var isValidMainContract = {!! $contract->check_valid !!};
                function setDisabledConcurrentContract(check) {
                    let tagForm = $('form#concurrent-contract-form');
                    if (check == 1) {
                        tagForm.find('input').attr('disabled', true)
                        tagForm.find('select').attr('disabled', true)
                        tagForm.find('.btn').attr('disabled', true)
                    }
                }
                setDisabledConcurrentContract(isValidMainContract)
                $('body').on('click', '.save-concurrent-contract', function(e) {
                    e.preventDefault()
                    let dataForm = $("#concurrent-contract-form");
                    validateFormRequired(dataForm)
                    if (!validateFormRequired(dataForm)) return false
                    let formData = dataForm.serialize();
                    showLoading()
                    $.ajax({
                        url:"{!! route('admin.concurrent-contracts.ajaxUpdateOrCreate') !!}",
                        type:'POST',
                        data: formData,
                        headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                        success:function(res) {
                            toastr.success(res.message, "{!! trans('system.info') !!}")
                            location.reload();
                        },
                        error: function (err) {
                            let error = $.parseJSON(err.responseText);
                            toastr.warning(error.message, "{!! trans('system.have_error') !!}")
                        }
                    }).always(function() {
                        hideLoading()
                    });
                })
                let timeout = 4000
                function validateFormRequired(tagForm) {
                    let check = true
                    tagForm.find('input:not(".salary_global"):not(".salary-parttime"), select').each(function (e) {
                        if (!$(this).val() && $(this).attr('info')) {
                            let info = $(this).attr('info')
                            toastr.warning('Trường ' + info + ' không được để trống!.')
                            $(this).focus()
                            $(this).select2('open');
                            $(this).addClass('setBorderColor')
                            $(this).parent().find('span.select2-selection').addClass('setBorderColor')
                            let tag = $(this)
                            setTimeout(function() {
                                tag.removeClass('setBorderColor')
                                tag.parent().find('span.select2-selection').removeClass('setBorderColor')
                            }, timeout );
                            check = false
                            return false
                        }
                    })
                    return check
                }
                function diffDates(dateOne, dateTwo) {
                    return moment(dateOne.split("/").reverse().join("-")).diff(moment(dateTwo.split("/").reverse().join("-")), 'days')
                }
                $('body').on('click', '.save-appendix-allowance, button.update-appendix-allowance', function(e){
                    e.preventDefault()
                    console.log('save')
                    let dataId = $(this).attr('data-id')
                    let tagForm = dataId ? $('#update-appendix-allowance-form-' + dataId) : $("#appendix-allowance-form");
                    validateFormRequired(tagForm)
                    if (!validateFormRequired(tagForm)) return false
                    let tagDatepickerTo = tagForm.find('input.datepicker-to-appendix')
                    let tagDatepickerFrom = tagForm.find('input.datepicker-from-appendix')
                    let valid_from = tagForm.find('input.datepicker-from-appendix').val()
                    let valid_to = tagDatepickerTo.val()
                    let valid_to_main = $('div.contract-content').find('.datepicker-to').val()
                    let valid_from_main = $('div.contract-content').find('.datepicker-from').val()
                    if (diffDates(valid_to, valid_from) < 1 ) {
                        toastr.warning('{!! trans('contracts.validate_valid_to') !!}')
                        tagDatepickerTo.focus()
                        tagDatepickerTo.addClass('setBorderColor')
                        setTimeout(function() {
                            tagDatepickerTo.removeClass('setBorderColor')
                        }, timeout );
                        return false
                    }
                    if (valid_to_main) {
                        if (diffDates(valid_to, valid_to_main) > 0) {
                            toastr.warning('Hiệu lực đến của phụ lục không lớn hơn hợp đồng chính!')
                            tagDatepickerFrom.focus()
                            tagDatepickerFrom.addClass('setBorderColor')
                            setTimeout(function() {
                                tagDatepickerFrom.removeClass('setBorderColor')
                            }, timeout );
                            return false
                        }
                        if (diffDates(valid_from, valid_from_main) < 0) {
                            toastr.warning('Hiệu lực đến của phụ lục không lớn hơn hợp đồng chính!')
                            tagDatepickerFrom.focus()
                            tagDatepickerFrom.addClass('setBorderColor')
                            setTimeout(function() {
                                tagDatepickerFrom.removeClass('setBorderColor')
                            }, timeout );
                            return false
                        }
                    }
                    let formData = tagForm.serialize();
                    let url = dataId ? '{!! route('admin.appendix-allowances.ajaxUpdate') !!}' : '{!! route('admin.appendix-allowances.ajaxStore') !!}';
                    $.ajax({
                        url:url,
                        type:'POST',
                        data:formData,
                        success:function(res) {
                            if(res.data) {
                                location.reload();
                                toastr.success(res.message, "{!! trans('system.info') !!}")
                            } else {
                                console.log(res.error)
                                toastr.warning(res.message, "{!! trans('system.info') !!}")
                                if (res.errClass) {
                                    tagForm.find("input.code_global").focus()
                                    tagForm.find("input.code_global").addClass('setBorderColor')
                                    setTimeout(function() {
                                        tagForm.find("input.code_global").removeClass('setBorderColor')
                                    }, timeout );
                                    return false
                                }
                            }
                        },
                    });
                });
                // $('input[type="checkbox"].minimal').iCheck({
                //     checkboxClass: 'icheckbox_minimal-blue',
                //     increaseArea: '20%'
                // });
                if($('.datepicker-to').val().split("/").reverse().join("-")) {
                    $('.datepicker').datepicker({
                        endDate: new Date($('.datepicker-to').val().split("/").reverse().join("-")),
                        startDate: new Date($('.datepicker-from').val().split("/").reverse().join("-")),
                        format: 'dd/mm/yyyy',
                        autoclose: true,
                        todayHighlight: true,
                        language: "vi",
                    })
                }
                $('.datepicker').datepicker({
                    format: 'dd/mm/yyyy',
                    autoclose: true,
                    todayHighlight: true,
                    language: "vi",
                    startDate: new Date($('.datepicker-from').val().split("/").reverse().join("-")),
                });
                $(".select2").select2({
                    width: '100%',
                    minimumResultsForSearch: -1
                });

                let codeContract = {!! json_encode($contract->code) !!}
                $('span.add-appendix-allowance').on('click', function (e) {
                    let tagForm = $('form#appendix-allowance-form')
                    tagForm.show()
                    let codeAppendix = codeContract.replace('HDLD', 'PLLD').replace('HDTV', 'PLLD')
                    tagForm.find('.new-appendix-allowance-code').val(codeAppendix)
                    tagForm.find('.new-appendix-allowance-code').focus()
                    // $('.save-appendix-allowance').attr('disabled', false)
                })
                $('button.cancel-add-appendix-allowance').on('click', function (e) {
                    e.preventDefault()
                    let tagForm = $('form#appendix-allowance-form')
                    tagForm.trigger('reset')
                    tagForm.slideUp('slow')
                })
                var maxAllowance = {!! count($allowancesOption) !!}
                var counterAllowances = {!! count($appendix_allowance_cat[$j]) ? (count($appendix_allowance_cat[$j])+1) : 2 !!};
                $(document).on("click", ".add-allowance", function (event) {
                    console.log('add allowance...')
                    if (counterAllowances > maxAllowance) {
                        toastr.warning('Đã vượt quá số lượng phụ cấp')
                        return false
                    }
                    let id = $(this).attr('data-id')
                    var newRow = $("<tr>");
                    var cols = "";
                    cols += '<td style="text-align: center; vertical-align: middle;">' + counterAllowances + '</td>';
                    cols += '<td style="text-align: center; vertical-align: middle;">';
                    cols += '{!! Form::select('appendix_allowance_cat_new[]', ['' => trans('system.dropdown_choice')] + $allowancesOption, old('appendix_allowance_cat[]'), ['class' => 'form-control select2 appendix_allowance_cat_new', ]) !!}';
                    cols += '</td>';
                    cols += '<td style="text-align: center; vertical-align: middle;">';
                    cols += '{!! Form::text('appendix_allowance_cost_new[]', old('appendix_allowance_cost[]'), ['class' => 'form-control currency appendix_allowance_cost_new']) !!}';
                    cols += '</td>';
                    cols += '<td>{!! Form::text("appendix_allowance_desc[]", old("appendix_allowance_desc[]"), ['class' => 'form-control']) !!}</td>';;
                    cols += '<td style="text-align: center; vertical-align: middle;">';
                    cols += '<a href="javascript:void(0);" class="btn btn-xs btn-default remove-allowance" data-id="new">';
                    cols += '<i class="text-danger fa fa-minus"></i>';
                    cols += '</a>';
                    cols += '</td>';
                    newRow.append(cols);
                    $("table.appendix-allowance-" + id).append(newRow);
                    counterAllowances++;
                    callSelect2()
                    callInputMask({digit: getCurrencyDigit($('.currency_code').val())})
                })
                $(document).on("click", ".remove-allowance", function (event) {
                    let id = $(this).attr('data-id') ?? ''
                    console.log('id', id)
                    $(this).closest("tr").remove();
                    counterAllowances -= 1;
                    let tmp = 1;
                    let tag = "table.appendix-allowance-" + id + ' ' + "tbody td:first-child"
                    $(tag).each(function() {
                        $(this).html(tmp++);
                    });
                });
                var counterConcurrentContracts = {!! count($appendix_company_id) ? (count($appendix_company_id)+1) : 2 !!};

                $(document).on("click", ".add-part-time-contract", function (e) {
                    var maxCompanies = {!! count(\App\Helpers\GetOption::getCompaniesForOption()) - 1  !!};
                    if (counterConcurrentContracts > maxCompanies) {
                        toastr.warning('Đã vượt quá số lượng công ty')
                        return false
                    }
                    var newRow = $("<tr>");
                    var cols = "";
                    // cols += '<td style="text-align: center; vertical-align: middle;">' + counterConcurrentContracts + '</td>';
                    cols += '<td style="text-align: center; vertical-align: middle;">';
                    cols += '{!! Form::select("appendix_company_id[]", ['' => trans('system.dropdown_choice')] + \App\Helpers\GetOption::getCompaniesForOption($signed_company_id), old("appendix_company_id[]"), ['info' => trans('contracts.company_id'), 'class' => 'form-control select2 appendix_company_id', 'required' ]) !!}';
                    cols += '</td>';
                    cols += '<td style="text-align: center; vertical-align: middle">{!! Form::select("appendix_department_id[]", ['' => trans('system.dropdown_choice')] , old("appendix_department_id[]"), ['info' => trans('contracts.department_id'), 'class' => 'form-control select2 appendix_department_id', ]) !!}</td>';;
                    cols += '<td style="text-align: center; vertical-align: middle">{!! Form::select("appendix_position_id[]", ['' => trans('system.dropdown_choice')] + \App\Helpers\GetOption::getStaffPositionsForOption(), old("appendix_position_id[]"), ['info' => trans('contracts.position_id'), 'class' => 'form-control select2' ]) !!}</td>';;
                    cols += '<td style="text-align: center; vertical-align: middle">{!! Form::select("appendix_qualification_id[]", ['' => trans('system.dropdown_choice')] + \App\Helpers\GetOption::getStaffTitlesForOption(), old("appendix_qualification_id[]"), ['info' => trans('contracts.qualification_id'), 'class' => 'form-control select2', ]) !!}</td>'
                    cols += '<td style="text-align: center; vertical-align: middle;">';
                    cols +='<div class="input-group">'
                    cols += '{!! Form::text('salary[]', old('salary[]'), ["class" => "form-control currency salary-parttime"]) !!}'
                    cols += '</div>'
                    cols += '</td>';
                    cols += '<td style="text-align: center; vertical-align: middle;">';
                    cols += '<div class="input-group">'
                    cols += '{!! Form::text('appendix_valid_from[]', old('appendix_valid_from[]'), ['info' => trans('contracts.valid_from'), 'class' => 'form-control datepicker datepicker-from-appendix', 'autocomplete' => 'off', 'required']) !!}'
                    cols += '</div></td>'
                    cols += '<td style="text-align: center; vertical-align: middle;">';
                    cols += '<div class="input-group">'
                    cols += '{!! Form::text('appendix_valid_to[]', old('appendix_valid_to[]'), ['info' => trans('contracts.valid_to'), 'class' => 'form-control datepicker datepicker-from-appendix', 'autocomplete' => 'off', 'required']) !!}'
                    cols += '</div></td>'
                    cols += '<td style="text-align: center; vertical-align: middle;">';
                    cols += '<a href="javascript:void(0);" class="btn btn-xs btn-default remove-part-time-contract">';
                    cols += '<i class="text-danger fa fa-minus"></i>';
                    cols += '</a>';
                    cols += '</td>';
                    cols += '{!! Form::hidden("id[]", "") !!}';
                    cols += '{!! Form::hidden("contract_id", $contract->id) !!}';
                    newRow.append(cols);
                    $("table.part-time-contract").append(newRow);
                    counterConcurrentContracts++;
                    callSelect2()
                    if($('.datepicker-to').val().split("/").reverse().join("-")) {
                        $('.datepicker').datepicker({
                            endDate: new Date($('.datepicker-to').val().split("/").reverse().join("-")),
                            startDate: new Date($('.datepicker-from').val().split("/").reverse().join("-")),
                            format: 'dd/mm/yyyy',
                            autoclose: true,
                            todayHighlight: true,
                            language: "vi",
                        })
                    }
                    $('.datepicker').datepicker({
                        format: 'dd/mm/yyyy',
                        autoclose: true,
                        todayHighlight: true,
                        language: "vi",
                        startDate: new Date($('.datepicker-from').val().split("/").reverse().join("-")),
                    });
                    callInputMask({digit: getCurrencyDigit($('.currency_code').val())})
                    setDepartmentOption()
                })
                $('button.delete-concurrent-contract').on('click', function (e) {
                    e.preventDefault()
                    let dataId = $(this).attr('data-id')
                    let tagTr = $('tr.col-concurrent-' + dataId)
                    let check = confirm("{!! trans('system.confirm_delete') !!}");
                    if (check == true) {
                        let dataId = $(this).attr('data-id')
                        $.ajax({
                            url: "{!! route('admin.concurrent-contracts.ajaxDestroy') !!}",
                            type: 'POST',
                            data: {id:dataId},
                            headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                            success:function(res) {
                                location.reload()
                                counterConcurrentContracts -= 1;
                                toastr.success(res.message, "{!! trans('system.info') !!}")
                            },
                            error: function (err) {
                                let error = $.parseJSON(err.responseText);
                                toastr.warning(error.message, "{!! trans('system.have_error') !!}")
                            }
                        });
                    } else {
                        return false
                    }
                })

                $(document).on("click", ".remove-part-time-contract", function (event) {
                    $(this).closest("tr").remove();
                    counterConcurrentContracts -= 1;
                    let tmp = 1;
                    /*$("table.part-time-contract tbody td:first-child").each(function() {
                        $(this).html(tmp++);
                    });*/
                });
            });
        }(window.jQuery);
    </script>
    <script>
        !function ($) {
            var url = '';
            var id = '';
            $('.btn-cancel-concurrent').on('click', function() {
                url = $(this).data('url');
                id = $(this).data('id');
            });

            $('.save-cancel-concurrent').on('click', function () {
                $('#exampleModalCenter').modal('hide');

                $.ajax({
                    url: url,
                    data: {'id' :id },
                    type: 'POST',
                    datatype: 'json',
                    headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                    success: function(res) {
                        location.reload();
                    },
                    error: function(obj, status, err) {
                        var error = $.parseJSON(obj.responseText);
                        toastr.error(error.message, '{!! trans('system.have_an_error') !!}');
                    }
                }).always(function() {
                    $('.loading').hide();
                });
            })
            
        }(window.jQuery);
    
    </script>
@stop
