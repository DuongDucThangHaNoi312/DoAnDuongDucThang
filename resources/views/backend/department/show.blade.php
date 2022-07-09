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
            <th class="table_right_middle" style="width: 15%;">
                {!! trans('departments.telephone') !!}
            </th>
            <td>
                {!! Form::text('telephone', old('telephone',$department->telephone), ['class' => 'form-control', 'disabled', 'maxlength' => 13, 'required']) !!}
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

        <tr>
            <td class="text-center table_right_middle1" colspan="4">
                <label>
                    {!! Form::checkbox('status', 1, old('status', $department->status), ['class' => 'minimal', '']) !!}
                    {!! trans('system.status.active') !!}
                </label>
                <label>
                    {!! Form::checkbox('is_ph', 1, old('is_ph', $department->is_ph), [ 'class' => 'minimal', '' ]) !!}
                    {!! 'Phòng Họp' !!}
                </label>
            </td>
        </tr>

    </table>

    {!! Form::close() !!}
    <div style="margin-left: 45%;padding-top: 2%">
        {!! HTML::link(route( 'admin.departments.index' ), trans('system.action.return'), ['class' => 'btn btn-danger btn-flat back','id'=>'cancel']) !!}
    </div>
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
                $('.status, .is_ph').prop('disabled',  true);
            });
        }(window.jQuery);
    </script>
@stop