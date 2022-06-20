@extends('backend.master')
@section('title')
    {!! trans('system.action.edit') !!} - {!! trans('allowance_categories.label') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') !!}"/>
@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('allowance_categories.label') !!}
            <small>{!! trans('system.action.edit') !!}</small>
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
        {!! Form::open(['url' => route('admin.allowance-categories.update', $allowanceCategory->id), 'method' => 'put', 'role' => 'form']) !!}
        <div class="box-body">
            {!! Form::label(trans('allowance_categories.name') ) !!}
            {!! Form::text('name', old('name', $allowanceCategory->name), ['class' => 'form-control', 'required']) !!}
            <div align="center" style="margin-top: 30px;">
                {!! Form::checkbox('status', 1, old('status', $allowanceCategory->status), ['class' => 'minimal-red']) !!}
                {!! trans('system.status.active') !!}
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
    <script>
        !function ($) {
            $(function() {
                $('input[type="checkbox"].minimal-red').iCheck({
                    checkboxClass: 'icheckbox_minimal-red'
                });
            });
        }(window.jQuery);
    </script>
@stop
