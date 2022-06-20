@extends('backend.master')
@section('title')
    {!! trans('system.action.create') !!} - {!! trans('departments.combined') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('departments.combined') !!}
            <small>{!! trans('system.action.create') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.combined.index') !!}">{!! trans('departments.combined') !!}</a></li>
        </ol>
    </section>
    {!! Form::open(['url' => route('admin.combined.store'), 'method'=>'POST']) !!}
   
    <table class='table borderless' style="width: 1000px;margin: auto;margin-top: 50px;">
        <tr>
            <th class="table_right_middle " style="width: 20%;">
                {!! trans('departments.combined_name') !!}
            </th>
            <td>
                {!! Form::text('name', old('name'), ['class' => 'form-control']) !!}
            </td>
            <th class="table_right_middle " style="width: 15%;">
            </th>
            <td>
            </td>
        </tr>
        <tr >
            <th class="table_right_middle">
                {!! trans('departments.combined_member') !!}
            </th>
            <td>
                <select class="form-control select2" multiple name="department_id[]" >
                    @foreach($departmentGroup as $key => $value)
                        <option value="{!! $key !!}">{!! $value !!}</option>
                        @endforeach
                </select>
            </td>
        </tr>
        <tr >
            <th class="table_right_middle">
                {!! trans('departments.type') !!}
            </th>
            <td>
                {!! Form::select('type', ['' => trans('system.dropdown_choice')]+\App\Define\Department::getTypeDepartmentGroups() ,old('type'), ['class' => 'form-control select2 company-id', ]) !!}
            </td>
        </tr>
        <tr>
            <td class="text-center table_right_middle1" colspan="3">
                <label>
                    {!! Form::checkbox('only_manager', 1, old('only_manager', 1), [ 'class' => 'minimal' ]) !!}
                    {!! trans('departments.combined_only_manager') !!}
                </label>
                &nbsp; &nbsp; &nbsp; &nbsp;
                <label>
                    {!! Form::checkbox('status', 1, old('status', 1), [ 'class' => 'minimal' ]) !!}
                    {!! trans('system.status.active') !!}
                </label>
            </td>
        </tr>
        <tr>
            <td colspan="4" class="text-center">
                {!! HTML::link(route( 'admin.departments.index' ), trans('system.action.cancel'), ['class' => 'btn btn-danger btn-flat']) !!}
                {!! Form::submit(trans('system.action.save'), ['class' => 'btn btn-primary btn-flat']) !!}
            </td>
        </tr>
    </table>
    {!! Form::close() !!}
@stop
@section('footer')
    <script src="{!! asset('assets/backend/plugins/iCheck/icheck.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/select2/select2.full.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/moment/min/moment-with-locales.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/input-mask/jquery.inputmask.min.js') !!}"></script>
    <script>
        !function ($) {
            $(function () {
                $(".select2").select2({
                    width: '100%',
                    placeholder: '  {!! trans('overtimes.choose_department') !!} '
                });
                $('input[type="checkbox"].minimal').iCheck({
                    checkboxClass: 'icheckbox_minimal-red'
                });
            });
        }(window.jQuery);
    </script>
@stop