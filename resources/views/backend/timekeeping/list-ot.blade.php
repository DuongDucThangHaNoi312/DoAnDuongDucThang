@extends('backend.master')
@section('title')
    {!! trans('system.action.list') !!} {!! trans('timekeeping.ot') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css"
          href="{!! asset('assets/backend/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') !!}"/>
    <style>
        .error {
            width: 100%;
            height: 100px;
            line-height: 100px;
        }

        .text-size {
            font-size: 16px;
        }

        tr td {
            text-align: center;
        }

        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type=number] {
            -moz-appearance: textfield;
        }

        b, strong {
            font-weight: 500;
        }
    </style>
@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('timekeeping.ot') !!}
            <small>{!! trans('system.action.list') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.ot.index') !!}">{!! trans('timekeeping.ot') !!}</a></li>
        </ol>
    </section>
    <section class="content overlay">
        <div class="box">
            <div class="box-body no-padding">
                <table class="table table-striped table-bordered table-hover" id="tableTimeKeeping" style="width: 100%">
                    <thead>
                    <tr>
                        <th style="text-align: center; vertical-align: middle; width: 70px">{!! trans('system.no.') !!}</th>
                        <th style="text-align: center; vertical-align: middle;" class="company_id">{!! trans('timekeeping.company') !!}</th>
                        <th style="text-align: center; vertical-align: middle;" class="department_id">{!! trans('timekeeping.department') !!}</th>
                        <th style="text-align: center; vertical-align: middle;">{!! trans('timekeeping.month') !!}</th>
                        <th style="text-align: center; vertical-align: middle;">{{ trans('timekeeping.created_by') }}</th>
                        <th style="text-align: center; vertical-align: middle; width: 70px">{!! trans('system.action.label') !!}</th>
                    </tr>
                    </thead>
                    <tbody>
                        @if (count($timekeeping) > 0)
                            @foreach ($timekeeping as $key => $item)
                                <tr>
                                    <td>{!! $key + 1 !!}</td>
                                    <td class="company_id">{{ $item->company->shortened_name }}</td>
                                    <td class="department_id">{{ $item->department->name }}</td>
                                    <td>{{ $item->month }}/{{ $item->year }}</td>
                                    <td>{{ $item->user_by->fullname }}</td>
                                    <td>
                                        @if ($item->version == 1)
                                        <a href="{{ route('admin.ots.detail', $item->id) }}" class="btn btn-info btn-xs">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @else    
                                        <a href="{{ route('admin.ot.detail', $item->id) }}" class="btn btn-info btn-xs">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endif
                                        
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>           
                </table>
                @if (count($timekeeping) == 0)
                <div class="text-center error">
                    <span class="text-size"><i class="fas fa-search"></i> {!! trans('timekeeping.no_data') !!}</span>
                </div>
                @endif
            </div>
        </div>
    </section>
@stop
@section('footer')
<script src="{!! asset('assets/backend/plugins/select2/select2.full.min.js') !!}"></script>

<script type="text/javascript" charset="utf8"
    src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>

<script>
    !function ($) {
        $(function () {
            $(".select2").select2({width: '100%'});

            $('#tableTimeKeeping thead tr').clone(true).appendTo('#tableTimeKeeping thead');
            $('#tableTimeKeeping thead tr:eq(1) th').each(function (i) {
                if (i == 3) {
                    $(this).html('<input type="text" class="search-form month_filter date" autocomplete="off" />');
                } else if (i == 4) {
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

            var table = $('#tableTimeKeeping').DataTable({
                orderCellsTop: true,
                fixedHeader: true,
                pageLength: 20,
                lengthChange: false,
                // ordering: false,
                columnDefs: [
                    {orderable: false, className: 'reorder', targets: 5},
                    {orderable: false, targets: 0}
                ],
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
                dom: '<"top "i>rt<"bottom"flp>'

            });
            table.columns('.company_id').every(function () {
                var that = this;
                var select = $('{!! Form::select('company_id',['' => '']+ \App\Helpers\GetOption::getCompaniesForOption() ,'', ['class' => 'search-form company_select select2']) !!}')
                    .appendTo(
                        $('#tableTimeKeeping thead tr:eq(1) th.company_id')
                    )
                    .on('change', function () {
                        var text = $('.company_select option:selected').text()
                        that
                            .search("\\b" + text + "\\b", true, false)
                            .draw();
                    });
                $(".select2").select2({width: '100%'});
            });
            table.columns('.department_id').every(function () {
                var that = this;
                var select = $('{!! Form::select('department_id',['' => '']+ \App\Helpers\GetOption::getAllDepartmentsForOption() ,'', ['class' => 'search-form department_select select2']) !!}')
                    .appendTo(
                        $('#tableTimeKeeping thead tr:eq(1) th.department_id')
                    )
                    .on('change', function () {
                        var text = $('.department_select option:selected').text()
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