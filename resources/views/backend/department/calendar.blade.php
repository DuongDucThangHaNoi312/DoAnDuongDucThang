@extends('backend.master')
@section('title')
    {!! trans('calendar_departments.calendar') !!} - {!! trans('departments.label') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/>
    <link rel="stylesheet" type="text/css"
          href="{!! asset('assets/backend/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/css/calendar.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
    <style>
        .table-off tr th {
            height: 50px;
            border: 1px solid black;
        }

        .table-off tr td {
            height: 50px;
            border: 1px solid black;

        }

        .table-off {
            height: 50px;
            border: 1px solid black;
            width: 100%;
            margin-bottom: 5%;
        }

        td {
            position: relative;
        }

        td div.setBgAfternoon {
            width: 100%;
            height: 100%;
            background: rgba(180, 180, 180, 1);
            clip-path: polygon(100% 0, 0 100%, 100% 100%);
            position: absolute;
            right: 0;
            bottom: 0;
            z-index: 1;
        }

        td div.setBgAfternoonHoliday {
            width: 100%;
            height: 100%;
            background: red;
            clip-path: polygon(100% 0, 0 100%, 100% 100%);
            position: absolute;
            right: 0;
            bottom: 0;
            z-index: 1;
        }

        td div.setBgMorning {
            width: 100%;
            height: 100%;
            background: rgba(180, 180, 180, 1);
            clip-path: polygon(100% 0%, 0% 0%, 0% 100%);
            position: absolute;
            left: 0;
            top: 0;
            z-index: 1;
        }

        td div.setBgMorningHoliday {
            width: 100%;
            height: 100%;
            background: red;
            clip-path: polygon(100% 0%, 0% 0%, 0% 100%);
            position: absolute;
            left: 0;
            top: 0;
            z-index: 1;
        }

        select[readonly].select2-hidden-accessible + .select2-container {
            pointer-events: none;
            touch-action: none;
        }

        input[readonly] {
            pointer-events: none;
            touch-action: none;
        }

        input[readonly] {
            pointer-events: none;
            touch-action: none;
        }

        select[readonly].select2-hidden-accessible + .select2-container .select2-selection {
            background: #eee;
            box-shadow: none;
        }

        select[readonly].select2-hidden-accessible + .select2-container .select2-selection__arrow,
        select[readonly].select2-hidden-accessible + .select2-container .select2-selection__clear {
            display: none;
        }

        .error {
            border-color: #dd4b39;
        }

        .swal-modal {
            width: 600px;
        }

        .swal-button {
            padding: 7px 19px;
            font-size: 14px;
        }

        .swal-button--delete_all:hover {
            background-color: #dd4b39 !important;
        }

        .swal-button--delete_one:hover {
            background-color: #dd4b39 !important;
        }

        .swal-text {
            text-align: center;
        }

        .SaSu {
            background-color: #00a7d0;

        }

        #days:hover {
            background-color: darkgrey;
        }


    </style>
