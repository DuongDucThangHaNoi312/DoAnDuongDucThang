@extends('backend.master')
@section('title')
    {!! trans('system.action.detail') !!} - {!! trans('meeting-rooms.label') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('meeting-rooms.label') !!}
            <small>{!! trans('system.action.detail') !!}</small>
                <label class="label label-success">
                    {!! trans('system.status.active') !!}
                </label>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.meeting-rooms.index') !!}">{!! trans('meeting-rooms.label') !!}</a></li>
        </ol>
    </section>
    {!! Form::open(['url' => route('admin.meeting-rooms.update', $meetingRoom->id), 'method' => 'PUT', 'role' => 'form']) !!}
    <table class='table borderless'>
        <tr>
            <th class="table_right_middle " style="width: 15%;">
               Tên phòng họp
            </th>
            <td>
                {!! Form::text('name', old('name',$meetingRoom->name), ['class' => 'form-control ', 'disabled',  'required']) !!}
            </td>
            <th class="table_right_middle " style="width: 15%;">
            </th>
            <td>
            </td>
        </tr>
        <tr>
            <th class="table_right_middle" style="width: 15%">
                Ảnh
            </th>
            <td style="width: 70%;">
                <img style="width: 160px; height:auto"  src='{!! asset($meetingRoom->path_img) !!}'>
            </td>
            <th class="table_right_middle " style="width: 15%;"></th>
        </tr>
        <tr>
            <th class="table_right_middle" style="width: 15%">
                Giá thuê
            </th>
            <td style="width: 70%;"> 
                {!! Form::text('price', old('price', $meetingRoom->price), ['class' => 'form-control currency',  'required', 'disabled']) !!}
            </td>
            <th class="table_right_middle " style="width: 15%;"></th>
        </tr>
        <tr>
            <th class="table_right_middle" style="width: 15%;">
                {!! trans('departments.telephone') !!}
            </th>
            <td>
                {!! Form::text('telephone', old('telephone', $meetingRoom->telephone), ['class' => 'form-control', 'disabled', 'maxlength' => 13, 'required']) !!}
            </td>
        </tr>
        <tr>
            <th class="table_right_middle">
                {!! trans('departments.description') !!}
            </th>
            <td>
                {!! Form::textarea('description', old('tax_code', $meetingRoom->description), ['class' => 'form-control', 'disabled']) !!}
            </td>
        </tr>

        <tr>
            <td class="text-center table_right_middle1" colspan="4">
                <label>
                    {!! Form::checkbox('status', 1, old('status', $meetingRoom->status), ['class' => 'minimal', '']) !!}
                    {!! trans('system.status.active') !!}
                </label>
            </td>
        </tr>

    </table>

    {!! Form::close() !!}
    <div style="margin-left: 45%;padding-top: 2%">
        {!! HTML::link(route( 'admin.meeting-rooms.index' ), trans('system.action.return'), ['class' => 'btn btn-danger btn-flat back','id'=>'cancel']) !!}
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
                callInputMaskInteger();

                $(".select2").select2({width: '100%'});
                $('input[type="checkbox"].minimal').iCheck({
                    checkboxClass: 'icheckbox_minimal-red'
                });
                $('.status, .is_ph').prop('disabled',  true);
            });
        }(window.jQuery);
    </script>
@stop