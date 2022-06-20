@extends('backend.master')
@section('title')
    {!! trans('system.action.list') !!} {!! trans('kpi.label') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') !!}"/>
    <style>
        .btn-creat {
            float: right;
            margin-bottom: 15px;
        }

        .error {
            width: 100%;
            height: 100px;
            line-height: 100px;
        }
        .text-size {
            font-size: 16px;
        }
        input[type=number]::-webkit-inner-spin-button {
            -webkit-appearance: none;
        }
    </style>
@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('kpi.label') !!}
            <small>{!! trans('system.action.list') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.targets.index') !!}">{!! trans('kpi.label') !!}</a></li>
        </ol>
    </section>
    <section class="content overlay">
            <div class="box box-default">
                <div class="box-header with-bconsumer">
                    <h3 class="box-title">{!! trans('system.action.filter') !!}</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    {!! Form::open([ 'url' => route('admin.manager_kpi.index'), 'method' => 'GET', 'role' => 'search' ]) !!}
                    <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="form-group">
                                        {!! Form::label('user_filter', trans('kpi.staffs')) !!}
                                        {!! Form::text('name', Request::input('name'), ['class' => 'form-control','placeholder'=>'Nhập tên nhân viên']) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                {!! Form::label('month', trans('kpi.time')) !!}
                                {!! Form::text('month', $month_filter, ['class' => 'form-control', 'id' => 'month_filter']) !!}
                            </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('page_num', trans('system.page_num')) !!}
                                {!! Form::select('page_num', [ 10 => '10' . trans('system.items'), 20 => '20' . trans('system.items'), 50 => '50' . trans('system.items') , 100 => '100' . trans('system.items'), 500 => '500' . trans('system.items') ], Request::input('page_num', 20), ['class' => 'form-control select2']) !!}
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                {!! Form::label('filter', trans('system.action.label'), ['style' => 'display: block;']) !!}
                                <button type="submit" class="btn btn-default btn-flat" style="display: block;">
                                    <span class="glyphicon glyphicon-search"></span>&nbsp; {!! trans('system.action.search') !!}
                                </button>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        <div class="row">
                <div class="col-md-2">
                    <a href="{!! route('admin.manager_kpi.create') !!}" class='btn btn-primary btn-flat'>
                        <span class="glyphicon glyphicon-plus"></span>&nbsp;{!! trans('system.action.create') !!}
                    </a>
                </div>
            <div class="col-md-10 text-right">
                {!!  $targets->appends( Request::except('page') )->render() !!}
            </div>
        </div>
        <?php $labels = ['default', 'success', 'info', 'danger', 'warning']; ?>
        <div class="box">
                <div class="box-header">
                    <?php $i = (($targets->currentPage() - 1) * $targets->perPage()) + 1; ?>
                    <div class="form-inline">
                        <div class="form-group">
                            {!! trans('system.show_from') !!} {!! $i . ' ' . trans('system.to') . ' ' . ($i - 1 + $targets->count()) . ' ( ' . trans('system.total') . ' ' . $targets->total() . ' )' !!}
                        </div>
                    </div>
                </div>
            <div class="box-body no-padding">
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                            <th style="text-align: center; vertical-align: middle;">{!! trans('system.no.') !!}</th>
                        <th style="text-align: center; vertical-align: middle;">{!! trans('kpi.name_staff') !!}</th>
                        <th style="text-align: center; vertical-align: middle;">{!! trans('kpi.kpi_value') !!}</th>
                        <th style="text-align: center; vertical-align: middle;">{!! trans('kpi.month') !!}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($targets->total()>0)
                        @foreach($targets as $item)
                            @if($item->user_id!=auth()->id())
                            <tr>
                                <td style="text-align: center; vertical-align: middle;">{!! $i++ !!}</td>
                                <td style="text-align: center; vertical-align: middle;">{{ $item->fullname }}</td>
                                <td align="center" style="vertical-align: middle;" >{{ $item->kpi }}</td>
                                <td style="text-align: center; vertical-align: middle;">{!! date('m/Y',strtotime($item->timestamp)) !!}</td>
                            </tr>
                            @endif
                        @endforeach
                    @else
                        <tr style="height: 40px">
                            <td  align="center" colspan="6"><span class="text-size"><i class="fas fa-search"></i> {!! trans('staff_positions.no_data') !!}</span></td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>

    </section>
@stop
@section('footer')
    <script src="{!! asset('assets/backend/plugins/iCheck/icheck.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/select2/select2.full.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/moment/min/moment-with-locales.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/input-mask/jquery.inputmask.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') !!}"></script>
    <script>
        $(function () {
            $('#month_filter').datepicker({
                format: "mm/yyyy",
                viewMode: "months",
                minViewMode: "months",
                clearBtn:true,
                autoclose:true,
            });

            $(".select2").select2({
                width: '100%'
            });
        });

    </script>
@stop
