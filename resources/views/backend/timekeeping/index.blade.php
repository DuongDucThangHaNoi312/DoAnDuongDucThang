@extends('backend.master')
@section('title')
    {!! trans('system.action.list') !!} {!! trans('timekeeping.label') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
    {{--<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">--}}
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/datatables/jquery.dataTables.min.css') !!}" />
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
        .dataTables_filter {
            display: none;
        }
        .select2-container--default .select2-selection--single {
            height: 28px !important;
            border-radius: 3px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 24px !important;
            font-weight: normal;
        }

        /* Absolute Center Spinner */
        .loading {
            position: fixed;
            z-index: 999;
            height: 2em;
            width: 2em;
            overflow: visible;
            margin: auto;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
        }
        
        /* Transparent Overlay */
        .loading:before {
            content: '';
            display: block;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.3);
        }
        
        /* :not(:required) hides these rules from IE9 and below */
        .loading:not(:required) {
            /* hide "loading..." text */
            font: 0/0 a;
            color: transparent;
            text-shadow: none;
            background-color: transparent;
            border: 0;
        }
        
        .loading:not(:required):after {
            content: '';
            display: block;
            font-size: 10px;
            width: 1em;
            height: 1em;
            margin-top: -0.5em;
            -webkit-animation: spinner 1500ms infinite linear;
            -moz-animation: spinner 1500ms infinite linear;
            -ms-animation: spinner 1500ms infinite linear;
            -o-animation: spinner 1500ms infinite linear;
            animation: spinner 1500ms infinite linear;
            border-radius: 0.5em;
            -webkit-box-shadow: rgba(0, 0, 0, 0.75) 1.5em 0 0 0, rgba(0, 0, 0, 0.75) 1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) 0 1.5em 0 0, rgba(0, 0, 0, 0.75) -1.1em 1.1em 0 0, rgba(0, 0, 0, 0.5) -1.5em 0 0 0, rgba(0, 0, 0, 0.5) -1.1em -1.1em 0 0, rgba(0, 0, 0, 0.75) 0 -1.5em 0 0, rgba(0, 0, 0, 0.75) 1.1em -1.1em 0 0;
            box-shadow: rgba(0, 0, 0, 0.75) 1.5em 0 0 0, rgba(0, 0, 0, 0.75) 1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) 0 1.5em 0 0, rgba(0, 0, 0, 0.75) -1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) -1.5em 0 0 0, rgba(0, 0, 0, 0.75) -1.1em -1.1em 0 0, rgba(0, 0, 0, 0.75) 0 -1.5em 0 0, rgba(0, 0, 0, 0.75) 1.1em -1.1em 0 0;
        }
        
        /* Animation */
        
        @-webkit-keyframes spinner {
            0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
            }
            100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
            }
        }
        @-moz-keyframes spinner {
            0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
            }
            100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
            }
        }
        @-o-keyframes spinner {
            0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
            }
            100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
            }
        }
        @keyframes spinner {
            0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
            }
            100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
            }
        }

        .modal-header {
            background-color: #3c8dbc; 
            color: white; 
            text-align: center
        }

        .modal-footer {
            text-align: center
        }

        .modal-footer .btn-default {
            float: none;
        }
    </style>
