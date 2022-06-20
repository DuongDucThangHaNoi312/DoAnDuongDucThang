@extends('backend.master')
@section('title')
    {!! trans('system.action.list') !!} {!! trans('payrolls.label') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
    <link rel="stylesheet" type="text/css"
          href="{!! asset('assets/backend/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">

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

        .modal-header {
            background-color: #3c8dbc;
            color: white;
            text-align: center;
        }

        .modal-footer {
            text-align: center;
        }
        .select2-container--default .select2-selection--single {
            height: 28px !important;
            border-radius: 3px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 24px !important;
            font-weight: normal;
        }

    </style>
@stop
@section('content')
@php
$user = Auth::user();
@endphp
    <section class="content-header">
        <h1>
            {!! trans('salary_cont.label_table') !!}
            <small>{!! trans('system.action.list') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.timekeeping.index') !!}">{!! trans('payrolls.label') !!}</a></li>
        </ol>
    </section>
    <section class="content overlay">
        <div class="row">
            <div class="col-md-4">
                {{-- @if (Auth::user()->hasRole('TP') || Auth::user()->hasRole('system')) --}}
                    <button onclick="reset()" type="button" class="btn btn-primary btn-flat btn-creat" data-toggle="modal" data-target="#exampleModal">
                        <span class="glyphicon glyphicon-plus"></span>&nbsp;{!! trans('system.action.create') !!}
                    </button> 
                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#export">
                        <span class="far fa-file-excel fa-fw"></span>&nbsp; Xuất excel
                    </button>
                {{-- @endif --}}
                @include('backend.salary-choose-cont.create')
                @include('backend.salary-choose-cont.export')
            </div>
            <div class="col-md-10">
                
            </div>
        </div>
        @include('backend.salary-choose-cont.list')
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
            $('.hiden-status').hide();
            $(function () {
                $(".select2").select2({width: '100%'});
            });
        }(window.jQuery);
        
        let $currentRoute = {!! json_encode(\App\PermissionUserObject::getCurrentModule(\Route::getCurrentRoute())) !!};

        var oldDepartmentId = {!! old('department_id') ?? 0 !!};
        
        var modalCreate = $('.modal-create');
        function setDepartmentOption() {
            let companyId = modalCreate.find('.companySelect'). val();
            console.log(companyId);
            if (companyId) {
                modalCreate.find('.departmentSelect').prop('disabled', false)
                $.ajax({
                    url: "{!! route('admin.contracts.setDepartmentOption') !!}",
                    data: {companyId: companyId, route: $currentRoute},
                    type: 'POST',
                    headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                    success: function (res) {
                        modalCreate.find('.departmentSelect option').remove()
                        modalCreate.find('.departmentSelect').append('<option value="">'+ '{!! trans('system.dropdown_choice') !!}'  + '</option>')
                        $.each(res, function (index, value) {
                            // let isSelected = oldDepartmentId == index ? 'selected' : ''
                            let isSelected = value.includes('chọn vỏ') ? 'selected' : ''
                            modalCreate.find('.departmentSelect').append('<option value="' + index + '"' + isSelected + '>' + value + '</option>')
                        })
                    },
                    error: function (data) {
                        console.log(data)
                    }
                })
            } else {
                modalCreate.find('.departmentSelect').prop('disabled', true)
            }
        }
        $(document).on('change', '.modal-create .companySelect', function () {
            setDepartmentOption();
        })
        if ($('.modal-create .companySelect'). val()) {
            $('.modal-create .departmentSelect').prop('disabled', false);
            setDepartmentOption()
        }

        $('.btn-create').on('click', function () {
            $(this).attr('disabled', 'disabled');
            $('#btn_create').submit();
        })
       
        $('.btn-export-excel').on('click', function () {
            $(this).attr('disabled', 'disabled');
            $('#export_excel').submit();
        })
        


    </script>
    <script>
        !function ($) {
            $(function () {

                $(".select2").select2({width: '100%'});
                var check_admin = '{!! Auth::user()->hasRole("NV") !!}';
                if (check_admin != 1) {
                    $('#tablePayrolls thead tr').clone(true).appendTo('#tablePayrolls thead');
                }
                $('#tablePayrolls thead tr:eq(1) th').each(function (i) {
                    if (i == 3) {
                        $(this).html('<input type="text" class="search-form month_filter date" autocomplete="off" />');
                    } else if (i == 4 || i == 5  || i == 6 || i == 8 || i == 9 || i == 10 || i == 11 ) {
                        $(this).html('<input type="text" class="search-form input-text" autocomplete="off" />');
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

                    $('input', this).on('keyup change', function () {
                        if (table.column(i).search() !== this.value) {
                            table
                                .column(i)
                                .search(this.value)
                                .draw();
                        }
                    });
                });

                var table = $('#tablePayrolls').DataTable({
                    orderCellsTop: true,
                    fixedHeader: true,
                    pageLength: 20,
                    lengthChange: false,
                    // ordering: false,
                    columnDefs: [
                        {orderable: false, className: 'reorder', targets: 7},
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
                    },
                    dom: '<"top "i>rt<"bottom"flp>'

                });
                table.columns('.company_id').every(function () {
                    var that = this;
                    var select = $('{!! Form::select('company_id',['' => 'Tất cả']+ \App\Helpers\GetOption::getCompaniesForOption() ,'', ['class' => 'search-form company_select select2']) !!}')
                        .appendTo(
                            $('#tablePayrolls thead tr:eq(1) th.company_id')
                        )
                        .on('change', function () {
                            var text = $('.company_select option:selected').text();
                            if (text == "Tất cả") {
                                text = '';
                            }
                            that
                                .search("\\b" + text + "\\b", true, false)
                                .draw();
                        });
                    $(".select2").select2({width: '100%'});
                });
                table.columns('.department_id').every(function () {
                    var that = this;
                    var select = $('{!! Form::select('department_id',['' => 'Tất cả']+ \App\Helpers\GetOption::getAllDepartmentsForOption() ,'', ['class' => 'search-form department_select select2']) !!}')
                        .appendTo(
                            $('#tablePayrolls thead tr:eq(1) th.department_id')
                        )
                        .on('change', function () {
                            var text = $('.department_select option:selected').text();
                            if (text == "Tất cả") {
                                text = '';
                            }
                            that
                                .search(text)
                                .draw();
                        });
                    $(".select2").select2({width: '100%'});
                });
                table.columns('.status').every(function () {
                    var that = this;
                    var select = $('{!! Form::select('status', \App\Helpers\GetOption::statusSalaryDeclaration() ,'', ['class' => 'search-form status_select select2']) !!}')
                        .appendTo(
                            $('#tablePayrolls thead tr:eq(1) th.status')
                        )
                        .on('change', function () {
                            var text = $('.status_select option:selected').text();
                            if (text == "Tất cả") {
                                text = '';
                            }
                            var value = $('.status_select').val();
                            if (value == 0) {
                                that.search('').draw();

                            } else {
                                that.search(text).draw();
                            }
                            
                        });
                    $(".select2").select2({width: '100%'});
                });
            });
        }(window.jQuery);
    </script>
@stop