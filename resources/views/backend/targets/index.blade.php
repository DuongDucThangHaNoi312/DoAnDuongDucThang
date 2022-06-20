@extends('backend.master')
@section('title')
    {!! trans('system.action.list') !!} {!! trans('kpi.label') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}" />
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') !!}" />
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

        input[type=number]::-webkit-inner-spin-button {
            -webkit-appearance: none;
        }

        .dataTables_filter {
            display: none;
        }

        table {
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single {
            height: 28px !important;
            border-radius: 3px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 24px !important;
            font-weight: normal;
        }

        td {
            vertical-align: middle !important;
        }

    </style>
@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('kpi.label') !!}
            <small>{!! trans('system.action.list') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.targets.index') !!}">{!! trans('kpi.label') !!}</a></li>
        </ol>
    </section>
    <section class="content overlay">
        <div class="row">
            @permission('targets.create')
                <div class="col-md-2">
                    <a href="{!! route('admin.targets.create') !!}" class='btn btn-primary btn-flat'>
                        &nbsp;{!! trans('targets.setup') !!}
                    </a>
                </div>
            @endpermission
        </div>
        <div class="box">
            <div class="box-body no-padding">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="tableKPI">
                        <thead>
                            <tr>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('system.no.') !!}</th>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('kpi.name_staff') !!}</th>
                                <th style="text-align: center; vertical-align: middle; width: 100px" class="company_id">
                                    {!! trans('kpi.name_company') !!}</th>
                                <th style="text-align: center; vertical-align: middle;" class="department_id">
                                    {!! trans('kpi.name_department') !!}</th>
                                <th style="text-align: center; vertical-align: middle; width: 70px">{!! trans('kpi.kpi_value') !!}
                                </th>
                                <th style="text-align: center; vertical-align: middle; width: 70px">{!! trans('kpi.month') !!}
                                </th>
                                <th style="text-align: center; vertical-align: middle; width: 100px">
                                    {!! trans('kpi.created_by') !!}</th>
                                <th style="text-align: center; vertical-align: middle; width: 100px">
                                    {!! trans('kpi.description') !!}</th>
                                <th style="text-align: center; vertical-align: middle; width: 100px">
                                    {!! trans('kpi.note2') !!}</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
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
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>
    <script>
        ! function($) {
            $(function() {
                $(".select2").select2({
                    width: '100%'
                });
                $('#tableKPI thead tr').clone(true).appendTo('#tableKPI thead');
                $('#tableKPI thead tr:eq(1) th').each(function(i) {
                    if (i == 5) {
                        $(this).html(
                            '<input type="text" class="search-form month_filter date" name="month_year" autocomplete="off" />'
                        );
                    } else if (i == 1) {
                        $(this).html(
                            '<input type="text" class="search-form input-text" name="name" autocomplete="off" />'
                        );
                    } else if (i == 4) {
                        $(this).html(
                            '<input type="text" class="search-form input-text" name="kpi" autocomplete="off" />'
                        );
                    } else {
                        $(this).html('');
                    }
                    $('.month_filter').datepicker({
                        format: "mm/yyyy",
                        viewMode: "months",
                        minViewMode: "months",
                        clearBtn: true,
                        autoclose: true,
                        language: 'vi'
                    });

                    $('input', this).on('keyup change', function() {
                        table.draw();
                    });
                });
                var table = $('#tableKPI').DataTable({
                    processing: true,
                    serverSide: true,
                    orderCellsTop: true,
                    fixedHeader: true,
                    pageLength: 20,
                    lengthChange: false,
                    rowReorder: true,
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
                        'emptyTable': "<span class='text-size text-center'><i class='fas fa-search'></i>{!! trans('staff_positions.no_data') !!}</span>"
                    },
                    ajax: {
                        url: "{{ route('admin.targets.get-data') }}",
                        data: function(d) {
                            d.name = $("#tableKPI input[name=name]").val();
                            d.company = $(".companySelect").val();
                            d.department = $('.department_select').val();
                            d.kpi = $("#tableKPI input[name=kpi]").val();
                            d.monthYear = $("#tableKPI input[name=month_year]").val();
                        }
                    },
                    columns: [{
                            data: 'id'
                        },
                        {
                            data: 'fullname_code'
                        },
                        {
                            data: 'shortened_name'
                        },
                        {
                            data: 'department_name'
                        },
                        {
                            data: 'kpi'
                        },
                        {
                            data: 'month_year'
                        },
                        {
                            data: 'created_by_fullname'
                        },
                        {
                            data: 'description'
                        },
                        {
                            data: 'note'
                        }
                    ],
                    "columnDefs": [{
                        "className": "dt-center",
                        "targets": [0, 2, 4, 5, 6, 7, 8]
                    }],
                    dom: '<"top "i>rt<"bottom"flp>',
                });
                table.columns('.company_id').every(function() {
                    var that = this;

                    var select = $('{!! Form::select('company_id', $companysOption ? ['' => ''] + $companysOption : ['' => ''] + \App\Define\OverTime::getCompanyNamesForOption(), '', ['class' => 'search-form companySelect select2']) !!}')
                        .appendTo(
                            $('#tableKPI thead tr:eq(1) th.company_id')
                        )
                        .on('change', function() {
                            that.draw();
                        });
                    $(".select2").select2({
                        width: '100%'
                    });
                });
                table.columns('.department_id').every(function() {
                    var that = this;

                    var select = $('{!! Form::select('department_id', $departmentOption ? ['' => ''] + $departmentOption : ['' => ''] + \App\Helpers\GetOption::getAllDepartmentsForOption(), '', ['class' => 'search-form department_select select2']) !!}')
                        .appendTo(
                            $('#tableKPI thead tr:eq(1) th.department_id')
                        )
                        .on('change', function() {
                            that.draw();
                        });
                    $(".select2").select2({
                        width: '100%'
                    });
                });
            });
        }(window.jQuery);
    </script>
@stop
