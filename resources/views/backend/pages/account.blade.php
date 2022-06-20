@extends('backend.master')
@section('title')
    Tài khoản {!! trans('users.label') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}" />
@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('users.label') !!}
            <small>Tài khoản</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
        </ol>
    </section>
    <section class="content overlay">
        <div class="box box-default">
            <div class="box-body">
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
                {!! Form::open(['role' => 'form']) !!}
                    <table class='table borderless' style="width: 60%;">
                        <tr>
                            <th class="text-right">
                                {!! trans('users.fullname') !!}
                            </th>
                            <td>
                                {!! Form::text('fullname', old('fullname', $user->fullname), array('class' => 'form-control', 'maxlength' => 30)) !!}
                            </td>
                        </tr>
                        <tr>
                            <th class="text-right">
                                {!! trans('users.email') !!}
                            </th>
                            <td>
                                {!! Form::text('email', old('email', $user->email), array('class' => 'form-control', 'disabled')) !!}
                            </td>
                        </tr>
                        <tr>
                            <th class="text-center" colspan="2">
                                <label>
                                    {!! Form::checkbox('menu_is_collapse', 1, old('menu_is_collapse', $user->menu_is_collapse), [ 'class' => 'minimal' ]) !!}
                                    {!! trans('users.menu_is_collapse') !!}
                                </label>
                            </th>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-center">
                                {!! HTML::link(route('admin.home'), trans('system.action.cancel'), array('class' => 'btn btn-default'))!!}
                                {!! Form::submit(trans('system.action.save'), array('class' => 'btn btn-primary')) !!}
                            </td>
                        </tr>
                    </table>
                {!! Form::close() !!}
            </div>
        </div>
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