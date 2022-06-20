@extends('backend.master')

@section('title')
    {!! trans('users.change_password') !!} - {!! trans('users.label') !!}
@stop

@section('content')
    <section class="content-header">
        <h1>
            {!! trans('users.label') !!}
            <small>{!! trans('users.change_password') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.users.index') !!}">{!! trans('users.label') !!}</a></li>
        </ol>
    </section>
    <section class="content overlay">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">{!! trans('system.action.detail') !!} {!! trans('users.label') !!}</h3>
            </div>
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
                {!! Form::open(['role' => 'form', 'method' => 'PUT', 'url' => route('admin.users.update_password_put', $user->id)]) !!}
                    <table class='table'>
                        <tr>
                            <th class="table_right_middle">
                                {!! trans('users.fullname') !!}
                            </th>
                            <td>
                                {!! $user->fullname !!}
                            </td>
                            <th class="table_right_middle">
                                {!! trans('users.email') !!}
                            </th>
                            <td>
                                {!! $user->email !!}
                            </td>
                        </tr>
                        <tr>
                            <th class="text-right">
                                {!! trans('users.new_password') !!}
                            </th>
                            <td>
                                {!! Form::input('password', 'new_password', '', array('class' => 'form-control', 'maxlength' => 30, 'required')) !!}
                            </td>
                            <th class="text-right">
                                {!! trans('users.re_password') !!}
                            </th>
                            <td>
                                {!! Form::input('password', 're_password', '', array('class' => 'form-control', 'maxlength' => 30, 'required')) !!}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" align="center">
                                {!! HTML::link(route('admin.users.index'), trans('system.action.cancel'), array('class' => 'btn btn-default btn-flat'))!!}
                                {!! Form::submit(trans('system.action.save'), array('class' => 'btn btn-primary btn-flat')) !!}
                            </td>
                        </tr>
                    </table>
                {!! Form::close() !!}
            </div>
        </div>
    </section>
@stop
