@extends('backend.master')
@section('title')
    {!! trans('system.action.detail') !!} {!! trans('users.label') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}" />
@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('users.label') !!}
            <small>{!! trans('system.action.detail') !!}</small>
            @if($user->activated)
                <label class="label label-success">{!! trans('system.status.active') !!}</label>
            @else
                <label class="label label-danger">{!! trans('system.status.deactive') !!}</label>
            @endif
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.users.index') !!}">{!! trans('users.label') !!}</a></li>
        </ol>
    </section>
    <section class="content">
            <table class='table borderless'>
                <tr>
                    <th class="text-right" style="width: 15%;">
                        {!! trans('users.fullname') !!}
                    </th>
                    <td style="width: 35%;">
                        {!! $user->fullname !!}
                    </td>
                    <th class="text-right" style="width: 15%;">
                        {!! trans('users.email') !!}
                    </th>
                    <td>
                        {!! $user->email !!}
                    </td>
                </tr>
            </table>
            <div class="box box-info">
                <div class="box-body">
                    {!! Form::label('role', trans('users.roles') ) !!}
                    <div class="row">
                        @foreach($roles as $role)
                            <div class="col-md-3">
                                {!! Form::checkbox('roles[]', $role->id, old('roles', isset($uRoles[$role->id]) ? $role->id : ''), ['disabled']) !!} {!! $role->display_name !!}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
    </section>
@stop