@extends('backend.master')
@section('title')
    {!! trans('system.action.list') !!} {!! trans('allowance_categories.label') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/daterangepicker/daterangepicker.css') !!}" />
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('allowance_categories.label') !!}
            <small>{!! trans('system.action.list') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.allowance-categories.index') !!}">{!! trans('allowance_categories.label') !!}</a></li>
        </ol>
    </section>
    <section class="content overlay">
        <div class="row">
            <div class="col-md-2">
{{--                <a href="{!! route('admin.allowance-categories.create') !!}" class='btn btn-primary btn-flat'>--}}
{{--                    <span class="glyphicon glyphicon-plus"></span>&nbsp;{!! trans('system.action.create') !!}--}}
{{--                </a>--}}
            </div>
            <div class="col-md-10 text-right">
                {!!  $allowanceCategories->appends( Request::except('page') )->render() !!}
            </div>
        </div>
        @if (count($allowanceCategories) > 0)
            <?php $labels = ['default', 'success', 'info', 'danger', 'warning']; ?>
            <div class="box">
                <div class="box-header">
                    <?php $i = (($allowanceCategories->currentPage() - 1) * $allowanceCategories->perPage()) + 1; ?>
                    <div class="form-inline">
                        <div class="form-group">
                            {!! trans('system.show_from') !!} {!! $i . ' ' . trans('system.to') . ' ' . ($i - 1 + $allowanceCategories->count()) . ' ( ' . trans('system.total') . ' ' . $allowanceCategories->total() . ' )' !!}
                        </div>
                    </div>
                </div>
                <div class="box-body no-padding">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('system.no.') !!}</th>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('allowance_categories.name') !!}</th>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('allowance_categories.has_kpi') !!}</th>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('allowance_categories.is_social_security') !!}</th>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('allowance_categories.is_exemp') !!}</th>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('allowance_categories.ot') !!}</th>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('system.status.label') !!}</th>
                                <th style="text-align: center; vertical-align: middle; white-space: nowrap;">{!! trans('allowance_categories.created_at') !!}</th>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('system.action.label') !!}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($allowanceCategories as $item)
                                <tr>
                                    <td style="text-align: center; vertical-align: middle;">{!! $i++ !!}</td>
                                    <td style="vertical-align: middle;">
                                        <a>{!! $item->name !!}</a><br/>
                                    </td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        @if($item->type == 1)
                                            <span class="label label-success"><span class='glyphicon glyphicon-ok'></span></span>
                                        @elseif($item->type == 0)
                                            <span class="label label-danger"><span class='glyphicon glyphicon-remove'></span></span>
                                        @endif
                                    </td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        @if($item->is_social_security == 1)
                                            <span class="label label-success"><span class='glyphicon glyphicon-ok'></span></span>
                                        @elseif($item->is_social_security == 0)
                                            <span class="label label-danger"><span class='glyphicon glyphicon-remove'></span></span>
                                        @endif
                                    </td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        @if($item->is_exemp == 1)
                                            <span class="label label-success"><span class='glyphicon glyphicon-ok'></span></span>
                                        @elseif($item->is_exemp == 0)
                                            <span class="label label-danger"><span class='glyphicon glyphicon-remove'></span></span>
                                        @endif
                                    </td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        @if($item->ot == 1)
                                            <span class="label label-success"><span class='glyphicon glyphicon-ok'></span></span>
                                        @elseif($item->ot == 0)
                                            <span class="label label-danger"><span class='glyphicon glyphicon-remove'></span></span>
                                        @endif
                                    </td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        @if($item->status == 0)
                                            <span class="label label-danger">{!! trans('system.status.notactive') !!}</span></span>
                                        @elseif($item->status == 1)
                                            <span class="label label-success">{!! trans('system.status.active') !!}</span></span>
                                        @endif
                                    </td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        {!! $item->created_at ? $item->created_at->format('d-m-Y') : '' !!}
                                    </td>
                                    <td style="text-align: center; vertical-align: middle; white-space: nowrap;">&nbsp;&nbsp;
                                        <a href="{!! route('admin.allowance-categories.edit', $item->id) !!}"
                                           class="btn btn-xs btn-default"
                                           data-toggle="tooltip" data-placement="top" title="{!! trans('system.action.update') !!}">
                                            <i class="text-warning glyphicon glyphicon-edit"></i>
                                        </a>&nbsp;&nbsp;
{{--                                        <a href="javascript:void(0)"--}}
{{--                                           link="{!! route('admin.allowance-categories.destroy', $item->id) !!}"--}}
{{--                                           class="btn-confirm-del btn btn-default btn-xs"--}}
{{--                                           data-toggle="tooltip" data-placement="top" title="{!! trans('system.action.delete') !!}">--}}
{{--                                            <i class="text-danger glyphicon glyphicon-remove"></i>--}}
{{--                                        </a>--}}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-info">{!! trans('system.no_record_found') !!}</div>
        @endif
    </section>
@stop
@section('footer')
    <script src="{!! asset('assets/backend/plugins/daterangepicker/moment.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/daterangepicker/daterangepicker.js') !!}"></script>
    <script>
        !function ($) {
            $(function(){
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