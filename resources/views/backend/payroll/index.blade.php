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
    <section class="content-header">
        <h1>
            {!! trans('payrolls.label') !!}
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
                @permission('payrolls.create')
                    <button onclick="reset()" type="button" class="btn btn-primary btn-flat btn-creat" data-toggle="modal" data-target="#exampleModal">
                        <span class="glyphicon glyphicon-plus"></span>&nbsp;{!! trans('system.action.create') !!}
                    </button> 
                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#export">
                        <span class="far fa-file-excel fa-fw"></span>&nbsp; Xuất excel
                    </button>
                    
                @endpermission
               
                {{-- @endif --}}
               
                @include('backend.payroll.create')
                <div class="modal fade" id="export" tabindex="-1" role="dialog" aria-labelledby="exportLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                    <div class="modal-dialog" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h4 class="modal-title" id="exportLabel">Xuất excel bảng lương</h4>
                        </div>
                        {!! Form::open(['id' => 'payroll', 'url' => route('admin.payrolls.exportExcel1'), 'method' => 'GET', 'target' => "_blank"]) !!}
                            <div class="modal-body">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label>{!! trans('timekeeping.company') !!} <span class="text-danger">(*)</span></label>
                                            <select name="company_id" id="company" class="companySelect1 form-control select2" required="required">
                                                <option value="" selected="selected">{{ trans('system.dropdown_choice') }}</option>
                                                @foreach (\App\Helpers\GetOption::getCompaniesForOption() as $key => $item)
                                                <option value="{{ $key }}">{{ $item }}</option>
                                                @endforeach
                                            </select>
                                            <span class="text-danger">
                                                <strong id="company-error"></strong>
                                            </span>
                                        </div>
                                        <div class="col-md-4">
                                            <label>{!! trans('timekeeping.month') !!} <span class="text-danger">(*)</span></label>
                                            <select name="month" id="" class="form-control select2" required="required">
                                                @foreach (\App\Define\Timekeeping::getMonth() as $key => $item)
                                                <option value="{{ $key }}" {{ $key == date('m') ? "selected" : '' }}>{{ $item }}</option>
                                                @endforeach
                                            </select>
                                            <span class="text-danger">
                                                <strong id="month-error"></strong>
                                            </span>
                                        </div>
                                        <div class="col-md-4">
                                            <label>{!! trans('timekeeping.year') !!} <span class="text-danger">(*)</span></label>
                                            <select name="year" id="" class="form-control select2" required="required">
                                                <option value="">{{ trans('system.dropdown_choice') }}</option>
                                                @foreach (\App\Define\Timekeeping::getYear() as $key => $item)
                                                <option value="{{ $key }}" {{ $key == date('Y') ? "selected" : '' }}>{{ $item }}</option>
                                                @endforeach
                                            </select>
                                            <span class="text-danger">
                                                <strong id="year-error"></strong>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Đóng</button>
                                <button type="submit" class="btn btn-primary">Xuất excel</button>
                            </div>
                        {!! Form::close() !!}
                      </div>
                    </div>
                </div>
            </div>
            <div class="col-md-10">
                
            </div>
        </div>
        @include('backend.payroll.list-table')
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
        function setDepartmentOption() {
            let companyId = $('.companySelect'). val();
            if (companyId) {
                $('#departmentSelect').attr('disabled', false)
                $.ajax({
                    url: "{!! route('admin.contracts.setDepartmentOption') !!}",
                    data: {companyId: companyId, route: $currentRoute},
                    type: 'POST',
                    headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                    success: function (res) {
                        $('#departmentSelect option').remove()
                        $('#departmentSelect').append('<option value="">'+ '{!! trans('system.dropdown_choice') !!}'  + '</option>')
                        $.each(res, function (index, value) {
                            let isSelected = oldDepartmentId == index ? 'selected' : ''
                            $('#departmentSelect').append('<option value="' + index + '"' + isSelected + '>' + value + '</option>')
                        })
                    },
                    error: function (data) {
                        console.log(data)
                    }
                })
            } else {
                $('#departmentSelect').attr('disabled', true)
            }
        }
        $(document).on('change', '.companySelect', setDepartmentOption)
        if ($('.companySelect'). val()) {
            $('#departmentSelect').attr('disabled', false)
            setDepartmentOption()
        }

        $('.btn-tinh-luong').on('click', function () {
            $(this).attr('disabled', 'disabled');

            // $('#exampleModalCenter').modal('hide');
            $('#tinh_luong').submit();
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
                    } else if (i == 4 || i == 5  || i == 6) {
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
                    var select = $('{!! Form::select('company_id',['' => '']+ \App\Helpers\GetOption::getCompaniesForOption() ,'', ['class' => 'search-form company_select select2']) !!}')
                        .appendTo(
                            $('#tablePayrolls thead tr:eq(1) th.company_id')
                        )
                        .on('change', function () {
                            var text = $('.company_select option:selected').text()
                            that
                                .search("\\b" + text + "\\b", true, false)
                                .draw();
                        });
                    $(".company_select").select2({width: '100%', dropdownAutoWidth : true});
                });
                table.columns('.department_id').every(function () {
                    var that = this;
                    var select = $('{!! Form::select('department_id',['' => '']+ \App\Helpers\GetOption::getAllDepartmentsForOption() ,'', ['class' => 'search-form department_select select2']) !!}')
                        .appendTo(
                            $('#tablePayrolls thead tr:eq(1) th.department_id')
                        )
                        .on('change', function () {
                            var text = $('.department_select option:selected').text()
                            that
                                .search(text)
                                .draw();
                        });
                    $(".department_select").select2({width: '100%', dropdownAutoWidth : true});
                });

                table.columns('.status').every(function () {
                    var that = this;
                    var select = $('{!! Form::select('status',['' => '']+ \App\Helpers\GetOption::statusPayroll() ,'', ['class' => 'search-form status_select select2']) !!}')
                        .appendTo(
                            $('#tablePayrolls thead tr:eq(1) th.status')
                        )
                        .on('change', function () {
                            var text = $('.status_select option:selected').text();
                            var value = $('.status_select').val();
                            if (value == 0) {
                                that.search('').draw();

                            } else {
                                that.search(text).draw();
                            }
                            
                        });
                    $(".status_select").select2({width: '100%', dropdownAutoWidth : true});
                });

            });
        }(window.jQuery);
    </script>
@stop