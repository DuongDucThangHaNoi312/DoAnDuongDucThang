@extends('backend.master')
@section('title')
    {!! trans('system.action.detail') !!} - {!! trans('departments.label') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('departments.label') !!}
            <small>{!! trans('system.action.detail') !!}</small>
            @if($department->status)
                <label class="label label-success">
                    {!! trans('system.status.active') !!}
                </label>
            @else
                <label class="label label-default">
                    {!! trans('system.status.deactive') !!}
                </label>
            @endif
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.departments.index') !!}">{!! trans('departments.label') !!}</a></li>
        </ol>
    </section>
    {!! Form::open(['url' => route('admin.departments.update', $department->id), 'method' => 'PUT', 'role' => 'form']) !!}
    <table class='table borderless'>
        <tr>
            <th class="table_right_middle " style="width: 15%;">
                {!! trans('departments.code') !!}
            </th>
            <td>
                {!! Form::text('code', old('code',$department->code), ['class' => 'form-control ', 'disabled',  'required']) !!}
            </td>
            <th class="table_right_middle " style="width: 15%;">
            </th>
            <td>
            </td>
        </tr>
        <tr>
            <th class="table_right_middle " style="width: 15%;">
                {!! trans('departments.name') !!}
            </th>
            <td>
                {!! Form::text('name', old('name',$department->name), ['class' => 'form-control ', 'disabled',  'required']) !!}
            </td>
            <th class="table_right_middle " style="width: 15%;">
            </th>
            <td>
            </td>
        </tr>
        <tr>
            <th class="table_right_middle " style="width: 15%;">
                {!! trans('departments.name_es') !!}
            </th>
            <td>
                {!! Form::text('name_es', old('name',$department->name_es), ['class' => 'form-control', 'disabled']) !!}
            </td>
            <th class="table_right_middle " style="width: 15%;">
            </th>
            <td>
            </td>
        </tr>
        <tr>
            <th class="table_right_middle" style="width: 15%;">
                {!! trans('departments.telephone') !!}
            </th>
            <td>
                {!! Form::text('telephone', old('telephone',$department->telephone), ['class' => 'form-control', 'disabled', 'maxlength' => 13, 'required']) !!}
            </td>
        </tr>
        <tr>
            <th class="table_right_middle">
                {!! trans('departments.time_in_works') !!}
            </th>
            <td >
                <label style="padding-top: 5px">
                    {!! Form::radio('type', '1', old('type',$department->type)== '1' ? 'checked' : '',  ['id' => 'type','disabled']) !!}
                    <label>{!! trans('departments.office_time') !!}</label>
                </label>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <label>
                    {!! Form::radio('type', '2', old('type',$department->type)== '2' ? 'checked' : '',  ['id' => 'type','disabled']) !!}
                    <label>{!! trans('departments.shifts_time') !!}</label>
                </label>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <label>
                    {!! Form::radio('type', '3', old('type',$department->type)== '3 ' ? 'checked' : '',  ['id' => 'type','disabled']) !!}
                    <label>{!! trans('departments.shifts_and_ot_time') !!}</label>
                </label>

            </td>
        </tr>
        <tr>
            <th class="table_right_middle">
                {!! trans('departments.company_id') !!}
            </th>
            <td>
                <label>
                    {!! Form::checkbox('company_id', $department->company->id, old('company_id',$department->company->id), [ 'class' => 'minimal company_id']) !!}
                    {!! $department->company->shortened_name !!}
                </label>

            </td>
        </tr>
        <tr>
        <tr>
            <th class="table_right_middle">
                {!! trans('departments.address') !!}
            </th>
            <td>
                {!! Form::text('address', old('address',$department->address), ['class' => 'form-control', 'disabled']) !!}
            </td>
        </tr>
        <tr>
            <th class="table_right_middle">
                {!! trans('departments.address_es') !!}
            </th>
            <td>
                {!! Form::text('address_es', old('address_es',$department->address_es), ['class' => 'form-control' , 'disabled']) !!}
            </td>

        </tr>
        <tr>
            <th class="table_right_middle">
                {!! trans('departments.description') !!}
            </th>
            <td>
                {!! Form::textarea('description', old('tax_code',$department->description), ['class' => 'form-control', 'disabled']) !!}
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
                $(".select2").select2({width: '100%'});
                $('input[type="checkbox"].minimal').iCheck({
                    checkboxClass: 'icheckbox_minimal-red'
                });
            });
        }(window.jQuery);
    </script>
@stop