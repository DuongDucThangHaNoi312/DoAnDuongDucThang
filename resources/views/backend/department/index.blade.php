@extends('backend.master')
@section('title')
    {!! trans('system.action.list') !!} {!! trans('departments.label') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}" />
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}" />
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/datatables/jquery.dataTables.min.css') !!}" />
    <style>
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0;
        }

    </style>
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
        @permission('departments.create')
            <div class="row">
                <div class="col-md-6">
                    <a href="{!! route('admin.departments.create') !!}" class='btn btn-primary btn-flat'>
                        <span class="glyphicon glyphicon-plus"></span>&nbsp;{!! trans('system.action.create') !!}
                    </a>

                </div>
            </div>
        @endpermission
        @if (count($departmentGroupsName) > 0)
            <div class="box">
                <div class="box-body no-padding">
                    {!! Form::open(['url' => route('admin.departments.index'), 'method' => 'GET', 'role' => 'search']) !!}
                    <table class="table table-bordered table-hover" id="tableDepartment" style="width: 100%">
                        <thead>
                            <tr>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('system.no.') !!}</th>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('departments.name') !!}</th>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('departments.code') !!}</th>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('departments.telephone') !!}</th>
                                <th style="text-align: center; vertical-align: middle; width: 14%">{!! trans('system.action.label') !!}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            @foreach ($departmentGroupsName as $key => $value)
                                <tr>
                                    <td style="text-align: center; vertical-align: middle;"> {!! $i++ !!}</td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        <a>{!! $key !!}</a>
                                    </td>
                                    <td style="text-align: center; vertical-align: middle;"> {!! $value[0]->code !!}</td>
                                    <td style="text-align: center; vertical-align: middle;">{!! $value[0]->telephone !!}</td>
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
                    </table>
                    {!! Form::close() !!}
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
    <script src="{!! asset('assets/backend/plugins/datatables/jquery.dataTables.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/datatables/dataTables.bootstrap.min.js') !!}"></script>
    <script
        src="https://cdn.jsdelivr.net/gh/ashl1/datatables-rowsgroup@fbd569b8768155c7a9a62568e66a64115887d7d0/dataTables.rowsGroup.js">
    </script>
    <script>
        jQuery(document).ready(function($) {
            $(".select2").select2({
                width: '100%'
            });
            $('#tableDepartment thead tr').clone(true).appendTo('#tableDepartment thead');
            $('#tableDepartment thead tr:eq(1) th').each(function(i) {
                if (i != 0 && i != 4) {
                    $(this).html(
                        '<input type="text" class="search-form input-text" autocomplete="off"  />');
                } else {
                    $(this).html('');
                }
                $('input', this).on('keyup change', function() {
                    if (table.column(i).search() !== this.value) {
                        table.column(i).search(this.value).draw();
                    }
                });
            });
            var table = $('#tableDepartment').DataTable({
                orderCellsTop: true,
                fixedHeader: true,
                pageLength: 20,
                // lengthChange: false,
                // paging: false,
                ordering: false,
                pagingType: "full_numbers",
                rowsGroup: [1],
                lengthMenu: [
                    [20, 50, -1],
                    [20, 50, "All"]
                ],
                language: {
                    "info": "Hiển thị _START_ - _END_ của _TOTAL_ kết quả",
                    "paginate": {
                        "first": "«",
                        "last": "»",
                        "next": "→",
                        "previous": "←"
                    },
                    "infoFiltered": " ( trong tổng số _MAX_ kết quả)",
                    "lengthMenu": "Hiển thị _MENU_ bản ghi",
                },
                // dom: '<"top "i>rt<"bottom"flp>'
            });
            $(".dataTables_length select").select2();

        });
    </script>
@stop