@stop
@section('content')

    <section class="content-header">
        <h1>
            {!! trans('timekeeping.label') !!}
            <small>{!! trans('system.action.list') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.timekeeping.index') !!}">{!! trans('timekeeping.label') !!}</a></li>
        </ol>
    </section>
    <section class="content overlay">
        <div class="row">
            <div class="col-md-2">
                {{-- @permission('timekeeping.create') --}}

                @if (auth()->user()->hasPermission('timekeepings.create') || in_array("create", $moreActions) )
                    <button onclick="reset()" type="button" class="btn btn-primary btn-flat btn-creat" data-toggle="modal" data-target="#exampleModal">
                        <span class="glyphicon glyphicon-plus"></span>&nbsp;{!! trans('timekeeping.calculation') !!}
                    </button>
                    @include('backend.timekeeping.create')    
              @endif
  
            </div>
            <div class="col-md-10">

            </div>
        </div>
        @if (count($timekeeping) > 0 && !Auth::user()->hasRole('LEADER'))
            @include('backend.timekeeping.list-table')
        @else
            @include('backend.timekeeping.list-table-team')
        @endif
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
    {{--<script type="text/javascript" charset="utf8"
            src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>--}}
    <script>
        $('.hiden-status').hide();

        !function ($) {
            $(function () {
                $(".select2").select2({width: '100%'});
            });
        }(window.jQuery);
        
        var $currentRoute = {!! json_encode(\App\PermissionUserObject::getCurrentModule(\Route::getCurrentRoute())) !!};

        var oldDepartmentId = {!! old('department_id') ?? 0 !!};
        var departmentOption = {!! json_encode($department_group)  !!};
        function setDepartmentOption() {
            let companyId = $('.companySelect'). val();
            if (companyId) {
                $('#departmentSelect').attr('disabled', false)
                $.ajax({
                    url: "{!! route('admin.contracts.setDepartmentOption') !!}",
                    data: {companyId: companyId,  route: $currentRoute},
                    type: 'POST',
                    headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                    success: function (res) {
                        $('#departmentSelect option').remove()
                        $('#departmentSelect').append('<option value="">'+ '{!! trans('overtimes.choose_department') !!}'  + '</option>')
                        $.each(res, function (index, value) {
                            let isSelected = oldDepartmentId == index ? 'selected' : ''
                            if(departmentOption){
                                if(jQuery.inArray( Number(index), departmentOption ) !== -1){
                                    $('#departmentSelect').append('<option value="' + index + '"' + isSelected + '>' + value + '</option>')
                                }
                            }
                            else {
                                $('#departmentSelect').append('<option value="' + index + '"' + isSelected + '>' + value + '</option>')
                            }
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

        function reset() {
            $('#timekeeping').trigger("reset");
            $('#company').val('').change();
            $('#departmentSelect').val('').change();
            $('.text-danger').find('strong').html('');
        }

        $('body').on('click', '#submitForm', function(){
            let load = `
                <div class="loading">Loading&#8230;</div>
            `;

            $('.modal-content').append(load);
            var registerForm = $("#timekeeping");
            var formData = registerForm.serialize();

            $.ajax({
                url: "{{ route('admin.timekeepings.store') }}",
                type: "POST",
                headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                data:formData,
                success:function(response) {
                    if (response.errors) {
                        $('.loading').remove();

                        if (response.errors.title) {
                            $('#title-error').html(response.errors.title[0]);
                        } else {
                            $('#title-error').html('');
                        }

                        if (response.errors.company_id) {
                            $('#company-error').html(response.errors.company_id[0]);
                        } else {
                            $('#company-error').html('');
                        }

                        if (response.errors.department_id) {
                            $('#department-error').html(response.errors.department_id[0]);
                        } else {
                            $('#department-error').html('');
                        }

                        if (response.errors.month) {
                            $('#month-error').html(response.errors.month[0]);
                        } else {
                            $('#month-error').html('');
                        }

                        if (response.errors.year) {
                            $('#year-error').html(response.errors.year[0]);
                        } else {
                            $('#year-error').html('');
                        }
                    }

                    if (response.status == 'FAIL') {
                        $('.loading').remove();
                        toastr.error(response.message);
                    } else if (response.status == 'SUCCESS') {
                        toastr.success(response.message);
                        $('.loading').remove();
                        // location.reload();
                        window.location.href = response.link;
                    }
                },
            });
        });
    </script>
    <script>
        !function ($) {
            $(function () {
                //$(".select2").select2({width: '100%'});
    
                $('#tableTimeKeeping thead tr').clone(true).appendTo('#tableTimeKeeping thead');
                $('#tableTimeKeeping thead tr:eq(1) th').each(function (i) {
                    if (i == 3) {
                        $(this).html('<input type="text" class="search-form month_filter date" autocomplete="off" />');
                    } else if (i == 4) {
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
                    callSelect2AutoWidth()
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
                    callSelect2AutoWidth()
                });

                table.columns('.status').every(function () {
                    var that = this;
                    var select = $('{!! Form::select('status',['' => '']+ \App\Helpers\GetOption::statusTimekeeping() ,'', ['class' => 'search-form status_select select2']) !!}')
                        .appendTo(
                            $('#tableTimeKeeping thead tr:eq(1) th.status')
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
                    callSelect2AutoWidth()
                });
            });
        }(window.jQuery);
    </script>
@stop