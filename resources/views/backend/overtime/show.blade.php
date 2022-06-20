@extends('backend.master')
@section('title')
    {!! trans('system.action.edit') !!} - {!! trans('overtimes.label') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
    <link rel="stylesheet" type="text/css"
          href="{!! asset('assets/backend/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') !!}"/>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">

@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('overtimes.label') !!}
            <small>{!! trans('system.action.edit') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.overtimes.index') !!}">{!! trans('overtimes.label') !!}</a></li>
        </ol>
    </section>

    <div class="">
        <!-- /.box-header -->
        <div class="box-body card card-default" style="padding: 40px 130px;">
            {!! Form::open(['url' => route('admin.overtimes.update',$overtime->id), 'role' => 'form','method'=>'PUT']) !!}

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>{!! trans('overtimes.company_id') !!} <span style="color: red">*</span></label>
                        {!! Form::select('company_id', ['' => trans('system.dropdown_choice')] + App\Define\OverTime::getCompanyNamesForOption(), old('company_id',$overtime->company_id), ['class' => 'form-control select2 companySelect']) !!}
                    </div>
                    <div class="form-group">
                        <label>{!! trans('overtimes.department_id') !!}<span style="color: red">*</span></label>
                        {!! Form::select('department_id',$departmentOption ,old('department_id',$overtime->department_id), ['class' => 'form-control select2', 'required', 'id' => 'departmentSelect']) !!}
                    </div>
                    <div class="form-group">
                        <label>{!! trans('overtimes.shifts') !!}<span style="color: red">*</span></label>
                        {!! Form::select('shifts', $shiftsOption, old('shifts',$overtime->shifts), ['class' => 'form-control select2', 'required', 'id' => 'shiftSelect']) !!}
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="start_date">{!! trans('overtimes.start_date') !!} <span
                                            style="color: red">*</span></label>
                                <div class='input-group'>
                                    {!! Form::text('start_date', old('start_date',$overtime->start_date->format('d/m/Y')), ['class' => 'form-control datepicker start_date','id'=>'start_date' ,'placeholder'=>trans('overtimes.start_date_placeholder'),'autocomplete'=>'off']) !!}
                                    <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                          </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="end_date">{!! trans('overtimes.end_date') !!} <span
                                                    style="color: red">*</span></label>
                                    </div>
                                </div>
                                <div class='input-group'>
                                    {!! Form::text('end_date', old('end_date',$overtime->end_date ? $overtime->end_date->format('d/m/Y') : ''), ['class' => 'form-control datepicker','id'=>'end_date' ,'placeholder'=>trans('overtimes.end_date_placeholder'),'autocomplete'=>'off','disabled']) !!}
                                    <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <label for="radio-type">{!! trans('overtimes.limit') !!}<span style="color: red">*</span> :
                    </label>
                    <div class="radio">
                        @if (!Auth::user()->hasRole('LEADER'))
                        <label class="type-option">
                            {!! Form::radio('display_with_type', '1', old('display_with_type',$overtime->display_with_type) == '1' ? 'checked' : '',  ['id' => 'display_with_type']) !!}
                            {!! trans('overtimes.all_department') !!}</label>
                        </label>
                        @endif
                    </div>
                    <div class="radio">
                        <label class="type-option">
                            {!! Form::radio('display_with_type', '2', old('display_with_type',$overtime->display_with_type) == '2' ? 'checked' : '',  ['id' => 'display_with_type']) !!}
                            {!! trans('overtimes.user/users') !!}</label>
                        </label>
                        {!! Form::select('display_with_data[]', $userOption, old('display_with_data[]',json_decode($overtime->display_with_data)), ['class' => 'form-control select2 display_with_data user_id',$overtime->display_with_type == 1 ?'disabled' : '','multiple', 'required']) !!}
                    </div>
                   @if (!Auth::user()->hasRole('LEADER'))
                    <div class="form-group">
                        <label for="hidden_with_users">{!! trans('overtimes.except') !!}</label>
                        {!! Form::select('hidden_with_users[]', $userOption, old('hidden_with_users[]',json_decode($overtime->hidden_with_users)), ['class' => 'form-control select2 hidden_with_users',$overtime->display_with_type == 2 ?'disabled' : '','multiple', ]) !!}
                    </div>
                   @endif

                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>{!! trans('overtimes.hours') !!} <span
                                            style="color: red">*</span></label>
                                <div class='input-group'>
                                    {!! Form::number('overtime_hours', old('overtime_hours',$overtime->overtime_hours), ['class' => 'form-control ','id'=>'overtime_hours' ,'placeholder'=>trans('overtimes.hours_placeholder'),'style'=>'text-align:right','min'=>1]) !!}
                                    <span class="input-group-addon">
                                           {!! trans('overtimes.hour') !!}
                                          </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="input-validate">
                                    <label class="status">
                                        @if($overtime->status == 1)
                                            {!! Form::checkbox('status', 1, old('status',1), [ 'class' => 'minimal status','id'=>'status' ]) !!}
                                        @else
                                            {!! Form::checkbox('status', 1, old('status',0), [ 'class' => 'minimal status','id'=>'status' ]) !!}

                                        @endif
                                        <span class="every-week"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}

@stop
@section('footer')
    <script src="{!! asset('assets/backend/plugins/iCheck/icheck.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/select2/select2.full.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/moment/min/moment-with-locales.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/input-mask/jquery.inputmask.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') !!}"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
    <script>
        !function ($) {
            $(function () {


                $('#start_date').datepicker({
                    format: 'dd/mm/yyyy',
                    autoclose: true,
                });
                $('#end_date').datepicker({
                    format: 'dd/mm/yyyy',
                    useCurrent: false,
                    autoclose: true,
                })

                $(".select2").select2({
                    width: '100%',
                    placeholder: ' {!! trans('system.dropdown_choice') !!} '
                });
                $('input[type="checkbox"].minimal').iCheck({
                    checkboxClass: 'icheckbox_minimal-blue'
                });


                if ($("#start_date").val()) {
                    let date = $("#start_date").datepicker("getDate").getDay();

                    date == '1' ? $('.every-week').html('{!! trans('overtimes.mo') !!}') : '';
                    date == '2' ? $('.every-week').html('{!! trans('overtimes.tu') !!}') : '';
                    date == '3' ? $('.every-week').html('{!! trans('overtimes.we') !!}') : '';
                    date == '4' ? $('.every-week').html('{!! trans('overtimes.th') !!}') : '';
                    date == '5' ? $('.every-week').html('{!! trans('overtimes.fr') !!}') : '';
                    date == '6' ? $('.every-week').html('{!! trans('overtimes.st') !!}') : '';
                    date == '0' ? $('.every-week').html('{!! trans('overtimes.sn') !!}') : '';
                }
            });
            var status = {{ $overtime->status }}
            if(status != 1){
                $('label.status').hide()
            }
            $('input[type=text],input[type=radio]').prop('disabled','disabled')
            $('input[type=checkbox]').iCheck('disable')
            $('select').prop('disabled','disabled')
        }(window.jQuery);




    </script>
@stop