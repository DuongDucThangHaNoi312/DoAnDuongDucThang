@extends('backend.master')
@section('title')
    {!! trans('system.action.create') !!} - {!! trans('overtimes.label') !!}
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
            <small>{!! trans('system.action.create') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.overtimes.index') !!}">{!! trans('overtimes.label') !!}</a></li>
        </ol>
    </section>

    <div class="">
        <!-- /.box-header -->
        <div class="box-body card card-default" style="padding: 40px 100px;">
            {!! Form::open(['url' => route('admin.overtimes.store'), 'role' => 'form','method'=>'POST', 'id' => 'submit_form']) !!}

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>{!! trans('overtimes.company_id') !!} <span style="color: red">*</span></label>
                        @if (Auth::user()->hasRole('LEADER'))
                            <select name="company_id" id="" class="form-control">
                                <option value="{{ Auth::user()->company->id }}">{{ Auth::user()->company->shortened_name }}</option>
                            </select>
                        @else
                            {!! Form::select('company_id', $companysOption ? ['' =>  trans('overtimes.choose_company')] + $companysOption  : ['' => trans('overtimes.choose_company')] + \App\Define\OverTime::getCompanyNamesForOption(), old('company_id'), ['class' => 'form-control select2 companySelect']) !!}
                        @endif
                    </div>
                    <div class="form-group">
                        <label>{!! trans('overtimes.department_id') !!}<span style="color: red">*</span></label>
                        @if (Auth::user()->hasRole('LEADER'))
                            <select name="department_id" id="" class="form-control">
                                <option value="{{ Auth::user()->department->id }}">{{ Auth::user()->department->name }}</option>
                            </select>
                        @else
                            {!! Form::select('department_id',['' =>  trans('overtimes.choose_department')], old('department_id'), ['class' => 'form-control select2', 'required','disabled' => true, 'id' => 'departmentSelect']) !!}
                        @endif
                    </div>
                    <div class="form-group">
                        <label>{!! trans('overtimes.shifts') !!}<span style="color: red">*</span></label>
                        @if (Auth::user()->hasRole('LEADER') && Auth::user()->department->type == 2)
                            <select name="shifts" id="" class="form-control">
                                <option value="">{{ trans('overtimes.choose_shift') }}</option>
                                <option value="1">Ngày</option>
                                <option value="3">Đêm</option>
                            </select>
                        @else
                            {!! Form::select('shifts', ['' => trans('overtimes.choose_shift')], old('shifts'), ['class' => 'form-control select2', 'required','disabled' => true, 'id' => 'shiftSelect']) !!}
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{!! trans('overtimes.start_date') !!} <span
                                            style="color: red">*</span></label>
                                <div class='input-group'>
                                    {!! Form::text('start_date', old('start_date'), ['class' => 'form-control datepicker start_date','id'=>'start_date' ,'placeholder'=>trans('overtimes.start_date_placeholder'),'autocomplete'=>'off']) !!}
                                    <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                          </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group ">
                                <label>{!! trans('overtimes.end_date') !!} <span style="color: red">*</span></label>

                                <div class='input-group'>
                                    {!! Form::text('end_date', old('end_date'), ['class' => 'form-control datepicker','id'=>'end_date' ,'placeholder'=>trans('overtimes.end_date_placeholder'),'disabled','autocomplete'=>'off']) !!}
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
                                {!! Form::radio('display_with_type', '1', old('display_with_type') ,  ['id' => 'display_with_type']) !!}
                                {!! trans('overtimes.all_department') !!}
                            </label>
                        @endif
                    </div>
                    <div class="radio">
                        <label class="type-option">
                            {!! Form::radio('display_with_type', '2', old('display_with_type'),  ['id' => 'display_with_type', Auth::user()->hasRole('LEADER') ? 'checked' : '']) !!}
                            {!! trans('overtimes.user/users') !!}
                        </label>
                        {!! Form::select('display_with_data[]', \App\Define\OverTime::getUserNamesForOption(), old('display_with_data[]'), ['class' => 'form-control select2 display_with_data user_id','multiple', 'disabled' => true,]) !!}
                    </div>
                    <div class="form-group">
                        @if (!Auth::user()->hasRole('LEADER'))
                            <label for="hidden_with_users">{!! trans('overtimes.except') !!}</label>
                            {!! Form::select('hidden_with_users[]', \App\Define\OverTime::getUserNamesForOption(), old('hidden_with_users[]'), ['class' => 'form-control select2 hidden_with_users','multiple', 'disabled' => true]) !!}
                        @endif
                    </div>

                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>{!! trans('overtimes.hours') !!} <span
                                            style="color: red">*</span></label>
                                <div class='input-group'>
                                    {!! Form::number('overtime_hours', old('overtime_hours'), ['class' => 'form-control ','id'=>'overtime_hours' ,'style'=>'text-align:right','min'=>1]) !!}
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
                                        {!! Form::checkbox('status', 1, old('status'), [ 'class' => 'minimal status','id'=>'status' ]) !!}
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
                {!! Form::button(trans('system.action.save'), ['class' => 'btn btn-primary btn-flat btn-click']) !!}
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

                }).on('changeDate', function (e) {

                    var minDate = new Date(e.date.valueOf());
                    minDate.setDate(minDate.getDate() + 1)
                    var dateStart = minDate.getDate()
                    var monthStart = minDate.getMonth()
                    var yearStart = minDate.getFullYear()
                    var lastDay = new Date(yearStart, monthStart, 25)
                    dateStart > 25 ? $('#end_date').datepicker('setEndDate', (moment(lastDay).add('1', 'months')).format("DD/MM/YYYY")) : $('#end_date').datepicker('setEndDate', moment(lastDay).format("DD/MM/YYYY"))
                });

                $('#end_date').datepicker({
                    format: 'dd/mm/yyyy',
                    useCurrent: false,
                    autoclose: true,
                }).on('changeDate', function (e) {
                    var maxDate = new Date(e.date.valueOf());
                    $('#end_date').datepicker('setStartDate', maxDate)
                })

                $(".select2").select2({
                    width: '100%',
                });
                $('input[type="checkbox"].minimal').iCheck({
                    checkboxClass: 'icheckbox_minimal-red'
                });
                $('label.status').hide()
                $("#start_date").on("change", function () {
                    $('input[type=checkbox]:checked').iCheck('uncheck')
                    let date = $("#start_date").datepicker("getDate").getDay();
                    $('label.status').show()
                    $("#end_date").prop('disabled', false)
                    let departmentId = $('#departmentSelect').val();
                    let startDate = $('#start_date').val();
                    let shiftSelect = $('#shiftSelect').val();
                    if (departmentId && startDate && shiftSelect != 'Chọn ca/kíp') {
                        expectUserOption()
                        setUserOptionForShift()
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
                                console.log(res)
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
                    else {
                        $('label.status').show()
                    }

                    $('label.status').removeClass('hidden') && $('input[type=checkbox]').prop('disabled', false)
                    date == '1' ? $('.every-week').html('{!! trans('overtimes.mo') !!}') : '';
                    date == '2' ? $('.every-week').html('{!! trans('overtimes.tu') !!}') : '';
                    date == '3' ? $('.every-week').html('{!! trans('overtimes.we') !!}') : '';
                    date == '4' ? $('.every-week').html('{!! trans('overtimes.th') !!}') : '';
                    date == '5' ? $('.every-week').html('{!! trans('overtimes.fr') !!}') : '';
                    date == '6' ? $('.every-week').html('{!! trans('overtimes.st') !!}') : '';
                    date == '0' ? $('.every-week').html('{!! trans('overtimes.sn') !!}') : '';

                });
                if ($("#start_date").val()) {
                    $('input[type=checkbox]:checked').iCheck('uncheck')
                    $('label.status').removeClass('hidden')
                    $('.timepicker').prop('disabled', false)
                    $("#end_date").prop('disabled', false)
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
            $("#end_date").val(null).prop('disabled', false)
        });

        $('input[type=radio][name=display_with_type]').change(function () {
            let val = $(this).val();
            val == '1' ? $('.hidden_with_users').prop('disabled', false) && expectUserOption() : $('.hidden_with_users').prop('disabled', true);
            val == '2' ? $('.hidden_with_users').val('').trigger('change.select2') && $('.display_with_data').prop('disabled', false) && setUserOptionForShift()  : $('.display_with_data').prop('disabled', true);
        });

        $('input[type=radio][name=display_with_type]:checked').val() == '1' ? $('.hidden_with_users').prop('disabled', false) && expectUserOption() : $('.hidden_with_users').prop('disabled', true);
        $('input[type=radio][name=display_with_type]:checked').val() == '2' ? $('.display_with_data').prop('disabled', false) && setUserOptionForShift() && $('.hidden_with_users').val('').trigger('change.select2'): $('.display_with_data').prop('disabled', true);

        var title = 'Vui lòng chọn công ty trước'
        $('#department-tooltip').tooltip({
            // title: title
        }).tooltip('show');
        let $currentRoute = {!! json_encode(\App\PermissionUserObject::getCurrentModule(\Route::getCurrentRoute())) !!};

        var oldDepartmentId = {!! old('department_id') ?? 0 !!};
        var departmentOption = {!! json_encode($department_group)  !!}
        function setDepartmentOption() {
            let companyId = $('.companySelect').val();
            if (companyId) {
                $('#departmentSelect').attr('disabled', false)
                $.ajax({
                    url: "{!! route('admin.contracts.setDepartmentOption') !!}",
                    data: {companyId: companyId, route: $currentRoute},
                    type: 'POST',
                    headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                    success: function (res) {
                        $('#departmentSelect option').remove()
                        $('#departmentSelect').append('<option>' + '{!! trans('overtimes.choose_department') !!}' + '</option>')
                        $.each(res, function (index, value) {
                            let isSelected = oldDepartmentId == index ? 'selected' : ''
                            if(departmentOption){
                                // if(jQuery.inArray( Number(index), departmentOption ) !== -1){
                                //     $('#departmentSelect').append('<option value="' + index + '"' + isSelected + '>' + value + '</option>')
                                // }
                                $('#departmentSelect').append('<option value="' + index + '"' + isSelected + '>' + value + '</option>')
                            }
                            else {
                                $('#departmentSelect').append('<option value="' + index + '"' + isSelected + '>' + value + '</option>')
                            }
                        })
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
        var type_de = '';

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

                        $('#shiftSelect option').remove()
                        $('#shiftSelect').append('<option>' + '{!! trans('overtimes.choose_shift') !!}' + '</option>')
                        if (res.type == 2) {
                            type_de = res.type;
                            $('label.status').hide()
                            $('#shiftSelect').attr('disabled', false)
                            let html = `
                                    <option value="1">Ngày</option>
                                    <option value="3">Đêm</option>
                                `;

                            $('#shiftSelect').append(html);

                            // for (let i = 1; i <= 4; i++) {
                            //     let isSelected = oldshifts == i ? 'selected' : '';
                            //     $('#shiftSelect').append('<option value="' + i + '"' + isSelected + '>' + 'Ca ' + i + '</option>')
                            // }
                        } else if (res.type == 3) {
                            $('label.status').hide()
                            $('#shiftSelect').attr('disabled', false)
                            for (let j = 4; j < 6; j++) {
                                let isSelected = oldshifts == j ? 'selected' : ''
                                j == 4 ? i = 1 : i = 2
                                $('#shiftSelect').append('<option value="' + j + '"' + isSelected + '>' + 'Kíp ' + i + '</option>')
                            }
                        } else {
                            $('label.status').show()

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
                        console.log(res)
                        if ($('input[type=radio][name=display_with_type]:checked').val() == '1') {
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
            // if (shifts && departmentId && startDate) {
            //     $('.hidden_with_users option:disabled').prop('disabled', false)
            //     $('.hidden_with_users').select2()
            //     $.ajax({
            //         url: "{!! route('admin.overtimes.setUserOptionForShift') !!}",
            //         data: {
            //             shifts: shifts,
            //             departmentId: departmentId,
            //             startDate: startDate
            //         },
            //         type: 'POST',
            //         headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
            //         success: function (res) {
            //             if(Object.entries(res).length != 0){
            //                 $('.user_id option').remove()
            //                 $.each(res, function (index, value) {
            //                     $('.user_id').append('<option value="' + index + '">' + value + '</option>')
            //                 })
            //             }

            //             else if(Object.entries(res).length == 0 ){
            //                 setUserOption()
            //             }


            //         },
            //         error: function (data) {
            //             console.log(data)
            //         }
            //     })
            // }

        }


        if (!$('.companySelect').val()) {
            $('#department-tooltip').attr('title', title).tooltip('show')
        }
        $(document).on('change', '.companySelect', setDepartmentOption)
        if ($('.companySelect').val()) {
            $('#departmentSelect').attr('disabled', false)
            setDepartmentOption()
        }
        $(document).on('change', '#departmentSelect', setUserOption)
        $(document).on('change', '#departmentSelect', function () {
            setShiftsOption()
            $('#start_date').val('');
            $('#end_date').val('');

        })
        $(document).on('change', '#shiftSelect', expectUserOption)
        $(document).on('change', '#shiftSelect', setUserOptionForShift)
        if ($('#departmentSelect').val()) {
            setUserOption()
            setShiftsOption()
        }

        $('.btn-click').on('click', function() {
            let overtime_hours = $('#overtime_hours').val();
            if (!overtime_hours) {
                toastr.error('Số giờ làm/ngày không được để trống');
                return ;
            }

            $('#submit_form').submit();
        })
    </script>
@stop