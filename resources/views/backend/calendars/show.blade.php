@extends('backend.master')
@section('title')
    {!! trans('system.action.show') !!} {!! trans('calendars.label') !!}
@stop
@section('head')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.css"/>
@stop
@section('content')
    <section class="content-header">
        <h1>
            <small>{!! trans('system.action.show') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.calendars.index') !!}">{!! trans('calendars.label') !!}</a></li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-3">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h4 class="box-title">Ngày nghỉ trong năm</h4>
                    </div>
                    <div class="box-body">
                        <!-- the events -->
                        <div id="external-events">
                            <div class="external-event bg-green ui-draggable ui-draggable-handle" style="position: relative;">Nghỉ phép</div>
                            <div class="external-event bg-yellow ui-draggable ui-draggable-handle" style="position: relative;">Nghỉ ốm</div>
                            <div class="external-event bg-aqua ui-draggable ui-draggable-handle" style="position: relative;">Nghỉ hiếu,hỉ</div>
                            <div class="external-event bg-light-blue ui-draggable ui-draggable-handle" style="position: relative;">Nghỉ sinh</div>
                            <div class="external-event bg-red ui-draggable ui-draggable-handle" style="position: relative;">Nghỉ lễ</div>
                            <div class="external-event bg-red ui-draggable ui-draggable-handle" style="position: relative;">Lịch nghỉ công ty</div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.col -->
            <div class="col-md-9">
                <div class="box box-primary">
                    <div class="box-body no-padding">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
@section('footer')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"
            integrity="sha256-4iQZ6BVL4qNKlQ27TExEhBN1HFPvAvAMbFavKKosSWQ=" crossorigin="anonymous"></script>
    <script>
        $(function () {
            function init_events(ele) {
                ele.each(function () {
                    let eventObject = {
                        title: $.trim($(this).text())
                    }
                    $(this).data('eventObject', eventObject)
                    $(this).draggable({
                        zIndex        : 1070,
                        revert        : true, // will cause the event to go back to its
                        revertDuration: 0  //  original position after the drag
                    })
                })
            }
            init_events($('#external-events div.external-event'))
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                editable: true,
                droppable : true,
                // eventLimit: true,
                // events: [
                //     {
                //         // title          : 'All Day Event',
                //         // start          : new Date(),
                //         // backgroundColor: '#f56954', //red
                //         // borderColor    : '#f56954' //red
                //     },
                // ],
                drop: function (date, allDay) {
                    console.log('date', allDay)
                    let originalEventObject = $(this).data('eventObject')
                    let copiedEventObject = $.extend({}, originalEventObject)
                    copiedEventObject.start           = date
                    copiedEventObject.allDay          = allDay
                    copiedEventObject.backgroundColor = $(this).css('background-color')
                    copiedEventObject.borderColor     = $(this).css('border-color')
                    $('#calendar').fullCalendar('renderEvent', copiedEventObject, true)
                },
                eventClick: function(event, element) {
                    console.log(event)
                    // $('#calendar').fullCalendar('updateEvent', event);
                }
            });
            // var currColor = '#3c8dbc'
            var colorChooser = $('#color-chooser-btn')
            $('#color-chooser > li > a').click(function (e) {
                e.preventDefault()
                currColor = $(this).css('color')
                $('#add-new-event').css({ 'background-color': currColor, 'border-color': currColor })
            })
            $('#add-new-event').click(function (e) {
                e.preventDefault()
                let val = $('#new-event').val()
                if (val.length == 0) {
                    return
                }
                let event = $('<div />')
                event.css({
                    'background-color': currColor,
                    'border-color'    : currColor,
                    // 'color'           : '#fff'
                }).addClass('external-event')
                event.html(val)
                $('#external-events').prepend(event)
                init_events(event)
                $('#new-event').val('')
            })
        });
    </script>
@stop