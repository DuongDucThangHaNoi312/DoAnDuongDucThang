@extends('backend.master')
@section('title')
    {!! trans('system.action.show') !!}
@stop
@section('head')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.css">
    <link rel="stylesheet" type="text/css"
          href="{!! asset('assets/backend/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/css/calendar.css') !!}"/>
    <style>
        table.desc_icon tr td {
            padding-right: 5px;
        }
    </style>
@stop

@section('content')
    <div class="row schedule" style="padding-right: 5px">
        <div class="col-md-3">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h4 class="box-title" style="font-weight: 600;">Thống kê</h4>
                </div>
                <div class="box-body">
                    <div style="margin: 5px 0">
                        <span>Số ngày nghỉ phép trong năm:</span>
                        <span style="font-weight: 600;">{!! \Auth::user()->original_rest !!}</span>
                    </div>
                    <div style="margin: 5px 0">
                        <span>Số đơn nghỉ chưa duyệt tháng</span>
                        <span class="month"></span>
                        <span class="countPending"></span>
                    </div>
                    <div style="margin: 5px 0">
                        <span>Số đơn nghỉ đã duyệt tháng</span>
                        <span class="month"></span>
                        <span class="countAccept"></span>
                    </div>
                    <div style="margin: 5px 0">
                        <span>Số ngày nghỉ phép còn lại:</span>
                        <span class="dayOffCanUse1"
                              style="font-weight: 600;">{!! \Auth::user()->rest !!}</span>
                    </div>
                </div>
            </div>
            <?php $isOffice = $typeDept == \App\Define\Shift::OFFICE_TIME; ?>
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h4 class="box-title" style="font-weight: 600;">{!! trans('schedules.time-off') !!}</h4>
                </div>
                <div class="box-body">
                    <table class="desc_icon">
                        <tr>
                            <td>{!! $isOffice ? trans('schedules.morning') : trans('schedules.half_shift1') !!}</td>
                            <td>◤</td>
                        </tr>
                        <tr>
                            <td>{!! $isOffice ? trans('schedules.afternoon') : trans('schedules.half_shift2') !!}</td>
                            <td>◢</td>
                        </tr>
                        <tr>
                            <td>{!! $isOffice ? trans('schedules.allDay') : trans('schedules.all_shift') !!}</td>
                            <td style="position: relative">
                                <span style="position: absolute; left: 0; top: 0; display: block;">◤</span>
                                <span style="position: absolute; left: 0; top: 0; display: block;">◢</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="box box-solid">
                <div class="box-header with-border">
                    <h4 class="box-title" style="font-weight: 600;">{!! trans('schedules.reason') !!}</h4>
                </div>
                <div class="box-body">
                    <!-- the events -->
                    <?php 
                        $contract = \App\Models\Contract::where('user_id', Auth::user()->id)->orderBy('id', 'DESC')->first();
                    ?>
                    @if (!is_null($contract))
                        @if ($contract->is_main == 2)
                            <div id="external-events">
                                <div class="external-event" style="background-color: #679a96; color: #FFFFFF"
                                    data-code="{!! \App\Defines\Schedule::DAY_OFF_12 !!}">{!! \App\Defines\Schedule::DAY_OFF_12 !!}
                                    -{!! trans('schedules.day-offs.' . \App\Defines\Schedule::DAY_OFF_12) !!}</div>
                                <div class="external-event bg-green"
                                    data-code="{!! \App\Defines\Schedule::DAY_OFF_SICK !!}">{!! \App\Defines\Schedule::DAY_OFF_SICK !!}
                                    -{!! trans('schedules.day-offs.' . \App\Defines\Schedule::DAY_OFF_SICK) !!}</div>
                                <div class="external-event bg-light-blue"
                                    data-code="{!! \App\Defines\Schedule::DAY_OFF_WEDDING !!}">{!! \App\Defines\Schedule::DAY_OFF_WEDDING !!}
                                    -{!! trans('schedules.day-offs.' . \App\Defines\Schedule::DAY_OFF_WEDDING) !!}</div>
                                <div class="external-event" style="background-color: #425256; color: #FFFFFF"
                                    data-code="{!! \App\Defines\Schedule::DAY_OFF_FUNERAL !!}">{!! \App\Defines\Schedule::DAY_OFF_FUNERAL !!}
                                    -{!! trans('schedules.day-offs.' . \App\Defines\Schedule::DAY_OFF_FUNERAL) !!}</div>
                                <div class="external-event" style="background-color: #bc6fbd; color: #FFFFFF"
                                    data-code="{!! \App\Defines\Schedule::DAY_OFF_NO_SALARY !!}">{!! \App\Defines\Schedule::DAY_OFF_NO_SALARY !!}
                                    -{!! trans('schedules.day-offs.' . \App\Defines\Schedule::DAY_OFF_NO_SALARY) !!}</div>
                                <div class="external-event bg-yellow"
                                    data-code="{!! \App\Defines\Schedule::DAY_OFF_70_SALARY !!}">{!! \App\Defines\Schedule::DAY_OFF_70_SALARY !!}
                                    -{!! trans('schedules.day-offs.' . \App\Defines\Schedule::DAY_OFF_70_SALARY) !!}</div>
        
                                <div class="external-event" style="background-color: #f1e72b; color: #FFFFFF"
                                    data-code="{!! \App\Defines\Schedule::DAY_OFF_MISSION !!}">{!! \App\Defines\Schedule::DAY_OFF_MISSION !!}
                                    -{!! trans('schedules.day-offs.' . \App\Defines\Schedule::DAY_OFF_MISSION) !!}</div>
                                <div class="external-event" style="background-color: #00c0ef; color: #FFFFFF"
                                    data-code="{!! \App\Defines\Schedule::DAY_OFF_BABE !!}">{!! \App\Defines\Schedule::DAY_OFF_BABE !!}
                                    -{!! trans('schedules.day-offs.' . \App\Defines\Schedule::DAY_OFF_BABE) !!}</div>
                                <div class="external-event" style="background-color: rgb(65 64 139); color: white"
                                    data-code="{!! \App\Defines\Schedule::WORK_FROM_HOME !!}">{!! \App\Defines\Schedule::WORK_FROM_HOME !!}
                                    -{!! trans('schedules.day-offs.' . \App\Defines\Schedule::WORK_FROM_HOME) !!}</div>
                            </div>
                        @else    
                            <div id="external-events">
                                <div class="external-event" style="background-color: #bc6fbd; color: #FFFFFF"
                                    data-code="{!! \App\Defines\Schedule::DAY_OFF_NO_SALARY !!}">{!! \App\Defines\Schedule::DAY_OFF_NO_SALARY !!}
                                    -{!! trans('schedules.day-offs.' . \App\Defines\Schedule::DAY_OFF_NO_SALARY) !!}</div>
                                <div class="external-event" style="background-color: #f1e72b; color: #FFFFFF"
                                    data-code="{!! \App\Defines\Schedule::DAY_OFF_MISSION !!}">{!! \App\Defines\Schedule::DAY_OFF_MISSION !!}
                                    -{!! trans('schedules.day-offs.' . \App\Defines\Schedule::DAY_OFF_MISSION) !!}</div>
                                <div class="external-event" style="background-color: rgb(65 64 139); color: white"
                                    data-code="{!! \App\Defines\Schedule::WORK_FROM_HOME !!}">{!! \App\Defines\Schedule::WORK_FROM_HOME !!}
                                    -{!! trans('schedules.day-offs.' . \App\Defines\Schedule::WORK_FROM_HOME) !!}</div>
                            </div>
                        @endif
                    @endif
                    
                </div>
            </div>
        </div>
        <div class="col-md-9" style="padding-left: 5px!important;">
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
                    <span style="font-size: 20px; font-weight: 600;">Đơn xin nghỉ</span>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="event-id">
                    <input type="hidden" id="staff-id">
                    <input type="hidden" id="cell-id">
                    <input type="hidden" id="event-click-total">
                    {!! Form::hidden('type-dept', $typeDept) !!}
                    <div class="row" style="margin: 5px auto;">
                        <label class="col col-md-2">{!! trans('schedules.type_leave') !!}</label>
                        <div class="col col-md-5">
                            <input id="day-off" type="hidden" value="">
                            {!! Form::select('title', \App\Defines\Schedule::getDayOffTypeForOption(), old('title'), ['class' => 'form-control select2 titleSelect', 'required']) !!}
                        </div>
                        {{--                        <div class="col-md-5 text-right half-shift" style="display: none">--}}
                        {{--                            <label style="margin: 0 5px">{!! trans('schedules.half_shift') !!}</label>--}}
                        {{--                            {!! Form::checkbox('half_shift', 1, old('half_shift', 0), [ 'class' => 'minimal-blue half-shift' ]) !!}--}}
                        {{--                        </div>--}}
                    </div>
                    <?php $timeOffOption = !$isOffice ? \App\Defines\Schedule::getTimeShiftOffForOption() : \App\Defines\Schedule::getTimeOffForOption();  ?>
                    <div class="row" style="margin: 5px 0;">
                        <label class="col col-md-2" for="start_at">{!! trans('schedules.start') !!}</label>
                        <div class="col col-md-5">
                            {!! Form::text('start_at', Request::input('start_at'), ['id' =>'start-at', 'autocomplete' => 'off', 'class' => 'form-control datepicker datepicker-from', 'required']) !!}
                        </div>
                        <div class="col col-md-5 days">
                            <select id="from-type" class="select2">
                                @foreach($timeOffOption as $index => $val)
                                    <option value='{{$index}}'> {{$val}} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row row-end" style="margin: 5px 0; vertical-align: middle;">
                        <label class="col col-md-2" for="end_at">{!! trans('schedules.to') !!}</label>
                        <div class="col col-md-5">
                            {!! Form::text('end_at', Request::input('end_at'), ['id' =>'end-at', 'autocomplete' => 'off', 'class' => 'form-control datepicker datepicker-to', 'required']) !!}
                        </div>
                        <div class="col col-md-5 days">
                            <select id="to-type" class="select2">
                                @foreach($timeOffOption as $index => $val)
                                    <option value='{{$index}}'> {{$val}} </option>
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

    @if(!$departmentId)
        <div class="modal show" id="infoModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title" id="exampleModalLabel">Thông báo</h3>
                    </div>
                    <div class="modal-body">
                        <h5>Tài khoản đăng nhập chưa thuộc phòng ban nào!</h5>
                    </div>
                    <div class="modal-footer text-center">
                        {!! HTML::link(route( 'admin.home' ), 'Quay về trang chủ', ['class' => 'btn btn-danger btn-flat']) !!}
                    </div>
                </div>
            </div>
        </div>
    @endif
