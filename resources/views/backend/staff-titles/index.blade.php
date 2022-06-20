@extends('backend.master')
@section('title')
    {!! trans('system.action.list') !!} {!! trans('staff_titles.label') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
    <link rel="stylesheet" type="text/css"
          href="{!! asset('assets/backend/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">

    <style>
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
            {!! trans('staff_titles.label') !!}
            <small>{!! trans('system.action.list') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.titles.index') !!}">{!! trans('staff_titles.label') !!}</a></li>
        </ol>
    </section>
    <section class="content overlay">
        <div class="row">
            <div class="col-md-2">
                <div class="btn-group">
                    <a href="{!! route('admin.titles.create') !!}" class='btn btn-primary btn-flat'>
                        <span class="glyphicon glyphicon-plus"></span>&nbsp;{!! trans('system.action.create') !!}
                    </a>
                </div>
            </div>
            <div class="col-md-10 text-right">
            </div>
        </div>
        <?php $labels = ['default', 'success', 'info', 'danger', 'warning']; ?>
        <div class="box">
            <div class="box-header" style="padding: 0">
                <?php $i = 1; ?>

            </div>
            <div class="box-body no-padding">
                <table class="table table-striped table-bordered table-hover" id="qualifications">
                    <thead>
                    <tr>
                        <th style="text-align: center; vertical-align: middle;">{!! trans('system.no.') !!}</th>
                        <th style="text-align: center; vertical-align: middle;">{!! trans('staff_titles.code') !!}</th>
                        <th style="text-align: center; vertical-align: middle;">{!! trans('staff_titles.name') !!}</th>
                        <th style="text-align: center; vertical-align: middle;">{!! trans('staff_titles.name_es') !!}</th>
                        <th style="text-align: center; vertical-align: middle;">{!! trans('contracts.desc_qualification') !!}</th>
                        <th style="text-align: center; vertical-align: middle; white-space: nowrap;">{!! trans('staff_titles.created_at') !!}</th>
                        <th style="text-align: center; vertical-align: middle;">{!! trans('system.action.label') !!}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($qualification->count()>0)
                        @php
                            $i=0;
                        @endphp
                        @foreach ($qualification as $item)
                            <tr>
                                <td align="center" style="text-align: center; vertical-align: middle;">{!! ++$i!!}</td>
                                <td align="center" style="vertical-align: middle;">
                                    <a>{!! $item->code !!}</a><br/>
                                </td>
                                <td align="center" style="vertical-align: middle;">
                                    {!! $item->name !!}<br/>
                                </td>
                                <td align="center" style="vertical-align: middle;">
                                    {!! $item->name_es !!}<br/>
                                </td>
                                <td align="center" style="vertical-align: middle;">
                                    {!! $item->description !!}<br/>
                                </td>
                                <td align="center" style="vertical-align: middle;">
                                    {!! date($item->created_at->format('d/m/Y')) !!}<br/>
                                </td>
                                <td style="text-align: center; vertical-align: middle; white-space: nowrap;">&nbsp;&nbsp;
                                    <a data-toggle="tooltip" title="Cập nhật"
                                       href="{{route('admin.titles.edit',$item->id)}}" class="btn btn-xs btn-default"><i
                                                class="text-warning glyphicon glyphicon-edit"></i></a>
                                    <a data-toggle="tooltip" title="Xóa" href="javascript:void(0)"
                                       link="{!! route('admin.titles.destroy', $item->id) !!}"
                                       class="btn-confirm-del btn btn-default btn-xs"><i
                                                class="text-danger glyphicon glyphicon-remove"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr style="height: 40px">
                            <td align="center" colspan="5"><span class="text-size"><i class="fas fa-search"></i> {!! trans('staff_positions.no_data') !!}</span>
                            </td>
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
                $('#qualifications thead tr').clone(true).appendTo('#qualifications thead');
                $('#qualifications thead tr:eq(1) th').each(function (i) {
                    if (i == 5) {
                        $(this).html('<input type="text" class="search-form datepicker date" autocomplete="off" />');
                    } else if (i != 0 && i != 6) {
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

                var table = $('#qualifications').DataTable({
                    orderCellsTop: true,
                    fixedHeader: true,
                    pageLength: 20,
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
                table.columns('.status_company').every(function () {
                    var that = this;
                    var select = $('{!! Form::select('status',['' => '',1 => trans('companies.active'),2=>trans('system.status.deactive')] ,'', ['class' => 'search-form status_select']) !!}')
                        .appendTo(
                            $('#tableCompany thead tr:eq(1) th.status_company')
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
