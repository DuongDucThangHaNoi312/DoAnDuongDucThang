@extends('backend.master')
@section('title')
    {!! trans('system.action.edit') !!} {!! trans('users.label') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}" />
@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('users.label') !!}
            <small>{!! trans('system.action.edit') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.users.index') !!}">{!! trans('users.label') !!}</a></li>
        </ol>
    </section>
    <section class="content">
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

        {!! Form::open(array('url' => route('admin.users.update', $user->id), 'method' => 'PUT', 'user' => 'form' )) !!}
            <table class='table borderless'>
                <tr>
                    <th class="text-right" style="width: 15%;">
                        {!! trans('users.fullname') !!}
                    </th>
                    <td style="width: 35%;">
                        {!! Form::text("fullname", old("fullname", $user->fullname), ['class' => 'form-control', 'required']) !!}
                    </td>
                    <th class="text-right" style="width: 15%;">
                        {!! trans('users.email') !!}
                    </th>
                    <td>
                        {!! Form::text("email", old("email", $user->email), ['class' => 'form-control', 'required']) !!}
                    </td>
                </tr>
            </table>
            <div class="box box-info">
                <div class="box-body">
                    {!! Form::label('role', trans('users.roles') ) !!}
                    <div class="row">
                        @foreach($roles as $role)
                            <div class="col-md-3">
                                {!! Form::checkbox('roles[]', $role->id, old('roles', isset($uRoles[$role->id]) ? $role->id : ''), []) !!} {!! $role->display_name !!}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class='text-center'>
                        <label>
                            {!! Form::checkbox('status', 1, old('status', $user->activated), [ 'class' => 'minimal' ]) !!}
                            {!! trans('system.status.active') !!}
                        </label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    &nbsp;
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class='form-actions text-center'>
                        {!! HTML::link(route('admin.users.index'), trans('system.action.cancel'), array('class' => 'btn btn-default btn-flat'))!!}
                        {!! Form::submit(trans('system.action.save'), array('class' => 'btn btn-primary btn-flat')) !!}
                    </div>
                </div>
            </div>
        {!! Form::close() !!}
    </section>
@stop
@section('footer')
<script src="{!! asset('assets/backend/plugins/iCheck/icheck.min.js') !!}"></script>
<script>
    !function ($) {
        $(function() {
            $('input[type="checkbox"].minimal').iCheck({
                checkboxClass: 'icheckbox_minimal-blue'
            });
        });
    }(window.jQuery);
</script>
@stop