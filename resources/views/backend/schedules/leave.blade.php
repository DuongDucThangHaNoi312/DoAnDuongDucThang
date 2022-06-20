@extends('backend.master')
@section('title')
    {!! trans('menus.reports.leave_label') !!} {!! trans('staffs.label') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
    <link rel="stylesheet" type="text/css"
          href="{!! asset('assets/backend/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') !!}"/>
    <link rel="stylesheet" type="text/css"
          href="{!! asset('assets/backend/plugins/daterangepicker/daterangepicker.css') !!}"/>
@stop
@section('content')
    <section class="content-header">
        <h1>
            LOVE
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.staffs.index') !!}">{!! trans('reports.label') !!}</a></li>
        </ol>
    </section>
    <section class="content overlay">
        <div class="box box-default">
            <div class="box-body">
                {!! Form::open(['url' => route('admin.reports.leave'), 'method' => 'GET']) !!}
                <div class="row">
                    @include('backend.reports.filter.FILTER_STAFF_LEAVE')
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('filter', trans('system.action.label'), ['style' => 'width: 100%;']) !!}
                                    <button type="submit" class="btn btn-info" id="report">
                                        <span class="glyphicon glyphicon-flash"></span>&nbsp; Lập báo cáo
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('download', trans('system.action.label'), ['style' => 'width: 100%;']) !!}
                                    <button type="button" class="btn btn-success" onclick="">
                                        <span class="far fa-file-excel fa-fw"></span>&nbsp; Xuất excel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}

            </div>
        </div>
        <div class="box">
            <div class="box-body no-padding">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs" style="font-weight: 700; font-size: 14px;">
                        <li class="active"><a href="#detail-leave-tab"
                                              data-toggle="tab">Chi tiết</a></li>
                        <li><a href="#leave-tab" data-toggle="tab">Tổng hợp</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="detail-leave-tab">
                            <div class="table-responsive">
                                @include('backend.schedules.partitions._detail_leave_table', compact('users', 'data'))
                            </div>
                        </div>
                        <div class="tab-pane" id="leave-tab">
                            <div class="table-responsive">
                                @include('backend.schedules.partitions._leave_table', compact('users'))
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
@section('footer')
    <script src="{!! asset('assets/backend/plugins/select2/select2.full.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.vi.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/daterangepicker/moment.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/daterangepicker/daterangepicker.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/moment/locale/vi.js') !!}"></script>
    {{--<script src="{!! asset('assets/backend/plugins/sticky-table-headers/js/jquery.stickytableheaders.min.js') !!}"></script>--}}
    <script>
        const URL_GET_DEPT = "{!! route('admin.contracts.setDepartmentOption') !!}";
        const CSRF = {'X-CSRF-Token': "{!! csrf_token() !!}"};
        !function ($) {
            $(function () {
                callDateRangePicker($('.range-date-leave'))
                $(".select2").select2({});
                $(document).on('change', '.companySelect', function (e) {
                    setDepartmentOption(URL_GET_DEPT, CSRF)
                })

                function setDepartmentOption(url, csrf, oldDepartmentId = null) {
                    let companyId = $('.companySelect').val();
                    let tagDepartment = $('.departmentSelect');
                    if (companyId) {
                        tagDepartment.attr('disabled', false)
                        $.ajax({
                            url: url,
                            data: {companyId: companyId},
                            type: 'POST',
                            headers: csrf,
                            success: function (res) {
                                $('.departmentSelect option').remove()
                                tagDepartment.append('<option value="">' + 'Chọn 1 mục' + '</option>')
                                $.each(res, function (index, value) {
                                    let isSelected = oldDepartmentId == index ? 'selected' : ''
                                    tagDepartment.append('<option value="' + index + '"' + isSelected + '>' + value + '</option>')
                                })
                            },
                            error: function (err) {
                                let error = $.parseJSON(err.responseText);
                                toastr.warning(error.message, "{!! trans('system.have_error') !!}")
                            }
                        })
                        $('#department-tooltip').attr('title', '').tooltip('show')

                    } else {
                        $('.departmentSelect option').remove()
                        tagDepartment.append('<option value="">' + 'Chọn 1 mục' + '</option>')
                    }
                }
            });
        }(window.jQuery);
    </script>
@stop