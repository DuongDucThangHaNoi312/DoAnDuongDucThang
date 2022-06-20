@extends('backend.master')
@section('title')
    {!! trans('system.action.list') !!} {!! trans('staff_positions.label') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css"
          href="{!! asset('assets/backend/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') !!}"/>
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
        .dataTables_filter {
            display: none;
        }
        table{
            width: 100% !important;
        }
    </style>
@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('staff_positions.label') !!}
            <small>{!! trans('system.action.list') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.positions.index') !!}">{!! trans('staff_positions.label') !!}</a></li>
        </ol>
    </section>
    <section class="content overlay">

        <div class="row">
            <div class="col-md-2">
                <a href="{!! route('admin.positions.create') !!}" class='btn btn-primary btn-flat'>
                    <span class="glyphicon glyphicon-plus"></span>&nbsp;{!! trans('system.action.create') !!}
                </a>
            </div>
            <div class="col-md-10 text-right">
                {!!  $positions->appends( Request::except('page') )->render() !!}
            </div>
        </div>
            <?php $labels = ['default', 'success', 'info', 'danger', 'warning']; ?>
            <div class="box">
                <div class="box-header" style="padding: 0">
                    <?php $i =  1; ?>
                </div>
                <div class="box-body no-padding">
                    <table class="table table-striped table-bordered table-hover" id="positions">
                        <thead>
                        <tr>
                            <th style="text-align: center; vertical-align: middle;">{!! trans('system.no.') !!}</th>
                            <th style="text-align: center; vertical-align: middle;">{!! trans('staff_positions.code') !!}</th>
                            <th style="text-align: center; vertical-align: middle;">{!! trans('staff_positions.weight_p') !!}</th>
                            <th style="text-align: center; vertical-align: middle; white-space: nowrap;">{!! trans('staff_positions.created_at') !!}</th>
                            <th style="text-align: center; vertical-align: middle;">{!! trans('system.action.label') !!}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if($positions->total()>0)
                            @foreach ($positions as $item)
                                <tr>
                                    <td style="text-align: center; vertical-align: middle;">{!! $i++ !!}</td>
                                    <td align="center" style="vertical-align: middle;">
                                        <a>{!! $item->code !!}</a><br/>
                                    </td>
                                    <td align="center" style="vertical-align: middle;" >{!! $item->name!!}
                                        <br/>
                                    </td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        {!! $item->created_at->format('d/m/Y') !!}
                                    </td>
                                    <td style="text-align: center; vertical-align: middle; white-space: nowrap;">&nbsp;&nbsp;
                                        @if($item->is_system==0)
                                            <a data-toggle="tooltip" title="Cập nhật" href="{!! route('admin.positions.edit', $item->id) !!}" class="btn btn-xs btn-default"><i class="text-warning glyphicon glyphicon-edit"></i></a>
                                            &nbsp;&nbsp;
                                            <a data-toggle="tooltip" title="Xóa" href="javascript:void(0)"
                                               link="{!! route('admin.positions.destroy', $item->id) !!}"
                                               class="btn-confirm-del btn btn-default btn-xs">
                                                <i class="text-danger glyphicon glyphicon-remove"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            @else
                            <tr style="height: 40px">
                                <td  align="center" colspan="5"><span class="text-size"><i class="fas fa-search"></i> {!! trans('staff_positions.no_data') !!}</span></td>
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
    <script src="{!! asset('assets/backend/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.vi.min.js') !!}"></script>
    <script type="text/javascript" charset="utf8"
            src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>
    <script>
        !function ($) {
            $(function () {


                $(".select2").select2({width: '100%'});
                $('#positions thead tr').clone(true).appendTo('#positions thead');
                $('#positions thead tr:eq(1) th').each(function (i) {
                    if (i == 3) {
                        $(this).html('<input type="text" class="search-form datepicker date " autocomplete="off" />');
                    } else if (i != 0 && i != 4) {
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

                var table = $('#positions').DataTable({
                    orderCellsTop: true,
                    fixedHeader: true,
                    paging: false,
                    lengthChange: false,
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

                    },
                    dom: '<"top "i>rt<"bottom"flp>'

                });

            });
        }(window.jQuery);
    </script>
@stop