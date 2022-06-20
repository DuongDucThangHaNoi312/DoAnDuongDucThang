@extends('backend.master')
@section('title')
    {!! trans('system.action.show') !!}
@stop
@section('head')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.css">
    <link rel="stylesheet" type="text/css"
          href="{!! asset('assets/backend/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/css/calendar.css') !!}"/>
@stop

@section('content')
    <div class="row schedule" style="padding-right: 5px">
        <div class="col-md-3">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h4 class="box-title" style="font-weight: 600;">Nhân Viên</h4>
                </div>
                <div class="box-body">
                    <div style="margin: 5px 0">
                        {!! Form::select('user_id', [''=>trans('shifts.choose.user')]+$users , old('user_id'), ['class' => 'form-control select2 user_id',]) !!}
                    </div>
                </div>
            </div>
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h4 class="box-title" style="font-weight: 600;">{!! trans('shifts.apply') !!}</h4>
                </div>
                <div class="box-body">
                    <div style="margin: 5px 0">
                        {!! Form::select('apply_user_id[]', '' , old('apply_user_id[]'), ['class' => 'form-control select2 ','multiple','id'=>'applyUser','disabled']) !!}
                    </div>
                </div>
                <div class="box-footer">
                    <button type="button" class="btn btn-primary"
                            id="apply">{!! trans('system.action.save') !!}</button>
                </div>
            </div>
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h4 class="box-title" style="font-weight: 600;">{!! trans('shifts.shift') !!}</h4>
                </div>
                <div class="box-body">
                    <!-- the events -->
                    <div id="external-events">
                        {{ $shift = \App\Define\Shift::getShiftByDepartment1($id) }}
                        {{ $color = \App\Define\Shift::getColorByDepartment($id) }}
                        @if (count($shift) > 0)
                            @foreach ($shift as $key1 => $value1)
                                <div class="external-event" style="background: {{ $color[$key1] }}; color: white "
                                    data-code="{{ $key1 }}">{{ $value1 }}</div>
                            @endforeach
                        @else
                            <span>Chưa cài đặt thời gian làm việc
                                <a href="{{ route('admin.workschedules.list-shift') }}">Đi tới cài đặt</a>
                            </span>
                        @endif
                        
                        {{-- <div class="external-event" style="background: #3CB371 "
                             data-code="{!! \App\Define\Shift::FIRST_SHIFT !!}">{!! trans('shifts.shift_one') !!}</div>
                        <div class="external-event " style="background: #1E90FF "
                             data-code="{!! \App\Define\Shift::SECOND_SHIFT !!}">{!! trans('shifts.shift_two') !!}</div>
                        <div class="external-event " style="background: #F4A460 "
                             data-code="{!! \App\Define\Shift::THREE_SHIFT !!}">{!! trans('shifts.shift_three') !!}</div>
                        <div class="external-event " style="background: rgb(244 103 96)"
                             data-code="{!! \App\Define\Shift::FOUR_SHIFT !!}">{!! trans('shifts.shift_four') !!}</div> --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-9" style="padding-left: 5px!important; opacity: 0" id="divCalendar">
            <div class="box box-primary">
                <div class="box-body no-padding">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
    <div id="modal-event" class="modal fade" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="padding: 5px">
                    <span style="font-size: 20px; font-weight: 600;">{!! trans('shifts.shift') !!}</span>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="event-id">
                    <input type="hidden" id="staff-id">
                    <input type="hidden" id="cell-id">
                    <input type="hidden" id="event-click-total">
                    <div class="row" style="margin: 5px auto;">
                        <label class="col col-md-2">{!! trans('shifts.shift') !!}</label>
                        <div class="col col-md-10">
                            <input id="day-off" type="hidden" value="">
                            {!! Form::select('shifts', [''=>trans('shifts.choose.shifts')]+\App\Define\Shift::getShiftByDepartment1($id), old('shifts'), ['class' => 'form-control select2 shiftsSelect', 'required']) !!}
                        </div>
                    </div>

                    <div class="row" style="margin: 5px 0;">
                        <label class="col col-md-2" for="start_at">{!! trans('schedules.start') !!}</label>
                        <div class="col-md-10">
                            {!! Form::text('start', Request::input('start'), ['id' =>'start', 'autocomplete' => 'off', 'class' => 'form-control datepicker datepicker-from', 'required']) !!}
                        </div>
                    </div>
                    <div class="row row-end" style="margin: 5px 0; vertical-align: middle;">
                        <label class="col col-md-2" for="end_at">{!! trans('schedules.to') !!}</label>
                        <div class="col-md-10">
                            {!! Form::text('end', Request::input('end'), ['id' =>'end', 'autocomplete' => 'off', 'class' => 'form-control datepicker datepicker-to', 'required']) !!}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="cancel-event"
                            data-dismiss="modal">{!! trans('system.action.cancel') !!}</button>
                    <button type="button" class="btn btn-primary"
                            id="save-event">{!! trans('system.action.save') !!}</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

@stop

@section('footer')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"
            integrity="sha256-4iQZ6BVL4qNKlQ27TExEhBN1HFPvAvAMbFavKKosSWQ=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/locale/vi.js"></script>
    <script src="{!! asset('assets/backend/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.vi.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/select2/select2.full.min.js') !!}"></script>

    <script>
        // !function ($) {
        $(function () {

            $(".select2").select2({
                width: '100%',
                placeholder : '{!! trans('shifts.choose.user') !!}'
            });
            $("select.shiftsSelect").select2({
                width: '100%',
                placeholder : '{!! trans('shifts.choose.shifts') !!}'
            });

            function init_events(ele) {
                ele.each(function () {
                    let eventObject = {
                        title: $.trim($(this).text()),
                        shifts: $(this).attr('data-code'),
                    }
                    $(this).data('eventObject', eventObject)
                    $(this).draggable({
                        zIndex: 1070,
                        revert: true, // will cause the event to go back to its
                        revertDuration: 0  //  original position after the drag
                    })
                })
            }

            init_events($('#external-events div.external-event'));

            let dayOffDepartments = {!! $dayOffDepartments !!};
            $('#cancel-event').on('click', function () {
                $('#event-click-total').val(0)
            })

            function showModal(event = null, dayClick = null) {
                if (!$('select.user_id').val()) return false;
                let tag = $("#modal-event");
                tag.modal("show");
                let code = '{!! \App\Define\Shift::FIRST_SHIFT !!}';
                let start = moment().format('DD/MM/YYYY')
                let end = moment().format('DD/MM/YYYY')
                if (event) {
                    code = event.shifts
                    tag.find('#event-id').val(event.id)
                    tag.find('#cell-id').val(event._id)
                    tag.find("#day-off").val(event.title);
                    start = event.start.format().split("-").reverse().join("/")
                    end = event.end ? event.end.format('DD/MM/YYYY') : start
                }
                tag.find('.shiftsSelect').val(code).change()
                if (dayClick) {
                    start = dayClick.format('DD/MM/YYYY')
                    end = start
                }
                $(".datepicker-from").datepicker({
                    format: 'dd/mm/yyyy',
                    autoclose: true
                }).datepicker("update", start);
                $(".datepicker-to").datepicker({
                    format: 'dd/mm/yyyy',
                    autoclose: true
                }).datepicker("update", end);
                $("#save-event").show();
            }

            function formatDate(date) {
                return moment(date).format('YYYY-MM-DD')
            }

            function isDateHasEvent(start, end = null, cellId = null, isEventDrop = null) {
                let allEvents = [];
                allEvents = $('#calendar').fullCalendar('clientEvents');

                let check = false
                if (end) {
                    $.each(allEvents, function (index, event) {
                        var end_date = event.end || event.start;
                        if (event._id == cellId) return;
                        if (formatDate(end) >= formatDate(event.start) && formatDate(start) <= formatDate(end_date)) {
                            check = true
                            return false
                        }

                    });
                } else {
                    $.each(allEvents, function (index, event) {
                        if (moment(start).isBetween(moment(event.start), moment(event.end), undefined, '[]')) {
                            check = true
                            return false
                        }
                    });

                }
                return check;
            }

            $('#calendar').fullCalendar({
                locale: 'vi',
                todayHighlight: false,
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month, basicWeek',
                },
                firstDay: 1,
                defaultView: 'month',
                height: 'auto',
                editable: true,
                droppable: true,
                displayEventTime: false,
                eventSources: [
                    // eventStaffs,
                    dayOffDepartments
                ],
                drop: function (date) {
                    // if (formatDate(moment()) > formatDate(date)) {
                    //     $("#modal-event").modal("hide");
                    //     toastr.error('{!! trans('shifts.past_date') !!}', '{!! trans('system.info') !!}')
                    //     return false
                    // }
                    if (isDateHasEvent(date)) {
                        toastr.error('{!! trans('shifts.same_day_off') !!}', '{!! trans('system.info') !!}')
                        return false
                    }
                    let originalEventObject = $(this).data('eventObject')

                    let copiedEventObject = $.extend({}, originalEventObject)
                    copiedEventObject.start = date
                    copiedEventObject.backgroundColor = $(this).css('background-color')
                    showModal(copiedEventObject)
                },
                eventRender: function (event, element, view) {

                    let content = ''
                    if (formatDate(event.start) == formatDate(event.end) || !event.end) {
                        content = 'Làm ' + event.title + ' ngày ' + event.start.format('DD/MM/YYYY')
                    } else {
                        content = 'Làm ' + event.title + ' từ ngày ' + event.start.format('DD/MM/YYYY') + ' đến  ngày ' + event.end.format('DD/MM/YYYY')
                    }
                    element.popover({
                        title: event.title,
                        content: content,
                        trigger: 'hover',
                        placement: 'top',
                        container: 'body'
                    });
                    let dateEnd = event.end
                    if (!event.end) {
                        console.log('Error eventRender: End Date null because allDay!')
                        dateEnd = event.start
                    }
                    element.find(".fc-content").prepend("<span class='closeon fa fa-times'>&#xe5cd;</span>");
                    element.find(".closeon").on("click", function (e) {
                        e.stopPropagation()
                        if (confirm("{!! trans('system.confirm_delete') !!}")) {
                            let url = "{!! route('admin.shifts.destroy', [-1]) !!}"
                            url = url.replace('-1', event.id)
                            $.ajax({
                                url: url,
                                type: 'POST',
                                data: {_method: 'DELETE'},
                                headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                                success: function (res) {
                                    $("#calendar").fullCalendar("removeEvents", event._id);
                                },
                                error: function (err) {
                                    let error = $.parseJSON(err.responseText);
                                    toastr.warning(error.message, "{!! trans('system.have_error') !!}")
                                }
                            })
                        } else {
                            return false
                        }
                    });
                },
                eventDrop: function (event, delta, revertFunc) {
                    if (!event.user_id) {
                        if (event.type == 1) {
                            toastr.error('Không thể sửa đổi ngày nghỉ phòng ban.', '{!! trans('system.info') !!}')
                        } else {
                            toastr.error('Không thể sửa đổi ngày nghỉ lễ.', '{!! trans('system.info') !!}')
                        }
                        revertFunc()
                        return false
                    }
                    if (!delta._days) return false
                    // if (moment() > event.start) {
                    //     toastr.error('{!! trans('shifts.past_date') !!}', '{!! trans('system.info') !!}')
                    //     revertFunc()
                    //     return false
                    // }
                    if (isDateHasEvent(event.start, event.end, event._id, event.from_type, event.to_type)) {
                        toastr.error('{!! trans('shifts.same_day_off') !!}', '{!! trans('system.info') !!}')
                        revertFunc()
                        return false
                    }
                    if (confirm("{!! trans('system.update_confirm') !!}")) {
                        let eventId = event.id
                        let url = "{!! route('admin.shifts.update', [-1]) !!}"
                        url = url.replace('-1', eventId)
                        $.ajax({
                            url: url,
                            data: {_method: 'PUT', duration: delta._days},
                            type: 'POST',
                            headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                            success: function (res) {
                                if (res.data) {
                                    res.data.end = res.data.end + "T23:59:00"
                                    $('#calendar').fullCalendar('updateEvent', event);
                                    toastr.success(res.message, "{!! trans('system.info') !!}")
                                } else {
                                    toastr.error(res.message, "{!! trans('system.have_error') !!}")
                                    revertFunc()
                                }
                            },
                            error: function (err) {
                                let error = $.parseJSON(err.responseText);
                                revertFunc()
                                toastr.error(error.message, "{!! trans('system.have_error') !!}")
                            }
                        })
                    } else {
                        revertFunc()
                        return false
                    }
                },
                eventClick: function (event, element) {
                    if (!event.user_id) {
                        return false
                    }
                    if (formatDate(moment()) > formatDate(event.start)) {
                        $("#modal-event").modal("hide");
                        toastr.error('{!! trans('shifts.error_past_day_off') !!}', '{!! trans('system.info') !!}')
                        return false
                    }
                    $('#event-click-total').val(event.total)
                    showModal(event)
                },
                dayClick: function (date, jsEvent, view) {
                    showModal(null, date)
                },
            });
            $("body").on('click', '.shift-title', function (e) {
                e.stopPropagation()
                return false
            })

            function checkDateInRangeDate(date, rangeDateStart, rangeDateEnd) {
                return moment(formatDate(date)).isBetween(formatDate(rangeDateStart), formatDate(rangeDateEnd), undefined, '[]')
            }

            $("#save-event").on("click", function () {
                let shifts = $(".shiftsSelect").val();
                let start = $("#start").val().split("/").reverse().join("-");
                let end = $("#end").val().split("/").reverse().join("-");
                let cellId = $('#cell-id').val()
                let eventId = $('#event-id').val()
                let apply_user = $('#applyUser').val()
                if (!start) {
                    toastr.error('{!! trans('shifts.required_start') !!}', "{!! trans('system.info') !!}")
                    return false
                }
                if (!end) {
                    toastr.error('{!! trans('shifts.required_end') !!}', "{!! trans('system.info') !!}")
                    return false
                }
                // if (formatDate(moment()) >= formatDate(start)) {
                //     toastr.error('{!! trans('shifts.past_date') !!}', '{!! trans('system.info') !!}')
                //     return false
                // }
                // if (isDateHasEvent(start, end, cellId)) {
                //     toastr.error('{!! trans('shifts.same_day_off') !!}', '{!! trans('system.info') !!}')
                //     return false
                // }
                let user_id = $('.user_id').val()
                console.log('u', user_id)
                let eventData = {
                    id: eventId,
                    shifts: shifts,
                    start: start,
                    end: end,
                    user_id: user_id,

                };
                let url = "{!! route('admin.shifts.update', [-1]) !!}"
                let method = ''
                url = eventId ? url.replace('-1', eventId) : "{!! route('admin.shifts.store') !!}"
                method = eventId ? 'PUT' : method
                showLoading()
                $.ajax({
                    url: url,
                    data: {_method: method, event: eventData},
                    type: 'POST',
                    headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                    success: function (res) {
                        if (res.data) {
                            res.data.end = res.data.end + "T23:59:00"
                            $('#calendar').fullCalendar('removeEvents', cellId);
                            $('#calendar').fullCalendar('renderEvent', res.data, true);
                            toastr.success(res.message, "{!! trans('system.info') !!}")
                            $("#modal-event").modal("hide");
                        }
                    },
                    error: function (err) {
                        let error = $.parseJSON(err.responseText);
                        toastr.error(error.message, "{!! trans('system.have_error') !!}")
                    }
                }).always(function() {
                    hideLoading()
                });

            });

            $('.datepicker').datepicker({
                format: 'dd/mm/yyyy',
                autoclose: true,
                todayHighlight: true,
                language: "vi",
            });
            $(".user_id").on("change", function () {
                if (!$('.user_id ').val()) {
                    $('#divCalendar').css('opacity', 0)
                } else {
                    $('#divCalendar').css('opacity', 1)
                    showLoading()
                    $.ajax({
                        url: "{!! route('admin.shifts.getShiftUser') !!}",
                        data: {
                            user_id: $('.user_id ').val()
                        },
                        type: 'POST',
                        headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                        success: function (res) {
                            $('#calendar').fullCalendar('removeEvents');
                            if (res.data.length > 0) {
                                $.each(res.data, function (index, value) {
                                    $('#calendar').fullCalendar('renderEvent', value, true);
                                })

                            }
                            if (res.user) {
                                $('#applyUser').prop('disabled',false)
                                $('#applyUser option').remove()
                                $('#applyUser').append('<option value="">' + '' + '</option>')
                                $.each(res.user, function (index, value) {
                                    $('#applyUser').append('<option value="' + index + '">' + value + '</option>')
                                })
                            }
                            $('#calendar').fullCalendar( 'addEventSource', dayOffDepartments)
                        },
                        error: function (err) {
                            console.log(err)
                            let error = $.parseJSON(err.responseText);
                            toastr.error(error.message, "{!! trans('system.have_error') !!}")
                        }
                    }).always(function() {
                        hideLoading()
                    });
                }
            })
            $('#apply').on('click',function () {
                let user_id = $('.user_id ').val()
                let user_apply =  $('#applyUser').val()
                let date = $("#calendar").fullCalendar('getDate');
                let month = date.month();
                let year = date.year();
                if (!user_apply || !user_id) {
                    toastr.error('{!! trans('shifts.user_required') !!}', "{!! trans('system.info') !!}")
                    return false
                }
                showLoading()
                $.ajax({
                    url: "{!! route('admin.shifts.copyShifts') !!}",
                    data: {
                        user_id: user_id,
                        user_id_apply: user_apply,
                        month: month+1,
                        year: year,
                    },
                    type: 'POST',
                    headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                    success: function (res) {
                        toastr.success(res.message, "{!! trans('system.info') !!}")

                        setTimeout(function () {
                            $('#applyUser').val('').change()
                        }, 1500);
                    },
                    error: function (err) {
                        console.log(err)
                        let error = $.parseJSON(err.responseText);
                        toastr.error(error.message, "{!! trans('system.have_error') !!}")
                    }
                }).always(function() {
                    hideLoading()
                });

            })
        });
        // }(window.jQuery);
    </script>
@stop
