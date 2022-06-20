@extends('backend.master')
@section('title')
    {!! trans('system.action.list') !!} {!! trans('staffs.manager-leave') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/daterangepicker/daterangepicker.css') !!}" />
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/switchery/0.8.2/switchery.min.css">
@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('staffs.manager-leave') !!}
            <small>{!! trans('system.action.list') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.manager.leave.index') !!}">{!! trans('staffs.manager-leave') !!}</a></li>
        </ol>
    </section>
    <section class="content overlay">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">{!! trans('system.action.filter') !!}</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                </div>
            </div>
            <div class="box-body">
                {!! Form::open([ 'url' => route('admin.manager.leave.index'), 'method' => 'GET', 'role' => 'search' ]) !!}
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('fullname', trans('staffs.staff_name')) !!}
                            {!! Form::text('name', Request::input('name'), ['class' => 'form-control','placeholder'=>'Nhập tên nhân viên']) !!}
                        </div>
                    </div>
                    <div  class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('status', trans('system.status.label')) !!}
                            {!! Form::select('status', [ -1 => trans('system.dropdown_all'), 0 => trans('system.status.unapproved'), 1 => trans('system.status.approved') ], Request::input('status'), ['class' => 'form-control select2',  "style" => "width: 100%;"])!!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('update_range', trans('system.update_range')) !!}
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                {!! Form::text('date_range', Request::input('date_range'), ['class' => 'form-control pull-right date_range', 'autocomplete' => 'off']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        {!! Form::label('filter', trans('system.action.label')) !!}
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-flat">
                                <span class="glyphicon glyphicon-search"></span>&nbsp; {!! trans('system.action.search') !!}
                            </button>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <div class="row">
            <div class="col-md-10">
                {!! $manager_leaves ->appends( Request::except('page') )->render() !!}
            </div>
        </div>
        <div class="box">
            <div class="box-header">
				<?php $i = (($manager_leaves->currentPage() - 1) * $manager_leaves->perPage()) + 1; ?>
                <div class="form-inline">
                    <div class="form-group">
                        {!! trans('system.show_from') !!} {!! $i . ' ' . trans('system.to') . ' ' . ($i - 1 + $manager_leaves->count()) . ' ( ' . trans('system.total') . ' ' . $manager_leaves->total() . ' )' !!}
                    </div>
                </div>
            </div>
            <div class="box-body no-padding">
                <table class='table table-striped table-bordered tree'>
                    <thead>
                    <tr>
                        <th style="text-align: center; vertical-align: middle;">{!! trans('system.no.') !!}</th>
                        <th style="text-align: center; vertical-align: middle;"> {!! trans('staffs.staff_name') !!} </th>
                        <th style="text-align: center; vertical-align: middle;">{!! trans('staff_titles.type') !!}</th>
                        <th style="text-align: center; vertical-align: middle;"> {!! trans('staff_titles.start') !!} </th>
                        <th style="text-align: center; vertical-align: middle;"> {!! trans('staff_titles.end') !!} </th>
                        <th style="text-align: center; vertical-align: middle;"> {!! trans('staff_titles.day_off') !!} </th>
                        <th style="text-align: center; vertical-align: middle;"> {!! trans('staff_titles.status') !!} </th>
                        <th style="text-align: center; vertical-align: middle;"> {!! trans('staff_titles.delete_leave') !!} </th>
                    </tr>
                    </thead>
                    <tbody class="borderless">
                    @if($manager_leaves->count() > 0)
                        @foreach ($manager_leaves as $item)
                            @php
                                $checktype=['S','W','D','L'];
                                    $today=date('d-m-Y');
                                    $start=date('d-m-Y', strtotime($item->start));
                                    $date=(strtotime($start)-strtotime($today));
                            @endphp
                            <tr>
                                <td style="text-align: center; vertical-align: middle;">{!! ++$i !!}</td>
                                <td  style="text-align: center; vertical-align: middle;">{!!optional($item->user)->fullname!!}</td>
                                <td style="text-align: center; vertical-align: middle;">{{$item->title}}</td>

                                <td style="text-align: center; vertical-align: middle;">
                                    {!!date('d/m/Y',strtotime($item->start))!!}<br>
                                    @if ($item->from_type == 1  || $item->from_type == 2)
                                        (<span style="color: #D29E3B">{!!  trans('schedules.time-offs.'.$item->from_type) !!}</span>)
                                    @endif
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    {!!date('d/m/Y',strtotime($item->end))!!}<br>
                                    @if ( $item->to_type == 1 || $item->to_type == 2)
                                        (<span style="color: #A94442"> {!! trans('schedules.time-offs.'.$item->to_type) !!}</span>)
                                    @endif
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    {!!$item->total!!}
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    @if($item->status == 1)
                                        <span data-toggle="tooltip" title="Đơn đã duyệt" class="label label-success"><span class="glyphicon glyphicon-ok"></span></span>
                                        {{--                                        <button class="btn btn-xs btn-success"><i data-toggle="tooltip" title="Đơn đã duyệt" style="font-size: 14px" class="fas fa-check-circle"></i></button>--}}
                                    @elseif($item->status == 0&&$date<=0)
                                        <span class="label label-danger">{!! trans('staff_titles.out of date') !!}</span>
                                    @elseif($item->status == 0&&$item->code=='S'&&$date>0||$item->status == 0&&$item->code=='L'&&$date>0||$item->status == 0&&$item->code=='W'&&$date>0||$item->status == 0&&$item->code=='D'&&$date>0||$item->status == 0&&$item->code=='C'&&$date>0)
                                        <span data-toggle="tooltip" title="Đơn chưa duyệt">
                                        <input  type="checkbox"  data-id="{{ $item->id }}" name="status" class="btn-confirm-canel js-switch" data-status="{{ $item->status }}" {{ $item->status == 1 ? 'checked' : '' }}/>
                                        </span>
                                    @elseif($item->status == 0&&$date>0)
                                        <span style="color: red">{!! trans('staff_titles.day_unpaidLeave') !!}</span>
                                    @endif
                                </td>
                                <td align="center">
                                    @if($item->deleted_at!=null)
                                        <a data-toggle="tooltip" title="Xác nhận hủy đơn xin nghỉ" href="javascript:void(0)" link="{!! route('admin.manager.leave.confirms',$item->id) !!}" class=" btn-confirm-canel   btn btn-default btn-xs"><i class="text-danger glyphicon glyphicon-remove"></i></a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr style="height: 40px">
                            <td  align="center" colspan="8"><span class="text-size"><i class="fas fa-search"></i> {!! trans('schedules.no_data') !!}</span></td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@stop
@section('footer')
    <script src="{!! asset('assets/backend/plugins/daterangepicker/moment.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/daterangepicker/daterangepicker.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/select2/select2.full.min.js') !!}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/switchery/0.8.2/switchery.min.js"></script>
    <script>

        $(document).ready(function(){
            // $("#tre").attr('title', 'Đơn đã được duyệt không thể hủy');
            $('.js-switch').change(function () {

                let status = $(this).prop('checked') === true ? 1 :0;
                console.log(status)
                let userId = $(this).data('id');
                if(status == 1){
                    $.ajax({
                        type: "GET",
                        dataType: "json",
                        url: '{{ route('admin.manager.leave.status') }}',
                        data: {'status': status, 'user_id': userId},
                        success: function (data) {
                            console.log(data)
                            if(data.success){
                                toastr.success(data.success);
                                setTimeout(function () {
                                    location.reload();
                                }, 1000);
                            }
                            else {
                                toastr.error(data.errors);
                                $('.js-switch').prop("unchecked", false);
                            }
                        }
                    });
                }

            });
        });
    </script>
    <script>let elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));

        elems.forEach(function(html) {
            let switchery = new Switchery(html,  { size: 'small' });
        });</script>
    <script>
        !function ($) {
            $(function(){
                $('.select2').select2();
                $('.date_range').daterangepicker({
                    autoUpdateInput: false,
                    "locale": {
                        "format": "DD/MM/YYYY",
                        "separator": " - ",
                        "applyLabel": "Áp dụng",
                        "cancelLabel": "Huỷ bỏ",
                        "fromLabel": "Từ ngày",
                        "toLabel": "Tới ngày",
                        "customRangeLabel": "Custom",
                        "weekLabel": "W",
                        "daysOfWeek": [
                            "CN",
                            "T2",
                            "T3",
                            "T4",
                            "T5",
                            "T6",
                            "T7"
                        ],
                        "monthNames": [
                            "Thg 1",
                            "Thg 2",
                            "Thg 3",
                            "Thg 4",
                            "Thg 5",
                            "Thg 6",
                            "Thg 7",
                            "Thg 8",
                            "Thg 9",
                            "Thg 10",
                            "Thg 11",
                            "Thg 12"
                        ],
                        "firstDay": 1
                    },
                    ranges: {
                        'Hôm nay': [moment(), moment()],
                        'Hôm qua': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        '7 ngày trước': [moment().subtract(6, 'days'), moment()],
                        '30 ngày trước': [moment().subtract(29, 'days'), moment()],
                        'Tháng này': [moment().startOf('month'), moment()],
                        'Tháng trước': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    },
                    "alwaysShowCalendars": true,
                    maxDate: moment(),
                    minDate: moment().subtract(1, "years"),
                }, function(start, end, label) {
                    $('.date_range').val(start.format('DD/MM/YYYY') + " - " + end.format('DD/MM/YYYY'));
                });

            });
        }(window.jQuery);
    </script>

@stop