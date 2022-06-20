@extends('backend.master')
@section('title')
    {!! trans('system.action.list') !!} Kinh phí công đoàn
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/daterangepicker/daterangepicker.css') !!}" />
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css"
          href="{!! asset('assets/backend/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') !!}"/>

@stop
@section('content')
    <section class="content-header">
        <h1>
            Kinh phí công đoàn
            <small>{!! trans('system.action.list') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.unionfunds.index') !!}">Kinh phí công đoàn</a></li>
        </ol>
    </section>
    <section class="content overlay">
        <div class="row">
            <div class="col-md-2">
               <a href="{!! route('admin.unionfunds.create') !!}" class='btn btn-primary btn-flat'>
                   <span class="glyphicon glyphicon-plus"></span>&nbsp;{!! trans('system.action.create') !!}
               </a>
            </div>
            <div class="col-md-10 text-right">
                {{-- {!!  $allowanceCategories->appends( Request::except('page') )->render() !!} --}}
            </div>
        </div>
        @if (count($items) > 0)
            <div class="box">
                
                <div class="box-body no-padding">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="tableOT">
                            <thead>
                            <tr>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('system.no.') !!}</th>
                                <th style="text-align: center; vertical-align: middle;">Nhân viên</th>
                                <th style="text-align: center; vertical-align: middle;">Mã</th>
                                <th style="text-align: center; vertical-align: middle;">Ngày áp dụng</th>
                                <th style="text-align: center; vertical-align: middle;">Người tạo</th>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('system.action.label') !!}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($items as $key => $item)
                                <tr>
                                    <td style="text-align: center; vertical-align: middle;">{!! $key + 1 !!}</td>
                                    <td style="vertical-align: middle;">
                                        {!! $item->user->fullname ?? "" !!}
                                    </td>
                                    <td style="vertical-align: middle;" class="text-center">{!! $item->user->code ?? '' !!}</td>
                                    <td style="vertical-align: middle;" class="text-center">{!! date('d/m/Y', strtotime($item->start)) ?? '' !!}</td>
                                    <td style="vertical-align: middle;" class="text-center">{!! $item->createdBy->fullname ?? '' !!}</td>
                                    <td style="text-align: center; vertical-align: middle; white-space: nowrap;">&nbsp;&nbsp;
                                        <a href="{!! route('admin.unionfunds.edit', $item->id) !!}"
                                           class="btn btn-xs btn-default"
                                           data-toggle="tooltip" data-placement="top" title="{!! trans('system.action.update') !!}">
                                            <i class="text-warning glyphicon glyphicon-edit"></i>
                                        </a>&nbsp;&nbsp;
                                       <a href="javascript:void(0)"
                                          link="{!! route('admin.unionfunds.destroy', $item->id) !!}"
                                          class="btn-confirm-del btn btn-default btn-xs"
                                          data-toggle="tooltip" data-placement="top" title="{!! trans('system.action.delete') !!}">
                                           <i class="text-danger glyphicon glyphicon-remove"></i>
                                       </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-info">{!! trans('system.no_record_found') !!}</div>
        @endif
    </section>
@stop
@section('footer')
    <script src="{!! asset('assets/backend/plugins/daterangepicker/moment.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/daterangepicker/daterangepicker.js') !!}"></script>
    <script type="text/javascript" charset="utf8"
        src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>
    <script src="{!! asset('assets/backend/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.vi.min.js') !!}"></script>

    <script>
        !function ($) {
            $(function(){
                $('#tableOT thead tr').clone(true).appendTo('#tableOT thead');
                $('#tableOT thead tr:eq(1) th').each(function (i) {
                    if (i == 1  || i == 2 || i == 3 || i == 4) {
                        $(this).html('<input type="text" class="search-form input-text" autocomplete="off" />');
                    }
                    //  else if (i == 4 || i == 5) {
                    //     $(this).html('<input type="text" class="search-form input-text datepicker" autocomplete="off" />');
                    // }
                     else {
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
                
                

            });
        }(window.jQuery);
    </script>
@stop