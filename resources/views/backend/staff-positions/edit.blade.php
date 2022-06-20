@extends('backend.master')
@section('title')
    {!! trans('system.action.edit') !!} - {!! trans('staff_positions.label') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') !!}"/>
@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('staff_positions.label') !!}
            <small>{!! trans('system.action.edit') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.positions.index') !!}">{!! trans('staff_positions.label') !!}</a></li>
        </ol>
    </section>
        {!! Form::open(['url' => route('admin.positions.update', $positions->id), 'method' => 'PUT', 'role' => 'form']) !!}
         <div style="margin-left: 20%;margin-top: 2%">
        <table class='table borderless'>

            <tr >
                <th class="table_right_middle " style="width: 15%;">
                    {!! trans('staff_positions.code') !!}
                </th>
                <td width="50%">
                    {!! Form::text('code', old('code',$positions->code), ['class' => 'form-control', 'required' ]) !!}
                </td>
                <th></th>
                <td></td>
            </tr>
            <tr>
                <th class="table_right_middle" style="width: 15%;">
                    {!! trans('staff_positions.name') !!}
                </th>
                <td>
                    {!! Form::text('name', old('name',$positions->name), ['class' => 'form-control', 'maxlength' => 13,  'required']) !!}
                </td>
            </tr>
            <tr>
                <td class="text-center table_right_middle1" colspan="3">
                    <label>
                        {!! Form::checkbox('unique_in_dept',1, old('unique_in_dept',$positions->unique_in_dept), [ 'class' => 'minimal' ]) !!}
                        {!! trans('staff_positions.only') !!}
                    </label>
                </td>
            </tr>
            <tr>
                <td colspan="3" class="text-center">
                    {!! HTML::link(route( 'admin.positions.index' ), trans('system.action.cancel'), ['class' => 'btn btn-danger btn-flat']) !!}
                    {!! Form::submit(trans('system.action.save'), ['class' => 'btn btn-primary btn-flat']) !!}
                </td>
            </tr>
        </table>
    </div>
    {!! Form::close() !!}
@stop
@section('footer')
    <script src="{!! asset('assets/backend/plugins/iCheck/icheck.min.js') !!}"></script>

    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="{!! asset('assets/backend/plugins/select2/select2.full.min.js') !!}"></script>
    <script type="text/javascript">
        $('input[type="checkbox"].minimal').iCheck({
            checkboxClass: 'icheckbox_minimal-red'
        });
    </script>
@stop