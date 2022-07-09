@extends('backend.master')
@section('title')
    {!! trans('system.action.create') !!} - {!! trans('departments.label') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('departments.label') !!}
            <small>{!! trans('system.action.create') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.departments.index') !!}">{!! trans('departments.label') !!}</a></li>
        </ol>
    </section>
    {!! Form::open(['url' => route('admin.departments.store'), 'method'=>'POST']) !!}
    <table class='table borderless'>
        <tr>
            <th class="table_right_middle " style="width: 15%;">
                {!! trans('departments.code') !!}
            </th>
            <td style="width: 70%;"> 
                {!! Form::text('code', old('code'), ['class' => 'form-control']) !!}
            </td>
            <th class="table_right_middle " style="width: 15%;"></th>
        </tr>

        <tr>
            <th class="table_right_middle " style="width: 15%;">
                {!! trans('departments.name') !!}
            </th>
            <td style="width: 70%;"> 
                {!! Form::text('name', old('name'), ['class' => 'form-control',  'required']) !!}
            </td>
            <th class="table_right_middle " style="width: 15%;"></th>
        </tr>

        <tr>
            <th class="table_right_middle" style="width: 15%;">
                {!! trans('departments.telephone') !!}
            </th>
            <td style="width: 70%;"> 
                {!! Form::text('telephone', old('telephone'), ['class' => 'form-control', 'required']) !!}
            </td>
            <th class="table_right_middle " style="width: 15%;"></th>
        </tr>

        <tr>
            <th class="table_right_middle">
                {!! trans('departments.description') !!}
            </th>
            <td style="width: 70%;"> 
                {!! Form::textarea('description', old('description'), ['class' => 'form-control']) !!}
            </td>
            <th class="table_right_middle " style="width: 15%;"></th>
        </tr>

        <tr>
            <td colspan="4" class="text-center">
                <label>
                    {!! Form::checkbox('status', 1, old('status', 1), [ 'class' => 'minimal' ]) !!}
                    {!! trans('system.status.active') !!}
                </label>
                <label>
                    {!! Form::checkbox('is_ph', 1, old('is_ph', 0), [ 'class' => 'minimal' ]) !!}
                    {!! 'Phòng Họp' !!}
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
                    placeholder: '  {!! trans('system.dropdown_choice') !!} '
                });
                $('input[type="checkbox"].minimal').iCheck({
                    checkboxClass: 'icheckbox_minimal-red'
                });
            });
        }(window.jQuery);
    </script>
@stop