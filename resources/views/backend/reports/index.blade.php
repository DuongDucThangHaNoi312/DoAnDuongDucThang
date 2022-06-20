@extends('backend.master')
@section('title')
{!! trans('reports.label') !!}
@stop
@section('head')
<link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}" />
<link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/daterangepicker/daterangepicker.css') !!}" />
<style type="text/css">
    .uppercase {
        text-transform: uppercase;
    }
</style>
@stop
@section('content')
<section class="content-header">
    <h1>
        {!! trans('reports.label') !!}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
        <li><a href="{!! route('admin.reports.index') !!}">{!! trans('reports.label') !!}</a></li>
    </ol>
</section>
<section class="content overlay">
    <div class="box box-default">
        <div class="box-body">
            <div class="row">
            	<div class="col-md-4">
            		<select class="form-control select2" name="type">
            			<option value="NONE">{!!  trans('reports.types.label') !!}</option>
            			@foreach ($types as $key => $name)
                            <option value="{!! $key !!}" @if(Request::input('type') == $key) selected @endif >{!! $name !!}</option>
                        @endforeach
            		</select>
            	</div>
                <div class="col-md-4">
                    {{-- <div class="btn-group">
                        <button type="submit" class="btn btn-info btn-flat" id="report">
                            <span class="glyphicon glyphicon-flash"></span>&nbsp; Lập báo cáo
                        </button>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-success btn-flat" onclick="window.open('{!! route('admin.reports.export', ['type' => 'excel']) !!}')">
                            <span class="far fa-file-excel fa-fw"></span>&nbsp; Xuất excel
                        </button>
                    </div> --}}
                    <div class="btn-group">
                        <a type="button" class="btn btn-primary btn-flat" id="report"><span class="glyphicon glyphicon-flash"></span> Lập báo cáo</a>
                        <a type="button" class="btn bg-orange btn-flat dropdown-toggle" data-toggle="dropdown">
                            <i class="fas fa-cloud-download-alt"></i>
                        </a>
                        <ul class="dropdown-menu" role="menu">
                            <li class="text-success"><a href="{!! route('admin.reports.export', ['type' => 'excel']) !!}" target="_blank" class="text-success"><i class="far fa-file-excel text-success"></i> Xuất excel</a></li>
                            {{-- <li class="text-danger"><a href="onclick="window.open('{!! route('admin.reports.export', ['type' => 'pdf']) !!}')" class="text-danger"><i class="far fa-file-pdf text-danger"></i> Xuất pdf</a></li> --}}
                        </ul>
                    </div>
                </div>
            </div>
            <hr/>
            <div class="row" id="type"></div>
        </div>
    </div>
    <div class="box">
        <div class="box-body no-padding">
            <div id="result" class="table-responsive">
                <h4 class="text-center" style="margin-top: 30px; margin-bottom: 30px;">Bạn chưa chọn loại báo cáo nào...</h4>
            </div>
        </div>
    </div>
</section>
@stop
@section('footer')
<script src="{!! asset('assets/backend/plugins/select2/select2.full.min.js') !!}"></script>
<script src="{!! asset('assets/backend/plugins/daterangepicker/moment.min.js') !!}"></script>
<script src="{!! asset('assets/backend/plugins/daterangepicker/daterangepicker.js') !!}"></script>
<script src="{!! asset('assets/backend/plugins/moment/locale/vi.js') !!}"></script>
<script>
    const _NoSelectedType = '{!! trans('reports.no_selected_type') !!}'
    function init() {
        $(".select2").select2({ width: '100%' });
        $('.date_range').daterangepicker({
            autoUpdateInput: false,
            "locale": {
                "format": "DD/MM/YYYY HH:mm",
                "separator": " - ",
                "applyLabel": "Áp dụng",
                "cancelLabel": "Huỷ bỏ",
                "fromLabel": "Từ ngày",
                "toLabel": "Tới ngày",
                "customRangeLabel": "Tuỳ chọn",
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
               'Tháng trước': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            },
            "alwaysShowCalendars": true,
            maxDate: moment(),
            minDate: moment().subtract(1, "years"),
        }, function(start, end, label) {
            $('.date_range').val(start.format('DD/MM/YYYY HH:mm') + " - " + end.format('DD/MM/YYYY HH:mm'));
        });
        $('.date_range1').daterangepicker({
            autoUpdateInput: false,
            "locale": {
                "format": "DD/MM/YYYY",
                "separator": " - ",
                "applyLabel": "Áp dụng",
                "cancelLabel": "Huỷ bỏ",
                "fromLabel": "Từ ngày",
                "toLabel": "Tới ngày",
                "customRangeLabel": "Tuỳ chọn",
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
               'Tháng trước': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            },
            "alwaysShowCalendars": true,
            // maxDate: moment(),
            // minDate: moment().subtract(1, "years"),
        }, function(start, end, label) {
            $('.date_range1').val(start.format('DD/MM/YYYY') + " - " + end.format('DD/MM/YYYY'));
        });
        $('.date_range2').daterangepicker({
            autoUpdateInput: false,
            "locale": {
                "format": "DD/MM/YYYY",
                "separator": " - ",
                "applyLabel": "Áp dụng",
                "cancelLabel": "Huỷ bỏ",
                "fromLabel": "Từ ngày",
                "toLabel": "Tới ngày",
                "customRangeLabel": "Tuỳ chọn",
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
               'Tháng trước': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            },
            "alwaysShowCalendars": true,
            // maxDate: moment(),
            // minDate: moment().subtract(1, "years"),
        }, function(start, end, label) {
            $('.date_range2').val(start.format('DD/MM/YYYY') + " - " + end.format('DD/MM/YYYY'));
        });
    }
    !function ($) {
        $(function(){
            init();
            $("select[name='type']").change(function(event) {
                var type = $.trim($(this).val());
                if(type == 'NONE') {
                    $("#type").html("");
                    toastr.warning(_NoSelectedType);
                    return false;
                }
                $.getJSON("{!! route('admin.reports.filter') !!}?type=" + type).done(function (data) {
                    $("#type").html(data.message);
                    init();
                }).fail(function(jqxhr, textStatus, error) {
                    var error = $.parseJSON(jqxhr.responseText);
                    toastr.error(error.message, '{!! trans('system.info') !!}');
                }).always(function() {
                });
                $('#result').html('');
            });

            $("#report").click(function(event) {
                var type = $("select[name='type']").val();
                if(type == 'NONE') {
                    toastr.warning(_NoSelectedType)
                    return false;
                }
                var values = {};
                $("#type :input").each(function(){
                    if (typeof $(this).attr('name') !== "undefined") {
                        values[$(this).attr('name')] = $(this).val();
                    }
                });
                box1 = new ajaxLoader('body', {classOveride: 'blue-loader', bgColor: '#000', opacity: '0.3'});
                $.ajax({
                    url: "{!! route('admin.reports.store') !!}",
                    type: 'POST',
                    headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                    data: {data : values, type: type},
                    datatype: 'json',
                    success: function(data) {
                        if (data.error) {
                            toastr.warning(data.message);
                        } else {
                            $('#result').html('').append(data.message);
                        }
                    },
                    error: function(obj, status, err) {
                        var error = $.parseJSON(obj.responseText);
                        toastr.warning(error.message);
                    }
                }).always(function() {
                    if(box1) box1.remove();
                });
            });

            const URL_GET_DEPT = "{!! route('admin.contracts.setDepartmentOption') !!}";
            const CSRF = {'X-CSRF-Token': "{!! csrf_token() !!}"};
            let $currentRoute = {!! json_encode(\App\PermissionUserObject::getCurrentModule(\Route::getCurrentRoute())) !!};

            $(document).on('change', 'select[name="company"]', function (e) {
                setDepartmentOption(URL_GET_DEPT, CSRF, $currentRoute)
            })

            function setDepartmentOption(url, csrf, $currentRoute = null) {
                let companyId = $('select[name="company"]').val();
                if (companyId) {
                    $.ajax({
                        url: url,
                        data: {companyId: companyId, route: $currentRoute},
                        type: 'POST',
                        headers: csrf,
                        success: function (res) {
                            $('select[name="department"] option').remove()
                            $('select[name="department"]').append('<option value="">' + 'Tất cả' + '</option>')
                            $.each(res, function (index, value) {
                                let isSelected = ''
                                $('select[name="department"]').append('<option value="' + index + '"' + isSelected + '>' + value + '</option>')
                            })
                        },
                        error: function (err) {
                            let error = $.parseJSON(err.responseText);
                            toastr.warning(error.message, "{!! trans('system.have_error') !!}")
                        }
                    })
                }
            }
        });
}(window.jQuery);
</script>
@stop