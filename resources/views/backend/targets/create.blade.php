@extends('backend.master')
@section('title')
    {!! trans('system.action.create') !!} - {!! trans('kpi.label') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') !!}"/>
    <style type="text/css">
        input[type=number]::-webkit-inner-spin-button {
            -webkit-appearance: none;
        }
        .fa-plus:before {
            content: "\f067";
        }
        #cancel{
            margin-bottom: 2%;
            background-color: #FFFFFF;
            margin-left: 80%;
            display: inline-block;
            border: 1px solid #0c0c0c;
            position: absolute;
            right: 2%;
            border-radius: 5px
        }
        .food{
            position: relative;
            margin-top: 2%;
        }
        #submitForm{
            background-color: #169BD5;
            width: 6%;
            border-radius: 5px;
            border: 1px solid #169BD5;
        }
    </style>
@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('kpi.label') !!}
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.targets.index') !!}">{!! trans('kpi.label') !!}</a></li>
        </ol>
    </section>
    <hr>
    @if($errors->count())
        <div class="alert alert-warning alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-warning"></i> {!! trans('messages.error') !!}</h4>
            <ul>
                @foreach($errors->all() as $message)
                    <li>{!! $message !!}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <?php $labels = ['default', 'success', 'info', 'danger', 'warning']; ?>
    <section class="content overlay">
        <div class="box box-default">
            <div class="box-header with-bconsumer">
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                </div>
            </div>
            <div class="box-body">
                {!! Form::open([ 'url' => route('admin.targets.create'), 'method' => 'GET', 'role' => 'search' ]) !!}
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('company_filter', trans('kpi.name_company')) !!}
                                {!! Form::select('company_filter', ['' =>  'Chọn công ty'] + \App\Helpers\GetOption::getCompaniesForOption(), old('company_filter', $company_filter), ['class' => 'search-form companySelect select2', 'id' => "company_filter"]) !!}
                            </div>
                        </div>
                        <div style="" class="col-md-3">
                            <div  class="form-group">
                                <div class="form-group">
                                    {!! Form::label('department_filter', trans('kpi.name_department')) !!}
                                    {!! Form::select('department_filter', ['' =>  'Chọn phòng ban'] + $companies, old('department_filter'), ['class' => 'search-form departmentSelect select2', 'disabled', 'id' => "department_filter"]) !!}
                                </div>
                            </div>
                        </div>
                        <div style="" class="col-md-4">
                            {!! Form::label('filter', trans('system.action.label')) !!}
                            <div class="form-group">
                                <button type="submit" class="btn btn-default">
                                    <span class="glyphicon glyphicon-search"></span>&nbsp; {!! trans('system.action.search') !!}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <div class="box">
            {!! Form::open(['url' => route('admin.targets.store')]) !!}
            <div class="box-header">
                <div class="row">
                    <div class="col-md-2">
                        KPI của Tháng
                    </div>
                    <div class="col-md-2">
                        <select name="timestamp" class="form-control select2" id="timestamp">
                            @for ($j = $year; $j >= 2021; $j--)
                                @for ($i = 12; $i >= 1; $i--)
                                    <?php if ($j == $year && $i > date("m")) continue; ?>
                                    <option data-month="{{ $i }}" data-year="{{ $j }}" value="{{ $j.'-'.$i }}" {{ date('Y') == $j && date('m') == $i ? 'selected' : '' }}>
                                        {{ str_pad($i, 2, '0', STR_PAD_LEFT).'/'.$j }}
                                    </option>
                                @endfor
                            @endfor
                        </select>
                    </div>
                </div>
            </div>
            <div class="box-body no-padding">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th style="text-align: center; vertical-align: middle; white-space: nowrap;">{!! trans('system.no.') !!}</th>
                                <th style="text-align: center; vertical-align: middle; white-space: nowrap;">{!! trans('kpi.code_staff') !!}</th>
                                <th style="text-align: center; vertical-align: middle; white-space: nowrap;">{!! trans('kpi.name_staff') !!}</th>
                                <th style="text-align: center; vertical-align: middle; white-space: nowrap;">{!! trans('kpi.kpi_value') !!}</th>
                                <th style="text-align: center; vertical-align: middle; white-space: nowrap;">{!! trans('kpi.description') !!}</th>
                                <th style="text-align: center; vertical-align: middle; white-space: nowrap;">Chú thích 2</th>
                                {{-- <th style="text-align: center; vertical-align: middle;">{!! trans('system.action.label') !!}</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @php $i=1; @endphp
                            @if(count($users) > 0)
                                @foreach($users as $item)
                                    <?php $kpi = ($item->targetCurrentMonth->kpi); ?>
                                        <tr>
                                            <td style="text-align: center; vertical-align: middle;">{!! $i++ !!}</td>
                                            <td align="center" style="vertical-align: middle;">{!! $item->code!!}</td>
                                            <td align="center" style="vertical-align: middle;"
                                                data-id="{{$item->id}}">{!! $item->fullname !!}
                                                <input type="hidden" data-id="{{ $item->id }}" name="user_id[]"
                                                       value="{{ $item->id }}">
                                            </td>
                                            <td style="vertical-align: middle;">
                                                <input style="min-width: 60px;" data-month="{{ intval($mc) }}" type="number" data-kpi="{{ $kpi }}" value="{{ $kpi }}" class="form-control kpi" name="kpi[]" id="kpi-value-{{$item->id}}">
                                            </td>
                                            <td style="text-align: center; position:relative;vertical-align: middle;">
                                                <div class="form-group">
                                                    <textarea style="position: absolute;width: 90%;top: 19%;height: 68%" class="form-control description" rows="1" cols="4" name="description[]" id="description-{{$item->id}}" rows="3">{{ $item->targetCurrentMonth->description }}</textarea>
                                                </div>
                                            </td>
                                            <td style="text-align: center; position:relative;vertical-align: middle;">
                                                <div class="form-group">
                                                    <textarea style="position: absolute;width: 90%;top: 19%;height: 68%" class="form-control note" rows="1" cols="4" name="note[]" id="note-{{$item->id}}" rows="3">{{ $item->targetCurrentMonth->note }}</textarea>
                                                </div>
                                            </td>

                                        </tr>
                                @endforeach
                                <tr>
                                    <td colspan="6">
                                        <div class="row">
                                            <div style="text-align: center" class="col-sm-12">
                                                {!! Form::submit(trans('system.action.save'), ['class' => 'btn btn-primary btn-flat']) !!}
                                                {!! HTML::link(route( 'admin.targets.index' ), trans('system.action.cancel'), ['class' => 'btn btn-danger btn-flat']) !!}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @else
                                <tr>
                                    <td colspan="6" align="center">
                                        <span class='text-size center'><i class='fas fa-search'></i> {!! trans('staff_positions.no_data') !!}</span>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </section>
@stop
@section('footer')
    <script src="{!! asset('assets/backend/plugins/iCheck/icheck.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/select2/select2.full.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/moment/min/moment-with-locales.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/input-mask/jquery.inputmask.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') !!}"></script>
    <script src="{{ asset('assets/backend/js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/backend/js/func-global.js') }}"></script>
    <script>
        $(function () {
            $('#month_filter').datepicker({
                format: "mm/yyyy",
                viewMode: "months",
                minViewMode: "months",
                clearBtn: true,
                autoclose: true,
            });
            $(".select2").select2({
                width: '100%'
            });
        });
        $('#title').validate({
            rules: {
                'kpi[]': {
                    // required: true,
                    max: 200,
                    min:0,
                },
            },
            messages: {
                'kpi[]': {
                    max: "<span class='text-danger'>{!! trans('kpi.min_max') !!}</span>",
                    min: "<span class='text-danger'>{!! trans('kpi.min_max') !!}</span>",
                }
            },
            submitHandler: function (form) {
                $.ajax({
                    url: "{{ route('admin.targets.store') }}",
                    type: 'POST',
                    data: new FormData(form),
                    dataType: 'JSON',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function (data) {
                        console.log(data);
                        if (data.errors) {
                            $("#my-form").replaceWith(data.form);
                            toastr.error('{!!trans('staff_titles.errors') !!}');
                        }
                        if (data.success) {
                            {{--window.location.href = '{{route('admin.targets.index')}}';--}}
                            toastr.success('{!!trans('staff_titles.add') !!}');
                            setTimeout(function () {
                                location.reload();
                            }, 500);
                        }
                    }
                })
            }
        });
        $('#month_filter').change(function () {
            $('#month').val($(this).val());
        });
        $(".editForm").click(function (e) {
            var id = $(this).attr('data-user');
            $('#kpi-value-' + id).attr('readonly', false);
            $('#description-' + id).attr('readonly', false);
            $('#note-' + id).attr('readonly', false);
        });
        let departmentOld = {!! json_encode($department_filter) !!};
        let routeSetDept = "{!! route('admin.contracts.setDepartmentOption') !!}"
        let csrfToken = "{!! csrf_token() !!}";
        let $currentRoute = {!! json_encode(\App\PermissionUserObject::getCurrentModule(\Route::getCurrentRoute())) !!};
        setDepartment(routeSetDept, csrfToken, $currentRoute, departmentOld)
        $('body').on('change', '.companySelect', function () {
            setDepartment(routeSetDept, csrfToken, $currentRoute)
        });
        $('#timestamp').on('change', function() {
            let month = $('#timestamp :selected').data('month');
            let year = $('#timestamp :selected').data('year');
            $('#month1').val(month).change();
            $('#year1').val(year).change();
            $('.kpi').val('').change();
            $('.description').val('').change();
            $('.note').val('').change();
            $.ajax({
                type: "POST",
                url: "{{ route('admin.targets.get-kpi') }}",
                data: {
                    month: month,
                    year: year
                },
                headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                dataType: "json",
                success: function (response) {
                    if (response.status == 200) {
                        $.each(response.data, function (key, value) { 
                            if (month == value.month) {
                                $('#kpi-value-' + value.user_id).val(value.kpi).change();
                                $('#description-' + value.user_id).val(value.description).change();
                                $('#note-' + value.user_id).val(value.note).change();
                            }
                        });
                    }
                }
            });
        });
    </script>
@stop