@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('departments.label') !!} {!! $department->name !!} - {!! $department->company ? $department->company->shortened_name : '' !!}
            <small>{!! trans('calendar_departments.calendar') !!}</small>
            @if($department->type == 1)
                <label class="label label-success">
                    {!! trans('calendar_departments.office_time') !!}
                </label>
            @elseif($department->type == 2)
                <label class="label label-success">
                    {!! trans('shifts.shift') !!}
                </label>
            @elseif($department->type == 3)
                <label class="label label-success">
                    {!! trans('shifts.shift_and_ot') !!}
                </label>
            @endif
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.departments.index') !!}">{!! trans('departments.label') !!}</a></li>
            <li>{!! trans('calendar_departments.calendar') !!}</li>
        </ol>
        <div class="row">

        </div>
    </section>
    <section class="content overlay">
        <div class="box">
            <div class="box-header">
                <div class="form-inline">
                    <div class="form-group">
                        <i>{!! trans('system.note') !!}: </i>&nbsp;&nbsp;
                        @if($department->type == 1)
                        <span style="color:rgba(180,180,180,1)">◤</span>{!! trans('calendar_departments.morning') !!}
                        &nbsp;&nbsp;
                        <span style="color:rgba(180,180,180,1)">◢</span>{!! trans('calendar_departments.afternoon') !!}
                        @endif
                        <span style="color:rgba(180,180,180,1)"><i
                                    class="fas fa-square-full"></i>&nbsp;</span>{!! trans('calendar_departments.all_day') !!}
                        <span style="color:red"><i
                                    class="fas fa-square-full"></i>&nbsp;</span>{!! trans('calendar_departments.all_day_holiday') !!}
                        @if($department->type != 1)
                            <span style="color:#39de07"><i
                                        class="fas fa-square-full"></i>&nbsp;</span>{!! trans('calendar_departments.work_day') !!}
                            @endif
                    </div>


                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <a href="{{ request()->fullUrlWithQuery(['year' => $year - 1]) }}"
                           class='btn btn-primary btn-flat'>
                            <i class="fas fa-arrow-left"></i>&nbsp;{!! $year - 1 !!}
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['year' => $year + 1]) }}"
                           class='btn btn-primary btn-flat'>
                            {!! $year + 1 !!}&nbsp;<i class="fas fa-arrow-right"></i>
                        </a>
                        @if($department->type == 2)
                            <a href="{!! route('admin.shifts.index',$department->id) !!}"
                               class='btn btn-primary btn-flat work-shift'>
                                {!! trans('shifts.create') !!}
                            </a>
                        @endif
                        @if (!Auth::user()->hasRole('LEADER'))
                        <a data-toggle="modal" data-target="#formCopys"
                           data-backdrop="static" data-keyboard="false"
                           data-dismiss="modal"
                           class='btn btn-primary btn-flat work-shift'>
                            {!! trans('calendar_departments.copy') !!}
                        </a>    
                        @endif
                        
                    </div>
                    <div class="col-lg-6 text-right">
                        <span style="font-size: 30px">{!! $year !!}</span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table-off">
                        <thead>
                        <tr>
                            <th style="text-align: center; color: red" id="year">{!! $year !!}</th>
                            @for ($i = 0; $i < 5; $i++)
                                <th style="text-align: center; vertical-align: middle;">{!! trans('calendar_departments.Mo') !!}</th>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('calendar_departments.Tu') !!}</th>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('calendar_departments.We') !!}</th>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('calendar_departments.Th') !!}</th>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('calendar_departments.Fr') !!}</th>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('calendar_departments.Sa') !!}</th>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('calendar_departments.Su') !!}</th>
                            @endfor
                            <th style="text-align: center; vertical-align: middle;">{!! trans('calendar_departments.Mo') !!}</th>
                            <th style="text-align: center; vertical-align: middle;">{!! trans('calendar_departments.Tu') !!}</th>
                            <th style="text-align: center; vertical-align: middle; width:8%">{!! trans('calendar_departments.total_working') !!}
                            </th>
                        </tr>
                        </thead>
                        @foreach ($data as $key => $value)
                            @foreach ($value as $month => $maxDateAndFristDays)
                                <tr>
                                    <td style="text-align: center"><b><a href="{{ $department->type != 1 ? route('admin.departments.getShift', ['departmentId' => $department->id, 'month' => $key+1, 'year' => $year]) : '#' }}">{!! $month !!}</a></b></td>
                                    <?php $count = 0; ?>
                                    @foreach ($dayOfWeek as $day => $date)
                                        @if ($maxDateAndFristDays[1] == $date)
                                            {!! str_repeat("<td bgcolor='white'>&nbsp;</td>", $day) !!}
                                            @for ($j = 1; $j < $maxDateAndFristDays[0] + 1; $j++)
                                                <?php $weekend = date('l', mktime(0, 0, 0, $key + 1, $j, $year)); ?>
                                                <td data-toggle="modal" data-target="{{ !Auth::user()->hasRole('LEADER') ? '#formOff' : ''}}"
                                                     data-backdrop="static" data-keyboard="false"
                                                    class="text-center DayOff" data-dismiss="modal"
                                                    id="{!! $year !!}-{!! $key + 1 !!}-{!! $j !!}">
                                                    <span style="z-index: 2;position: relative " data-toggle="tooltip" data-placement="top">{{ $j }}</span>
                                                </td>
                                            @endfor
                                            {!! str_repeat("<td bgcolor='white'>&nbsp;</td>", 37 - ($day - 1) - $j) !!}
                                        @endif
                                    @endforeach
                                    <th id="{!! $key !!}">
                                    </th>
                                </tr>
                            @endforeach
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="formOff" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog ">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans('calendar_departments.edit_day') }}
                        <span id="day-off"></span></h4>
                </div>
                <div class="modal-body">
                    @include('backend.department.dayoff_form')
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="formWorkShift" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog ">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans('shifts.edit_day') }}
                        <span id="day-off"></span></h4>
                </div>
                <div class="modal-body">
                    @include('backend.department.shift_form')
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="formWorkShiftAndOT" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog ">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans('shifts.shift_and_ot') }}
                        <span id="day-off"></span></h4>
                </div>
                <div class="modal-body">
                    @include('backend.department.shift_and_ot_form')
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="formCopys" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog ">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans('calendar_departments.copy_title') }}
                        <span id="day-off"></span></h4>
                </div>
                <div class="modal-body">
                    @include('backend.department.copy_form')
                </div>
            </div>
        </div>
    </div>

