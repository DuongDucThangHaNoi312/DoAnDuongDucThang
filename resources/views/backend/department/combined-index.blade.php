@extends('backend.master')
@section('title')
    {!! trans('system.action.list') !!} {!! trans('departments.combined') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">

    <style>
        .dataTables_filter {
            display: none;
        }
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
        .select2-container--default .select2-selection--single {
            height: 28px !important;
            border-radius: 3px !important;
            border: 1px solid #ddd !important;
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
            {!! trans('departments.combined') !!}
            <small>{!! trans('system.action.list') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.combined.index') !!}">{!! trans('departments.combined') !!}</a></li>
        </ol>
    </section>
    <section class="content overlay">

        <div class="row">
            <div class="col-md-4">
                <a href="{!! route('admin.combined.create') !!}" class='btn btn-primary btn-flat'>
                    <span class="glyphicon glyphicon-plus"></span>&nbsp;{!! trans('system.action.create') !!}
                </a>
            </div>

{{--            <div class="col-md-8 text-right">--}}
{{--                {!! $departmentGroup->appends( Request::except('page') )->render() !!}--}}
{{--            </div>--}}
        </div>
        @if ((count($departmentGroup)) > 0)
            <div class="box">
                <div class="box-body no-padding">
                    <table class="table table-striped table-hover table-bordered" id="tableCombiend" >
                        <thead>
                        <tr>
                            <th style="text-align: center; vertical-align: middle;">{!! trans('system.no.') !!}</th>
                            <th style="text-align: center; vertical-align: middle;">{!! trans('departments.combined_name') !!}
                            <th style="text-align: center; vertical-align: middle;">{!! trans('departments.combined_member') !!}
                            <th style="text-align: center; vertical-align: middle;" class="type_office">{!! trans('departments.type') !!}
                            <th style="text-align: center; vertical-align: middle;">{!! trans('departments.combined_only_manager') !!}</th>
                            <th style="text-align: center; vertical-align: middle;" class="status_department_combiend">{!! trans('departments.status.label') !!}</th>
                            <th style="text-align: center; vertical-align: middle;">{!! trans('system.action.label') !!}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i = 1 ?>
                        @foreach ($departmentGroup as $item)
                            <?php $allName = ''; ?>
                            <tr>
                                <td style="text-align: center; vertical-align: middle;">{!! $i++ !!}</td>
                                <td style="text-align: left; vertical-align: middle;">
                                   <a>{!! $item->name !!}</a>
                                </td>
                                <td style="">
                                    @foreach(\App\Models\DepartmentRelationship::where('group_id', $item->id)->get() as $department)
                                        <?php $dept = \App\Models\Department::where('id', $department->department_id)->with('company')->first();
                                            $comName = $dept->company ? $dept->company->shortened_name : '';
                                            $deptName = is_null($dept) ? '' : $dept->name;
                                            $allName .= $comName .' - '.$deptName.',';
                                        ?>
                                    @endforeach
                                        {!! rtrim($allName, ',')  !!}
                                </td>
                                <td style="text-align: left; vertical-align: middle;" class="type_office">
                                    {!! \App\Define\Department::getTypeDepartmentGroups()[$item->type] ?? '' !!}
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    @if($item->only_manager == 0)
                                        <span class="label label-danger "><i class="fas fa-times"></i></span>
                                    @elseif($item->only_manager == 1)
                                        <span class="label label-success"> <i class=" fas fa-check"></i></span>
                                    @endif
                                </td>
                                <td style="text-align: center; vertical-align: middle;" class="status_department_combiend">
                                    @if($item->status == 0)
                                        <span class="label label-default">{!! trans('system.status.deactive') !!}</span>
                                    @elseif($item->status == 1)
                                        <span class="label label-success">{!! trans('system.status.active') !!}</span>
                                    @endif
                                </td>
                                <td style="text-align: center; vertical-align: middle; white-space: nowrap;">
                                    <a href="{!! route('admin.combined.edit', $item->id) !!}"
                                       class="btn btn-xs btn-default"><i
                                                class="text-warning glyphicon glyphicon-edit"></i></a>
                                    &nbsp;&nbsp;
                                    <a href="javascript:void(0)"
                                       link="{!! route('admin.combined.destroy', $item->id) !!}"
                                       class="btn-confirm-del btn btn-default btn-xs">
                                        <i class="text-danger glyphicon glyphicon-remove"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="alert alert-info">{!! trans('system.no_record_found') !!}</div>
        @endif
    </section>
@stop
@section('footer')
    <script src="{!! asset('assets/backend/plugins/iCheck/icheck.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/select2/select2.full.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/moment/min/moment-with-locales.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/input-mask/jquery.inputmask.min.js') !!}"></script>
    <script type="text/javascript" charset="utf8"
            src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>
    <script>
        !function ($) {
            $(function () {

                $(".select2").select2({width: '100%'});
                $('#tableCombiend thead tr').clone(true).appendTo('#tableCombiend thead');
                $('#tableCombiend thead tr:eq(1) th').each(function (i) {

                    if (i == 1 || i ==2) {
                        $(this).html('<input type="text" class="search-form input-text" autocomplete="off" />');
                    } else {
                        $(this).html('');
                    }

                    $('input', this).on('keyup change', function () {
                        if (table.column(i).search() !== this.value) {
                            table
                                .column(i)
                                .search(this.value)
                                .draw();
                        }
                    });
                });

                var table = $('#tableCombiend').DataTable({
                    orderCellsTop: true,
                    fixedHeader: true,
                    paging: false,
                    lengthChange: false,
                    ordering:  false,
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
                        'emptyTable': "<span class='text-size center'><i class='fas fa-search'></i> {!! trans('staff_positions.no_data') !!}</span>",
                        'zeroRecords': "<span class='text-size center'><i class='fas fa-search'></i> {!! trans('staff_positions.no_data') !!}</span>",
                        "processing": '<div class="widget-loader" id="loader"><div class="load-dots"><span></span><span></span><span></span></div></div>',
                    },
                    dom : '<"top "i>rt<"bottom"flp>'

                });
                table.columns('.status_department_combiend').every(function () {
                    var that = this;
                    var select = $('{!! Form::select('status',['' => '',1 => trans('departments.active'),2=>trans('system.status.deactive')] ,'', ['class' => 'search-form status_select select2']) !!}')
                        .appendTo(
                            $('#tableCombiend thead tr:eq(1) th.status_department_combiend')
                        )
                        .on('change', function () {
                            var text = $('.status_select option:selected').text()
                            that
                                .search(text)
                                .draw();
                        });
                    $(".select2").select2({width: '100%'});
                });
                table.columns('.type_office').every(function () {
                    var that = this;
                    var select = $('{!! Form::select('type', ['' => '']+\App\Define\Department::getTypeDepartmentGroups() ,old('type'), ['class' => 'search-form type select2', ]) !!}')
                        .appendTo(
                            $('#tableCombiend thead tr:eq(1) th.type_office')
                        )
                        .on('change', function () {
                            var text = $('.type option:selected').text()
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