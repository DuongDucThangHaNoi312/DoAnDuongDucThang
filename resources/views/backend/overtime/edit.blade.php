@extends('backend.master')
@section('title')
    {!! trans('system.action.edit') !!} - {!! trans('overtimes.label') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
    <link rel="stylesheet" type="text/css"
          href="{!! asset('assets/backend/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') !!}"/>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">

@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('overtimes.label') !!}
            <small>{!! trans('system.action.edit') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.overtimes.index') !!}">{!! trans('overtimes.label') !!}</a></li>
        </ol>
    </section>

    <div class="">
        <!-- /.box-header -->
        <div class="box-body card card-default" style="padding: 40px 130px;">
            {!! Form::open(['url' => route('admin.overtimes.update',$overtime->id), 'role' => 'form','method'=>'PUT']) !!}

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>{!! trans('overtimes.company_id') !!} <span style="color: red">*</span></label>
                        {!! Form::select('company_id',$companysOption ? ['' =>  trans('overtimes.choose_company')] + $companysOption  : ['' => trans('overtimes.choose_company')] + \App\Define\OverTime::getCompanyNamesForOption(), old('company_id',$overtime->company_id), ['class' => 'form-control select2 companySelect', Auth::user()->hasRole('LEADER') ? 'disabled' : '']) !!}
                    </div>
                    <div class="form-group">
                        <label>{!! trans('overtimes.department_id') !!}<span style="color: red">*</span></label>
                        {!! Form::select('department_id', $departmentsOption ?? $departmentOption ,old('department_id',$overtime->department_id), ['class' => 'form-control select2', 'required', 'id' => 'departmentSelect', Auth::user()->hasRole('LEADER') ? 'disabled' : '']) !!}
                    </div>
                    <div class="form-group">
                        <label>{!! trans('overtimes.shifts') !!}<span style="color: red">*</span></label>
                        {!! Form::select('shifts', $shiftsOption, old('shifts',$overtime->shifts), ['class' => 'form-control select2', 'required', 'id' => 'shiftSelect']) !!}
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="start_date">{!! trans('overtimes.start_date') !!} <span
                                            style="color: red">*</span></label>
                                <div class='input-group'>
                                    {!! Form::text('start_date', old('start_date',$overtime->start_date->format('d/m/Y')), ['class' => 'form-control datepicker start_date','id'=>'start_date' ,'placeholder'=>trans('overtimes.start_date_placeholder'),'autocomplete'=>'off']) !!}
                                    <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                          </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="end_date">{!! trans('overtimes.end_date') !!} <span
                                                    style="color: red">*</span></label>
                                    </div>
                                </div>
                                <div class='input-group'>
                                    {!! Form::text('end_date', old('end_date',$overtime->end_date ? $overtime->end_date->format('d/m/Y') : ''), ['class' => 'form-control datepicker','id'=>'end_date' ,'placeholder'=>trans('overtimes.end_date_placeholder'),'autocomplete'=>'off']) !!}
                                    <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <label for="radio-type">{!! trans('overtimes.limit') !!}<span style="color: red">*</span> :
                    </label>
                    <div class="radio">
                        @if (!Auth::user()->hasRole('LEADER'))
                        <label class="type-option">
                            {!! Form::radio('display_with_type', '1', old('display_with_type',$overtime->display_with_type) == '1' ? 'checked' : '',  ['id' => 'display_with_type']) !!}
                            {!! trans('overtimes.all_department') !!}</label>
                        </label>
                        @endif
                        
                    </div>
                    <div class="radio">
                        <label class="type-option">
                            {!! Form::radio('display_with_type', '2', old('display_with_type',$overtime->display_with_type) == '2' ? 'checked' : '',  ['id' => 'display_with_type']) !!}
                            {!! trans('overtimes.user/users') !!}</label>
                        </label>
                        {!! Form::select('display_with_data[]', $userOption, old('display_with_data[]',json_decode($overtime->display_with_data)), ['class' => 'form-control select2 display_with_data user_id',$overtime->display_with_type == 1 ?'disabled' : '','multiple', 'required']) !!}
                    </div>
                    @if (!Auth::user()->hasRole('LEADER'))
                    <div class="form-group">
                        <label for="hidden_with_users">{!! trans('overtimes.except') !!}</label>
                        {!! Form::select('hidden_with_users[]', $userOption, old('hidden_with_users[]',json_decode($overtime->hidden_with_users)), ['class' => 'form-control select2 hidden_with_users',$overtime->display_with_type == 2 ?'disabled' : '','multiple', ]) !!}
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>{!! trans('overtimes.hours') !!} <span
                                            style="color: red">*</span></label>
                                <div class='input-group'>
                                    {!! Form::number('overtime_hours', old('overtime_hours',$overtime->overtime_hours), ['class' => 'form-control ','id'=>'overtime_hours' ,'placeholder'=>trans('overtimes.hours_placeholder'),'style'=>'text-align:right','min'=>1]) !!}
                                    <span class="input-group-addon">
                                           {!! trans('overtimes.hour') !!}
                                          </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="input-validate">
                                    <label class="status">
                                        @if($overtime->status == 1)
                                            {!! Form::checkbox('status', 1, old('status',1), [ 'class' => 'minimal status','id'=>'status' ]) !!}
                                        @else
                                            {!! Form::checkbox('status', 1, old('status',0), [ 'class' => 'minimal status','id'=>'status' ]) !!}

                                        @endif
                                        <span class="every-week"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-md-offset-3 text-center">
                {!! HTML::link(route( 'admin.overtimes.index' ), trans('system.action.cancel'), ['class' => 'btn btn-danger btn-flat']) !!}
                {!! Form::submit(trans('system.action.save'), ['class' => 'btn btn-primary btn-flat']) !!}
            </div>
        </div>
    </div>
    {!! Form::close() !!}

@stop
@section('footer')
    <script src="{!! asset('assets/backend/plugins/iCheck/icheck.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/select2/select2.full.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/moment/min/moment-with-locales.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/input-mask/jquery.inputmask.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.vi.min.js') !!}"></script>

    <script>
        !function ($) {
            $(function () {
                $('#start_date').datepicker({
                    format: 'dd/mm/yyyy',
                    autoclose: true,
                    language: 'vi'
                });
                $('#end_date').datepicker({
                    format: 'dd/mm/yyyy',
                    useCurrent: false,
                    language: 'vi',
                    autoclose: true,
                })

                $(".select2").select2({
                    width: '100%',
                    placeholder: ' {!! trans('system.dropdown_choice') !!} '
                });
                $('input[type="checkbox"].minimal').iCheck({
                    checkboxClass: 'icheckbox_minimal-red'
                });


                $("#start_date").on("change", function () {
                    var minDate = $("#start_date").datepicker("getDate");
                    minDate.setDate(minDate.getDate() + 1)
                    $('input[type=checkbox]:checked').iCheck('uncheck')
                    $("#end_date").val(null).prop('disabled', false)

                    var dateStart = minDate.getDate()
                    var monthStart = minDate.getMonth()
                    var yearStart = minDate.getFullYear()
                    var lastDay = new Date(yearStart, monthStart, 25)
                    dateStart > 25 ? $('#end_date').datepicker('setEndDate', (moment(lastDay).add('1', 'months')).format("DD/MM/YYYY")) : $('#end_date').datepicker('setEndDate', moment(lastDay).format("DD/MM/YYYY"))

                    let departmentId = $('#departmentSelect').val();
                    let startDate = $('#start_date').val();
                    let shiftSelect = $('#shiftSelect').val();
                    if (departmentId && startDate && shiftSelect != 'Chọn ca/kíp') {

                        $('label.status').hide()
                        $.ajax({
                            url: "{!! route('admin.overtimes.setEndDate') !!}",
                            data: {
                                departmentId: departmentId,
                                startDate: startDate
                            },
                            type: 'POST',
                            headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                            success: function (res) {
                                if (res) {
                                    $('#end_date').datepicker('setEndDate', new Date(res))
                                } else {
                                    $('#end_date').datepicker('setStartDate', $('#start_date').datepicker('getDate'))
                                }
                            },
                            error: function (data) {
                                console.log(data)
                            }
                        })
                    }
                    let date = $("#start_date").datepicker("getDate").getDay();
                    date == '1' ? $('.every-week').html('{!! trans('overtimes.mo') !!}') : '';
                    date == '2' ? $('.every-week').html('{!! trans('overtimes.tu') !!}') : '';
                    date == '3' ? $('.every-week').html('{!! trans('overtimes.we') !!}') : '';
                    date == '4' ? $('.every-week').html('{!! trans('overtimes.th') !!}') : '';
                    date == '5' ? $('.every-week').html('{!! trans('overtimes.fr') !!}') : '';
                    date == '6' ? $('.every-week').html('{!! trans('overtimes.st') !!}') : '';
                    date == '0' ? $('.every-week').html('{!! trans('overtimes.sn') !!}') : '';


                });
                if ($("#start_date").val()) {
                    var minDate = $("#start_date").datepicker("getDate");
                    minDate.setDate(minDate.getDate() + 1)
                    var dateStart = minDate.getDate()
                    var monthStart = minDate.getMonth()
                    var yearStart = minDate.getFullYear()
                    var lastDay = new Date(yearStart, monthStart, 25)
                    dateStart > 25 ? $('#end_date').datepicker('setEndDate', (moment(lastDay).add('1', 'months')).format("DD/MM/YYYY")) : $('#end_date').datepicker('setEndDate', moment(lastDay).format("DD/MM/YYYY"))

                    $('.timepicker').prop('disabled', false)
                    $("#end_date").prop('disabled', false)
                    let departmentId = $('#departmentSelect').val();
                    let startDate = $('#start_date').val();
                    let shiftSelect = $('#shiftSelect').val();
                    if (departmentId && startDate && shiftSelect != 'Chọn ca/kíp') {
                        $.ajax({
                            url: "{!! route('admin.overtimes.setEndDate') !!}",
                            data: {
                                departmentId: departmentId,
                                startDate: startDate
                            },
                            type: 'POST',
                            headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                            success: function (res) {
                                if (res) {
                                    $('#end_date').datepicker('setEndDate', new Date(res))
                                } else {
                                    $('#end_date').datepicker('setStartDate', $('#start_date').datepicker('getDate'))


                                }
                            },
                            error: function (data) {
                                console.log(data)
                            }
                        })
                    }
                    let date = $("#start_date").datepicker("getDate").getDay();

                    date == '1' ? $('.every-week').html('{!! trans('overtimes.mo') !!}') : '';
                    date == '2' ? $('.every-week').html('{!! trans('overtimes.tu') !!}') : '';
                    date == '3' ? $('.every-week').html('{!! trans('overtimes.we') !!}') : '';
                    date == '4' ? $('.every-week').html('{!! trans('overtimes.th') !!}') : '';
                    date == '5' ? $('.every-week').html('{!! trans('overtimes.fr') !!}') : '';
                    date == '6' ? $('.every-week').html('{!! trans('overtimes.st') !!}') : '';
                    date == '0' ? $('.every-week').html('{!! trans('overtimes.sn') !!}') : '';
                }
                $("input#end_date").on("change", function () {
                    $(this).val() ? $('label.status').addClass('hidden') && $('input[type=checkbox]').prop('disabled', true) : $('label.status').removeClass('hidden') && $('input[type=checkbox]').prop('disabled', false)
                })
            });

        }(window.jQuery);

        $('input[type=checkbox][name=status]').on('ifChecked', function (event) {
            var dateStart = $("#start_date").datepicker("getDate").getDate()
            var monthStart = $("#start_date").datepicker("getDate").getMonth()
            var yearStart = $("#start_date").datepicker("getDate").getFullYear()
            var date = new Date(yearStart, monthStart, 25)
            dateStart > 25 ? $("#end_date").val(moment(date).add('1', 'months').format("DD/MM/YYYY")) : $("#end_date").val(moment(date).format("DD/MM/YYYY"))
            $("#end_date").prop('disabled', true)
        });
        $('input[type=checkbox][name=status]').on('ifUnchecked', function (event) {
            $("#end_date").prop('disabled', false)
        });

        $('input[type=radio][name=display_with_type]').change(function () {
            let val = $(this).val();
            val == '1' ? $('.hidden_with_users').prop('disabled', false) && expectUserOption() : $('.hidden_with_users').prop('disabled', true);
            val == '2' ? $('.hidden_with_users').val(null).trigger('change.select2') && $('.display_with_data').prop('disabled', false) && setUserOptionForShift() : $('.display_with_data').prop('disabled', true);

        });
        $('input[type=radio][name=display_with_type]:checked').val() == '1' ? $('.hidden_with_users').prop('disabled', false) : $('.hidden_with_users').prop('disabled', true);
        $('input[type=radio][name=display_with_type]:checked').val() == '2' ? $('.display_with_data').prop('disabled', false) && $('.hidden_with_users').val(null).trigger('change.select2') : $('.display_with_data').prop('disabled', true);

        var title = 'Vui lòng chọn công ty trước'
        $('#department-tooltip').tooltip({
            // title: title
        }).tooltip('show');

        var oldDepartmentId = {!! old('department_id') ?? 0 !!};
        var departmentOption = {!! json_encode($department_group)  !!}
        function setDepartmentOption() {
            let companyId = $('.companySelect').val();
            if (companyId) {
                $('#departmentSelect').attr('disabled', false)
                $.ajax({
                    url: "{!! route('admin.contracts.setDepartmentOption') !!}",
                    data: {companyId: companyId},
                    type: 'POST',
                    headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                    success: function (res) {
                        $('#departmentSelect option').remove()
                        $('#departmentSelect').append('<option>' + '{!! trans('overtimes.choose_department') !!}' + '</option>')
                        $.each(res, function (index, value) {
                            let isSelected = oldDepartmentId == index ? 'selected' : ''
                            if(departmentOption){
                                if(jQuery.inArray( Number(index), departmentOption ) !== -1){
                                    $('#departmentSelect').append('<option value="' + index + '"' + isSelected + '>' + value + '</option>')
                                }
                            }
                            else {
                                $('#departmentSelect').append('<option value="' + index + '"' + isSelected + '>' + value + '</option>')
                            }                        })
                    },
                    error: function (data) {
                        console.log(data)
                    }
                })
                $('#department-tooltip').attr('title', '').tooltip('show')

            } else {
                $('#departmentSelect').attr('disabled', true)
                $('#department-tooltip').attr('title', title).tooltip('show')
            }
        }

        function setUserOption() {
            let companyId = $('.companySelect').val();
            let departmentId = $('#departmentSelect').val();
            $('.hidden_with_users option:disabled').prop('disabled', false)

            if (companyId && departmentId) {
                $.ajax({
                    url: "{!! route('admin.overtimes.setUserOption') !!}",
                    data: {
                        companyId: companyId,
                        departmentId: departmentId
                    },
                    type: 'POST',
                    headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                    success: function (res) {

                        $('.user_id option').remove()
                        $('.user_id').append('<option>' + '' + '</option>')
                        $.each(res, function (index, value) {
                            $('.user_id').append('<option value="' + index + '">' + value + '</option>')
                        })
                        $('.hidden_with_users option').remove()
                        $.each(res, function (index, value) {
                            $('.hidden_with_users').append('<option value="' + index + '">' + value + '</option>')
                        })
                    },
                    error: function (data) {
                        console.log(data)
                    }
                })
                $('#department-tooltip').attr('title', '').tooltip('show')
            }
        }

        var oldshifts = {!! old('shifts') ?? 0 !!};

        function setShiftsOption() {
            let departmentId = $('#departmentSelect').val();
            if (departmentId) {
                $.ajax({
                    url: "{!! route('admin.overtimes.setShiftsOption') !!}",
                    data: {
                        departmentId: departmentId
                    },
                    type: 'POST',
                    headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                    success: function (res) {
                        $('label.status').addClass('hidden')
                        $('#shiftSelect option').remove()
                        $('#shiftSelect').append('<option>' + '' + '</option>')
                        if (res.type == 2) {
                            $('#shiftSelect').attr('disabled', false)
                            for (let i = 1; i < 4; i++) {
                                let isSelected = oldshifts == i ? 'selected' : ''
                                $('#shiftSelect').append('<option value="' + i + '"' + isSelected + '>' + 'Ca ' + i + '</option>')
                            }
                        } else if (res.type == 3) {
                            $('#shiftSelect').attr('disabled', false)
                            for (let j = 4; j < 6; j++) {
                                let isSelected = oldshifts == j ? 'selected' : ''
                                j == 4 ? i = 1 : i = 2
                                $('#shiftSelect').append('<option value="' + j + '"' + isSelected + '>' + 'Kíp ' + i + '</option>')
                            }
                        } else {
                            $('#shiftSelect').attr('disabled', true)

                        }
                    },
                    error: function (data) {
                        console.log(data)
                    }
                })
                $('#department-tooltip').attr('title', '').tooltip('show')
            }
        }

        function expectUserOption() {
            let departmentId = $('#departmentSelect').val();
            let shifts = $('#shiftSelect').val();
            let startDate = $('#start_date').val();
            if (shifts && departmentId && startDate) {
                $('.hidden_with_users option:disabled').prop('disabled', false)
                $('.hidden_with_users').select2()
                $.ajax({
                    url: "{!! route('admin.overtimes.expectUserOption') !!}",
                    data: {
                        shifts: shifts,
                        department_id: departmentId,
                        start_date: startDate
                    },
                    type: 'POST',
                    headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                    success: function (res) {
                        if ($('input[type=radio][name=display_with_type]:checked').val() == '1') {
                            if (res) {
                                if (res == 'null') {
                                    $('select.hidden_with_users').val('').trigger('change.select2')
                                }
                                else if(res == ''){
                                    setUserOption()
                                }
                                else {
                                    $('.hidden_with_users').val(JSON.parse(res)).trigger('change.select2')
                                }
                                if (!$('.hidden_with_users').val() == false) {
                                    $.each($('.hidden_with_users').val(), function (index, value) {
                                        $('.hidden_with_users option[value=' + value + ']').prop('disabled', !$('.hidden_with_users option[value=' + value + ']').prop('disabled'))
                                        $('.hidden_with_users').select2()
                                    })
                                }
                            }
                        }

                    },
                    error: function (data) {
                        console.log(data)
                    }
                })
            }
        }

        function setUserOptionForShift() {
            let departmentId = $('#departmentSelect').val();
            let shifts = $('#shiftSelect').val();
            let startDate = $('#start_date').val();
            if (shifts && departmentId && startDate) {
                $('.hidden_with_users option:disabled').prop('disabled', false)
                $('.hidden_with_users').select2()
                $.ajax({
                    url: "{!! route('admin.overtimes.setUserOptionForShift') !!}",
                    data: {
                        shifts: shifts,
                        departmentId: departmentId,
                        startDate: startDate
                    },
                    type: 'POST',
                    headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                    success: function (res) {
                        if(Object.entries(res).length != 0){
                            $('.user_id option').remove()
                            $.each(res, function (index, value) {
                                $('.user_id').append('<option value="' + index + '">' + value + '</option>')
                            })
                        }

                        else if(Object.entries(res).length == 0 ){
                            setUserOption()
                        }
                    },
                    error: function (data) {
                        console.log(data)
                    }
                })
            }

        }

        if (!$('.companySelect').val()) {
            $('#department-tooltip').attr('title', title).tooltip('show')
        }
        $(document).on('change', '.companySelect', setDepartmentOption)
        $(document).on('change', '#departmentSelect', setUserOption)
        $(document).on('change', '#departmentSelect', function () {
            setShiftsOption()
            $('#start_date').val('');
            $('#end_date').val('');

        })
        $(document).on('change', '#shiftSelect', expectUserOption)
        $(document).on('change', '#shiftSelect', setUserOptionForShift)

    </script>
@stop