@stop
@section('footer')
    <script src="{!! asset('assets/backend/plugins/iCheck/icheck.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/select2/select2.full.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/moment/min/moment-with-locales.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/input-mask/jquery.inputmask.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.vi.min.js') !!}"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        !function ($) {
            $(function () {
                $(".select2readonly").select2();
                $(document).on('click', '.close', function () {
                    window.location.reload()
                })
            });
        }(window.jQuery);
    </script>
    <script>
        var status = {!! $department->type !!};
        var departmentId = {!! $department->id !!};
        var year = {!! $year !!};
        var url = '';
        url = '{!! route('admin.calendar.loadDataDepartments') !!}'
        getData(url)
            .done(function (data) {
                $(data).each(function (index, value) {
                    var holiday = " {{trans('calendar_departments.holiday')}}: " + value.reason + "";
                    var normal = " {{trans('calendar_departments.normal')}}: " + value.reason + "";
                    var date = new dateOFF(value.start_date, value.end_date)
                    var dayStart = $('#' + date.dayStart + '')
                    var dayEnd = $('#' + date.dayEnd + '')
                    var fromType = value.from_type
                    var toType = value.to_type
                    var type = value.type
                    var categories = value.categories
                    var from = date.from
                    var to = date.to
                    if (type == 'one') {
                        dayStart.append('<span class="id hidden">' + value.id + '</span>');
                        if (categories == 'holiday') {
                            dayStart.find('span').prop('title', holiday)
                            fromType == 'MORNING' && toType == 'AFTERNOON' ? dayStart.css('background-color', 'red') : '';
                            fromType == 'MORNING' && toType == 'MORNING' ? dayStart.append('<div class="setBgMorningHoliday "></div>') : ''
                            fromType == 'AFTERNOON' && toType == 'AFTERNOON' ? dayStart.append('<div class="setBgAfternoonHoliday  "></div>') : ''
                        } else {
                            dayStart.find('span').prop('title', normal)
                            fromType == 'MORNING' && toType == 'AFTERNOON' ? dayStart.css('background-color', 'rgba(180,180,180,1)') : '';
                            fromType == 'MORNING' && toType == 'MORNING' ? dayStart.append('<div class="setBgMorning "></div>') : ''
                            fromType == 'AFTERNOON' && toType == 'AFTERNOON' ? dayStart.append('<div class="setBgAfternoon  "></div>') : ''
                        }

                    }
                    if (type == 'multiple') {
                        from.setDate(from.getDate() + 1);
                        to.setDate(to.getDate() - 1);
                        for (var dt = from; dt <= to; dt.setDate(dt.getDate() + 1)) {
                            var day = $('#' + moment(dt).format('YYYY-M-D') + '')
                            if (categories == 'holiday') {
                                day.find('span').prop('title', holiday)
                                day.css('background-color', 'red')
                                day.append('<span class="id hidden">' + value.id + '</span>');
                            } else {
                                day.find('span').prop('title', normal)
                                day.css('background-color', 'rgba(180,180,180,1)')
                                day.append('<span class="id hidden">' + value.id + '</span>');
                            }
                        }
                        dayStart.find('span').append('<span class="id hidden">' + value.id + '</span>');
                        dayEnd.find('span').append('<span class="id hidden">' + value.id + '</span>');
                        if (categories == 'holiday') {
                            dayStart.find('span').prop('title', holiday)
                            dayEnd.find('span').prop('title', holiday)
                            fromType == 'MORNING' ? dayStart.css('background-color', 'red') : dayStart.append('<div class="setBgAfternoonHoliday  "></div>');
                            toType == 'MORNING' ? dayEnd.append('<div class="setBgMorningHoliday  "></div>') : dayEnd.css('background-color', 'red')
                        } else {
                            dayStart.find('span').prop('title', normal)
                            dayEnd.find('span').prop('title', normal)
                            fromType == 'MORNING' ? dayStart.css('background-color', '#B4B4B4') : dayStart.append('<div class="setBgAfternoon  "></div>');
                            toType == 'MORNING' ? dayEnd.append('<div class="setBgMorning  "></div>') : dayEnd.css('background-color', 'rgba(180,180,180,1)')
                        }

                    }
                    if (type == 'everyweek') {
                        for (var dt = from; dt <= to; dt.setDate(dt.getDate() + 7)) {
                            var day = $('#' + moment(dt).format('YYYY-M-D') + '')

                            day.find('span').prop('title', normal)
                            day.append('<span class="id hidden">' + value.id + '</span>');
                            fromType == 'MORNING' && toType == 'AFTERNOON' ? day.css('background-color', '#B4B4B4') : '';
                            fromType == 'MORNING' && toType == 'MORNING' ? day.append('<div class="setBgMorning "></div>') : ''
                            fromType == 'AFTERNOON' && toType == 'AFTERNOON' ? day.append('<div class="setBgAfternoon  "></div>') : ''
                        }
                    }
                })
            })
            .fail(function (response) {
                toastr.error(response.responseJSON.data[0], "{!! trans('system.have_an_error') !!}")
            })
        url = '{!! route('admin.calendar.totalWorking') !!}'
        getData(url)
            .done(function (data) {
                $(data).each(function (index, val) {
                    $('th#' + index + '').append('<center><span class="text-danger">' + val + '</span></center>')
                })
            })
            .fail(function (response) {
                toastr.error(response.responseJSON.data[0], "{!! trans('system.have_an_error') !!}")
            })
        if(status != 1){
            url = '{!! route('admin.calendar.loadWorking') !!}'
            getData(url)
                .done(function (data) {
                    $.each(data.data, function (index, value) {
                        var date = new dateOFF(value.start_date, value.end_date)
                        var dateStart = $('#' + date.dayStart + '')
                        var from = date.from
                        var to = date.to

                        if (date.dayStart == date.dayEnd) {
                            dateStart.css('background-color', '#39de07');
                            status == 2 ? dateStart.addClass('Shift') : '';
                            status == 3 ? dateStart.addClass('ShiftAndOT') : '';
                            dateStart.removeAttr('data-toggle');
                            status == 2 ? dateStart.append('<span class="idShift hidden">' + value.id + '</span>') : '';
                            status == 3 ? dateStart.append('<span class="idShiftAndOT hidden">' + value.id + '</span>') : '';
                        } else {
                            for (var dt = from; dt <= to; dt.setDate(dt.getDate() + 1)) {
                                var day = $('#' + moment(dt).format('YYYY-M-D') + '')
                                day.css('background-color', '#39de07')
                                status == 2 ? day.addClass('Shift') : '';
                                status == 3 ? day.addClass('ShiftAndOT') : '';
                                day.removeAttr('data-toggle');
                                status == 2 ? day.append('<span class="idShift hidden">' + value.id + '</span>') : '';
                                status == 3 ? day.append('<span class="idShiftAndOT hidden">' + value.id + '</span>') : '';
                            }
                        }
                    })
                })
                .fail(function (response) {
                    toastr.error(response.responseJSON.data[0], "{!! trans('system.have_an_error') !!}")
                })
        }


        function dateOFF(start_date, end_date) {
            var dayStart = moment(start_date).format('YYYY-M-D')
            var dayEnd = moment(end_date).format('YYYY-M-D')
            var from = new Date(moment(start_date).format('YYYY-M-D'));
            var to = new Date(moment(end_date).format('YYYY-M-D'));
            return {dayStart, dayEnd, from, to}
        }

        function getData(url) {
            return $.ajax({
                url: url,
                type: 'GET',
                data: {departmentId: departmentId, year: year}
            });
        }


    </script>

@stop
