@extends('backend.master')

@section('title')
    {!! trans('system.action.list') !!} {!! trans('contracts.label') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css"
          href="{!! asset('assets/backend/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') !!}"/>
    <link rel="stylesheet" type="text/css"
          href="{!! asset('assets/backend/plugins/daterangepicker/daterangepicker.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
    {{--<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.css">--}}
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/datatables/jquery.dataTables.min.css') !!}" />
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/>

    <style>
        th, td {
            text-align: center;
            vertical-align: middle;
        }

        td span.label {
            padding: 5px 5px;
        }

        .dataTables_filter {
            display: none;
        }

        table {
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single {
            height: 28px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 24px !important;
            font-weight: normal;
        }

        .modal-export .modal-body {
            padding-bottom: 0;
        }

        .modal-export .modal-footer {
            padding: 10px;
        }

        .tab {
            padding: 2px 0;
        }

        .tab span:first-child {
            margin-left: 0;
        }
        .tab span {
            margin: 2px 1px;
            min-width: 100px;
            min-height: 35px;
            background-color: #c8d2e0;
            /*border-color: #c8d2e0;*/
            border-color: #3c8dbc;
            text-align: center;
            vertical-align: middle;
        }

        .tab span:hover {
            background: #3c8dbc;
        }
        .tab span a {
            vertical-align: auto;
            color: #FFFFFF;
            /*padding: 8px 9px;*/
            /*min-width: 100px;*/
            /*min-height: 30px;*/

        }

        .active-tab {
            background: #3c8dbc !important;
            border-color: #3c8dbc !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0;
        }
        .tab-contract {
            display: flex;
            flex-wrap: wrap;
        }
    </style>
@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('contracts.label') !!}
            <small>{!! trans('system.action.list') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.contracts.index') !!}">{!! trans('contracts.label') !!}</a></li>
        </ol>
    </section>
    <section class="content overlay">
        @permission('contracts.create')
        <div class="row">
            <div class="col-md-6">
                <a href="{!! route('admin.contracts.create') !!}" class='btn btn-primary btn-flat'>
                    <span class="glyphicon glyphicon-plus"></span>&nbsp;{!! trans('system.action.create') !!}
                </a>
                <div class="btn-group">
                    <a href="{!! route('admin.contracts.create-bulk') !!}" class='btn btn-info btn-flat'>
                        <span class="glyphicon glyphicon-import"></span>&nbsp;{!! trans('system.action.import') !!}
                    </a>
                </div>
                <div class="btn-group">
                    <span class='btn btn-success btn-flat show-contract-excel'>
                        <span class="glyphicon glyphicon-export"></span>&nbsp;{!! trans('system.action.export') !!}
                    </span>
                </div>
            </div>
        </div>
        @endpermission
        <div class="box">
            <div class="box-header" style="padding: 0">
                <?php $i = 1; $typeActive = \App\Defines\Contract::ACTIVE; ?>

            </div>
            <div class="box-body no-padding">
                <div class="table-responsive">
                    <div class="tab tab-contract">
                        <span class="all">
                             <a href="{!! route('admin.contracts.index') !!}"
                                data-toggle="tooltip" data-placement="top" title="Tất cả"
                                style="outline: none;">
                                 Tất cả
                             </a>
                        </span>
                        <span class="active-contract">
                             <a href="{!! route('admin.contracts.index', ['type' => \App\Defines\Contract::ACTIVE]) !!}"
                                data-toggle="tooltip" data-placement="top" title="Đang hoạt động"
                                style="outline: none;">
                                 Đang hoạt động
                             </a>
                        </span>
                        <span class="leave-work">
                             <a href="{!! route('admin.contracts.index', ['type' => \App\Defines\Contract::LEAVE_WORK]) !!}"
                                data-toggle="tooltip" data-placement="top" title="Nghỉ việc"
                                style="outline: none;">
                                 Nghỉ việc
                             </a>
                        </span>
                        <span class="transfer-full">
                             <a href="{!! route('admin.contracts.index', ['type' => \App\Defines\Contract::TRANSFER, 'child-type' => 1]) !!}"
                                data-toggle="tooltip" data-placement="top" title="Đã điều chuyển"
                                style="outline: none;">
                                Đã điều chuyển
                             </a>
                        </span>
                        <span class="transfer-half">
                             <a href="{!! route('admin.contracts.index', ['type' => \App\Defines\Contract::TRANSFER, 'child-type' => 2]) !!}"
                                data-toggle="tooltip" data-placement="top" title="Điều chuyển chưa có hợp đồng mới"
                                style="outline: none;">
                                Đang điều chuyển
                             </a>
                        </span>
                        <span class="appoint-full">
                             <a href="{!! route('admin.contracts.index', ['type' => \App\Defines\Contract::APPOINT, 'child-type' => 1]) !!}"
                                data-toggle="tooltip" data-placement="top" title="Đã bổ nhiệm"
                                style="outline: none;">
                                Đã bổ nhiệm
                             </a>
                        </span>
                        <span class="appoint-half">
                             <a href="{!! route('admin.contracts.index', ['type' => \App\Defines\Contract::APPOINT, 'child-type' => 2]) !!}"
                                data-toggle="tooltip" data-placement="top" title="Bổ nhiệm nhưng chưa có hợp đồng mới."
                                style="outline: none;">
                                Đang bổ nhiệm
                             </a>
                        </span>
                        <span class="dismissal">
                             <a href="{!! route('admin.contracts.index', ['type' => \App\Defines\Contract::DISMISSAL]) !!}"
                                data-toggle="tooltip" data-placement="top" title="Miễn nhiệm."
                                style="outline: none;">
                               Miễn nhiệm
                             </a>
                        </span>
                        <span class="expired">
                             <a href="{!! route('admin.contracts.index', ['type' => \App\Defines\Contract::EXPIRED]) !!}"
                                data-toggle="tooltip" data-placement="top" title="Hợp đồng hết hạn"
                                style="outline: none;">
                               Hết thời hạn
                             </a>
                        </span>
                        <span class="future">
                             <a href="{!! route('admin.contracts.index', ['type' => \App\Defines\Contract::FUTURE]) !!}"
                                data-toggle="tooltip" data-placement="top" title="Hợp đồng chờ áp dụng"
                                style="outline: none;">
                               Chờ áp dụng
                             </a>
                        </span>
                    </div>

                    <table class="table table-striped table-hover table-bordered" id="tableContracts">
                        <thead>
                        <tr>
                            <th style="text-align: center; vertical-align: middle;">{!! trans('system.no.') !!}</th>
                            <th style="vertical-align: middle; white-space: nowrap">{!! trans('contracts.code') !!}</th>
                            <th style="vertical-align: middle; white-space: nowrap; min-width: 100px">{!! trans('contracts.staff_id') !!}</th>
                            <th style="vertical-align: middle; text-align: center;"
                                class="company_id">{!! trans('contracts.company_id') !!}</th>
                            <th style="vertical-align: middle; min-width: 120px"
                                class="department_id">{!! trans('contracts.department_id') !!}</th>
                            <th style="vertical-align: middle; width: 100px" class="position_id">{!! trans('contracts.position_id') !!}</th>
                            <th style="vertical-align: middle;">Thời hạn</th>
                            <th style="vertical-align: middle; min-width: 120px">{!! trans('contracts.remains') !!}</th>
                            <th style="text-align: center; vertical-align: middle;" class="status">{!! trans('system.status.label') !!}</th>
                            <th style="text-align: center; vertical-align: middle;">{!! trans('staffs.staff_start') !!}</th>
                            <th style="text-align: center; vertical-align: middle;">Hạng bằng LX oto</th>
                            <th style="text-align: center; vertical-align: middle; min-width: 100px; white-space: nowrap">{!! trans('system.action.label') !!}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $labels = ['success', 'info', 'danger', 'warning', 'default']; ?>
                        @if (count($contracts) > 0)
                            <?php $_STATUS_EXPIRED = \App\Defines\Contract::EXPIRED; ?>
                            @foreach ($contracts as $item)
                                {{--@if(\Request::get('type') != $_STATUS_EXPIRED && $item->type_status == $_STATUS_EXPIRED && $item->user && $item->user->active == 1) @continue; @endif--}}
                                <tr @if ($item->nearly_expired) style="background-color: #e48a96" @endif data-id="{{$item->id}}">
                                    {!! Form::hidden('contractIds[]', $item->id, ["class" => 'contract-id']) !!}
                                    <td style="text-align: center; vertical-align: middle;">{!! $i++ !!}</td>
                                    <td style="text-align: left; vertical-align: middle; white-space: nowrap;">
                                        <a href="{!! route('admin.contracts.show', $item->id) !!}"
                                           data-toggle="tooltip"
                                           data-placement="top"
                                           title="Chi tiết hợp đồng | Phụ lục">
                                            {!! $item->code !!}
                                        </a>
                                    </td>
                                    <td style="vertical-align: middle; text-align: left">{!! $item->user->fullname !!}
                                    </td>
                                    <td style="vertical-align: middle;" class="company_id">{!! $item->company->shortened_name !!}
                                    </td>
                                    <td style="vertical-align: middle; text-align: left" class="department_id">{!! $item->department->name !!}
                                    </td>
                                    <td style="vertical-align: middle;" class="position_id">{!! $item->position->name !!}
                                    </td>
                                    <td style="vertical-align: middle; text-align: center">
                                        <span class="label label-default">{!! date('d/m/Y', strtotime($item->valid_from)) !!}</span><br>
                                        <span class="label label-default">{!! $item->valid_to ? date('d/m/Y', strtotime($item->valid_to)) : null !!}</span>
                                    </td>
                                    <td data-order="{!! $item->count_expired !!}" style="vertical-align: middle;" title="{!! $item->set_notvalid_on !!}">
                                        @if($item->type_status == 1)
                                            {!! $item->time_remains !!}
                                        @endif
                                    </td>
                                    <td style="text-align: center; vertical-align: middle;" class="status"
                                        data-id= {!! $item->id !!}>
                                        @if($item->check_valid == \App\Defines\Contract::NOT_YET_VALID)
                                                {{--<span class="label label-default">Nháp</span>--}}
                                            <span class="label label-default">{!! trans('system.status.notyetvalid') !!}</span>
                                        @elseif($item->check_valid == \App\Defines\Contract::NOT_VALID)
                                            @if($item->type_status == \App\Defines\Contract::ACTIVE)
                                                <span class="label label-danger">{!! trans('contracts.no_status') !!}</span>
                                            @else
                                                <span class="label label-danger">{!! trans('contracts.type_status.' . $item->type_status) !!}</span>
                                            @endif
                                        @else
                                            @if($item->type_status == \App\Defines\Contract::ACTIVE)
                                                <span class="label label-success">{!! trans('contracts.type_status.' . $item->type_status) !!}</span>
                                            @else
                                                <span class="label label-danger">{!! trans('contracts.type_status.' . $item->type_status) !!}</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td style="vertical-align: middle; text-align: center; white-space: nowrap">{!! date('d/m/Y', strtotime($item->user->staff_start)) !!}
                                    </td>
                                    <td style="vertical-align: middle; text-align: center">{!! $item->user->driver_license_class !!}
                                    </td>
                                    <td style="text-align: center; vertical-align: middle; white-space:nowrap;">
                                        <a href="{!! route('admin.contracts.show', $item->id) !!}"
                                           class="btn-detail btn btn-default btn-xs"
                                           data-toggle="tooltip"
                                           data-placement="top"
                                           title="Chi tiết hợp đồng | Phụ lục">
                                            <i class="text-info glyphicon glyphicon-eye-open"></i>
                                        </a>
                                        <a href="{!! route('admin.contracts.edit', $item->id) !!}"
                                           class="btn btn-default btn-xs"
                                           data-toggle="tooltip" data-placement="top"
                                           title="{!! trans('system.action.update') !!}">
                                            <i class="text-warning glyphicon glyphicon-edit"></i>
                                        </a>
                                    <!-- <a href="{!! route('admin.contracts.export', $item->id) !!}"
                                           class="btn btn-default btn-xs"
                                           data-toggle="tooltip" data-placement="top" title="Xuất file word"
                                           data-id="{!! $item->id !!}" style="outline: none;">
                                            <i class="text-success glyphicon glyphicon-save-file"></i>
                                        </a> -->
                                        <span class="btn-show-export btn btn-default btn-xs"
                                              data-id="{{$item->id}}"
                                              data-toggle="tooltip" data-placement="top"
                                              title="Tùy chọn xuất báo cáo">
                                            <i class="text-success glyphicon glyphicon-export"></i>
                                        </span>
                                        <a class="btn-copy btn btn-default btn-xs"
                                              data-id="{{$item->id}}"
                                              data-toggle="tooltip" data-placement="top"
                                              title="Copy"
                                              href="{!! route('admin.contracts.create', ['ref' => $item->id]) !!} ">
                                              <i class="text-warning far fa-copy"></i>
                                        </a>
                                        @if ($item->type_status == \App\Defines\Contract::FUTURE)
                                            <a href="javascript:void (0)"
                                               link="{!! route('admin.contracts.destroy', $item->id) !!}"
                                               class="btn-confirm-del btn btn-default btn-xs"
                                               data-toggle="tooltip" data-placement="top"
                                               title="{!! trans('system.action.delete') !!}">
                                                <i class="text-danger glyphicon glyphicon-remove"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{--        @else--}}
        {{--            <div class="alert alert-info"> {!! trans('system.no_record_found') !!}</div>--}}
        <div id="modal-area"></div>
        @include('backend.contracts.partitions._modal_excel')
    </section>
@stop
@section('footer')
    <script src="{!! asset('assets/backend/plugins/daterangepicker/moment.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/daterangepicker/daterangepicker.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/moment/locale/vi.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/select2/select2.full.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.vi.min.js') !!}"></script>
{{--    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.js"></script>--}}
    <script src="{!! asset('assets/backend/plugins/datatables/jquery.dataTables.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/datatables/dataTables.bootstrap.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/iCheck/icheck.min.js') !!}"></script>
    <script>
        !function ($) {
            $(function () {
                $('input[type="radio"]').iCheck({
                    radioClass: 'iradio_square-blue',
                });
                callSelect2()
                $(".tab span a").bind("click", function () {
                    $(this).addClass("btn-primary");
                });
                let contractIds = [];
                $('.show-contract-excel').on('click', function (e) {
                    let test = table.rows({search: 'applied'}).nodes()
                    // console.log($("table.dataTable").dataTable().$('tr', {"filter": "applied"}))
                    $.each(test, function (i, e) {
                        contractIds.push(e.dataset.id)
                    })
                    let v = $('#modal-excel').find('input[name="type_export"]')
                    let nameExcel = 'HopDong-'
                    if (v == 2) nameExcel = 'PhuLuc-'
                    else if (v == 3) nameExcel = 'KiemNhiem-'
                    else nameExcel = 'HopDong-'
                    nameExcel = nameExcel + moment().format('YYYY-MM-DD_HH-mm-ss')
                    $('input[name="name_excel"]').val(nameExcel)
                    $('#modal-excel').find('input[name="name_excel"]').val(nameExcel);
                    $('#modal-excel').modal("show");
                    // contractIds = $('.contract-id').map((i, e) => e.value).get();
                    // console.log('id', contractIds)
                })
                $('#btn-export').on('click', function (e) {
                    e.preventDefault();
                    if (contractIds.length === 0) {
                        toastr.warning('Không có dữ liệu để xuất.')
                        return false
                    }
                    for (var key in contractIds) {
                        let input1 = `<input type="hidden" class="user-input-excel" name="contractIds[${key}]" value="${contractIds[key]}">`
                        $('#excel-form').append(input1);
                    }
                    document.getElementById('excel-form').submit();
                })
                $("#modal-excel").on("hidden.bs.modal", function () {
                    contractIds = []
                    $(this).find('input.user-input-excel').remove()
                    $(this).removeData();
                    $('#show').html('')
                });
                let typeParam = new URL(location.href).searchParams.get("type");
                let childTypeParam = new URL(location.href).searchParams.get("child-type");
                if (!typeParam && !childTypeParam) {
                    $('.tab span:first-child a').addClass('active-tab')
                } else if (typeParam == {!! \App\Defines\Contract::LEAVE_WORK !!} && !childTypeParam) {
                    $('.tab span a').removeClass('active-tab')
                    $('.tab span.leave-work a').addClass('active-tab')
                } else if (typeParam == {!! \App\Defines\Contract::ACTIVE !!} && !childTypeParam) {
                    $('.tab span a').removeClass('active-tab')
                    $('.tab span.active-contract a').addClass('active-tab')
                } else if (typeParam == {!! \App\Defines\Contract::TRANSFER  !!} && childTypeParam == 1) {
                    $('.tab span a').removeClass('active-tab')
                    $('.tab span.transfer-full a').addClass('active-tab')
                } else if (typeParam == {!! \App\Defines\Contract::TRANSFER !!} && childTypeParam == 2) {
                    $('.tab span a').removeClass('active-tab')
                    $('.tab span.transfer-half a').addClass('active-tab')
                } else if (typeParam == {!! \App\Defines\Contract::APPOINT  !!} && childTypeParam == 1) {
                    $('.tab span a').removeClass('active-tab')
                    $('.tab span.appoint-full a').addClass('active-tab')
                } else if (typeParam == {!! \App\Defines\Contract::APPOINT !!} && childTypeParam == 2) {
                    $('.tab span a').removeClass('active-tab')
                    $('.tab span.appoint-half a').addClass('active-tab')
                } else if (typeParam == {!! \App\Defines\Contract::DISMISSAL !!} && !childTypeParam) {
                    $('.tab span a').removeClass('active-tab')
                    $('.tab span.dismissal a').addClass('active-tab')
                } else if (typeParam == {!! \App\Defines\Contract::EXPIRED !!} && !childTypeParam) {
                    $('.tab span a').removeClass('active-tab')
                    $('.tab span.expired a').addClass('active-tab')
                } else if (typeParam == {!! \App\Defines\Contract::FUTURE !!} && !childTypeParam) {
                    $('.tab span a').removeClass('active-tab')
                    $('.tab span.future a').addClass('active-tab')
                }
                $('a.active-tab').closest('span').addClass('active-tab')
                $('.btn-show-export').on('click', function (e) {
                    let id = $(this).attr('data-id')
                    $.ajax({
                        url: "{!! route('admin.contracts.showModalExport') !!}",
                        data: {id: id},
                        type: 'POST',
                        headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                        success: function (res) {
                            $('#modal-area').html('').append(res.template)
                            $('.modal-export').modal('show');
                        },
                        error: function (err) {
                            let error = $.parseJSON(err.responseText);
                            toastr.warning(error.message, "{!! trans('system.have_error') !!}")
                        }
                    })
                    // $('#modal-export-' + id).modal('show')
                })
                $('#tableContracts thead tr').clone(true).appendTo('#tableContracts thead');
                $('#tableContracts thead tr:eq(1) th').each(function (i) {
                    if (i == 6) {
                        $(this).html('<input type="text" class="search-form datepicker date" autocomplete="off" />');
                    } else if (i == 1 || i == 2 || i == 7) {
                        $(this).html('<input type="text" class="search-form input-text" autocomplete="off" />');
                    } else {
                        $(this).html('');
                    }
                    callDatePickerDown()

                    $('input', this).on('keyup change', function () {
                        if (table.column(i).search() !== this.value) {
                            table
                                .column(i)
                                .search(this.value)
                                .draw();
                        }
                    });
                });

                var table = $('#tableContracts').DataTable({
                    orderCellsTop: true,
                    fixedHeader: true,
                    //lengthChange: false,
                    pageLength: 20,
                    rowReorder: true,
                    columnDefs: [
                        {orderable: false, className: 'reorder', targets: [0, 6, 8, 9]},
                    ],
                    pagingType: "full_numbers",
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
                        'emptyTable': "<span class='text-size center'><i class='fas fa-search'></i> {!! trans('staff_positions.no_data') !!}</span>",
                        'zeroRecords': "<span class='text-size center'><i class='fas fa-search'></i> {!! trans('staff_positions.no_data') !!}</span>",
                        "processing": '<div class="widget-loader" id="loader"><div class="load-dots"><span></span><span></span><span></span></div></div>',
                        "lengthMenu": "Hiển thị _MENU_ bản ghi",
                    },
                    dom: '<"top "i>rt<"bottom"flp>',

                });
                window.setNoAfterSearchDatatables(table, 0)
                table.columns('.company_id').every(function () {
                    var that = this;
                    var select = $('{!! Form::select('company_id',['' => '']+ $companyOptionSearch ,'', ['class' => 'search-form company_select select2']) !!}')
                        .appendTo(
                            $('#tableContracts thead tr:eq(1) th.company_id')
                        )
                        .on('change', function () {
                            var text = $('.company_select option:selected').text()
                            that
                                .search("\\b" + text + "\\b", true, false)
                                .draw();
                        });
                    callSelect2AutoWidth()
                });
                table.columns('.department_id').every(function () {
                    var that = this;
                    var select = $('{!! Form::select('department_id',['' => ''] + $deptOptionSearch ,'', ['class' => 'search-form department_select select2']) !!}')
                        .appendTo(
                            $('#tableContracts thead tr:eq(1) th.department_id')
                        )
                        .on('change', function () {
                            var text = $('.department_select option:selected').text()
                            that
                                .search(text)
                                .draw();
                        });
                    callSelect2AutoWidth()
                });
                table.columns('.position_id').every(function () {
                    var that = this;
                    var select = $('{!! Form::select('position_id',['' => '']+ \App\Helpers\GetOption::getStaffPositionsForOption() ,'', ['class' => 'search-form position_select select2']) !!}')
                        .appendTo(
                            $('#tableContracts thead tr:eq(1) th.position_id')
                        )
                        .on('change', function () {
                            var text = $('.position_select option:selected').text().toLowerCase()
                            that
                                .search(text)
                                .draw();
                        });
                    callSelect2AutoWidth()
                });
                table.columns('.status').every(function () {
                    var that = this;
                    var select = $('{!! Form::select('status',['' => ' '] + \App\Defines\Contract::getTypeStatusForOption() ,'', ['class' => 'search-form status_select select2']) !!}')
                        .appendTo(
                            $('#tableContracts thead tr:eq(1) th.status')
                        )
                        .on('change', function () {
                            var text = $('.status_select option:selected').text()
                            that
                                .search(text)
                                .draw();
                        });
                    callSelect2AutoWidth()
                });
            });
        }(window.jQuery);
    </script>
@stop