@stop

@section('footer')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"
            integrity="sha256-4iQZ6BVL4qNKlQ27TExEhBN1HFPvAvAMbFavKKosSWQ=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.js?v=3.10.2"></script>
    {{--<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/locale/vi.js?v=3.9.0"></script>--}}
    <script src="{!! asset('assets/backend/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.vi.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/iCheck/icheck.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/select2/select2.full.min.js') !!}"></script>

    <script>
        const DAY_OFF_SICK = '{!! \App\Defines\Schedule::DAY_OFF_SICK !!}';
        const DAY_OFF_WEDDING = '{!! \App\Defines\Schedule::DAY_OFF_WEDDING !!}';
        const DAY_OFF_FUNERAL = '{!! \App\Defines\Schedule::DAY_OFF_FUNERAL !!}';
        const DAY_OFF_NO_SALARY = '{!! \App\Defines\Schedule::DAY_OFF_NO_SALARY !!}';
        const DAY_OFF_70_SALARY = '{!! \App\Defines\Schedule::DAY_OFF_70_SALARY !!}';
        const DAY_OFF_12 = '{!! \App\Defines\Schedule::DAY_OFF_12 !!}';
        const DAY_OFF_MISSION = '{!! \App\Defines\Schedule::DAY_OFF_MISSION !!}';
        const DAY_OFF_BABE = '{!! \App\Defines\Schedule::DAY_OFF_BABE !!}';
        const SICK_LIMIT = {!! \App\Defines\Schedule::SICK_LIMIT !!};
        const FUNERAL_LIMIT = {!! \App\Defines\Schedule::FUNERAL_LIMIT !!};
        const WEDDING_LIMIT = {!! \App\Defines\Schedule::WEDDING_LIMIT !!};
        const OFFICE_TIME = {!! \App\Define\Shift::OFFICE_TIME !!};
        const SHIFT_TIME = {!! \App\Define\Shift::SHIFT_TIME !!};
        let typeDept = {!! json_encode($typeDept) !!};
        const DAY_OFF_FREE = {!! json_encode(\App\Defines\Schedule::dayOffFree()) !!};

        // !function ($) {
        $(function () {
            // $('#infoModal').modal('show')
            let morning = {!! \App\Defines\Schedule::TIME_OFF_MORNING !!};
            let afternoon = {!! \App\Defines\Schedule::TIME_OFF_AFTERNOON !!};
            $(".select2").select2({width: '100%'});
            $('input[type="checkbox"].minimal-blue').iCheck({
                checkboxClass: 'icheckbox_minimal-blue'
            });
            /*
            * Hàm init_events để drag ngày nghỉ bên ngoài vào
            *
            * */
            function init_events(ele) {
                ele.each(function () {
                    let eventObject = {
                        title: $.trim($(this).text()),
                        code: $(this).attr('data-code'),
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
            $('.button-add').on('click', function () {
                showModal()
            })

            let eventStaffs = {!! $eventStaffs !!};
            let dayOffDepartments = {!! $dayOffDepartments !!};
            $('#cancel-event').on('click', function () {
                $('#event-click-total').val(0)
                $('.half-shift').iCheck('uncheck')
                $('.half-shift').iCheck('update')
            })

            function showModal(event = null, dayClick = null) {
                let tag = $("#modal-event");
                tag.modal("show");
                let code = '{!! \App\Defines\Schedule::DAY_OFF_12 !!}';
                let start = moment().format('DD/MM/YYYY')
                let end = moment().format('DD/MM/YYYY')
                tag.find('.reason').val('')
                // $('.half-shift').iCheck('uncheck')
                // $('.half-shift').iCheck('update')
                if (event) {
                    code = event.code
                    tag.find('#event-id').val(event.id)
                    tag.find('#staff-id').val(event.staff_id)
                    tag.find('#cell-id').val(event._id)
                    tag.find("#day-off").val(event.title);
                    tag.find('.reason').val(event.reason)
                    start = event.start.format().split("-").reverse().join("/")
                    end = event.end ? event.end.format('DD/MM/YYYY') : start
                    if (event.to_type) {
                        tag.find("#to-type").val(event.to_type).change()
                        tag.find("#from-type").val(event.from_type).change()
                    }
                    // if (event.half_shift) {
                    //     $('.half-shift').iCheck('check')
                    //     $('.half-shift').iCheck('update')
                    // } else {
                    //     $('.half-shift').iCheck('uncheck')
                    //     $('.half-shift').iCheck('update')
                    // }
                }
                // if (typeDept != OFFICE_TIME) {
                //     tag.find("#from-type").val(morning).change()
                //     tag.find("#to-type").val(afternoon).change()
                // }
                tag.find('.titleSelect').val(code).change()
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
                // checkShowHalfShift(start, typeDept)
                total()
                $("#save-event").show();
            }

            // function checkShowHalfShift(start, typeDept) {
            //     if (moment(start, 'DD/MM/YYYY').day() == 6 && typeDept == SHIFT_TIME) $('div.half-shift').show()
            //     else {
            //         $('.half-shift').iCheck('uncheck')
            //         $('.half-shift').iCheck('update')
            //         $('div.half-shift').hide()
            //     }
            // }

            $('#start-at, #end-at').on('change', total)
            $('#start-at').on('change', function () {
                // checkShowHalfShift(this.value, typeDept)
            })
            $('#to-type, #from-type').select2({width: '100%'}).on('select2:select select2:unselecting', total)

            function total() {
                let start = $("#start-at").val().split("/").reverse().join("");
                let end = $("#end-at").val() == '' ? '' : $("#end-at").val().slice(0, 10).split("/").reverse().join("");
                let toType = $('#to-type').find("option:selected").val();
                let fromType = $('#from-type').find("option:selected").val();
                let diff = moment(end).diff(moment(start), 'days')
                diff = (diff + 1) * 2
                let total = 0
                if (fromType == morning) {
                    if (diff == 2) {
                        $('#to-type').select2({width: '100%'}).find("option[value='1']").prop('disabled', false);
                    }
                    total = toType == afternoon ? diff : diff - 1
                } else {
                    if (diff == 2) {
                        $('#to-type').val(afternoon)
                        $('#to-type').select2({width: '100%'}).find("option[value=" + morning + "]").prop('disabled', true);
                        total = diff - 1
                    } else {
                        $('#to-type').select2({width: '100%'}).find("option[value=" + morning + "]").prop('disabled', false);
                        total = toType == afternoon ? diff - 1 : diff - 2
                    }
                }
                if (total < 0) total = 0
                $('span.total').html(total / 2)
            }

            function formatDate(date) {
                return moment(date).format('YYYY-MM-DD')
            }

            function isDateHasEvent(start, end = null, cellId = null, from_type = null, to_type = null, isEventDrop = null) {
                let allEvents = [];
                allEvents = $('#calendar').fullCalendar('clientEvents');
                let code = $('.titleSelect').val()
                // Với ngày nghỉ là công tác hoặc thai sản, chỉ check trùng với ngày nghỉ nhân viên ( cho đè lên ngày nghỉ phòng ban)

                allEvents = $.inArray(code, DAY_OFF_FREE) !== -1 ? allEvents.filter(e => e.user_id) : allEvents
                let check = false
                if (end) {
                    $.each(allEvents, function (index, event) {
                        if (event._id == cellId) return;
                        let newStart = formatDate(start), newEnd = formatDate(end), eventStart = formatDate(event.start), eventEnd = formatDate(event.end);
                        if (newEnd > eventStart && newStart < eventEnd) {
                            check = true
                            return false
                        } else if (newEnd < eventStart || newStart > eventEnd) {
                            check = false;
                        } else if (newStart == eventEnd) {
                            if (event.from_type == morning && event.to_type == afternoon){
                                check = true
                            } else {
                                // Trường hợp pb ca, pn nghỉ nửa ngày thì cho xin xả láng
                                if (typeDept != OFFICE_TIME && newStart == newEnd) check = false;
                                else if (event.to_type == afternoon) {
                                    check = true;
                                    if (newStart == newEnd) check = to_type == afternoon
                                }
                                else if (event.to_type == morning) check = from_type == morning
                            }

                        } else if (newEnd == eventStart) {
                            if (event.from_type == morning && event.to_type == afternoon){
                                check = true
                            } else {
                                if (typeDept != OFFICE_TIME && newStart == newEnd) check = false;
                                else if (event.from_type == morning) {
                                    check = true;
                                    if (newEnd == newStart) check = from_type == morning
                                }
                                else if (event.from_type == afternoon) check = to_type == afternoon
                            }

                        }
                        if (check == true) return false;
                    });
                } else {
                    $.each(allEvents, function (index, event) {
                        if (moment(start).isBetween(moment(event.start), moment(event.end))) {
                            check = true
                            return false
                        }
                    });
                }
                return check;
            }

            function removeIcon(start, end) {
                start = moment(start).format('YYYY-MM-DD')
                end = moment(end).format('YYYY-MM-DD')
                $('td.fc-day[data-date=' + start + ']').find('span.morning').remove()
                $('td.fc-day[data-date=' + start + ']').find('span.afternoon').remove()
                $('td.fc-day[data-date=' + end + ']').find('span.morning').remove()
                $('td.fc-day[data-date=' + end + ']').find('span.afternoon').remove()
            }

            function titleContent(event, typeDept) {
                let content = '',
                    startR = event.start.format('DD/MM/YYYY'),
                    isOffice = typeDept == OFFICE_TIME,
                    check = isOffice || event.type,
                    sub1 = check ? 'Nghỉ cả ngày ' : 'Nghỉ cả ca ngày ',
                    sub2 = check ? 'Nghỉ buổi sáng ngày ' : 'Nghỉ nửa ca đầu ngày ',
                    sub3 = check ? 'Nghỉ buổi chiều ngày ' : 'Nghỉ nửa ca sau ngày '
                if (formatDate(event.start) == formatDate(event.end) || !event.end) {
                    if (event.from_type == morning && event.to_type == afternoon) content = sub1 + startR
                    else if ((event.from_type == morning && event.to_type == morning)) content = sub2 + startR
                    else content = sub3 + startR

                } else {
                    let startPopup = event.from_type == morning ? sub2 : sub3
                    let endPopup = event.to_type == morning ? sub2 : sub3
                    endPopup = endPopup.replace('Nghỉ','');
                    content = startPopup + startR + ' đến ' + endPopup + event.end.format('DD/MM/YYYY')
                }
                return content
            }

            var workSchedules = {!! json_encode($workSchedules) !!};
            const typeShifts = {!! \App\Define\Shift::jsonAllShift() !!};

            $('#calendar').fullCalendar({
                locale: 'vi',
                // themeSystem: 'bootstrap4',
                todayHighlight: false,
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month, basicWeek',
                },
                firstDay: 1,
                // showNonCurrentDates: false,
                defaultView: 'month',
                height: 'auto',
                editable: true,
                droppable: true,
                displayEventTime: false,
                eventSources: [
                    eventStaffs,
                    dayOffDepartments
                ],
                viewRender: function (view, element) {
                    $('span.month').html(view.intervalStart.format('M') + ' : ')
                    $.ajax({
                        url: '{!! route('admin.schedules.getCountDayOff') !!}',
                        type: 'POST',
                        data: {month: view.intervalStart.format('M'), year: view.intervalStart.format('Y')},
                        headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                        success: function (res) {
                            $('div span.countPending').html(res.countPending)
                            $('div span.countAccept').html(res.countAccept)
                        },
                        error: function (err) {
                            let error = $.parseJSON(err.responseText);
                            toastr.warning(error.message, "{!! trans('system.have_error') !!}")
                        }
                    })
                },
                drop: function (date, jsEvent, ui, resourceId) {
                    let code = $(this).attr('data-code')
                    oldEndClick = ''
                    oldStartClick = ''
                    if (formatDate(moment()) > formatDate(date) && code != DAY_OFF_BABE) {
                        $("#modal-event").modal("hide");
                        toastr.warning('{!! trans('schedules.past_date') !!}', '{!! trans('system.info') !!}')
                        return false
                    }
                    let originalEventObject = $(this).data('eventObject')
                    let copiedEventObject = $.extend({}, originalEventObject)
                    copiedEventObject.start = date
                    copiedEventObject.backgroundColor = $(this).css('background-color')
                    showModal(copiedEventObject)
                },
                eventRender: function (event, element, view) {
                    let content = titleContent(event, typeDept)
                    let totalNo = event.total ? `( ${event.total} ngày)` : ''
                    let titlePopup = event.status ? ' - Đã duyệt' : ' - Chưa duyệt'
                    titlePopup = event.total ? titlePopup : ''
                    element.popover({
                        title: event.title + totalNo + titlePopup,
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
                    let dateStart = $.fullCalendar.formatDate(event.start, 'YYYY-MM-DD');
                    dateEnd = $.fullCalendar.formatDate(dateEnd, 'YYYY-MM-DD');
                    let tagStart = $('td.fc-day[data-date=' + dateStart + ']');
                    let tagEnd = $('td.fc-day[data-date=' + dateEnd + ']');
                    if (view.name == 'month' && !(event.type && typeDept != OFFICE_TIME)) { // view.name == 'month' && typeDept == OFFICE_TIME
                        let morningClass = event.type ? 'morning-dept' : 'morning'
                        let afternoonClass = event.type ? 'afternoon-dept' : 'afternoon'
                        if (event.from_type == morning) {
                            tagStart.append('<span class="' + morningClass + '">◤</span>')
                        } else if (event.from_type == afternoon) {
                            tagStart.append('<span class="' + afternoonClass + '">◢</span>')
                        }
                        if (event.to_type == morning) {
                            tagEnd.append('<span class="' + morningClass + '">◤</span>')
                        } else if (event.to_type == afternoon) {
                            tagEnd.append('<span class="' + afternoonClass + '">◢</span>')
                        }
                    }
                    if (event.from_type && event.user_id) {
                        if (event.status) {
                            element.find(".fc-content").prepend('(<span style="font-weight: 700;" class="fa fa-check"></span>)')
                        }
                        element.find(".fc-content").prepend("<span class='closeon fa fa-times'>&#xe5cd;</span>");

                        element.find(".closeon").on("click", function (e) {
                            e.stopPropagation()
                            if (confirm("{!! trans('system.confirm_delete') !!}")) {
                                let url = "{!! route('admin.schedules.destroy', [-1]) !!}"
                                url = url.replace('-1', event.id)
                                let startClass = event.from_type == morning ? 'morning' : 'afternoon'
                                let endClass = event.to_type == morning ? 'morning' : 'afternoon'
                                $.ajax({
                                    url: url,
                                    type: 'POST',
                                    data: {_method: 'DELETE'},
                                    headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                                    success: function (res) {
                                        $("#calendar").fullCalendar("removeEvents", event._id);
                                        $('td.fc-day[data-date=' + dateStart + ']').find('span.' + startClass).remove()
                                        $('td.fc-day[data-date=' + dateEnd + ']').find('span.' + endClass).remove()
                                        toastr.success(res.message, "{!! trans('system.info') !!}")
                                        if (!['O'].includes(event.code)) {
                                            $('div span.countPending').html(parseInt($('div span.countPending').text()) - 1)
                                        }
                                        if (['S', 'D', 'L'].includes(event.code)) {
                                            $('div span.dayOffCanUse').html(parseInt($('div span.dayOffCanUse').text()) + parseFloat(event.total))
                                        }
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
                    }
                },
                eventDrop: function (event, delta, revertFunc) {
                    if (!event.user_id) {
                        if (event.type == 1) {
                            toastr.warning('{!! trans('schedules.err_edit_day_off_dept') !!}', '{!! trans('system.info') !!}')
                        } else {
                            toastr.warning('{!! trans('schedules.err_edit_holiday') !!}', '{!! trans('system.info') !!}')
                        }
                        start = moment(event.start).format('YYYY-MM-DD')
                        end = moment(event.end).format('YYYY-MM-DD')
                        $('td.fc-day[data-date=' + start + ']').find('span.morning-dept').remove()
                        $('td.fc-day[data-date=' + start + ']').find('span.afternoon-dept').remove()
                        $('td.fc-day[data-date=' + end + ']').find('span.morning-dept').remove()
                        $('td.fc-day[data-date=' + end + ']').find('span.afternoon-dept').remove()
                        revertFunc()
                        return false
                    }
                    if (!delta._days) return false
                    {{--if (moment() > event.start) {--}}
                    {{--    toastr.warning('{!! trans('schedules.past_date') !!}', '{!! trans('system.info') !!}')--}}
                    {{--    removeIcon(event.start, event.end)--}}
                    {{--    revertFunc()--}}
                    {{--    return false--}}
                    {{--}--}}
                    if (typeDept != OFFICE_TIME && !checkDateHasShift(event.start, event.end, workSchedules, event.code) && {!! json_encode(!\Auth::user()->hasRole('TP') && !\Auth::user()->hasRole('TPNS')) !!}) {
                        toastr.warning('{!! trans('schedules.error_no_work_schedule') !!}', '{!! trans('system.info') !!}')
                        revertFunc()
                        return false
                    }
                    if (isDateHasEvent(event.start, event.end, event._id, event.from_type, event.to_type)) {
                        toastr.warning('{!! trans('schedules.same_day_off') !!}', '{!! trans('system.info') !!}')
                        removeIcon(event.start, event.end)
                        revertFunc()
                        return false
                    }
                    let oldStart = moment(event.start).subtract(delta._days, 'd').format('YYYY-MM-DD')
                    let oldEnd = moment(event.end).subtract(delta._days, 'd').format('YYYY-MM-DD')
                    if (oldStart < formatDate(moment())) {
                        toastr.warning('{!! trans('schedules.error_past_day_off') !!}', '{!! trans('system.info') !!}')
                        removeIcon(event.start, event.end)
                        revertFunc()
                        return false
                    }
                    let dateStart = event.start.format()
                    let dateEnd = event.end.format('YYYY-MM-DD')
                    if (confirm("{!! trans('system.update_confirm') !!}")) {
                        let eventId = event.id
                        let url = "{!! route('admin.schedules.update', [-1]) !!}"
                        url = url.replace('-1', eventId)
                        $.ajax({
                            url: url,
                            data: {_method: 'PUT', duration: delta._days},
                            type: 'POST',
                            headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                            success: function (res) {
                                if (res.data) {
                                    res.data.end = res.data.end + "T23:59:00"
                                    removeIcon(oldStart, oldEnd)
                                    $('#calendar').fullCalendar('updateEvent', event);
                                    // $('#calendar').fullCalendar('removeEvents', event._id);
                                    // $('#calendar').fullCalendar('renderEvent', res.data, true);
                                    toastr.success(res.message, "{!! trans('system.info') !!}")
                                } else {
                                    toastr.error(res.message, "{!! trans('system.have_error') !!}")
                                    removeIcon(dateStart, dateEnd)
                                    revertFunc()
                                }
                            },
                            error: function (err) {
                                let error = $.parseJSON(err.responseText);
                                removeIcon(dateStart, dateEnd)
                                revertFunc()
                                toastr.warning(error.message, "{!! trans('system.have_error') !!}")
                            }
                        })
                    } else {
                        removeIcon(dateStart, dateEnd)
                        revertFunc()
                        return false
                    }
                },
                eventClick: function (event, element) {
                    if (!event.user_id) {
                        return false
                    }
                    if (event.status == 1) {
                        toastr.warning('{!! trans('schedules.error_handle_day_off') !!}')
                        return false
                    }
                    {{--if (formatDate(moment()) > formatDate(event.start)) {--}}
                    {{--    $("#modal-event").modal("hide");--}}
                    {{--    toastr.warning('{!! trans('schedules.error_past_day_off') !!}', '{!! trans('system.info') !!}')--}}
                    {{--    return false--}}
                    {{--}--}}
                    $('#event-click-total').val(event.total)
                    oldStartClick = event.start.format('YYYY-MM-DD')
                    oldEndClick = event.end.format('YYYY-MM-DD')
                    showModal(event)
                },
                dayClick: function (date, jsEvent, view) {
                    showModal(null, date)
                },
                dayRender: function (date, cell) {
                    if (typeDept != OFFICE_TIME && !$.isEmptyObject(workSchedules)) {
                        $.each(workSchedules, function (index, value) {
                            if (moment(formatDate(date)).isBetween(formatDate(value.start_date), formatDate(value.end_date), undefined, '[]')) {
                                // cell.addClass('setBgFutureWeek');
                                // let isHalf = value.is_half == 1 ? "có nửa ca" : "";
                                cell.append('<span class="shift-title btn btn-flat btn-info btn-xs">' + typeShifts[value.shift] + '</span>')
                            }
                        })
                    }
                }
            });
            $("body").on('click', '.shift-title', function (e) {
                e.stopPropagation()
                return false
            })

            function checkDateInRangeDate(date, rangeDateStart, rangeDateEnd) {
                return moment(formatDate(date)).isBetween(formatDate(rangeDateStart), formatDate(rangeDateEnd), undefined, '[]')
            }

            function checkDateHasShift(start, end, workSchedules, code = null) {
                if ($.inArray(code, DAY_OFF_FREE) !== -1) return true
                if (!workSchedules) return false
                start = formatDate(start)
                end = formatDate(end)
                let check = false
                $.each(workSchedules, function (index, value) {
                    if (start >= formatDate(value.start_date) && end <= formatDate(value.end_date)) {
                        check = true
                        return false
                    }
                })
                return check
            }

            // $('.fc-content').hover(function () {
            //     let color = $(this).parent().css('background-color')
            //     $('.popover-title').css('background', color);
            // })
            var oldStartClick = ''
            var oldEndClick = ''
            $("#save-event").on("click", function () {
                let title = $("#day-off").val();
                let start = $("#start-at").val().split("/").reverse().join("-");
                let end = $("#end-at").val().split("/").reverse().join("-");
                let toType = $('#to-type').find("option:selected").val();
                let fromType = $('#from-type').find("option:selected").val();
                let diff = (moment(end).diff(moment(start), 'days') + 1) * 2;
                let total = $('span.total').text()
                let code = $('.titleSelect').val()
                let cellId = $('#cell-id').val()
                let eventId = $('#event-id').val()
                // let totalEventClick = $('#event-click-total').val() ?? 0;
                // let halfShift = !!$('.half-shift').is(':checked') ? 1 : 0
                if (!start) {
                    toastr.warning('{!! trans('schedules.required_start') !!}', "{!! trans('system.info') !!}")
                    return false
                }
                if (!end) {
                    toastr.warning('{!! trans('schedules.required_end') !!}', "{!! trans('system.info') !!}")
                    return false
                }
                if (diff < 2) {
                    toastr.warning("{!! trans('schedules.error_end_date') !!}", "{!! trans('system.info') !!}")
                    return false
                }
                {{--if (formatDate(moment()) > formatDate(start) && code != DAY_OFF_BABE) {--}}
                {{--    toastr.warning('{!! trans('schedules.past_date') !!}', '{!! trans('system.info') !!}')--}}
                {{--    return false--}}
                {{--}--}}
                {{--if (halfShift && diff > 2) {--}}
                        {{--    toastr.warning('{!! trans('schedules.err_half_shift') !!}', '{!! trans('system.info') !!}')--}}
                        {{--    return false--}}
                        {{--}--}}
                        {{--if (total > {!! \App\StaffDayOff::getRestLeave(\Auth::id()) !!} && code != 'C' && code != 'O') {--}}
                        {{--    console.log(1)--}}
                        {{--    toastr.warning("{!! trans('schedules.error_range_day_off') !!}", "{!! trans('system.info') !!}")--}}
                        {{--    return false--}}
                        {{--}--}}
                if (total > SICK_LIMIT && code == DAY_OFF_SICK) {
                    toastr.warning("{!! trans('schedules.error_range_day_off') !!}", "{!! trans('system.info') !!}")
                    return false
                }
                if (total > FUNERAL_LIMIT && code == DAY_OFF_FUNERAL) {
                    toastr.warning("{!! trans('schedules.error_range_day_off') !!}", "{!! trans('system.info') !!}")
                    return false
                }
                if (total > WEDDING_LIMIT && code == DAY_OFF_WEDDING) {
                    toastr.warning("{!! trans('schedules.error_range_day_off') !!}", "{!! trans('system.info') !!}")
                    return false
                }
                if (isDateHasEvent(start, end, cellId, fromType, toType)) {
                    toastr.warning('{!! trans('schedules.same_day_off') !!}', '{!! trans('system.info') !!}')
                    return false
                }
                if (typeDept != OFFICE_TIME && !checkDateHasShift(start, end, workSchedules, code) && {!! json_encode(!\Auth::user()->hasRole('TP') && !\Auth::user()->hasRole('TPNS')) !!}) {
                    toastr.warning('{!! trans('schedules.error_no_work_schedule') !!}', '{!! trans('system.info') !!}')
                    return false
                }
                let reason = $('.reason').val()
                let staffId = $('#staff-id').val()
                let eventData = {
                    id: eventId,
                    title: title,
                    start: start,
                    end: end,
                    code: code,
                    total: total,
                    staff_id: staffId,
                    reason: reason,
                    from_type: fromType,
                    to_type: toType,
                    half_shift: typeDept == OFFICE_TIME ? null : 1,
                };
                let url = "{!! route('admin.schedules.update', [-1]) !!}"
                let method = ''
                url = eventId ? url.replace('-1', eventId) : "{!! route('admin.schedules.store') !!}"
                method = eventId ? 'PUT' : method

                $.ajax({
                    url: url,
                    data: {_method: method, event: eventData},
                    type: 'POST',
                    headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                    success: function (res) {
                        if (res.data) {
                            if (oldStartClick) {
                                removeIcon(oldStartClick, oldEndClick)
                            }
                            res.data.end = res.data.end + "T23:59:00"
                            // $('#calendar').full Calendar('updateEvent', res.data);
                            $('#calendar').fullCalendar('removeEvents', cellId);
                            $('#calendar').fullCalendar('renderEvent', res.data, true);
                            toastr.success(res.message, "{!! trans('system.info') !!}")
                            if (!['O'].includes(res.data.code) && !eventId) {
                                $('div span.countPending').html(parseInt($('div span.countPending').text()) + 1)
                            }
                            if (['S', 'D', 'L'].includes(res.data.code)) {
                                $('div span.dayOffCanUse').html(parseInt($('div span.dayOffCanUse').text()) - parseFloat(res.data.total))
                            }
                            $('#event-click-total').val(0)
                            $("#modal-event")
                                .find("input")
                                .val("");
                            $("#modal-event")
                                .find("select")
                                .val('1').change();
                            $('#event-click-total').val(0)
                            $('.half-shift').iCheck('uncheck')
                            $('.half-shift').iCheck('update')
                            $("#modal-event").modal("hide");
                        }
                    },
                    error: function (err) {
                        let error = $.parseJSON(err.responseText);
                        toastr.warning(error.message, "{!! trans('system.have_error') !!}")
                    }
                })

            });

            $('.datepicker').datepicker({
                format: 'dd/mm/yyyy',
                autoclose: true,
                todayHighlight: true,
                language: "vi",
            });
        });
        // }(window.jQuery);
    </script>
@stop
