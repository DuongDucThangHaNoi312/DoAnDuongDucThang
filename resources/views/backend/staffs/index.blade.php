@extends('backend.master')
@section('title')
    {!! trans('system.action.list') !!} {!! trans('staffs.label') !!}
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
    @include('backend.header.content_header',['name'=>'staffs.label','key'=>'staffs.list'])
    @include('backend.staffs._modal_excel')
    <section class="content overlay">
        @permission('staffs.update')
        <div class="row">
            <div class="col-md-4">
                <div class="btn-group">
                    <a href="{!! route('admin.staffs.create') !!}" class='btn btn-primary btn-flat'>
                        <span class="glyphicon glyphicon-plus"></span>&nbsp;{!! trans('system.action.create') !!}
                    </a>
                </div>
                <div class="btn-group">
                    <a href="{!! route('admin.staffs.create-bulk') !!}" class='btn btn-info btn-flat'>
                        <span class="glyphicon glyphicon-import"></span>&nbsp;{!! trans('system.action.import') !!}
                    </a>
                </div>
                <div class="btn-group">
                    <span class='btn btn-success btn-flat show-user-export'>
                        <span class="glyphicon glyphicon-export"></span>&nbsp;{!! trans('system.action.export') !!}
                    </span>
                </div>
            </div>
        </div>
        @endpermission
        <div class="box">
            {{--<div class="box-header">
            </div>--}}
            <?php $i = 1; ?>
            <div class="box-body no-padding1">
                <div class="table-responsive">
                    <div class="tab">
                        <span class="active-staff">
                            <a href="{!! route('admin.staffs.index') !!}"
                            class="active-tab"
                            style="outline: none;">
                             Đang hoạt động
                            </a>
                        </span>
                        <span class="noactive-staff">
                            <a href="{!! route('admin.staffs.index', ['type' => 0]) !!}"
                            style="outline: none;">
                             Không hoạt động
                            </a>
                        </span>
                    </div>
                    <table class="table table-striped table-bordered table-hover" id="tableUser">
                        <thead>
                            <tr>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('system.no.') !!}</th>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('staffs.code') !!}</th>
                                <th style="text-align: center; vertical-align: middle; min-width: 100px">{!! trans('staffs.fullname') !!}</th>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('contracts.company_id') !!}</th>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('contracts.department_id') !!}</th>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('staffs.addresses') !!}</th>
                                <th style="text-align: center; vertical-align: middle; white-space: nowrap">{!! trans('staffs.date_of_birth') !!}</th>
                                <th style="text-align: center; vertical-align: middle; white-space: nowrap">{!! trans('staffs.code_timekeeping') !!}<br>Mã phụ</th>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('system.action.label') !!}</th>
                            </tr>
                        </thead>
                        @if (count($users) > 0)
                        <tbody>
                            @foreach ($users as $item)
                                <tr data-id="{{$item->id}}">
                                    {!! Form::hidden('userId[]', $item->id, ["class" => 'user-id']) !!}
                                    <td style="text-align: center; vertical-align: middle;">{!! $i++ !!}</td>
                                    <td style="vertical-align: middle;text-align: center">
                                        <a data-toggle="tooltip" title="Chi tiết" href="{!! route('admin.staffs.show', $item->id) !!}">{!! $item->code !!}</a>
                                    </td>
                                    <td style="vertical-align: middle;text-align: left">
                                        {!! $item->fullname !!}
                                    </td>

                                    <td style="text-align: center; vertical-align: middle;">
                                        {!! $item->company->shortened_name !!}
                                    </td>
                                    <td style="text-align: left; vertical-align: middle;">
                                        {!! $item->department->name !!}
                                    </td>
                                    <td style="text-align: left; vertical-align: middle;">
                                        {!! $item->addresses !!}
                                    </td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        <span class="label label-default">{!! date('d/m/Y', strtotime($item->date_of_birth)) !!}</span>
                                    </td>
                                    <td style="text-align: center; vertical-align: middle; white-space: nowrap">
                                        {!! $item->code_timekeeping !!}
                                        @if($item->code_timekeeping_subs)
                                            <br>--<br>
                                            {!! $item->code_timekeeping_subs !!}
                                        @endif
                                    </td>
                                    <td style="text-align: center; vertical-align: middle; white-space: nowrap;">&nbsp;&nbsp;
                                        <div class="row">
                                            @permission('staffs.update')
                                            @if($item->active)
                                                <div class="col-md-1">
                                                    <a data-toggle="tooltip" title="Đổi mật khẩu"
                                                       href="{!! route('admin.staffs.update_password', $item->id) !!}"
                                                       class="btn btn-default btn-xs"><i class="text-info fa fa-key"></i> </a>
                                                </div>
                                            @endif
                                            <div class="col-md-1">
                                                <a data-toggle="tooltip" title="Cập nhật"
                                                   href="{!! route('admin.staffs.edit', $item->id) !!}"
                                                   class="btn btn-xs btn-default"><i
                                                            class="text-warning glyphicon glyphicon-edit"></i></a>
                                            </div>
                                            @endpermission

                                            @permission('staffs.delete')
                                            <div class="col-md-1">
                                                <a data-toggle="tooltip" title="Xóa" href="javascript:void(0)"
                                                   link="{!! route('admin.staffs.destroy', $item->id) !!}"
                                                   class="btn-confirm-del btn btn-default btn-xs"><i
                                                            class="text-danger glyphicon glyphicon-remove"></i></a>
                                            </div>
                                            @endpermission

                                            @permission('staffs.roles')
                                            @if($item->active == 1 && \Auth::id() != $item->id)
                                                <div class="col-md-1">
                                                    <a data-toggle="tooltip" title="Thêm quyền cho nhân viên"
                                                       href="{!! route('admin.staffs.roles', $item->id) !!}"
                                                       class="btn btn-xs btn-default"><i
                                                                class="fas fa-user-plus"></i></a>
                                                </div>
                                            @endif
                                            @endpermission
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
            $('.show-user-excel').on('click', function (e) {
                $('#modal-excel').modal("show");
            });
            $('#tableUser thead tr').clone(true).appendTo('#tableUser thead');
            $('#tableUser thead tr:eq(1) th').each(function (i) {
                if (i == 6) {
                    $(this).html('<input type="text" class="search-form datepicker date" autocomplete="off" />');
                } else if (i != 0 && i != 8) {
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
                columnDefs: [
                    {orderable: false, className: 'reorder', targets: [7,8]},
                    {orderable: false, targets: 0}
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
                    'emptyTable': "<span class='text-size center'><i class='fas fa-search'></i> {!! trans('staff_positions.no_data') !!}</span>",
                    'zeroRecords': "<span class='text-size center'><i class='fas fa-search'></i> {!! trans('staff_positions.no_data') !!}</span>",
                },
                dom: '<"top "i>rt<"bottom"flp>',
            });
            window.setNoAfterSearchDatatables(table, 0)
            let users = {!! json_encode($users) !!};
            var userIds = [];
            $('.show-user-export').on('click', function (e) {
                let test = table.rows({search: 'applied'}).nodes()
                $.each(test, function (i, e) {
                    userIds.push(e.dataset.id)
                })
                $('#modal-excel').modal("show");
                // userIds = $('.user-id').map((i, e) => e.value).get();
            })
            $("#modal-excel").on("hidden.bs.modal", function () {
                userIds = []
                $(this).removeData();
                $(this).find('input.user-input-excel').remove()
                $('#show').html('')
            });
            $('#btn-export').on('click', function (e) {
                e.preventDefault();
                if (userIds.length === 0) {
                    toastr.warning('Không có dữ liệu để xuất.')
                    return false
                }
                for (var key in userIds) {
                    let input1 = `<input type="hidden" class="user-input-excel" name="userIds[${key}]" value="${userIds[key]}">`
                    $('#excel-form').append(input1);
                }
                document.getElementById('excel-form').submit();
            })
            
        });
    </script>
@stop
