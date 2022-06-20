@extends('backend.master')
@section('title')
    {!! trans('system.action.list') !!} {!! trans('staffs.manager-leave') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') !!}" />
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/daterangepicker/daterangepicker.css') !!}" />
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}" />
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/switchery/0.8.2/switchery.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">

    <style>
        table {
            width: 100% !important;
        }

        .search-form {
            width: 100%;
            background-color: #fff;
            color: #2780d1;
            transition: .3s;
            margin: 1px 0;
            outline: 0;
            box-shadow: inset 0 0 0 transparent;
            height: 28px;
            font-size: 13px;
            line-height: 1.42857143;
            padding: 2px 10px;
            border-radius: 3px;
            border: 1px solid #e7e6e6;
            background-size: 10px;
            background-position: 95% 8px;
            font-weight: normal
        }

        .input-text {

            background: url(https://upload.wikimedia.org/wikipedia/commons/thumb/0/0b/Search_Icon.svg/1024px-Search_Icon.svg.png) no-repeat;
            background-size: 10px;
            background-position: 95% 8px;
        }

        .dataTables_filter {
            display: none;
        }

        .date {

            background: url(https://images.echocommunity.org/85032db6-de87-47fc-abaf-d1fa3a5f498f/calendar-icon-marketing-image.png?w=600) no-repeat;
            background-size: 10px;
            background-position: 95% 8px;
        }

        td {
            vertical-align: middle !important;
        }

    </style>

@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('staffs.manager-leave') !!}
            <small>{!! trans('system.action.list') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.manager.leave.index') !!}">{!! trans('staffs.manager-leave') !!}</a></li>
        </ol>
    </section>
    <section class="content overlay">
        <div class="box">
            <div class="box-body no-padding" style="overflow-x:auto;">
                <table class='table table-striped table-bordered' id="tableTakeLeave">
                    <thead>
                        <tr>
                            <th style="text-align: center; vertical-align: middle;">{!! trans('system.no.') !!}</th>
                            <th style="text-align: center; vertical-align: middle;"> Nhân viên </th>
                            <th style="text-align: center; vertical-align: middle;">{!! trans('staff_titles.type') !!}</th>
                            <th style="text-align: center; vertical-align: middle;">Chi tiết lý do</th>
                            <th style="text-align: center; vertical-align: middle;"> {!! trans('staff_titles.start') !!} </th>
                            <th style="text-align: center; vertical-align: middle;"> {!! trans('staff_titles.end') !!} </th>
                            <th style="text-align: center; vertical-align: middle;"> {!! trans('staff_titles.day_off') !!} </th>
                            <th style="text-align: center; vertical-align: middle;"> {!! trans('staff_titles.approved_by') !!} </th>
                            <th style="text-align: center; vertical-align: middle;"> {!! trans('staff_titles.approved_date') !!} </th>
                            <th style="text-align: center; vertical-align: middle;" class="status">
                                {!! trans('staff_titles.status') !!} </th>
                            <th style="text-align: center; vertical-align: middle;"> {!! trans('staff_titles.delete_leave') !!} </th>
                            <th style="text-align: center; vertical-align: middle;"> {!! trans('staff_titles.edit_day_of') !!} </th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Modal -->
    <div class="modal fade" id="editDayOff" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"
        data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="text-align: center; background: #3c8dbc; color: white;">
                    <h4 class="modal-title" id="editDayOffLabel">Sửa đơn xin nghỉ: <span class="text-info-user"></span></h4>
                </div>
                <?php $timeOffOption = \App\Defines\Schedule::getTimeOffForOption(); ?>

                <form action="">
                    <div class="modal-body">
                        <div class="row" style="margin: 5px auto;">
                            <label class="col col-md-2">{!! trans('schedules.type_leave') !!}</label>
                            <div class="col col-md-5">
                                <input id="day-off" type="hidden" value="">
                                {!! Form::select('code', \App\Defines\Schedule::getDayOffTypeForOption(), old('code'), ['class' => 'form-control select2 code-dayoff', 'required']) !!}
                            </div>

                        </div>
                        <div class="row" style="margin: 5px 0;">
                            <label class="col col-md-2" for="start_at">{!! trans('schedules.start') !!}</label>
                            <div class="col col-md-5">
                                {!! Form::text('start', Request::input('start'), ['id' => 'start', 'autocomplete' => 'off', 'class' => 'form-control datepicker datepicker-from', 'required']) !!}
                            </div>
                            <div class="col col-md-5 days">
                                <select id="from-type" class="select2">
                                    @foreach ($timeOffOption as $index => $val)
                                        <option value='{{ $index }}'> {{ $val }} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row row-end" style="margin: 5px 0; vertical-align: middle;">
                            <label class="col col-md-2" for="end_at">{!! trans('schedules.to') !!}</label>
                            <div class="col col-md-5">
                                {!! Form::text('end', Request::input('end'), ['id' => 'end', 'autocomplete' => 'off', 'class' => 'form-control datepicker datepicker-to', 'required']) !!}
                            </div>
                            <div class="col col-md-5 days">
                                <select id="to-type" class="select2">
                                    @foreach ($timeOffOption as $index => $val)
                                        <option value='{{ $index }}'> {{ $val }} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row" style="margin: 5px 0; vertical-align: middle">
                            <label class="col col-md-2 text-nowrap">{!! trans('schedules.detail-reason') !!}</label>
                            <div class="col col-md-10">
                                {!! Form::textarea('reason', old('reason'), ['class' => 'form-control reason', 'rows' => 4, 'placeholder' => 'Nói rõ lý do (Bắt buộc)']) !!}
                            </div>
                        </div>
                        <div class="row" style="margin: 5px 0; font-weight: 600; float: right">
                            <div class="col-md-12 total">
                                <span>{!! trans('schedules.total-day-off') !!}</span>
                                <span class="total">0</span>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer" style="text-align: center">
                        <button type="button" class="btn btn-danger btn-flat" data-dismiss="modal">Hủy</button>
                        <button type="button" class="btn btn-primary btn-flat btn-edit-dayoff">Lưu lại</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="modal-cancel-leave-application" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"> Xác nhận </h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" />
                    <p> Bạn có chắc chắn muốn hủy đơn này không ? </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal" style="float: left;"> Không, hủy bỏ
                    </button>
                    <button type="submit" class="btn btn-danger" id="btn-confirm-cancel-leave-application"> Đồng ý,Hủy ngay
                    </button>
                </div>
            </div>
        </div>
    </div>
@stop
@section('footer')
    <script src="{!! asset('assets/backend/plugins/daterangepicker/moment.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/daterangepicker/daterangepicker.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/select2/select2.full.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') !!}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/switchery/0.8.2/switchery.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>
    <script>
        // $("#tre").attr('title', 'Đơn đã được duyệt không thể hủy');
        function someActionAfterDatatableRendered() {
            let elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch:not([data-switchery="true"])'));

            elems.forEach(function(html) {
                let switchery = new Switchery(html, {
                    size: 'small'
                });
            });
        }

        function getValueTimeOff(halfShift, type) {
            if (halfShift == 1) {
                return type == 1 ? "{!! trans('schedules.time-shift-offs.1') !!}" : "{!! trans('schedules.time-shift-offs.2') !!}"
            }
            return type == 1 ? "{!! trans('schedules.time-offs.1') !!}" : "{!! trans('schedules.time-offs.2') !!}"
        }
    </script>
    <script>
        ! function($) {
            $(function() {
                $('.select2').select2();
                $('.date_range').daterangepicker({
                    autoUpdateInput: false,
                    "locale": {
                        "format": "DD/MM/YYYY",
                        "separator": " - ",
                        "applyLabel": "Áp dụng",
                        "cancelLabel": "Huỷ bỏ",
                        "fromLabel": "Từ ngày",
                        "toLabel": "Tới ngày",
                        "customRangeLabel": "Custom",
                        "weekLabel": "W",
                        "daysOfWeek": [
                            "CN",
                            "T2",
                            "T3",
                            "T4",
                            "T5",
                            "T6",
                            "T7"
                        ],
                        "monthNames": [
                            "Thg 1",
                            "Thg 2",
                            "Thg 3",
                            "Thg 4",
                            "Thg 5",
                            "Thg 6",
                            "Thg 7",
                            "Thg 8",
                            "Thg 9",
                            "Thg 10",
                            "Thg 11",
                            "Thg 12"
                        ],
                        "firstDay": 1
                    },
                    ranges: {
                        'Hôm nay': [moment(), moment()],
                        'Hôm qua': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        '7 ngày trước': [moment().subtract(6, 'days'), moment()],
                        '30 ngày trước': [moment().subtract(29, 'days'), moment()],
                        'Tháng này': [moment().startOf('month'), moment()],
                        'Tháng trước': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                            'month').endOf('month')],
                    },
                    "alwaysShowCalendars": true,
                    maxDate: moment(),
                    minDate: moment().subtract(1, "years"),
                }, function(start, end, label) {
                    $('.date_range').val(start.format('DD/MM/YYYY') + " - " + end.format('DD/MM/YYYY'));
                });

            });
        }(window.jQuery);
    </script>
    <script>
        ! function($) {
            $(function() {

                $(".select2").select2({
                    width: '100%'
                });
                $('#tableTakeLeave thead tr').clone(true).appendTo('#tableTakeLeave thead');
                $('#tableTakeLeave thead tr:eq(1) th').each(function(i) {
                    switch (i) {
                        case 1:
                            $(this).html(
                                '<input type="text" class="search-form input-text" name="name" autocomplete="off" />'
                            );
                            break;
                        case 2:
                            $(this).html(
                                '<input type="text" class="search-form input-text" name="title" autocomplete="off" />'
                            );
                            break;
                        case 3:
                            $(this).html(
                                '<input type="text" class="search-form input-text" name="reason" autocomplete="off" />'
                            );
                            break;
                        case 4:
                            $(this).html(
                                '<input type="text" class="search-form datepicker date" name="date_start" autocomplete="off" />'
                            );
                            break;
                        case 5:
                            $(this).html(
                                '<input type="text" class="search-form datepicker date" name="date_end" autocomplete="off" />'
                            );
                            break;
                        case 6:
                            $(this).html(
                                '<input type="text" class="search-form input-text" name="total" autocomplete="off" />'
                            );
                            break;
                        default:
                            $(this).html('');
                    }

                    $('.datepicker').datepicker({
                        format: 'dd/mm/yyyy',
                        autoclose: true,
                        language: 'vi',
                        orientation: "bottom auto"
                    });

                    $('.search-form', this).on('keyup change', function() {
                        console.log($(this).val());
                        table.draw();
                    });
                });

                const userCode = @json(auth()->user()->code);
                const userId = @json(auth()->user()->id);
                const arrUserApproved = @json(\App\Define\Timekeeping::userApprovedTimekeeping());
                const isUserHasRoleApproved = arrUserApproved.includes(userCode);
                const qualificationId = @json($qualificationId);
                const positionId = @json($positionId);
                let check = false;
                let isCallBack = true;

                var table = $('#tableTakeLeave').DataTable({
                    processing: true,
                    serverSide: true,
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
                    ajax: {
                        url: "{{ route('admin.manager.leave.get-data') }}",
                        data: function(d) {
                            d.status = $("select[name='status']").val();
                            d.name = $("input[name='name']").val();
                            d.title = $("input[name='title']").val();
                            d.date_start = $("input[name='date_start']").val();
                            d.date_end = $("input[name='date_end']").val();
                            d.total = $("input[name='total']").val();
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex'
                        },
                        {
                            data: 'fullname_code',
                        },
                        {
                            data: 'title'
                        },
                        {
                            data: 'reason'
                        },
                        {
                            data: null,
                            render: function(data, type, row) {
                                return `${data['start']} <br><span style="color: #D29E3B">(${getValueTimeOff(data['half_shift'],data['from_type'])})</span>`;
                            }
                        },
                        {
                            data: null,
                            render: function(data, type, row) {
                                return `${data['end']} <br><span style="color: #A94442">(${getValueTimeOff(data['half_shift'],data['to_type'])})</span>`;
                            }
                        },
                        {
                            data: 'total'
                        },
                        {
                            data: null,
                            render: function(data, type, row) {
                                if (data['approved_by'] != null) {
                                    return data['approved_by']['fullname'];
                                }
                                return "";
                            }
                        },
                        {
                            data: 'approved_date'
                        },
                        {
                            data: null,
                            render: function(data, type, row) {
                                check = false;
                                if (qualificationId[data['id']] == 23 || positionId[data['id']] ==
                                    2 && data['user_id'] == userId) {
                                    check = true;
                                }
                                if (data['status'] == 1) {
                                    return '<span data-toggle="tooltip" title="Đơn đã duyệt" class="label label-success"><span class="glyphicon glyphicon-ok"></span></span>'
                                } else {
                                    if (check != true) {
                                        if (userId == data['staff_id']) {
                                            return "";
                                        } else {
                                            return `<span data-toggle="tooltip" title="Đơn chưa duyệt">
                                        <input type="checkbox" name="status" class="btn-confirm-canel js-switch" data-id="${data['id']}" data-status="${data['status']}" ${data['status'] == 1 ? 'checked' : ''} />
                                        </span>
                                    `
                                        }
                                    }
                                    return '<span style="color: red">{!! trans('staff_titles.day_unpaidLeave') !!}</span>'
                                }
                                return "";
                            }
                        },
                        {
                            data: null,
                            render: function(data, type, row) {
                                if (data['deleted_at'] != null) {
                                    return '<a data-toggle="tooltip" title="Xác nhận hủy đơn xin nghỉ" data-id="' +
                                        data['id'] +
                                        '" href="javascript:void(0)" class="btn-cancel-leave-application btn btn-default btn-xs"><i class="text-danger glyphicon glyphicon-remove"></i></a>';
                                }
                                return "";
                            }
                        },
                        {
                            data: null,
                            render: function(data, type, row) {
                                if (isUserHasRoleApproved) {
                                    return `
                                            <button type="button" 
                                                data-code="${data['code']}"
                                                data-start="${data['start']}"
                                                data-end="${data['end']}"
                                                data-reason="${data['reason']}"
                                                data-total="${data['total']}"
                                                data-id="${data['id']}"
                                                data-from="${data['from_type']}"
                                                data-to="${data['to_type']}"
                                                data-half-shift="${data['half_shift']}"
                                                data-fullname="${data['user']['fullname']}_${data['user']['code']}" class="btn btn-xs btn-default edit-dayoff"
                                                data-url="{{ route('admin.manager.edit.admin') }}"
                                               data-toggle="modal" data-target="#editDayOff" title="Sửa đơn">
                                                <i class="text-warning glyphicon glyphicon-edit"></i>
                                            </button>
                                            <button data-id="${data['id']}" class="btn btn-xs btn-info show-log" data-placement="top" data-toggle="tooltip" title="Lịch sử chỉnh sửa">
                                                <i class="fas fa-history"></i>
                                            </button>
                                    `
                                }
                                return "";
                            }
                        },
                    ],
                    "columnDefs": [{
                        "className": "dt-center",
                        "targets": [4, 5, 6, 9, 10]
                    }, {
                        "className": 'text-nowrap',
                        "targets": [11]
                    }],
                    dom: '<"top "i>rt<"bottom"flp>',
                    fnDrawCallback: function() {
                        someActionAfterDatatableRendered();
                        if (!isUserHasRoleApproved) {
                            table.column(11).visible(false);
                        }
                    }
                });

                $(document).on('click', '.btn-edit-dayoff', function() {
                    showLoading()
                    $.ajax({
                        url: url,
                        data: {
                            'id': id,
                            'start': $('#start').val(),
                            'end': $('#end').val(),
                            'reason': $('.reason').val(),
                            'from_type': $('#from-type').val(),
                            'to_type': $('#to-type').val(),
                            'code': $('.code-dayoff').val(),
                            'total': $('span.total').text()
                        },
                        type: 'POST',
                        datatype: 'json',
                        headers: {
                            'X-CSRF-Token': "{!! csrf_token() !!}"
                        },
                        success: function(res) {
                            toastr.success(res.message);
                            table.ajax.reload(null, false);
                            $('#editDayOff').modal('hide');
                        },
                        error: function(obj, status, err) {
                            var error = $.parseJSON(obj.responseText);
                            toastr.error(error.message, '{!! trans('system.have_an_error') !!}');
                        }
                    }).always(function() {
                        hideLoading()
                    });
                })

                $(document).on('change', '.js-switch', function() {
                    let status = $(this).prop('checked') === true ? 1 : 0;
                    let dayOffId = $(this).data('id');
                    if (status == 1) {
                        $.ajax({
                            type: "GET",
                            dataType: "json",
                            url: '{{ route('admin.manager.leave.status') }}',
                            data: {
                                'status': status,
                                'dayoff_id': dayOffId
                            },
                            success: function(data) {
                                toastr.success(data.success);
                                table.ajax.reload(null, false);
                            },error: function(obj, status, err) {
                                var error = $.parseJSON(obj.responseText);
                                toastr.error(error.message, '{!! trans('system.have_an_error') !!}');
                            }
                        }).always(function() {
                        });
                    }
                });

                $(document).on('click', '.btn-cancel-leave-application', function() {
                    $("#modal-cancel-leave-application input[name='id']").val($(this).attr('data-id'));
                    $("#modal-cancel-leave-application").modal('show');
                });

                $(document).on('click', '#btn-confirm-cancel-leave-application', function() {
                    const id_delete = $("#modal-cancel-leave-application input[name='id']").val();

                    $.ajax({
                        type: "DELETE",
                        dataType: "json",
                        url: '{{ route('admin.manager.leave.confirms') }}',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "id": id_delete
                        },
                        success: function(data) {
                            $("#modal-cancel-leave-application").modal('hide');
                            toastr.success(data.success);
                            table.ajax.reload(null, false);
                        },
                        error: function(error) {
                            $("#modal-cancel-leave-application").modal('hide');
                            toastr.error(error.responseJSON.error);
                        },
                    });
                });

                table.columns('.status').every(function() {
                    var that = this;
                    var select = $('{!! Form::select('status', ['' => 'Tất cả', 1 => 'Đã duyệt', 0 => 'Chưa duyệt'], '', ['class' => 'status_select select2']) !!}')
                        .appendTo(
                            $('#tableTakeLeave thead tr:eq(1) th.status')
                        )
                        .on('change', function() {
                            table.draw();
                        });
                    $(".select2").select2({
                        width: '100%'
                    });
                });

            });
        }(window.jQuery);
    </script>
    <script>
        $(document).ready(function() {
            var morning = {!! \App\Defines\Schedule::TIME_OFF_MORNING !!};
            var afternoon = {!! \App\Defines\Schedule::TIME_OFF_AFTERNOON !!};

            $('#start, #end').on('change', total)
            $('#start').on('change', function() {
                // checkShowHalfShift(this.value, typeDept)
            })
            $('#to-type, #from-type').select2({
                width: '100%'
            }).on('select2:select select2:unselecting', total)
            var total = 0

            function total() {
                let start = $("#start").val().split("/").reverse().join("");
                let end = $("#end").val() == '' ? '' : $("#end").val().slice(0, 10).split("/").reverse().join("");
                let toType = $('#to-type').find("option:selected").val();
                let fromType = $('#from-type').find("option:selected").val();
                let diff = moment(end).diff(moment(start), 'days')
                diff = (diff + 1) * 2
                if (fromType == morning) {
                    if (diff == 2) {
                        $('#to-type').select2({
                            width: '100%'
                        }).find("option[value='1']").prop('disabled', false);
                    }
                    total = toType == afternoon ? diff : diff - 1
                } else {
                    if (diff == 2) {
                        $('#to-type').val(afternoon)
                        $('#to-type').select2({
                            width: '100%'
                        }).find("option[value=" + morning + "]").prop('disabled', true);
                        total = diff - 1
                    } else {
                        $('#to-type').select2({
                            width: '100%'
                        }).find("option[value=" + morning + "]").prop('disabled', false);
                        total = toType == afternoon ? diff - 1 : diff - 2
                    }
                }
                if (total < 0) total = 0
                $('span.total').html(total / 2)
            }

            var start = end = id = url = reason = '';
            $(document).on('click', '.edit-dayoff', function() {
                $('.text-info-user').empty();
                $('span.total').text($(this).data('total'));

                reason = $(this).data('reason');
                start = $(this).data('start');
                end = $(this).data('end');
                url = $(this).data('url');
                id = $(this).data('id');
                let halfShift = $(this).data('half-shift'),
                    toType = $(this).data('to'),
                    fromType = $(this).data('from')
                if (halfShift == 1) {
                    $('select#from-type, select#to-type').empty();
                    $('select#from-type').append(`<option value="1" selected="1==${fromType}">{!! trans('schedules.time-shift-offs.1') !!}</option><option value="2" selected="2==${fromType}">{!! trans('schedules.time-shift-offs.2') !!}</option>`)
                    $('select#to-type').append(`<option value="1" selected="1==${toType}">{!! trans('schedules.time-shift-offs.1') !!}</option><option value="2" selected="2==${toType}">{!! trans('schedules.time-shift-offs.2') !!}</option>`)
                } else {
                    $('select#from-type, select#to-type').empty();
                    $('select#from-type').append(`<option value="1" selected="1==${fromType}">{!! trans('schedules.time-offs.1') !!}</option><option value="2" selected="2==${fromType}">{!! trans('schedules.time-offs.2') !!}</option>`)
                    $('select#to-type').append(`<option value="1" selected="1==${toType}">{!! trans('schedules.time-offs.1') !!}</option><option value="2" selected="2==${toType}">{!! trans('schedules.time-offs.2') !!}</option>`)
                }
                $('#from-type').val($(this).data('from')).change();
                $('#to-type').val($(this).data('to')).change();

                $('.text-info-user').text($(this).data('fullname'));
                $('.reason').val(reason);
                $(".datepicker-from").datepicker({
                    format: 'dd/mm/yyyy',
                    autoclose: true
                }).datepicker("update", start);
                $(".datepicker-to").datepicker({
                    format: 'dd/mm/yyyy',
                    autoclose: true
                }).datepicker("update", end);
                // $('#start').val(start);
                // $('#end').val(end);

                $('.code-dayoff').val($(this).data('code')).change();

            })
        });
    </script>
@stop
