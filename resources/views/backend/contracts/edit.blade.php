@extends('backend.master')
@section('title')
    {!! trans('system.action.edit') !!} - {!! trans('contracts.label') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/>
    <link rel="stylesheet" type="text/css"
          href="{!! asset('assets/backend/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>

    <style>
        table.allowances {
            text-align: center;
            vertical-align: center;
        }

        table.allowances thead th {
            text-align: center;
        }

        table.allowances tbody td {
            vertical-align: center;
        }

        .contract-content .row {
            margin: 15px -15px;
        }

        .contract-content label {
            margin-top: 7px;
            text-align: right;
        }
    </style>
@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('contracts.label') !!}
            <small>{!! trans('system.action.update') !!}</small>
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
    @if(Session::get('err_status'))
        <div class="alert alert-warning alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-warning"></i> {!! trans('messages.error') !!}</h4>
            <ul>
                <li>{!! Session::get('err_status') !!}</li>
            </ul>
        </div>
    @endif
    @if(Session::get('err_position'))
        <div class="alert alert-warning alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-warning"></i> {!! trans('messages.error') !!}</h4>
            <ul>
                <li>{!! Session::get('err_position') !!}</li>
            </ul>
        </div>
    @endif

    {!! Form::open(['url' => route('admin.contracts.update', $contract->id), 'method' => 'put', 'role' => 'form', 'id'=>'contract-content', 'enctype'=>'multipart/form-data']) !!}

    <div class="container col-md-12 contract-content">
        <div class="row">
            <label class="col col-md-2">{!! trans('contracts.staff_id') !!}</label>
            <div class="col col-md-4">
                {!! Form::select('user_id', [$contract->user_id => $contract->user->code . ' - ' .$contract->user->fullname], old('user_id', $contract->user_id), ['id' => 'userSelect', 'class' => 'form-control select2 staffSelect', 'required']) !!}
            </div>
            <label class="col col-md-2">{!! trans('contracts.company_id') !!}</label>
            <div class="col col-md-4">
                {!! Form::select('company_id', \App\Helpers\GetOption::getCompaniesForOption(), old('company_id', $contract->company_id), ['class' => 'form-control select2 companySelect', 'required']) !!}
            </div>
        </div>
        <div class="row">
            <label class="col col-md-2">{!! trans('contracts.department_id') !!}</label>
            <div class="col col-md-4">
                {!! Form::select('department_id', $departmentOption, old('department_id', $contract->department_id), ['id' => 'departmentSelect', 'class' => 'form-control select2', 'required']) !!}
            </div>
            <label class="col col-md-2">{!! trans('contracts.position_id') !!}</label>
            <div class="col col-md-4">
                {!! Form::select('position_id', \App\Helpers\GetOption::getStaffPositionsForOption(), old('position_id', $contract->position_id), ['class' => 'form-control select2 positionSelect', 'required']) !!}
            </div>
        </div>
        <div class="row">
            <label class="col col-md-2">{!! trans('contracts.title_id') !!}</label>
            <div class="col col-md-4">
                {!! Form::select('qualification_id', \App\Helpers\GetOption::getStaffTitlesForOption(), old('qualification_id', $contract->qualification_id), ['class' => 'form-control select2', 'required']) !!}
            </div>
            <label class="col col-md-2">{!! trans('contracts.desc_qualification') !!}</label>
            <div class="col-md-4">
                {!! Form::textarea('desc_qualification', old('desc_qualification', $contract->desc_qualification), ['class' => 'form-control desc_qualification', 'rows' => 1, $contract->check_valid != 1 ? '' : 'disabled'  ]) !!}
            </div>
        </div>
        <div class="row">
            <label class="col col-md-2">{!! trans('contracts.is_main') !!}</label>
            <div class="col col-md-4">
                {!! Form::select('is_main', ['' => trans('system.dropdown_choice')] + \App\Defines\Staff::getStatusForOptionContract(), old('is_main', $contract->is_main), ['class' => 'form-control select2 is-main', 'required' ]) !!}
            </div>
            <label class="col col-md-2">{!! trans('contracts.type') !!}</label>
            <div class="col col-md-4">
                {!! Form::select('type', ['' => trans('system.dropdown_choice')] + App\Defines\Contract::getTypesForOption(), old('type', $contract->type), ['class' => 'form-control select2 type']) !!}
            </div>
        </div>
        <div class="row">
            <label class="col col-md-2">{!! trans('contracts.basic_salary') !!}</label>
            <div class="col col-md-4">
                {{--<div class="input-group">
                    {!! Form::text('basic_salary',old('basic_salary', $contract->basic_salary), ["class" => "form-control"]) !!}
                    <div class="input-group-addon price-addon">VNĐ</div>
                </div>--}}
                <div class="input-group" style="display: inline-flex; width: 100%">
                    <div class="div_select_currency" @if(!$contract->currency_code) style="display: none" @endif>
                        {!! Form::select('currency_code', \App\Defines\Contract::getCurrencyOptions(), old('currency_code', $contract->currency_code ?? \App\Defines\Contract::VND), ['class' => 'form-control select2 currency_code',]) !!}
                    </div>
                    <div style="width: 100%">
                        {!! Form::text('basic_salary', old('basic_salary', $contract->basic_salary), ["class" => "form-control currency", 'autocomplete' => 'off',]) !!}
                    </div>
                </div>
            </div>
            <label class="col col-md-2">{!! trans('contracts.valid_from') !!}</label>
            <div class="col col-md-4">
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    {!! Form::text('valid_from', old('valid_from', $contract->valid_from->format('d/m/Y')), ['class' => 'form-control datepicker datepicker-from', 'required', 'autocomplete' => 'off']) !!}
                </div>
            </div>
        </div>
        <div class="row">
            <label class="col col-md-2">{!! trans('contracts.code') !!}</label>
            <div class="col col-md-4">
                {!! Form::text('code', old('code', $contract->code), ['class' => 'form-control', 'maxlength' => 50, 'required', ]) !!}
            </div>
            <label class="col col-md-2">{!! trans('contracts.valid_to') !!}</label>
            <div class="col col-md-4">
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    {!! Form::text('valid_to', old('valid_to', $contract->valid_to ? $contract->valid_to->format('d/m/Y') : null), ['readonly'=>true, 'class' => 'form-control datepicker datepicker-to', 'autocomplete' => 'off']) !!}
                    {{--                    {!! Form::hidden('valid_to', $contract->valid_to ? $contract->valid_to->format('d/m/Y') : null , ['class' => 'valid-to']) !!}--}}
                </div>
            </div>
        </div>
        <div class="row">
            <label class="col col-md-2" style="margin-top: 2px">Tệp đính kèm</label>
            <div class="col col-md-4">
                {!! Form::file('file[]', ['id' => 'files', 'multiple' => true]) !!}
            </div>
        </div>
        <div class="row">
            <div class="col-md-offset-2 col-md-10 file-show">
                @foreach($contract->contractFiles as $file)
                    <li class="pip">
                        {!! Form::hidden('file_edits[]', $file->id) !!}
                        <span>{!! $file->name !!}</span>
                        <span class="btn btn-default btn-xs remove" title="Xóa"><i class="text-danger fa fa-times"></i></span>
                        <a href="{!! route('admin.contracts.download', $file->id) !!}"
                           class="btn btn-default btn-xs download-file"
                           title="Tải xuống">
                            <i class="text-primary fa fa-download"></i>
                        </a>
                    </li>
                @endforeach
            </div>
            @if ($errors->get('file.*'))
                <div class="col-md-offset-2 col-md-10">
                    <span class="text-danger">{{ $errors->first('file.*') }}</span>
                </div>
            @endif
        </div>
        <div class="row">
            <label class="col col-md-offset-3 col-md-2">{!! trans('system.status.label') !!}</label>
            <div class="col col-md-2">
                {!! Form::select('type_status', App\Defines\Contract::getTypeStatusForOption(), old('type_status', $contract->type_status), ['class' => 'form-control select2 type_status'], [ \App\Defines\Contract::LEAVE_WORK => [ "disabled" => true ] ]) !!}
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
                        {!! Form::text('staff_submit_date', old('staff_submit_date', $contract->staff_submit_date ? date('d/m/Y', strtotime($contract->staff_submit_date)) : null), ['class' => 'form-control datepicker staff-submit-date', 'autocomplete' => 'off']) !!}
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
                        {!! Form::text('set_notvalid_date', old('set_notvalid_date', $contract->set_notvalid_on ? date('d/m/Y', strtotime($contract->set_notvalid_on)) : null), ['class' => 'form-control datepicker set-notvalid-date', 'autocomplete' => 'off',]) !!}
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
                        {!! Form::text('report_valid', old('report_valid', $contract->report_valid ? date('d/m/Y', strtotime($contract->report_valid)) : null), ['class' => 'form-control datepicker report-valid', 'autocomplete' => 'off']) !!}
                    </div>
                </div>
            </div>
        </div>
        {{ Form::hidden('temp', $contract->id) }}
        {{ Form::hidden('status', $contract->status) }}
    </div>

    <div class="row" style="margin: 0">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-header with-border">
                    <i class="fas fa-money-bill-alt"></i>
                    <h3 class="box-title">{!! trans('contracts.table_allowances') !!}</h3>
                </div>
                <?php $totalAllowance = 0; ?>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered expense">
                                <thead style="background: #3C8DBC;color: white;">
                                <tr>
                                    <th style="text-align: center; vertical-align: middle; width: 5%;">{!! trans('system.no.') !!}</th>
                                    <th style="text-align: center; vertical-align: middle; width: 35%;">{!! trans('contracts.allowance_cat') !!}</th>
                                    <th style="text-align: center; vertical-align: middle; width: 25%;">{!! trans('contracts.allowance_cost') !!}</th>
                                    <th style="text-align: center; vertical-align: middle; width: 27%;">{!! trans('system.desc') !!}</th>
                                    <th style="text-align: center; vertical-align: middle; width: 8%;">{!! trans('system.action.label') !!}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $allowance_cat = old('allowance_cat', $allowancesOptionIdCurrent); $totalAmount = $totalAdvanced = 0; ?>
                                @if (count($allowance_cat))
                                    <?php
                                    $desc = old('desc', $contract->allowances->pluck('desc'));
                                    $allowance_cost = old('allowance_cost', $contract->allowances->pluck('expense'));
                                    ?>
                                    @for ($i = 0; $i < count($allowance_cat); $i++)
                                        <tr>
                                            <td style="text-align: center; vertical-align: middle;">{!! $i+1 !!}</td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                {!! Form::select("allowance_cat[$i]", ['' => trans('system.dropdown_choice')] + $allowancesOption, old("allowance_cat[$i]", $allowance_cat[$i]), ['class' => 'form-control select2 ', ]) !!}
                                            </td>

                                            <td style="text-align: center; vertical-align: middle;">
                                                {!! Form::text("allowance_cost[$i]", old("allowance_cost[$i]", $allowance_cost[$i]), ['class' => 'form-control currency allowance_cost', ]) !!}
                                                <?php $totalAllowance += $allowance_cost[$i]; ?>
                                            </td>
                                            <td style="vertical-align: middle;">
                                                {!! Form::text("desc[$i]", old("desc[$i]", $desc[$i]), ['class' => 'form-control ' ]) !!}
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                @if ($i > 0)
                                                    <a href="javascript:void(0);"
                                                       class="btn btn-xs btn-default remove-expense">
                                                        <i class="text-danger fa fa-minus"></i>
                                                    </a>
                                                @else
                                                    <a href="javascript:void(0);"
                                                       class="btn btn-xs btn-default add-expense">
                                                        <i class="text-success fa fa-plus"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endfor
                                @else
                                    <tr>
                                        <td style="text-align: center; vertical-align: middle;">1</td>
                                        <td style="text-align: center; vertical-align: middle;">
                                            {!! Form::select('allowance_cat[]', ['' => trans('system.dropdown_choice')] + $allowancesOption, old('allowance_cat[]'), ['class' => 'form-control select2 ', ]) !!}
                                        </td>
                                        <td style="text-align: center; vertical-align: middle;">
                                            {!! Form::text('allowance_cost[]', old('allowance_cost[]'), ['class' => 'form-control currency allowance_cost']) !!}
                                        </td>
                                        <td style="vertical-align: middle;">
                                            {!! Form::text("desc[]", old("desc[]"), ['class' => 'form-control',]) !!}
                                        </td>
                                        <td style="text-align: center; vertical-align: middle;">
                                            <a href="javascript:void(0);" class="btn btn-xs btn-default add-expense">
                                                <i class="text-success fa fa-plus"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                            @if(Session::get('err_allowance'))
                                <ul class="alert alert-danger alert-dismissable">
                                    <li class="text-danger">{!! Session::get('err_allowance') !!}</li>
                                </ul>
                            @endif
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

    <div class="box box-default">
        <div class="box-body">
            <div class="container col-xs-12">
                <div class="row">
                    <div class="col-md-6 col-md-offset-3 text-center">
                        {!! HTML::link(route( 'admin.contracts.index' ), trans('system.action.cancel'), ['class' => 'btn btn-danger btn-flat']) !!}
                        {!! Form::submit(trans('system.action.save'), ['class' => 'btn btn-primary btn-flat']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {!! Form::close() !!}
@stop
@section('footer')
    <script src="{!! asset('assets/backend/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.vi.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/moment/locale/vi.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/select2/select2.full.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/input-mask/jquery.inputmask.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/iCheck/icheck.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/js/contract.js') !!}?v=25-02-2022"></script>

    <script>
        const TYPE_6_MONTH = {!! \App\Defines\Contract::TYPE_6_MONTH !!};
        const TYPE_1_YEAR = {!! \App\Defines\Contract::TYPE_1_YEAR !!};
        const TYPE_3_YEAR = {!! \App\Defines\Contract::TYPE_3_YEAR !!};
        const TYPE_UNLIMITED = {!! \App\Defines\Contract::TYPE_UNLIMITED !!};
        const PART_TIME = {!! \App\Defines\Staff::STATUS_PROBATIONARY !!};
        const OFFICIAL = {!! \App\Defines\Staff::STATUS_OFFICIAL !!};
        const URL_GET_DEPT = "{!! route('admin.contracts.setDepartmentOption') !!}";
        const CSRF = {'X-CSRF-Token': "{!! csrf_token() !!}"};
        let title = "{!! trans('contracts.select_first_company') !!}";
        let $currentRoute = {!! json_encode(\App\PermissionUserObject::getCurrentModule(\Route::getCurrentRoute())) !!};
        !function ($) {
            $(function () {
                callSelect2()
                callDatePicker()

                function setTotalAmount() {
                    let basicSalary = parseFloat($("input[name='basic_salary']").val().replace(/,/g, ""))
                    basicSalary = isNaN(basicSalary) ? 0 : basicSalary
                    let sumAllowance = 0;
                    $('.allowance_cost').each(function () {
                        let cost = parseFloat($(this).val().replace(/,/g, ""));
                        cost = isNaN(cost) ? 0 : cost
                        sumAllowance += cost
                    })
                    let digit = getCurrencyDigit($('.currency_code').val())
                    $('.total-allowance-cost').html(formatInputMaskDigits(sumAllowance, digit))
                    $('.total-amount').html(formatInputMaskDigits(sumAllowance+basicSalary, digit))
                }
                $(document).on('keyup', 'input[name="basic_salary"], .allowance_cost', function () {
                    setTotalAmount()
                })

                $('#departmentSelect').on('change', function (e) {
                    showSelectCurrencyByDept('{!! route('admin.departments.check-multi-currency') !!}')
                })

                if (window.File && window.FileList && window.FileReader) {
                    $("#files").on("change", function (e) {
                        var files = e.target.files,
                            filesLength = files.length;
                        console.log(files)
                        let tagShow = $('.file-show')
                        for (let i = 0; i < filesLength; i++) {
                            var f = files[i]
                            let fileName = $.trim(f.name).replace(/\s/g, '_')
                            var fileReader = new FileReader();
                            let tagFileName = $(`<li class="pip"><span>${fileName}</span> <span class="btn btn-default btn-xs remove" title="Xóa"><i class="text-danger fa fa-times"></i></span></li>`)
                            fileReader.onload = (function (e) {
                                var file = e.target;
                                tagShow.append(tagFileName)
                                $(".remove").click(function () {
                                    $(this).parent(".pip").remove();
                                });
                            });
                            fileReader.readAsDataURL(f);
                        }
                    });
                } else {
                    alert("Your browser doesn't support to File API")
                }
                $(".remove").click(function () {
                    $(this).parent(".pip").remove();
                });
                const active = {!! \App\Defines\Contract::ACTIVE !!};
                const transfer = {!! \App\Defines\Contract::TRANSFER !!};
                const appoint = {!! \App\Defines\Contract::APPOINT !!};
                const leaveWork = {!! \App\Defines\Contract::LEAVE_WORK !!};
                const dismissal = {!! \App\Defines\Contract::DISMISSAL !!};
                const endPartTime = {!! \App\Defines\Contract::END_PART_TIME !!};
                const choNgiViec = {!! \App\Defines\Contract::CHO_NGHI_VIEC !!};
                const expired = {!! \App\Defines\Contract::EXPIRED !!};
                const future = {!! \App\Defines\Contract::FUTURE !!};
                let maxDateSelect = {!! json_encode($contract->valid_to) !!};
                let minDate = {!! json_encode($contract->valid_from) !!};
                let typeStatus = {!! json_encode($contract->type_status) !!};
                let transferTo = {!! json_encode($contract->transfer_to) !!};
                let appointTo = {!! json_encode($contract->appoint_to) !!};
                maxDateSelect = maxDateSelect ? moment(maxDateSelect).format('DD/MM/YYYY') : ''
                $('.set-notvalid-date, .report-valid').datepicker({
                    format: 'dd/mm/yyyy',
                    autoclose: true,
                    todayHighlight: true,
                    language: "vi",
                    orientation: "bottom auto",
                    endDate: maxDateSelect,
                    startDate: moment(minDate).format('DD/MM/YYYY')
                });

                if ((typeStatus == transfer && transferTo) || (typeStatus == appoint && appointTo)) {
                    // $('.additional-date input').attr('readonly', true)
                    // $('select[name="type_status"]').attr('disabled', true)
                }

                function showHideAdditionalDate() {
                    let typeStatus = $('select[name="type_status"]').val()
                    /*
                    * additional-date là div cha cho các ngày mở rộng
                    * staff-submit-date-row : ngày nộp đơn thôi việc
                    * report-valid-row: ngày chấm dứt hđ
                    * */
                    $('input[name="status"]').val(0)
                    if (typeStatus == active) {
                        $('.additional-date').hide()
                        $('input[name="status"]').val(1)
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
                    } else if (typeStatus == leaveWork || typeStatus == choNgiViec) {
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
                $('select[name="type_status"]').on('change', function (e) {
                    showHideAdditionalDate()
                })

                function setDisabledContract(status, check) {
                    let tag = $('form#contract-content');
                    if (check == {!! \App\Defines\Contract::NOT_VALID !!}) {
                        tag.find('input').attr('disabled', true)
                        tag.find('select').attr('disabled', true)
                        $('textarea[name="desc_qualification"]').attr('disabled', 'disabled')
                        $('textarea[name="desc_qualification"]').prop('disabled', true);
                    } else {
                        if (check == {!! \App\Defines\Contract::IS_VALID !!}) {
                            tag.find('input:not([type="checkbox"]):not([type="submit"]):not([type="hidden"]):not([name="set_notvalid_date"]):not([name="report_valid"]):not([name="staff_submit_date"])').attr('disabled', true)
                            tag.find('select:not([name="type_status"])').attr('disabled', true)
                            tag.find('.expense a.btn').css('display', 'none')
                        }
                    }
                    $('input#files, .file-show input').attr('disabled', false)
                }

                var users;
                $("#userSelect").select2({
                    // minimumInputLength: 1,
                    // minimumResultsForSearch: 20,
                    width: '100%',
                    language: {
                        inputTooShort: function () {
                            return "Nhập tên nhân viên...";
                        }
                    },
                    ajax: {
                        url: "{!! route('admin.contracts.searchUserForSelect') !!}",
                        dataType: 'json',
                        type: "POST",
                        headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                        delay: 250,
                        data: function (params) {
                            return {
                                search: params.term || '',
                                page: params.page,
                                route: $currentRoute
                            };
                        },
                        processResults: function (res, params) {
                            users = res.data.data
                            params.page = params.page || 1;
                            return {
                                results: $.map(res.data.data, function (value) {
                                    return {
                                        text: value.code + ' - ' + value.fullname,
                                        id: value.id,
                                    }
                                }),
                                pagination: {
                                    more: (params.page * 20) < res.data.total
                                },
                            };
                        },
                        cache: true
                    }
                })

                $('.is-main').on('change', function () {
                    let company = $('.companySelect').select2('data')[0]
                    let isMain = $('.is-main').select2('data')[0]
                    setCodeContract(users, company, isMain.id)
                    if ($(this).val() == {!! \App\Defines\Staff::STATUS_PROBATIONARY !!}) {
                        $('.type').attr('disabled', true)
                        $('.type').val('').change()
                        $('.datepicker-to').attr('readonly', false)
                    } else {
                        $('.type').attr('disabled', false)
                    }
                })
                if ($('.is-main').val() == {!! \App\Defines\Staff::STATUS_PROBATIONARY !!}) {
                    $('.type').attr('disabled', true)
                    $('.type').val('').change()
                    $('.datepicker-to').attr('readonly', false)
                }
                $('.datepicker-from').on('change', function () {
                    let isMain = $('.is-main').select2('data')[0]
                    let company = $('.companySelect').select2('data')[0]
                    setCodeContract(users, company, isMain.id)
                    setDateFromTo()
                })
                $('.datepicker-to').on('change', function () {
                    let validFrom = $('.datepicker-from').val()
                    if (validFrom && this.value) {
                        if (moment(validFrom, 'DD/MM/YYY').format() >= moment(this.value, 'DD/MM/YYY').format()) {
                            toastr.warning('{!! trans('contracts.validate_valid_to') !!}', 'Thông báo')
                            $(this).val('')
                        }
                    }
                    $('.valid-to').val(this.value)
                })
                $('.type').on('change', function () {
                    let type = $(this).val()
                    let startFrom = $('.datepicker-from').val()
                    if (type == {!! \App\Defines\Contract::TYPE_UNLIMITED !!}) {
                        $('.datepicker-to').val('').datepicker('update')
                        $('.datepicker-to').attr('disabled', true)
                    }
                    if (!type) {
                        $('.datepicker-to').val('').datepicker('update')
                        $('.datepicker-to').attr('disabled', false).change()
                    }
                    if (startFrom) {
                        setDateFromTo()
                    }
                })
                if ($('.type').val() == {!! \App\Defines\Contract::TYPE_UNLIMITED !!}) {
                    $('.datepicker-to').attr('disabled', true)
                }

                function setDateFromTo() {
                    let startFrom = $('.datepicker-from').val()
                    let type = $('.type').val()
                    let new_date = '';
                    if (type == {!! \App\Defines\Contract::TYPE_6_MONTH !!}) {
                        new_date = moment(startFrom, "DD-MM-YYYY").add(6, 'M').subtract(1, 'days').format('DD-MM-YYYY')
                    } else if (type == {!! \App\Defines\Contract::TYPE_1_YEAR !!}) {
                        new_date = moment(startFrom, "DD-MM-YYYY").subtract(1, 'days').add(1, 'Y').format('DD-MM-YYYY');
                    } else if (type == {!! \App\Defines\Contract::TYPE_3_YEAR !!}) {
                        new_date = moment(startFrom, "DD-MM-YYYY").subtract(1, 'days').add(3, 'Y').format('DD-MM-YYYY');
                    } else {
                        new_date = ''
                    }
                    $('.datepicker-to').datepicker('update', new_date)
                }

                function setCodeContract(users, company, isMain) {
                    let isMainType = isMain == {!! \App\Defines\Staff::STATUS_PROBATIONARY !!} ? 'HDTV' : (isMain == {!! \App\Defines\Staff::STATUS_OFFICIAL !!} ? 'HDLD' : '')
                    let userSelected = $('#userSelect').val()
                    let validFrom = $('.datepicker-from').val()
                    if (company.id && userSelected && validFrom && isMain) {
                        var code = '{!! $contract->code !!}';
                        let d1 = moment(validFrom, 'DD/MM/YYYY')
                        let validFromF = `${d1.format('DD')}${d1.format('MM')}${d1.format('YY')}`
                        if (users) {
                            let userFilter = users.filter(e => e.id == $('#userSelect').val())
                            let codeUser = userFilter[0].code
                            code = `${validFromF}-${codeUser}-${company.text}/${isMainType}`
                        } else {
                            code = `${validFromF}-${'{!! $contract->user->code !!}'}-${company.text}/${isMainType}`
                        }
                        $('input[name="code"]').val(code)
                    } else {
                        $('input[name="code"]').val('{!! $contract->code !!}')
                    }
                }

                $(document).on('change', '.companySelect', function (e) {
                    let companyId = $(this).val();
                    setDepartmentOption(companyId)
                    let isMain = $('.is-main').select2('data')[0]
                    let company = $('.companySelect').select2('data')[0]
                    setCodeContract(users, company, isMain.id)
                })
                setDepartmentOption($('.companySelect').val())

                function setDepartmentOption(companyId) {
                    if (companyId) {
                        $('#departmentSelect').attr('disabled', false)
                        $.ajax({
                            url: "{!! route('admin.contracts.setDepartmentOption') !!}",
                            data: {companyId: companyId, route: $currentRoute},
                            type: 'POST',
                            headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                            success: function (res) {
                                $('#departmentSelect option').remove()
                                $('#departmentSelect').append('<option value="" selected>' + '{!! trans('system.dropdown_choice') !!}' + '</option>')
                                $.each(res, function (index, value) {
                                    let isSelected = oldDepartmentId == index ? 'selected' : ''
                                    $('#departmentSelect').append('<option value="' + index + '"' + isSelected + '>' + value + '</option>')
                                })
                            },
                            error: function (err) {
                                let error = $.parseJSON(err.responseText);
                                toastr.warning(error.message, "{!! trans('system.have_error') !!}")
                            }
                        })
                    } else {
                        $('#departmentSelect').attr('disabled', true)
                    }
                }

                var counterExpense = {!! count($allowance_cat) ? (count($allowance_cost)+1) : 2 !!};
                var maxAllowance = {!! count($allowancesOption) !!}
                $(".add-expense").on("click", function () {
                    if (counterExpense > maxAllowance) {
                        toastr.error('Đã vượt quá số lượng phụ cấp')
                        return false
                    }
                    var newRow = $("<tr>");
                    var cols = "";
                    cols += '<td style="text-align: center; vertical-align: middle;">' + counterExpense + '</td>';
                    cols += '<td style="text-align: center; vertical-align: middle;">';
                    cols += '{!! Form::select('allowance_cat[]', ['' => trans('system.dropdown_choice')] + $allowancesOption, old('allowance_cat[]'), ['class' => 'form-control select2 ', ]) !!}';
                    cols += '</td>';
                    cols += '<td style="text-align: center; vertical-align: middle;">';
                    cols += '{!! Form::text('allowance_cost[]', old('allowance_cost[]'), ['class' => 'form-control currency allowance_cost']) !!}';
                    cols += '</td>';
                    cols += '<td style="vertical-align: middle;">';
                    cols += '{!! Form::text("desc[]", old("desc[]"), ['class' => 'form-control ',]) !!}';
                    cols += '</td>';
                    cols += '<td style="text-align: center; vertical-align: middle;">';
                    cols += '<a href="javascript:void(0);" class="btn btn-xs btn-default remove-expense">';
                    cols += '<i class="text-danger fa fa-minus"></i>';
                    cols += '</a>';
                    cols += '</td>';

                    newRow.append(cols);
                    $("table.expense").append(newRow);
                    counterExpense++;
                    callSelect2()
                    callInputMask({digit: getCurrencyDigit($('.currency_code').val())})
                })

                $(document).on("click", ".remove-expense", function (event) {
                    $(this).closest("tr").remove();
                    counterExpense -= 1;
                    let tmp = 1;
                    $("table.expense tbody td:first-child").each(function () {
                        $(this).html(tmp++);
                    });
                    setTotalAmount()
                });
                $('.appendixes-select').select2({
                    closeOnSelect: false,
                    selectionTitleAttribute: false
                })
                var oldDepartmentId = {!! $contract->department_id !!};
                var oldPositionId = {!! $contract->position_id !!};
                $('#departmentSelect, .positionSelect').on('change', function () {
                    let departmentId = $('#departmentSelect').val()
                    let positionId = $('.positionSelect').val()
                    let userId = $("#userSelect").val()
                    if (departmentId && positionId) {
                        let nameDepartment = $('#departmentSelect').find('option[value=' + departmentId + ']').text()
                        let namePosition = $('.positionSelect').find('option[value=' + positionId + ']').text()
                        $.ajax({
                            url: "{!! route('admin.contracts.validateManager') !!}",
                            data: {
                                departmentId: departmentId,
                                positionId: positionId,
                                oldDepartmentId: oldDepartmentId,
                                oldPositionId: oldPositionId,
                                userId: userId
                            },
                            type: 'POST',
                            headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                            success: function (res) {
                                if (!res) {
                                    toastr.warning('Phòng ' + nameDepartment + ' đã có chức vụ ' + namePosition, '{!! trans('system.info') !!}')
                                    {{--$('.positionSelect').val('{!! $contract->position_id !!}').change()--}}
                                        return false
                                }
                            },
                            error: function (data) {
                                return false
                            }
                        })
                    }
                })
                let contractId = {!! $contract->id !!};
                let oldStaffId = {!! json_encode($contract->user->id) !!};
                let oldStaffF = '{!! json_encode($contract->user->code) !!}' + ' - ' + '{!! json_encode($contract->user->fullname) !!}';

                function checkStaffHasContract(staffId, contractId = null) {
                    $.ajax({
                        url: "{!! route('admin.contracts.checkStaffHasContract') !!}",
                        data: {staffId: staffId, contractId: contractId},
                        type: 'POST',
                        headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                        success: function (res) {
                            if (res.data) {
                                toastr.warning(res.mess, '{!! trans('system.info') !!}')
                                $('#userSelect option').remove()
                                $('#userSelect').append('<option value=' + oldStaffId + '>' + oldStaffF + '</option>')
                            }
                        },
                        error: function (data) {
                            console.log(data)
                        }
                    })
                }

                $('#userSelect').on('change', function () {
                    let staffId = $(this).val()
                    checkStaffHasContract(staffId, contractId)
                    let company = $('.companySelect').select2('data')[0]
                    setCodeContract(users, company)
                })

                function setStatusFollowTypeStatus() {
                    $(document).on('change', '.type_status', function (e) {
                        let typeStatus = this.value
                        let status = typeStatus == {!! \App\Defines\Contract::ACTIVE !!} ? 1 : 0
                        $('input[name="status"]').val(status)
                    })
                }

                setStatusFollowTypeStatus();

                function setDescQualificationDefault() {
                    let q = {!! json_encode(\App\Qualification::pluck('description', 'id')) !!};
                    let qSelect = $('select[name="qualification_id"]').val()
                    if (qSelect && q[qSelect]) {
                        $('.desc_qualification').val(q[qSelect])
                    } else {
                        $('.desc_qualification').val('')
                    }
                }
                $(document).on('change', 'select[name="qualification_id"]', function () {
                    setDescQualificationDefault()
                })
                setDescQualificationDefault()
            });
        }(window.jQuery);
    </script>
@stop