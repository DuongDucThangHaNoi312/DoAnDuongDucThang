@extends('backend.master')
@section('title')
    {!! trans('system.action.list') !!} {!! trans('departments.label') !!}
@stop
@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .modal-body .row {
            margin: auto;
        }

        .modal-body .col-md-4 {
            text-align: left;
        }

        .modal-body .col-md-8 {
            text-align: left;
        }

        #file-input {
            display: block;
            position: relative;
            width: 180px;
            margin: auto;
            cursor: pointer;
            border: 0;
            height: 50px;
            outline: 0;
        }

        #file-input:hover:after, .foo:focus {
            background: #34495E;
            color: #39D2B4;
        }

        #file-input:after {
            transition: 200ms all ease;
            background: #39D2B4;
            color: #fff;
            font-size: 16px;
            text-align: center;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: block;
            content: 'Chọn file excel';
            line-height: 50px;
            /*     border-radius: 5px; */
        }

        #show-file {
            font-style: italic;
            font-size: 1em;
            font-weight: bold;
        }

        #show-file:not(:empty):before {
            content: "File đã chọn: ";
            font-style: normal;
            font-weight: normal;
        }

        .dataTables_filter {
            display: none;
        }

        table {
            width: 100% !important;
        }

        table.excel thead tr th {
            background: #B7DEE8;
        }
        .tab {
            padding: 7px 0;
        }

        .tab span {
            margin: 0 1px;
        }

        .tab span:first-child {
            margin-left: 0;
        }

        .tab span a {
            background-color: #c8d2e0;
            border-color: #c8d2e0;
            color: #FFFFFF;
            padding: 8px 9px;
        }

        .active-tab {
            background: #3c8dbc !important;
            border-color: #3c8dbc !important;
        }
        table.dataTable thead > tr:nth-child(2) > th {
            padding-right: 10px;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/datatables/dataTables.bootstrap.css') !!}" />
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') !!}"/>
@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('departments.label') !!}
            <small>{!! trans('system.action.list') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.departments.index') !!}">{!! trans('departments.label') !!}</a></li>
        </ol>
    </section>
    <section class="content overlay">
        <div class="row">
            <div class="col-md-4">
                <div class="btn-group">
                    <a href="{!! route('admin.departments.create') !!}" class='btn btn-primary btn-flat'>
                        <span class="glyphicon glyphicon-plus"></span>&nbsp;{!! trans('system.action.create') !!}
                    </a>
                </div>
            </div>
        </div>
        <div class="box">
            <?php $i = 1; ?>
            <div class="box-body no-padding1">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="tableUser">
                        <thead>
                            <tr>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('system.no.') !!}</th>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('departments.name') !!}</th>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('departments.telephone') !!}</th>
                                <th style="text-align: center; vertical-align: middle;">{!! 'Loại phòng' !!}</th>
                                <th style="text-align: center; vertical-align: middle; width: 14%">{!! trans('system.action.label') !!}</th>
                            </tr>
                        </thead>
                        @if (count($departmentGroupsName) > 0)
                        <tbody>
                            <?php $i = 1; ?>
                            @foreach ($departmentGroupsName as $key => $value)
                                <tr>
                                    <td style="text-align: center; vertical-align: middle;"> {!! $i++ !!}</td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        <a>{!! $key !!}</a>
                                    </td>
                                    <td style="text-align: center; vertical-align: middle;">{!! $value[0]->telephone !!}</td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        @if ( $value[0]->is_ph == 1)
                                        <span class="label label-success" style="font-size: 14px">Phòng họp</span><br>
                                        @else
                                        <span class="label label-default" style="font-size: 14px">Phòng làm việc</span><br>
                                        @endif
                                    </td>
                                    {{-- <td style="text-align: center; vertical-align: middle;"> 
                                        @if ( $value[0]->is_ph == 1)
                                            {!! App\Helper\HString::currencyFormat($value[0]->price) !!} 
                                        @endif
                                    </td> --}}
                                    <td style="text-align: center; vertical-align: middle;">
                                        <div class="col-md-1">
                                            <a href="{!! route('admin.departments.show', $value[0]->id) !!}" class="btn-detail btn btn-default btn-xs"
                                                data-toggle="tooltip" data-placement="top" title="{!! trans('system.action.detail') !!}">
                                                <i class="text-info glyphicon glyphicon-eye-open"></i>
                                            </a>
                                        </div>
                                        <div class="col-md-1">
                                            <a href="{!! route('admin.departments.edit', $value[0]->id) !!}" data-toggle="tooltip" data-placement="top"
                                                class="btn btn-xs btn-default" title="{!! trans('system.action.update') !!}"><i
                                                    class="text-warning glyphicon glyphicon-edit"></i></a>
                                        </div>
                                       
                                        <div class="col-md-1">
                                            <a href="javascript:void(0)" data-toggle="tooltip" data-placement="top"
                                                link="{!! route('admin.departments.destroy', $value[0]->id) !!}"
                                                class="btn-confirm-del btn btn-default btn-xs"
                                                title="{!! trans('system.action.delete') !!}">
                                                <i class="text-danger glyphicon glyphicon-remove"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        @endif
                    </table>
                </div>
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
    <script src="{!! asset('assets/backend/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.vi.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/datatables/jquery.dataTables.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/datatables/dataTables.bootstrap.min.js') !!}"></script>
    <script>
        let typeParam = new URL(location.href).searchParams.get("type");
        if (!typeParam) {
            $('.tab span.active-staff a').addClass('active-tab')
        } else if (typeParam == 0) {
            $('.tab span a').removeClass('active-tab')
            $('.tab span.noactive-staff a').addClass('active-tab')
        }
        $(document).ready(function() {
            $(".select2").select2({width: '100%'});
            $('#tableUser thead tr').clone(true).appendTo('#tableUser thead');
            $('#tableUser thead tr:eq(1) th').each(function (i) {
               
                if (i != 0 && i != 4) {
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
            var table = $('#tableUser').DataTable({
                orderCellsTop: true,
                fixedHeader: true,
                pageLength: 20,
                lengthChange: false,
                responsive: true,
                rowReorder: true,
                // ordering: false,
                pagingType: "full_numbers",
                // columnDefs: [
                //     {orderable: false, className: 'reorder', targets: [7,8]},
                //     {orderable: false, targets: 0}
                // ],
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
                },
                dom: '<"top "i>rt<"bottom"flp>',
            });
            window.setNoAfterSearchDatatables(table, 0)
            
        });
    </script>
@stop
