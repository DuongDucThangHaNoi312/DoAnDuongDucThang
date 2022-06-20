@extends('backend.master')
@section('title')
    {!! trans('system.action.list') !!} {!! trans('adjustments.label') !!}
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
    </style>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/datatables/dataTables.bootstrap.css') !!}" />
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') !!}"/>
@stop
@section('content')
<section class="content-header">
    <h1>
        {!! trans('adjustments.label') !!}
        <small>{!! trans('system.action.list') !!}</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
        <li><a href="{!! route('admin.adjustments.index') !!}">{!! trans('adjustments.label') !!}</a></li>
    </ol>
</section>
    <section class="content overlay">
        @permission('adjustments.update')
        <div class="row">
            <div class="col-md-4">
                <div class="btn-group">
                    <a href="{{route('admin.adjustments.create') }}" class='btn btn-primary btn-flat'>
                        <span class="glyphicon glyphicon-plus"></span>&nbsp;{!! trans('system.action.create') !!}
                    </a>
                </div>
            </div>
        </div>
        @endpermission
        <div class="box">
            <div class="box-header">
                <?php $i = 1; ?>
            </div>
            <div class="box-body no-padding">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="tableUser">
                        <thead>
                            <tr>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('system.no.') !!}</th>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('adjustments.code') !!}</th>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('adjustments.adjustment_name') !!}</th>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('adjustments.tax_status.label') !!}</th>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('adjustments.adjustment_type') !!}</th>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('adjustments.status') !!}</th>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('adjustments.action') !!}</th>
                        </thead>
                        @if (count($adjustment) > 0)
                        <tbody>
                            @foreach ($adjustment as $item)
                                <tr data-id="{{$item->id}}">
                                    {!! Form::hidden('adjustmentId[]', $item->id, ["class" => 'adjustment-id']) !!}
                                    <td style="text-align: center; vertical-align: middle;">{!! $i++ !!}</td>
                                    <td style="vertical-align: middle;text-align: center">
                                        <a data-toggle="tooltip" href="{{route('admin.adjustments.show',$item->id)}}" >{!! $item->code !!}</a>
                                    </td>
                                    <td style="vertical-align: middle;text-align: center">
                                        {!! $item->title !!}
                                    </td>

                                    <td style="text-align: center; vertical-align: middle;">
                                        @if($item->status == 1)
                                        {!! trans('adjustments.taxable') !!}
                                     @else
                                     {!! trans('adjustments.tax_exemption') !!}
                                     @endif
                                    </td>
                                    <td style="text-align: center; vertical-align: middle;">
                                     @if($item->type == 1)
                                     {!! trans('adjustments.increase_adjustment') !!}
                                     @else
                                     {!! trans('adjustments.reduce_adjustment') !!}
                                     @endif
                                    </td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        @if($item->action == 1)
                                        <span class="label label-success">{!! trans('system.status.active') !!}</span>
                                        @else
                                        <span class="label label-default">{!! trans('system.status.deactive') !!}</span>
                                        @endif
                                    </td>
                                    <td style="text-align: center; vertical-align: middle; white-space: nowrap;">&nbsp;&nbsp;
                                        @permission('adjustments.update')
                                        <div class="col-md-1">
                                            <a data-toggle="tooltip" title="Cập nhật"
                                               href="{{route('admin.adjustments.edit',$item->id) }}"
                                               class="btn btn-xs btn-default"><i
                                                        class="text-warning glyphicon glyphicon-edit"></i></a>
                                        </div>
                                        @endpermission
                                        @permission('adjustments.delete')
                                        <div class="col-md-1">
                                            <a data-toggle="tooltip" title="Xóa" href="javascript:void(0)"
                                               link="{!! route('admin.adjustments.destroy', $item->id) !!}"
                                               class="btn-confirm-del btn btn-default btn-xs"><i
                                                        class="text-danger glyphicon glyphicon-remove"></i></a>
                                        </div>
                                        @endpermission
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
        $(document).ready(function() {
            $(".select2").select2({width: '100%'});
            $('#tableUser thead tr').clone(true).appendTo('#tableUser thead');
            $('#tableUser thead tr:eq(1) th').each(function (i) {
                if (i != 0 && i != 6) {
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
            var table = $('#tableUser').DataTable({
                orderCellsTop: true,
                fixedHeader: true,
                pageLength: 10,
                lengthChange: false,
                responsive: true,
                rowReorder: true,
                // ordering: false,
                pagingType: "full_numbers",
                columnDefs: [
                    {orderable: false, className: 'reorder', targets: 6},
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
