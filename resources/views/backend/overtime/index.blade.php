@extends('backend.master')
@section('title')
    {!! trans('system.action.list') !!} {!! trans('overtimes.label') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
    <link rel="stylesheet" type="text/css"
          href="{!! asset('assets/backend/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">

    <style>
        table{
            width: 100% !important;
        }
        .search-form{
            width: 100%;
            background-color: #fff;
            color: #2780d1;
            transition: .3s;
            margin: 1px 0;
            outline: 0;
            box-shadow: inset 0 0 0 transparent;
            height: 28px;
            font-size: 13px;
            line-height: 1.42857143;
            padding: 2px 10px;
            border-radius: 3px;
            border: 1px solid #e7e6e6;
            background-size: 10px;
            background-position: 95% 8px;
            font-weight: normal
        }
        .input-text{

            background: url(https://upload.wikimedia.org/wikipedia/commons/thumb/0/0b/Search_Icon.svg/1024px-Search_Icon.svg.png) no-repeat;
            background-size: 10px;
            background-position: 95% 8px;
        }
        .date{

            background: url(https://images.echocommunity.org/85032db6-de87-47fc-abaf-d1fa3a5f498f/calendar-icon-marketing-image.png?w=600) no-repeat;
            background-size: 10px;
            background-position: 95% 8px;
        }
        .dataTables_filter {
            display: none;
        }
        .select2-container--default .select2-selection--single {
            height: 28px !important;
            border-radius: 3px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 24px !important;
            font-weight: normal;
        }
    </style>
@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('overtimes.label') !!}
            <small>{!! trans('system.action.list') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.overtimes.index') !!}">{!! trans('overtimes.label') !!}</a></li>
        </ol>
    </section>
    <section class="content overlay">
        <div class="row">
            <div class="col-md-2">
                <a href="{!! route('admin.overtimes.create') !!}" class='btn btn-primary btn-flat'>
                    <span class="glyphicon glyphicon-plus"></span>&nbsp;{!! trans('system.action.create') !!}
                </a>
            </div>
            <div class="col-md-10 text-right">
            </div>
        </div>

        <div class="box">
            <div class="box-header" style="padding: 0">
                <?php $i = 1; ?>
            </div>
            <div class="box-body no-padding">
                <table class="table table-striped table-hover" id="tableOT">
                    <thead>
                    <tr>
                        <th style="text-align: center; vertical-align: middle;">{!! trans('system.no.') !!}</th>
                        <th style="text-align: center; vertical-align: middle;" class="company_id">{!! trans('overtimes.company_id') !!}
                        <th style="text-align: center; vertical-align: middle;" class="department_id">{!! trans('overtimes.department_id') !!}
                        <th style="text-align: center; vertical-align: middle;">{!! trans('overtimes.dates') !!}
                        <th style="text-align: center; vertical-align: middle; " width="130px">{!! trans('overtimes.hours') !!}</th>
                        <th style="text-align: center; vertical-align: middle;" class="status">{!! trans('overtimes.status.label') !!}</th>
                        <th style="text-align: center; vertical-align: middle;">{!! trans('system.action.label') !!}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if ((count($overtime)) > 0)
                        @foreach ($overtime as $item)
                            <tr>
                                <td style="text-align: center; vertical-align: middle;">{!! $i++ !!}</td>
                                <td style="text-align: center; vertical-align: middle;" class="company_id">
                                    {!! \App\Models\Company::find($item->company_id)->shortened_name !!}
                                </td>
                                <td style="text-align: center; vertical-align: middle;" class="department_id">
                                    {!! \App\Models\Department::find($item->department_id)->name !!}
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    {!! $item->start_date->format('d/m/Y') !!}
                                    {!! $item->end_date ? '=>'.$item->end_date->format('d/m/Y') : '' !!}
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    {!! $item->overtime_hours !!}
                                </td>
                                <td style="text-align: center; vertical-align: middle;" class="status">
                                    @if($item->status == 1)
                                        {!! trans('overtimes.every_week') !!}
                                    @elseif($item->status == 2)
                                        {!! trans('overtimes.many_day') !!}
                                    @else
                                        {!! trans('overtimes.one_day') !!}
                                    @endif
                                </td>
                                <td style="text-align: center; vertical-align: middle; white-space: nowrap;">
                                    <a href="{!! route('admin.overtimes.show', $item->id) !!}"
                                       class="btn-detail btn btn-default btn-xs"
                                       data-toggle="tooltip" data-placement="top"
                                       title="{!! trans('system.action.detail') !!}">
                                        <i class="text-info glyphicon glyphicon-eye-open"></i>
                                    </a>
                                    <a href="{!! route('admin.overtimes.edit', $item->id) !!}"
                                       class="btn btn-xs btn-default"><i
                                                data-toggle="tooltip" data-placement="top"
                                                title="{!! trans('system.action.update') !!}"
                                                class="text-warning glyphicon glyphicon-edit"></i></a>
                                    <a href="javascript:void(0)"
                                       link="{!! route('admin.overtimes.destroy', $item->id) !!}"
                                       data-toggle="tooltip" data-placement="top"
                                       title="{!! trans('system.action.delete') !!} "
                                       class="btn-confirm-del btn btn-default btn-xs">
                                        <i class="text-danger glyphicon glyphicon-remove"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @else

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
    <script type="text/javascript" charset="utf8"
            src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>

    <script>
        !function ($) {
            $(function () {

                $(".select2").select2({width: '100%'});
                $('#tableOT thead tr').clone(true).appendTo('#tableOT thead');
                $('#tableOT thead tr:eq(1) th').each(function (i) {
                    if (i == 4 || i == 3 ) {
                        $(this).html('<input type="text" class="search-form input-text" autocomplete="off" />');
                    } else {
                        $(this).html('');
                    }
                    $('.datepicker').datepicker({
                        format: 'dd/mm/yyyy',
                        autoclose: true,
                        language: 'vi',
                        orientation: "bottom auto"
                    });

                    $('input', this).on('keyup change', function () {
                        if (table.column(i).search() !== this.value) {
                            table
                                .column(i)
                                .search(this.value)
                                .draw();
                        }
                    });
                });

                var table = $('#tableOT').DataTable({
                    orderCellsTop: true,
                    fixedHeader: true,
                    pageLength: 20,
                    lengthChange: false,
                    rowReorder: true,
                    ordering: false,
                    pagingType: "full_numbers",
                    language: {
                        "info": "Hiển thị _START_ - _END_ của _TOTAL_ kết quả",
                        "paginate": {
                            "first": "«",
                            "last": "»",
                            "next": "→",
                            "previous": "←"
                        },
                        "infoFiltered": " ( trong tổng số _MAX_ kết quả)",
                        'emptyTable': "<span class='text-size center'><i class='fas fa-search'></i> {!! trans('staff_positions.no_data') !!}</span>"


                    },
                    dom: '<"top "i>rt<"bottom"flp>',

                });
                table.columns('.company_id').every(function () {
                    var that = this;
                    var select = $('{!! Form::select('company_id', $companysOption ? ['' =>  ''] +$companysOption  : ['' => '']+ \App\Define\OverTime::getCompanyNamesForOption() ,'', ['class' => 'search-form companySelect select2']) !!}')
                        .appendTo(
                            $('#tableOT thead tr:eq(1) th.company_id')
                        )
                        .on('change', function () {
                            var text = $('.companySelect option:selected').text()
                            that
                                .search(text)
                                .draw();
                        });
                    $(".select2").select2({width: '100%'});
                });
                table.columns('.department_id').every(function () {
                    var that = this;
                    var select = $('{!! Form::select('department_id', $departmentOption ? ['' =>  ''] +$departmentOption  : ['' => '']+ \App\Helpers\GetOption::getAllDepartmentsForOption() ,'', ['class' => 'search-form department_select select2']) !!}')
                        .appendTo(
                            $('#tableOT thead tr:eq(1) th.department_id')
                        )
                        .on('change', function () {
                            var text = $('.department_select option:selected').text()
                            that
                                .search(text)
                                .draw();
                        });
                    $(".select2").select2({width: '100%'});
                });
                table.columns('.status').every(function () {
                    var that = this;
                    var select = $('{!! Form::select('status',['' => '',
                                                        0 => trans('overtimes.one_day'),
                                                        1 => trans('overtimes.every_week'),
                                                        2 => trans('overtimes.many_day')] ,Request::input('status'), ['class' => 'search-form status_select']) !!}')
                        .appendTo(
                            $('#tableOT thead tr:eq(1) th.status')
                        )
                        .on('change', function () {
                            var text = $('.status_select option:selected').text()
                            that
                                .search(text)
                                .draw();
                        });
                    $(".select2").select2({width: '100%'});
                });

            });
        }(window.jQuery);
    </script>
@stop