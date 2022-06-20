<div class="row">
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <a href="{!! route('admin.staffs.index') !!}">
            <span class="info-box-icon bg-aqua"><i class="fa fa-user"></i></span>
            </a>
            <div class="info-box-content">
                <span class="info-box-number">Nhân viên</span>
                <span class="info-box-number">{!! $countStaffsFollowPer !!}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <a href="{!! route('admin.manager.leave.index') !!}">
            <span class="info-box-icon bg-green"><i class="fas fa-file-signature"></i></span>
            </a>
            <div class="info-box-content" >
                <span class="info-box-number">Nghỉ phép</span>
            </div>
            <div >
                <span style="margin-left: 8px">Chờ duyệt: </span>
                <span>{!! $countLeavePending[0] !!}</span>
            </div>
            <div >
                <span style="margin-left: 8px">Đã duyệt: </span>
                <span>{!! $countLeavePending[1] !!}</span>
            </div>
        </div>
    </div>
    <div class="clearfix visible-sm-block"></div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <a href="{!! route('admin.departments.index') !!}">
            <span class="info-box-icon bg-blue"><i class="far fa-calendar-alt"></i></span>
            </a>

            <div class="info-box-content">
                <span class="info-box-number">Quản lý phòng ban</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <a href="{!! route('admin.timekeeping.index') !!}">
            <span class="info-box-icon bg-red"><i class="fas fa-money-check-alt"></i></span>
            </a>
            <div class="info-box-content">
                <span class="info-box-number">Quản lý bảng công</span>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="box box-primary" style="height: 600px">
            <div class="box-header with-border" >
                <h3 class="box-title" style="font-weight: 600;">Lịch cá nhân</h3>
            </div>
            <div class="box-body">
                <div id="calendar1"></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="box box-primary" style="height: 600px">
            <div class="box-header with-border" >
                <h3 class="box-title" style="font-weight: 600;">Danh sách đơn nghỉ tạo hôm nay</h3>
                <span style="float: right"><a href="{!! route('admin.manager.leave.index') !!}">Chi tiết</a></span>
            </div>
            <div class="box-body">
                <table class='table table-striped table-bordered tree' id="list-leave">
                    <thead>
                    <tr>
                        <th style="text-align: center; vertical-align: middle;">{!! trans('system.no.') !!}</th>
                        <th style="text-align: center; vertical-align: middle;"> {!! trans('staffs.staff_name') !!} </th>
                        <th style="text-align: center; vertical-align: middle;">{!! trans('staff_titles.type') !!}</th>
                        <th style="text-align: center; vertical-align: middle;"> {!! trans('staff_titles.start') !!} </th>
                        <th style="text-align: center; vertical-align: middle;"> {!! trans('staff_titles.end') !!} </th>
                        <th style="text-align: center; vertical-align: middle;"> {!! trans('staff_titles.day_off') !!} </th>
                    </tr>
                    </thead>
                    <tbody class="borderless">
                    @if(count($nearLeaves))
                        @foreach($nearLeaves as $item)
                            @php
                                $checktype=['S','W','D','L'];
                                    $today=date('d-m-Y');
                                    $start=date('d-m-Y', strtotime($item->start));
                                    $date=(strtotime($start)-strtotime($today));
                            @endphp
                            <tr>
                                <td style="text-align: center; vertical-align: middle;">{!! ++$i !!}</td>
                                <td  style="text-align: center; vertical-align: middle;">{!! $item->user->fullname !!}</td>
                                <td style="text-align: center; vertical-align: middle;">{!! $item->title !!}</td>
                                <td style="text-align: center; vertical-align: middle;">
                                    {!! $item->start !!}<br>
                                    (<span style="color: #D29E3B">{!! trans('schedules.time-offs.' . $item->from_type) !!}</span>)
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    {!! $item->end !!}<br>
                                    (<span style="color: #D29E3B">{!! trans('schedules.time-offs.' . $item->to_type) !!}</span>)
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    {!! $item->total !!}
                                </td>
                            </tr>
                        @endforeach
                    @else
                    @endif
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script>
    !(function ($) {
        $(function () {
            $('#calendar1').fullCalendar({
                locale: 'vi',
                // themeSystem: 'bootstrap4',
                header: {
                    left: 'prev',
                    center: 'title',
                    right: 'next',
                },
                firstDay: 1,
                defaultView: 'month',
                events: [
                ],

                displayEventTime: false,
                eventSources: [
                    {!! json_encode($dayOffDepartments) !!},
                    {!! json_encode($events) !!}
                ],
            });

            var table = $('#list-leave').DataTable({
                orderCellsTop: true,
                fixedHeader: true,
                pageLength: 7,
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
                // "fnDrawCallback": function(oSettings) {
                //     if ($('#list-leave tr').length < 7) {
                //         $('.dataTables_paginate').hide();
                //     }
                // }
            });

            // var ctx = document.getElementById('myChartKpi');
            // var myChartKpi = new Chart(ctx, {
            //     type: 'bar',
            //     data: {
            //         labels:  moment.months(),
            //         datasets: [{
            //             label: 'KPI',
            //             data: [100, 90, 100, 100],
            //             backgroundColor: [
            //                 'rgba(255, 99, 132, 1)',
            //                 'rgba(54, 162, 235, 1)',
            //                 'rgba(255, 206, 86, 1)',
            //                 'rgba(75, 192, 192, 1)',
            //                 'rgba(255, 159, 64, 1)'
            //             ],
            //             borderColor: [
            //                 'rgba(255, 99, 132, 1)',
            //                 'rgba(54, 162, 235, 1)',
            //                 'rgba(255, 206, 86, 1)',
            //                 'rgba(75, 192, 192, 1)',
            //                 'rgba(255, 159, 64, 1)'
            //             ],
            //             borderWidth: 1
            //         }]
            //     },
            //     options: {
            //         maintainAspectRatio: false,
            //         plugins: {
            //             legend: {
            //                 display: false,
            //             }
            //         },
            //         indexAxis: 'y',
            //     }
            // });
        });
    })(window.jQuery);
</script>