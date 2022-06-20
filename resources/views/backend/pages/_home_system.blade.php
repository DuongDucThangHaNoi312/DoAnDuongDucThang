<div class="row">
    <div class="col-md-4 col-sm-6 col-xs-12">
        <div class="info-box">
            <a href="{!! route('admin.companies.index') !!}">
                <span class="info-box-icon bg-yellow"><i class="fas fa-landmark"></i></span>
            </a>
            <div class="info-box-content">
                <span class="info-box-text">Công ty</span>
                <span class="info-box-number">{!! \App\Models\Company::countActiveCompanies() !!}</span>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6 col-xs-12">
        <div class="info-box">
            <a href="{!! route('admin.staffs.index') !!}">
                <span class="info-box-icon bg-green"><i class="fas fa-user"></i></span>
            </a>
            <div class="info-box-content">
                <span class="info-box-text">Tổng nhân viên</span>
                <span class="info-box-number">{!! $countStaffs !!}</span>
            </div>
        </div>
    </div>
    <div class="clearfix visible-sm-block"></div>
    <div class="col-md-4 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-blue"><i class="far fa-building"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">Phòng ban</span>
                <span class="info-box-number">{!! \App\Models\Department::countActiveDepts() !!}</span>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title" style="font-weight: 600;">Chi phí</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div id="myChart" style="width: 100%; "></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title" style="font-weight: 600;">Phân loại hợp đồng</h3>
            </div>
            <div class="box-body text-center" style=" width: 100%;">
                <div id="no-data" style="display: none">Dữ liệu trống</div>
                <div id="myChartPie"></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title" style="font-weight: 600;">Biến động nhân sự</h3>
            </div>
            <div class="box-body">
                <div id="myChartLine"></div>
            </div>
        </div>
    </div>
</div>

<script>
    !(function ($) {
        $(function () {
            var arrSeniority = {!! json_encode($arrSeniority) !!};
            var countStaffs = {!! $countStaffs !!};
            let noDataMess = "Chưa có dữ liệu."
            let noDataStyle = {
                style: {
                    fontWeight: 'bold',
                    fontSize: '15px'
                }
            }
            let salaryCompanies = {!! json_encode(\App\Models\Payroll::getTotalSalaryByMonth()) !!};
            if (salaryCompanies.length > 0) {
                let companies = Object.values(salaryCompanies[1])
                let salaries = Object.values(salaryCompanies[0])
                let data = [];
                let checkZero = true
                $.each(companies, function (index, value) {
                    if (!Object.values(salaries[index]).every(item => item === 0)) {
                        checkZero = false
                        return false
                    }
                });

                $.each(companies, function (index, value) {
                    data.push({
                        name: companies[index],
                        data: Object.values(salaries[index])
                    });
                });

                let dataSalary = []
                let labelCompanies = ['SCM', 'SCM HD', 'SCM HP', 'JCNS', 'PAC HAN', 'LOG HAN']
                let bgColors = ["#ccc", 'red', 'orange', '#FF6320', 'blue', '#008080']

                Highcharts.chart('myChart', {
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: ''
                    },
                    lang: {
                        noData: noDataMess
                    },
                    noData: noDataStyle,
                    xAxis: {
                        categories: moment.months()
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Chi phí (triệu)'
                        },
                        stackLabels: {
                            enabled: true,
                            style: {
                                fontWeight: 'bold',
                                color: ( // theme
                                    Highcharts.defaultOptions.title.style &&
                                    Highcharts.defaultOptions.title.style.color
                                ) || 'gray'
                            }
                        }
                    },
                    legend: {
                        align: 'center',
                        verticalAlign: 'bottom',
                        backgroundColor:
                            Highcharts.defaultOptions.legend.backgroundColor || 'white',
                        borderColor: '#CCC',
                        shadow: false
                    },
                    tooltip: {
                        headerFormat: '<b>{point.x}</b><br/>',
                        pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}',
                    },
                    plotOptions: {
                        column: {
                            stacking: 'normal',
                            dataLabels: {
                                enabled: true
                            }
                        }
                    },
                    series: data,
                    credits: {
                        enabled: false
                    },
                });
            }

            // Chart Staff Variation
            let dataVariation = {!! json_encode(\App\Models\Contract::getStaffVariation()) !!};
            if (dataVariation.length > 0 ) {
                Highcharts.chart('myChartLine', {
                    chart: {
                        type: 'line'
                    },
                    title: {
                        text: ''
                    },
                    lang: {
                        noData: noDataMess
                    },
                    noData: noDataStyle,
                    xAxis: {
                        categories: moment.months()
                    },
                    yAxis: {
                        title: {
                            text: 'Nhân viên'
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
                        name: 'Nghỉ việc',
                        data: dataVariation[0]
                    }, {
                        name: 'Nhân viên mới',
                        data: dataVariation[1]
                    }],
                    credits: {
                        enabled: false
                    },
                });
            }

            // <- Build the chart Pie ->
            let dataTypeContract = {!! json_encode(\App\Models\Contract::countTypeContracts()) !!};
            // dataTypeContract = Object.values(dataTypeContract).every(item => item === 0) ? [] : dataTypeContract;
            if (dataTypeContract.length > 0) {
                Highcharts.chart('myChartPie', {
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: 'pie'
                    },
                    lang: {
                        noData: noDataMess
                    },
                    noData: noDataStyle,
                    title: {
                        text: ''
                    },
                    tooltip: {
                        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                    },
                    accessibility: {
                        point: {
                            valueSuffix: '%'
                        }
                    },
                    xAxis: {
                        categories: ["Thử việc", "Sáu tháng", "Một năm", "Ba năm", 'Không xác định']
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            showInLegend: true,
                            colors: ['#ff6384', '#36a2eb', 'blue', '#ccc', '#ff9f40'],
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b><br>{point.percentage:.1f} %',
                                distance: -50,
                                filter: {
                                    property: 'percentage',
                                    operator: '>',
                                    value: 4
                                }
                            }
                        }
                    },
                    series: [{
                        name: 'chiếm',
                        data: [
                            {name: 'Thử việc', y: dataTypeContract[0]},
                            {name: 'Sáu tháng', y: dataTypeContract[1]},
                            {name: 'Một năm', y: dataTypeContract[2]},
                            {name: 'Ba năm', y: dataTypeContract[3]},
                            {name: 'Không xác định', y: dataTypeContract[4]},
                        ],
                    }],
                    credits: {
                        enabled: false
                    },
                });
            }
        });
    })(window.jQuery);
</script>