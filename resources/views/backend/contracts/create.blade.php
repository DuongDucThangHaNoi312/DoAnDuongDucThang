@extends('backend.master')
@section('title')
    {!! trans('system.action.create') !!} - {!! trans('contracts.label') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/>
    <link rel="stylesheet" type="text/css"
          href="{!! asset('assets/backend/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>

    <style>
        /*table.allowances {*/
        /*    text-align: center;*/
        /*    vertical-align: center;*/
        /*}*/
        td {
            vertical-align: center;
            text-align: center;
        }

        th {
            vertical-align: center;
            text-align: center;
        }

        .contract-content .row {
            margin: 15px -15px;
        }

        .ui-datepicker.ui-datepicker-inline {
            width: 100% !important;
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
            <small>{!! trans('system.action.create') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.contracts.index') !!}">{!! trans('contracts.label') !!}</a></li>
        </ol>
    </section>
    @if(Session::get('err_try_catch'))
        <div class="alert alert-warning alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-warning"></i> {!! trans('messages.error') !!}</h4>
            <ul>
                <li>{!! Session::get('err_try_catch') !!}</li>
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

    {!! Form::open(['url' => route('admin.contracts.store'), 'role' => 'form', 'enctype'=>'multipart/form-data']) !!}
    {!! Form::hidden('old_contract', '') !!}
    <div class="container col-md-12 contract-content">
        <div class="row">
            <label class="col col-md-2">{!! trans('contracts.staff_id') !!}</label>
            <div class="col col-md-4">
                {!! Form::select('user_id', ['' => trans('system.dropdown_choice')], old('user_id'), ['id' =>'userSelect', 'class' => 'form-control select2 staffSelect', 'required']) !!}
                @if(Session::get('err_staff_contract'))
                    <p class="text-danger">{!! Session::get('validate_staff_contract') !!}</p>
                @endif
            </div>
            <label class="col col-md-2">{!! trans('contracts.company_id') !!}</label>
            <div class="col col-md-4">
                {!! Form::select('company_id', ['' => trans('system.dropdown_choice')] + \App\Helpers\GetOption::getCompaniesForOption(),old('company_id'), ['class' => 'form-control select2 companySelect', 'required']) !!}
            </div>
        </div>
        <div class="row">

            <label class="col col-md-2">{!! trans('contracts.department_id') !!}</label>
            <div class="col col-md-4" id="department-tooltip" data-toggle="tooltip" data-placement="bottom" title="">
                {!! Form::select('department_id', ['' => trans('system.dropdown_choice')], old('department_id'), ['disabled' => true, 'id' => 'departmentSelect', 'class' => 'form-control select2', 'required']) !!}
            </div>
            <label class="col col-md-2">{!! trans('contracts.position_id') !!}</label>
            <div class="col col-md-4">
                {!! Form::select('position_id', ['' => trans('system.dropdown_choice')] + \App\Helpers\GetOption::getStaffPositionsForOption(), old('position_id'), ['class' => 'form-control select2 positionSelect', 'required']) !!}
            </div>
        </div>
        <div class="row">
            <label class="col col-md-2">{!! trans('contracts.title_id') !!}</label>
            <div class="col col-md-4">
                {!! Form::select('qualification_id', ['' => trans('system.dropdown_choice')] + \App\Helpers\GetOption::getStaffTitlesForOption(), old('qualification_id'), ['class' => 'form-control select2', 'required']) !!}
            </div>
            <label class="col col-md-2">{!! trans('contracts.desc_qualification') !!}</label>
            <div class="col-md-4">
                {!! Form::textarea('desc_qualification', old('desc_qualification'), ['class' => 'form-control desc_qualification', 'rows' => 1]) !!}
            </div>
        </div>
        <div class="row">
            <label class="col col-md-2">{!! trans('contracts.is_main') !!}</label>
            <div class="col col-md-4">
                {!! Form::select('is_main', ['' => trans('system.dropdown_choice')] + \App\Defines\Staff::getStatusForOptionContract(), old('is_main'), ['class' => 'form-control select2 is-main', 'required' ]) !!}
            </div>
            <label class="col col-md-2">{!! trans('contracts.type') !!}</label>
            <div class="col col-md-4">
                {!! Form::select('type', ['' => trans('system.dropdown_choice')] + App\Defines\Contract::getTypesForOption(), old('type'), ['class' => 'form-control select2 type']) !!}
            </div>
        </div>
        <div class="row">
            <label class="col col-md-2">{!! trans('contracts.basic_salary') !!}</label>
            <div class="col col-md-4">
                {{--<div class="input-group">
                    {!! Form::text('basic_salary',old('basic_salary'), ["class" => "form-control",]) !!}
                    <div class="input-group-addon price-addon">VNĐ</div>
                </div>--}}
                <div class="input-group" style="display: inline-flex; width: 100%">
                    <div class="div_select_currency" style="display: none">
                        {!! Form::select('currency_code', \App\Defines\Contract::getCurrencyOptions(), old('currency_code', \App\Defines\Contract::VND), ['class' => 'form-control select2 currency_code',]) !!}
                    </div>
                    <div style="width: 100%">
                        {!! Form::text('basic_salary', old('basic_salary'), ["class" => "form-control currency", 'autocomplete' => 'off',]) !!}
                    </div>
                </div>
            </div>
            <label class="col col-md-2">{!! trans('contracts.valid_from') !!}</label>
            <div class="col col-md-4">
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    {!! Form::text('valid_from', Request::input('valid_from'), ['class' => 'form-control datepicker datepicker-from', 'required', 'autocomplete' => 'off']) !!}
                </div>
            </div>
        </div>
        <div class="row">
            <label class="col col-md-2">{!! trans('contracts.code') !!}</label>
            <div class="col col-md-4">
                {!! Form::text('code', old('code'), ['class' => 'form-control', 'maxlength' => 50,  ]) !!}
            </div>
            <label class="col col-md-2">{!! trans('contracts.valid_to') !!}</label>
            <div class="col col-md-4">
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    {!! Form::text('valid_to1', Request::input('valid_to1'), ['class' => 'form-control datepicker datepicker-to', 'autocomplete' => 'off', 'disabled']) !!}
                    {!! Form::hidden('valid_to', '', ['class' => 'valid-to']) !!}
                </div>
            </div>
        </div>
        <div class="row">
            <label class="col col-md-2" style="margin-top: 2px">Tệp đính kèm</label>
            <div class="col col-md-4">
                {!! Form::file('file[]', ['id' => 'files', 'multiple' => true]) !!}
                @if ($errors->get('file.*'))
                    <div class="col-md-offset-2 col-md-10">
                        <span class="text-danger">{{ $errors->first('file.*') }}</span>
                    </div>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-md-offset-2 col-md-10 file-show">
            </div>
        </div>
        {{--        <div class="row">--}}
        {{--            <div colspan="4" class="text-center">--}}
        {{--                {!! Form::checkbox('status', 1, old('status', 1), [ 'class' => 'minimal-red' ]) !!}--}}
        {{--                {!! trans('system.status.valid') !!}--}}
        {{--            </div>--}}
        {{--        </div>--}}
    </div>
    <div class="row allowance-row" style="margin: 0">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-header with-border">
                    <i class="fas fa-money-bill-alt"></i>
                    <h3 class="box-title">{!! trans('contracts.table_allowances') !!} (Nếu không có thì để trống 3 trường)</h3>
                </div>
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
								<?php $allowance_cat = old('allowance_cat', []); $totalAmount = $totalAdvanced = 0; ?>
                                @if (count($allowance_cat))
									<?php
									$desc = old('desc', []);
									$allowance_cost = old('allowance_cost', []);
									?>
                                    @for ($i = 0; $i < count($allowance_cat); $i++)
                                        <tr>
                                            <td style="text-align: center; vertical-align: middle;">{!! $i+1 !!}</td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                {!! Form::select("allowance_cat[$i]", ['' => trans('system.dropdown_choice')] + $allowancesOption, old("allowance_cat[$i]", $allowance_cat[$i]), ['class' => 'form-control select2 ', ]) !!}
                                            </td>

                                            <td style="text-align: center; vertical-align: middle;">
                                                {!! Form::text("allowance_cost[$i]", old("allowance_cost[$i]", $allowance_cost[$i]), ['class' => 'form-control currency allowance_cost']) !!}
                                            </td>
                                            <td style="vertical-align: middle;">
                                                {!! Form::text("desc[$i]", old("desc[$i]", $desc[$i]), ['class' => 'form-control ']) !!}
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
                                            {!! Form::select('allowance_cat[]', ['' => trans('system.dropdown_choice')] + $allowancesOption, old('allowance_cat[]'), ['class' => 'form-control select2 appendix_allowance_cat_new', ]) !!}
                                        </td>
                                        <td style="text-align: center; vertical-align: middle;">
                                            {!! Form::text('allowance_cost[]', old('allowance_cost[]'), ['class' => 'form-control currency appendix_allowance_cost_new allowance_cost']) !!}
                                        </td>
                                        <td style="vertical-align: middle;">
                                            {!! Form::text("desc[]", old("desc[]"), ['class' => 'form-control']) !!}
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
                            <th class="total-allowance-cost text-right">0</th>
                        </tr>
                        <tr style="font-size: 17px;">
                            <th style="width: 5%"></th>
                            <td style="text-align: left; font-weight: 500;">Tổng tiền lương + phụ cấp:&nbsp;&nbsp;&nbsp; </td>
                            <th class="total-amount text-right">0</th>
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
    <script src="{!! asset('assets/backend/js/contract.js') !!}?v=22-04-2022"></script>

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
        const _VND = @json(\App\Defines\Contract::VND);
        const _USD = @json(\App\Defines\Contract::USD);
        let urlCheckMultiCurrency = '{!! route('admin.departments.check-multi-currency') !!}';
        !function ($) {
            $(function () {
                // callInputMaskDecimal()
                // inputMarkSalary()
                callDatePicker()
                callSelect2()

                // Tính tổng lương và phụ cấp
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

                var codeUser = ''

                //File
                if (window.File && window.FileList && window.FileReader) {
                    $("#files").on("change", function (e) {
                        var files = e.target.files,
                            filesLength = files.length;
                        let tagShow = $('.file-show')
                        for (var i = 0; i < filesLength; i++) {
                            var f = files[i]
                            let fileName = $.trim(f.name).replace(/\s/g, '_')
                            let fileSize = Math.round(+f.size / 1024)
                            var fileReader = new FileReader();
                            let tagFileName = $(`<li class="pip"><span>${fileName} | ${fileSize}KB </span> <span class="btn btn-default btn-xs remove" title="Xóa"><i class="text-danger fa fa-times"></i></span></li>`)
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

                var users;

                //Tìm kiếm nhân viên
                function searchUser() {
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
                                        more: (params.page * 20) < res.data.total,
                                    },
                                };
                            },
                            cache: true
                        }
                    })
                }
                searchUser()

                //Tạo hợp đồng ngay sau khi tạo nhân viên
                var userFromCreatStaff;
                if ({!! json_encode(Session::get('user')) !!}) {
                    let user = {!! json_encode(Session::get('user')) !!}
                    $('#userSelect option').remove()
                    $('#userSelect').append('<option value=' + user[0] + '>' + user[1] + '</option>')
                    userFromCreatStaff = user
                }

                $('.appendixes-select').select2({
                    closeOnSelect: false,
                    selectionTitleAttribute: false
                })
                $('#department-tooltip').tooltip({
                    // title: title
                }).tooltip('show');
                $('.select2-selection__rendered').hover(function () {
                    $(this).removeAttr('title');
                });

                // Thay đổi code hợp đồng, disable/not thời hạn hợp đồng khi thay đổi loại hợp đồng
                $('.is-main').on('change', function () {
                    let company = $('.companySelect').select2('data')[0]
                    let isMain = $('.is-main').select2('data')[0]
                    setCodeContract(users, company, isMain.id, PART_TIME, OFFICIAL, userFromCreatStaff)
                    setDisabledTypeFromIsMain(this.value, PART_TIME)
                })
                if ($('.is-main').val() == PART_TIME) {
                    $('.type').val('').change()
                    $('.type').attr('disabled', true)
                    $('.datepicker-to').attr('disabled', false)

                }
                // Check thay đổi các ngày hiệu lực
                // Code hđ thay đổi khi thay đổi hiệu lực đến
                $('.datepicker-from').on('change', function () {
                    setDateFromTo(TYPE_6_MONTH, TYPE_1_YEAR, TYPE_3_YEAR, TYPE_UNLIMITED)
                    let company = $('.companySelect').select2('data')[0]
                    let isMain = $('.is-main').select2('data')[0]
                    setCodeContract(users, company, isMain.id, PART_TIME, OFFICIAL, userFromCreatStaff)
                })
                $('.datepicker-to').on('change', function () {
                    let validFrom = $('.datepicker-from').val()
                    if (validFrom && this.value) {
                        if (moment(validFrom, 'DD/MM/YYY').format() >= moment(this.value, 'DD/MM/YYY').format()) {
                            toastr.warning('{!! trans('contracts.validate_valid_to') !!}', 'Thông báo')
                            $(this).val('')
                            return false
                        }
                    }
                    $('.valid-to').val(this.value)
                })
                if ($('.datepicker-to').val()) {
                    $('.valid-to').val($('.datepicker-to').val())
                }

                //Check thay đổi thời hạn hđ -> thay đổi hiệu lực đến
                $('.type').on('change', function () {
                    setDateFromTo(TYPE_6_MONTH, TYPE_1_YEAR, TYPE_3_YEAR, TYPE_UNLIMITED)
                })

                //Các hàm thêm, xóa dòng phụ cấp
                var counterExpense = {!! count($allowance_cat) ? (count($allowance_cost)+1) : 2 !!};
                var maxAllowance = {!! count($allowancesOption) !!};

                function addAllowance(counterExpense, maxAllowance) {
                    $(".add-expense").on("click", function () {
                        if (counterExpense > maxAllowance) {
                            toastr.error('Đã vượt quá số lượng phụ cấp')
                            return false
                        }
                        var newRow = $("<tr>");
                        var cols = "";
                        cols += '<td style="text-align: center; vertical-align: middle;">' + counterExpense + '</td>';
                        cols += '<td style="text-align: center; vertical-align: middle;">';
                        cols += '{!! Form::select('allowance_cat[]', ['' => trans('system.dropdown_choice')] + $allowancesOption, old('allowance_cat[]'), ['class' => 'form-control select2' ]) !!}';
                        cols += '</td>';
                        cols += '<td style="text-align: center; vertical-align: middle;">';
                        cols += '{!! Form::text('allowance_cost[]', old('allowance_cost[]'), ['class' => 'form-control currency allowance_cost']) !!}';
                        cols += '</td>';
                        cols += '<td style="vertical-align: middle;">';
                        cols += '{!! Form::text("desc[]", old("desc[]"), ['class' => 'form-control']) !!}';
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
                }
                addAllowance(counterExpense, maxAllowance)
                function removeAllowance() {
                    $(document).on("click", ".remove-expense", function (event) {
                        $(this).closest("tr").remove();
                        counterExpense -= 1;
                        let tmp = 1;
                        $("table.expense tbody td:first-child").each(function () {
                            $(this).html(tmp++);
                        });
                        setTotalAmount()
                    });
                }
                removeAllowance()

                // var contractOldTransfer = 0;
                var oldDepartmentId = {!! old('department_id') ?? 0 !!};
                let $currentRoute = {!! json_encode(\App\PermissionUserObject::getCurrentModule(\Route::getCurrentRoute())) !!};
                // oldDepartmentId = oldDepartmentId ? oldDepartmentId : contractOldTransfer

                if (!$('.companySelect').val()) {
                    $('#department-tooltip').attr('title', title).tooltip('show')
                }

                $(document).on('change', '.companySelect', function (e) {
                    setDepartmentOption(URL_GET_DEPT, oldDepartmentId, CSRF, title, $currentRoute)
                    let company = $(this).select2('data')[0]
                    let isMain = $('.is-main').select2('data')[0]
                    setCodeContract(users, company, isMain.id, PART_TIME, OFFICIAL, userFromCreatStaff)
                })
                if ($('.companySelect').val()) {
                    $('#departmentSelect').attr('disabled', false)
                    setDepartmentOption(URL_GET_DEPT, oldDepartmentId, CSRF, title, $currentRoute)
                }

                //Check trùng chức vụ trong phòng ban
                function validateManager() {
                    $('#departmentSelect, .positionSelect').on('change', function () {
                        let departmentId = $('#departmentSelect').val()
                        let positionId = $('.positionSelect').val()
                        if (departmentId && positionId) {
                            let nameDepartment = $('#departmentSelect').find('option[value=' + departmentId + ']').text()
                            let namePosition = $('.positionSelect').find('option[value=' + positionId + ']').text()
                            $.ajax({
                                url: "{!! route('admin.contracts.validateManager') !!}",
                                data: {departmentId: departmentId, positionId: positionId},
                                type: 'POST',
                                headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                                success: function (res) {
                                    if (!res) {
                                        toastr.warning('Phòng ' + nameDepartment + ' đã có chức vụ ' + namePosition, '{!! trans('system.info') !!}')
                                        $('.positionSelect').val('').change()
                                        return false
                                    }
                                },
                                error: function (data) {
                                    return false
                                }
                            })
                        }
                    })
                }
                validateManager()

                var isChangeAllowance = 0; //Kiem tra lay phu cap theo phong ban hoac theo hop dong cu (dieu chuyen or bo nhiem)
                //Them chuc nang lay du lieu hop cu trong truong hop dieu chuyen or bo nhiem
                function checkStaffHasContract(staffId, oldStaffId = null) {
                    $.ajax({
                        url: "{!! route('admin.contracts.checkStaffHasContract') !!}",
                        data: {staffId: staffId},
                        type: 'POST',
                        headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                        success: function (res) {
                            if (res.data) {
                                if (res.template) {
                                    isChangeAllowance = 1;
                                    oldDepartmentId = res.contract['department_id']
                                    $('input[name="old_contract"]').val(res.contract['id'])
                                    setValueOldContract(res.contract)
                                    $('.allowance-row .box-body').html('').append(res.template)
                                    callSelect2()
                                    addAllowance(res.count + 1, maxAllowance)
                                    removeAllowance()
                                    $(".currency").inputmask({
                                        'alias': 'decimal',
                                        'groupSeparator': ',',
                                        'autoGroup': true,
                                        'max': 999999999.99,
                                        'removeMaskOnSubmit': true
                                    });
                                    searchUser()
                                    setTotalAmount()
                                } else {
                                    toastr.warning(res.mess, '{!! trans('system.info') !!}')
                                    $('#userSelect').append('<option value="">' + 'Chọn 1 mục' + '</option>')
                                    $('#userSelect').val('').change()
                                }
                            }
                        },
                        error: function (data) {
                            console.log(data)
                        }
                    })
                }

                $('#userSelect').on('change', function () {
                    if (users) {
                        let userFilter = users.filter(e => e.id == $('#userSelect').val())
                        let codeUser = userFilter.length ? userFilter[0].code : ''
                        if (codeUser) sessionStorage.setItem('codeUser', codeUser)
                    }
                    let company = $('.companySelect').select2('data')[0]
                    let staffId = $(this).val()
                    let isMain = $('.is-main').select2('data')[0]
                    checkStaffHasContract(staffId)
                    setCodeContract(users, company, isMain.id, PART_TIME, OFFICIAL, userFromCreatStaff)
                })

                function setValueOldContract(contract) {
                    $('.contract-content select:not([name="user_id"]):not([name="department_id"])').each(function (e) {
                        let name = $(this).attr('name')
                        $(this).val(contract[name]).change()
                    })
                    $('input[name="desc_qualification"]').val(contract['desc_qualification'])
                    $('input[name="basic_salary"]').val(contract['basic_salary'])
                    let validFrom = moment(contract['valid_from']).format('DD/MM/YYYY')
                    let notValidDate = contract['set_notvalid_on'] ? moment(contract['set_notvalid_on']).format('DD/MM/YYYY') : validFrom
                    $(".datepicker-from").datepicker({
                        format: 'dd/mm/yyyy',
                        autoclose: true,
                        todayHighlight: true,
                        language: "vi",
                    }).datepicker("update", notValidDate);
                }

                var oldUser = {!! old('user_id') ?? 0 !!};
                if (oldUser > 0) {
                    $.ajax({
                        url: "{!! route('admin.contracts.setOldUser') !!}",
                        data: {userId: oldUser},
                        type: 'POST',
                        headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                        success: function (res) {
                            $('#userSelect option').remove()
                            $('#userSelect').append('<option value=' + res.id + '>' + res.code + ' - ' + res.fullname + '</option>')
                        },
                        error: function (err) {
                            let error = $.parseJSON(err.responseText);
                            toastr.warning(error.message, "{!! trans('system.have_error') !!}")
                        }
                    })
                    // $('.staffSelect').val(oldStaff).change()
                }

                //Hiện thị danh sách phụ cấp theo phòng ban
                var oldAllowances = {!! json_encode(old('allowance_cat')) !!};
                function setAllowanceDefault() {
                    let deptId = $('#departmentSelect').val()
                    if (deptId && !isChangeAllowance && !oldAllowances) {
                        $.ajax({
                            url: "{!! route('admin.contracts.setAllowanceDefault') !!}",
                            data: {deptId: deptId},
                            type: 'POST',
                            headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                            success: function (res) {
                                $('.allowance-row .box-body').html('').append(res.template)
                                $(".select2").select2({
                                    width: '100%',
                                });
                                counterExpense = res.count
                                addAllowance(res.count + 1, maxAllowance)
                                removeAllowance()
                                callInputMask({digit: getCurrencyDigit($('.currency_code').val())})
                                setTotalAmount()
                            },
                            error: function (err) {
                                let error = $.parseJSON(err.responseText);
                                toastr.warning(error.message, "{!! trans('system.have_error') !!}")
                            }
                        })
                    }
                }
                $('#departmentSelect').on('change', function (e) {
                    setAllowanceDefault()
                    showSelectCurrencyByDept('{!! route('admin.departments.check-multi-currency') !!}')
                })

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