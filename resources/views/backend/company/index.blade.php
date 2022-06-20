@extends('backend.master')
@section('title')
    {!! trans('system.action.list') !!} {!! trans('companies.label') !!}
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
    </style>
@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('companies.label') !!}
            <small>{!! trans('system.action.list') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.companies.index') !!}">{!! trans('companies.label') !!}</a></li>
        </ol>
    </section>
    <section class="content overlay">
        @permission('companies.create')

        <div class="row">
            <div class="col-md-2">
                <a href="{!! route('admin.companies.create') !!}" class='btn btn-primary btn-flat'>
                    <span class="glyphicon glyphicon-plus"></span>&nbsp;{!! trans('system.action.create') !!}
                </a>
            </div>
        </div>
        @endpermission
        @if (count($companies) > 0)
            <div class="box">
                <div class="box-header" style="padding: 0">
                    <?php $i =  1; ?>

                </div>
                <div class="box-body no-padding">
                    <table class="table table-striped table-bordered table-hover" id="tableCompany">
                        <thead>
                        <tr>
                            <th style="text-align: center; vertical-align: middle;">{!! trans('system.no.') !!}</th>
                            <th style="text-align: center; vertical-align: middle;">{!! trans('companies.name') !!}
                            <th style="text-align: center; vertical-align: middle;">{!! trans('companies.shortened_name') !!}
                            <th style="text-align: center; vertical-align: middle;">{!! trans('companies.telephone') !!}
                            <th style="text-align: center; vertical-align: middle;">{!! trans('companies.tax_code') !!}
                            <th style="text-align: center; vertical-align: middle;">{!! trans('companies.address') !!}</th>
                            <th style="text-align: center; vertical-align: middle;" class="status_company" >{!! trans('companies.status.label') !!}</th>
                            <th style="text-align: center; vertical-align: middle; width: 10%" width="500px">{!! trans('system.action.label') !!}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($companies as $item)
                            <tr>
                                <td style="text-align: center; vertical-align: middle;">{!! $i++ !!}</td>
                                <td style="">
                                    <a href="{!! route('admin.companies.show', $item->id) !!}">{!! $item->name !!}</a><br/>
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    {!! $item->shortened_name !!}
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    {!! $item->telephone !!}
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    {!! $item->tax_code !!}
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    {!! $item->address !!}<br/>
                                </td>
                                <td style="text-align: center; vertical-align: middle;" class="status_company">
                                    @if($item->status == 0)
                                        <span class="label label-default">{!! trans('system.status.deactive') !!}</span>
                                    @elseif($item->status == 1)
                                        <span class="label label-success">{!! trans('companies.active') !!}</span>
                                    @endif
                                </td>
                                <td style="text-align: center; vertical-align: middle; white-space: nowrap;">
                                    @permission('companies.update')

                                    <div class="col-md-2">
                                        <a href="{!! route('admin.companies.edit', $item->id) !!}"
                                           data-toggle="tooltip" data-placement="top"   title="{!! trans('system.action.update') !!}"
                                           class="btn btn-xs btn-default"><i
                                                    class="text-warning glyphicon glyphicon-edit"></i></a>
                                    </div>
                                    @endpermission
                                    @permission('companies.delete')

                                    &nbsp;&nbsp; @if($item->status == 0)
                                        <div class="col-md-2">
                                            <a href="javascript:void(0)"
                                               data-toggle="tooltip" data-placement="top" link="{!! route('admin.companies.destroy', $item->id) !!}"
                                               class="btn-confirm-del btn btn-default btn-xs"
                                               title="{!! trans('system.action.delete') !!}">
                                                <i class="text-danger glyphicon glyphicon-remove"></i>
                                            </a>
                                        </div>
                                    @endif
                                    @endpermission
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
                $('#tableCompany thead tr').clone(true).appendTo('#tableCompany thead');
                $('#tableCompany thead tr:eq(1) th').each(function (i) {

                    if (i != 0 && i != 6 && i != 7) {
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

                var table = $('#tableCompany').DataTable({
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
                    },
                    dom : '<"top "i>rt<"bottom"flp>'

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