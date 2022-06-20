@extends('backend.master')
@section('title')
    {!! trans('system.action.create') !!} - {!! trans('allowance_categories.label') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/>
@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('allowance_categories.label') !!}
            <small>{!! trans('system.action.create') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.allowance-categories.index') !!}">{!! trans('allowance_categories.label') !!}</a></li>
        </ol>
    </section>
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
    <div style="width: 700px; margin: auto; margin-top: 50px;">
        {!! Form::open(['url' => route('admin.allowance-categories.store'), 'role' => 'form']) !!}
        <div class="box-body">
            <div>
                {!! Form::label(trans('allowance_categories.name') ) !!}
                {!! Form::text('name', old('name'), ['class' => 'form-control', 'required']) !!}
            </div>
            <div  style="margin-top: 10px">
                {!! Form::label(trans('allowance_categories.name_es') ) !!}
                {!! Form::text('name_es', old('name_es'), ['class' => 'form-control']) !!}
            </div>
            <div style="margin-top: 10px">
                {!! Form::label(trans('system.desc') ) !!}
                {!! Form::textarea('desc', old('desc'), ['rows' => 4, 'class' => 'form-control']) !!}
            </div>
            <div style="margin-top: 10px">
                <label>{!! trans('timekeeping.company') !!}</label>
                <select name="company_id" id="company" class="companySelect form-control select2">
                    <option value="" selected="selected">{{ trans('system.dropdown_choice') }}</option>
                    @foreach (\App\Helpers\GetOption::getCompaniesForOption() as $key => $item)
                    <option value="{{ $key }}">{{ $item }}</option>
                    @endforeach
                </select>
            </div>
            <div style="margin-top: 10px">
                <label>{!! trans('timekeeping.department') !!}</label>
                <select name="department[]" id="departmentSelect" class="form-control select2 department" disabled="true" multiple>
                    <option value="" {!! old('department') !!}>{!! trans('system.dropdown_choice') !!}</option>
                </select>
            </div>
            <div align="center" style="margin-top: 30px; font-weight: 600;">
                <span>
                    {!! Form::checkbox('status', 1, old('status', 1), [ 'class' => 'minimal-red ' ]) !!}
                    {!! trans('system.status.active') !!}
                </span>
                <span style="margin-left: 10px">
                     {!! Form::checkbox('type', 1, old('type', 0), [ 'class' => 'minimal-red ' ]) !!}
                    {!! trans('allowance_categories.has_kpi') !!}
                </span>
                <span style="margin-left: 10px">
                    {!! Form::checkbox('is_social_security', 1, old('is_social_security', 0), [ 'class' => 'minimal-red ' ]) !!}
                    {!! trans('allowance_categories.is_social_security') !!}
                </span>
                <span style="margin-left: 10px">
                    {!! Form::checkbox('is_exemp', 1, old('is_exemp', 0), [ 'class' => 'minimal-red ' ]) !!}
                    {!! trans('allowance_categories.is_exemp') !!}
                </span>
                <span style="margin-left: 10px">
                    {!! Form::checkbox('ot', 1, old('ot', 0), [ 'class' => 'minimal-red ' ]) !!}
                    {!! trans('allowance_categories.ot') !!}
                </span>
            </div>
            <div style="margin-top: 50px" align="center">
                {!! HTML::link(route( 'admin.allowance-categories.index' ), trans('system.action.cancel'), ['class' => 'btn btn-danger btn-flat']) !!}
                {!! Form::submit(trans('system.action.save'), ['class' => 'btn btn-primary btn-flat']) !!}
            </div>
            
        </div>
        {!! Form::close() !!}
    </div>
@stop

@section('footer')
    <script src="{!! asset('assets/backend/plugins/iCheck/icheck.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/select2/select2.full.min.js') !!}"></script>

    <script>
        !function ($) {
            $(".select2").select2({
                    width: '100%',
            });

            $(function() {
                $('input[type="checkbox"].minimal-red').iCheck({
                    checkboxClass: 'icheckbox_minimal-red'
                });
            });
        }(window.jQuery);
        
        var oldDepartmentId = {!! old('department') ?? 0 !!};
        function setDepartmentOption() {
            let companyId = $('.companySelect'). val();
            if (companyId) {
                $('#departmentSelect').attr('disabled', false)
                $.ajax({
                    url: "{!! route('admin.contracts.setDepartmentOption') !!}",
                    data: {companyId: companyId},
                    type: 'POST',
                    headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                    success: function (res) {
                        $('#departmentSelect option').remove()
                        $('#departmentSelect').append('<option value="">'+ '{!! trans('system.dropdown_choice') !!}'  + '</option>')
                        $.each(res, function (index, value) {
                            let isSelected = oldDepartmentId == index ? 'selected' : ''
                            $('#departmentSelect').append('<option value="' + index + '"' + isSelected + '>' + value + '</option>')
                        })
                    },
                    error: function (data) {
                        console.log(data)
                    }
                })
            } else {
                $('#departmentSelect').attr('disabled', true)
            }
        }
        $(document).on('change', '.companySelect', setDepartmentOption)
        if ($('.companySelect'). val()) {
            $('#departmentSelect').attr('disabled', false)
            setDepartmentOption()
        }
    </script>
@stop