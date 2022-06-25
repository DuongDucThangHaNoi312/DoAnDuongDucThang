@extends('backend.master')
@section('title')
    {!! trans('system.action.detail') !!} - {!! trans('services.label') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('services.label') !!}
            <small>{!! trans('system.action.detail') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.services.index') !!}">{!! trans('services.label') !!}</a></li>
        </ol>
    </section>
    {!! Form::open(['url' => route('admin.services.update', $service->id), 'method' => 'PUT', 'role' => 'form']) !!}
    <table class='table borderless'>
        <tr>
            <th class="table_right_middle" style="width: 15%">
                Tên dịch vụ
            </th>
            <td>
                {!! Form::text('name', old('', $service->name), ['class' => 'form-control',  'required', 'disabled']) !!}
            </td>
            <th class="table_right_middle " style="width: 15%;"></th>
        </tr>
        <tr>
            <th class="table_right_middle" style="width: 15%">
                Đơn giá
            </th>
            <td style="width: 70%;"> 
                {!! Form::text('price', old('', $service->price), ['class' => 'form-control currency', 'disabled', 'required']) !!}
            </td>
            <th class="table_right_middle " style="width: 15%;"></th>
        </tr>
        
    </table>

    {!! Form::close() !!}
    <div style="margin-left: 45%;padding-top: 2%">
        {!! HTML::link(route( 'admin.services.index' ), trans('system.action.return'), ['class' => 'btn btn-danger btn-flat back','id'=>'cancel']) !!}
    </div>
@stop
@section('footer')
<script src="{!! asset('assets/backend/plugins/iCheck/icheck.min.js') !!}"></script>
<script src="{!! asset('assets/backend/plugins/select2/select2.full.min.js') !!}"></script>
<script src="{!! asset('assets/backend/plugins/moment/min/moment-with-locales.min.js') !!}"></script>
<script src="{!! asset('assets/backend/plugins/input-mask/jquery.inputmask.min.js') !!}"></script>
<script>
    !function ($) {
            callInputMaskInteger();
            $(function () {
                $(".select2").select2({width: '100%'});
                $('input[type="checkbox"].minimal').iCheck({
                    checkboxClass: 'icheckbox_minimal-red'
                });
            });
        }(window.jQuery);
    </script>
@stop