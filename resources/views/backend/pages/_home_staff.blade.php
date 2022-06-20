<div class="row">
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <a href="{!! route('admin.schedules.index') !!}">
                <span class="info-box-icon bg-yellow"><i class="fas fa-sync"></i></span>
            </a>
            <div class="info-box-content">
                <span class="info-box-text">Nghỉ phép chờ duyệt</span>
                <span class="info-box-number">{!! $countLeavePerStaff[0] !!}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-green"><i class="fas fa-check-square"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Nghỉ phép dã duyệt</span>
                <span class="info-box-number">{!! $countLeavePerStaff[1] !!}</span>
            </div>
        </div>
    </div>
    <div class="clearfix visible-sm-block"></div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <a href="{!! route('admin.schedules.index') !!}">
            <span class="info-box-icon bg-blue"><i class="far fa-calendar-alt"></i></span>
            </a>
            <div class="info-box-content">
                <span class="info-box-text">Lịch cá nhân</span>
                {{--                    <span class="info-box-number">15</span>--}}
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-red"><i class="glyphicon glyphicon-signal"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Bảng công</span>
                {{--                    <span class="info-box-number">2</span>--}}
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Lịch cá nhân</h3>
            </div>
            <div class="box-body">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Biểu đồ KPI</h3>
            </div>
            <div class="box-body">
                <div id="myChartKpi"></div>
            </div>
        </div>

    </div>
</div>

<script>
    !(function ($) {
        $(function () {
            $('#calendar').fullCalendar({
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
            $('#calendar').fullCalendar('option', 'height', 400);
            let noDataMess = "Chưa có dữ liệu."
            let dataVariation = {!! json_encode(\App\Target::getKpiUserPerMonth(\Auth::id()))  !!};
            if (dataVariation.length > 0 ) {
                Highcharts.chart('myChartKpi', {
                    chart: {
                        type: 'line'
                    },
                    title: {
                        text: ''
                    },
                    lang: {
                        noData: noDataMess
                    },
                    xAxis: {
                        categories: moment.months()
                    },
                    yAxis: {
                        title: {
                            text: 'KPI'
                        }
                    },
                    plotOptions: {
                        line: {
                            dataLabels: {
                                enabled: true
                            },
                            enableMouseTracking: false
                        }
                    },
                    series: [{
                        name: 'Kpi',
                        data: dataVariation
                    }],
                    credits: {
                        enabled: false
                    },
                });
            }
        });
    })(window.jQuery);
</script>