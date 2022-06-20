@extends('backend.master')
@section('title')
    {!! trans('system.action.list') !!} {!! trans('workschedule.label') !!}
@stop

@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
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
        
        .time {
            width: 120px;
            text-align: center;
        }

        b, strong {
            font-weight: 500;
        }

        .tab {
            padding: 7px 0;
            margin-top: 5px;
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
@stop

@section('content')
    <section class="content-header">
        <h1>
            {!! trans('workschedule.label') !!}
            <small>{!! trans('system.action.list') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.companies.index') !!}">{!! trans('workschedule.label') !!}</a></li>
        </ol>
    </section>
    <section class="content overlay">
        <div class="row">
            <div class="col-md-2">
                <!-- Button trigger modal -->
                <button type="button" onclick="reset()" class="btn btn-primary btn-flat" data-toggle="modal" data-target="#exampleModal">
                    <span class="glyphicon glyphicon-plus"></span>&nbsp;{!! trans('system.action.create') !!}
                </button>
                @include('backend.work-schedule.create')
            </div>
            <div class="col-md-10">
                
            </div>
        </div>
        <div class="box">
            <div class="box-body no-padding">
                @include('backend.work-schedule.tab')

                @include('backend.work-schedule.list-table-shift')
            </div>
        </div>
    </section>
@stop

@section('footer')
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="{!! asset('assets/backend/plugins/iCheck/icheck.min.js') !!}"></script>
<script src="{!! asset('assets/backend/plugins/select2/select2.full.min.js') !!}"></script>
<script src="{!! asset('assets/backend/plugins/moment/min/moment-with-locales.min.js') !!}"></script>
<script src="{!! asset('assets/backend/plugins/input-mask/jquery.inputmask.min.js') !!}"></script>
<script src="{!! asset('assets/backend/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') !!}"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>

<script>
    !function ($) {
        var url_href = '{{ route("admin.workschedules.list-shift") }}';
        if (url_href == window.location) {
            $('.shift-tab').addClass('active-tab');
        }

        $('.office').hide();
        $('.shift').hide();

        $(function () {
            $('input[type="checkbox"].minimal').iCheck({
                checkboxClass: 'icheckbox_minimal-blue'
            });

            $(".select2").select2({width: '100%'});
            
            $('.timepicker').timepicker({
                timeFormat: 'HH:mm',
                dropdown: true,
                scrollbar: true,
                zindex: 9999999
            });

            $('.timepicker-am').timepicker({
                timeFormat: 'HH:mm',
                dropdown: true,
                scrollbar: true,
                minTime: '00:00am',
	            maxTime: '12:00pm',
                zindex: 9999999
            });

            $('.timepicker-pm').timepicker({
                timeFormat: 'HH:mm',
                dropdown: true,
                scrollbar: true,
                minTime: '12:00pm',
	            maxTime: '23:59pm',
                zindex: 9999999
            });
        });
    }(window.jQuery);


    var oldDepartmentId = {!! old('department_id') ?? 0 !!};
    var departmentOption = {!! json_encode($department_group)  !!}
    function setDepartmentOption() {
        let companyId = $('.companySelect'). val();
        if (companyId) {
            $('#departmentSelect').attr('disabled', false)
            $.ajax({
                url: "{!! route('admin.workschedule.setDepartmentOption') !!}",
                data: {companyId: companyId},
                type: 'POST',
                headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                success: function (res) {
                    $('#departmentSelect option').remove()
                    $('#departmentSelect').append('<option>'+ '{!! trans('system.dropdown_choice') !!}'  + '</option>')
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
    
    $(document).on('change', '#departmentSelect', function() {
        let departmentId = $(this).val();
        if (departmentId) {
            $.ajax({
                url: "{!! route('admin.workschedule.checkDepartment') !!}",
                data: {departmentId: departmentId},
                type: 'POST',
                headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                success: function (response) {
                    if (response.type == 1) {
                        $('.office').show();
                        $('.shift').hide();
                    } else if (response.type == 2) {
                        $('.shift').show();
                        $('.office').hide();
                    } else {
                        $('.office').hide();
                        $('.shift').hide();
                    }
                },
                error: function (data) {
                    
                }
            })
        }
    });

    $('body').on('click', '#submitForm', function(){
        var registerForm = $("#workschedule");
        var formData = registerForm.serialize();
        var state = $(this).val();
        
        if (state == 'update1') {
            var _url = $(this).data("url");
            var _type = "PUT";
        } else {
            var _url = "{!! route('admin.workschedule.store') !!}";
            var _type = "POST";
        }

        $.ajax({
            url: _url,
            type: _type,
            headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
            data:formData,
            success:function(data) {
                if (data.errors) {
                    if (data.errors.company_id) {
                        $('#company-error').html(data.errors.company_id[0]);
                    } else {
                        $('#company-error').html('');
                    }

                    if (data.errors.from_morning) {
                        $('#time1-error').html(data.errors.from_morning[0]);
                    } else if (data.errors.to_morning) {
                        $('#time1-error').html('{!! trans('workschedule.gt_morning') !!}');
                    } else {
                        $('#time1-error').html('');
                    }

                    if (data.errors.from_afternoon) {
                        $('#time2-error').html(data.errors.from_afternoon[0]);
                    } else if (data.errors.to_afternoon) {
                        $('#time2-error').html('{!! trans('workschedule.gt_afternoon') !!}');
                    } else {
                        $('#time2-error').html('');
                    }

                    if (data.errors.department_id) {
                        $('#department-error').html(data.errors.department_id[0]);
                    }

                    if (data.errors.ot) {
                        $('#ot-error').html(data.errors.ot[0]);
                    } else {
                        $('#ot-error').html('');
                    }


                    if (data.errors.shift1_in || data.errors.shift1_out) {
                        $('#shift1-error').html('Thời gian vào ra ca 1 không được để trống');
                    } else {
                        $('#shift1-error').html('');
                    }

                    if (data.errors.shift2_in || data.errors.shift2_out) {
                        $('#shift2-error').html('Thời gian vào ra ca 2 không được để trống');
                    } else {
                        $('#shift2-error').html('');
                    }

                    if (data.errors.shift3_in || data.errors.shift3_out) {
                        $('#shift3-error').html('Thời gian vào ra ca 3 không được để trống');
                    } else {
                        $('#shift3-error').html('');
                    }
                }
               
                if (data.status == 'SUCCESS') {
                    toastr.success(data.message);
                    location.reload();
                } else if (data.status == 'FAIL') {
                    toastr.error(data.message);
                }
            },
        });
    });
    
    function reset() {
        $('.office').hide();
        $('.shift').hide();
        $('#workschedule').trigger("reset");
        $('#company').val('').change();
        $("#company").addClass('companySelect');
        $('#company').removeAttr('disabled');
        $('#departmentSelect').val('').change();
        $('.text-danger').find('strong').html('');
        $('#submitForm').val('add');
        $('#submitForm').removeAttr("data-url");

        $('.modal-title').text('{{ trans('workschedule.add_title') }}');
    }
    
    $('.open-modal').on('click', function () {

        $('.icheckbox_minimal-blue').removeClass('checked')
        $('#workschedule').trigger("reset");
        $('.text-danger').find('strong').html('');

        var url = $(this).data("url");
        var get_url = $(this).data("get-url");
       
        $('.modal-title').text('{{ trans('workschedule.edit_title') }}');
        $('#submitForm').val('update1');
        $('#submitForm').attr("data-url", url);
        let obj = {
            type: 'shift' 
        };
        $.get(get_url, obj, function (response) {
            if (response.data.department.type == 2) {
                $('.shift').show();
            }
            $.each(response.data, function (key, value) {
                $('#'+key).val(value);
                if (key == 'company_id') {
                    $('#company_id').val('');
                }
                if (key == 'type' && value == 1) {
                    $('.icheckbox_minimal-blue').addClass('checked')
                }
            })

            $("#company").removeClass('companySelect');
            $("#company").val(response.data.company_id).change();
            $("#company").attr('disabled', 'disabled').change();
            $('#departmentSelect').append('<option value="' + response.data.department_id + '" selected="selected">' + response.data.department_name + '</option>');

            $('#exampleModal').modal('show');
        })
    });
</script>
<script type="text/javascript" charset="utf8"
        src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>
<script>
    !function ($) {
        $(function () {

            var table = $('#tableWorkShedule').DataTable({
                orderCellsTop: true,
                fixedHeader: true,
                pageLength: 10,
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
                    'emptyTable': "<span class='text-size center'><i class='fas fa-search'></i> {!! trans('staff_positions.no_data') !!}</span>",
                    'zeroRecords': "<span class='text-size center'><i class='fas fa-search'></i> {!! trans('staff_positions.no_data') !!}</span>",
                },
                dom: '<"top "i>rt<"bottom"flp>',

            });
            table.columns('.company_id').every(function () {
                var that = this;
                var select = $('{!! Form::select('company_id',['' => '']+ \App\Helpers\GetOption::getCompaniesForOption() ,'', ['class' => 'search-form company_select select2']) !!}')
                    .appendTo(
                        $('#tableWorkShedule thead tr:eq(0) th.company_id')
                    )
                    .on('change', function () {
                        var text = $('.company_select option:selected').text()
                        that
                            .search(text)
                            .draw();
                    });
                $(".select2").select2({width: '100%'});
            });
            table.columns('.department_id').every(function () {
                var that = this;
                var select = $('{!! Form::select('department_id',['' => '']+ \App\Helpers\GetOption::getAllDepartmentsForOption() ,'', ['class' => 'search-form department_select select2']) !!}')
                    .appendTo(
                        $('#tableWorkShedule thead tr:eq(0) th.department_id')
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