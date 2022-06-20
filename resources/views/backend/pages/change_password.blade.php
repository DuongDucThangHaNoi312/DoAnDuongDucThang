@extends('backend.master')
@section('title')
    {!! trans('users.change_password') !!} {!! trans('users.label') !!}
@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('users.label') !!}
            <small>{!! trans('users.update_password') !!}</small>
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
                                {!! trans('users.current_password') !!}
                            </th>
                            <td>
                                {!! Form::input('password', 'current_password', '', ['class' => 'form-control', 'maxlength' => 50]) !!}
                            </td>
                        </tr>
                        <tr>
                            <th class="text-right">
                                {!! trans('users.new_password') !!}
                            </th>
                            <td>
                                {!! Form::input('password', 'new_password', '', ['class' => 'form-control', 'maxlength' => 50]) !!}
                            </td>
                        </tr>
                        <tr>
                            <th class="text-right">
                                {!! trans('users.re_password') !!}
                            </th>
                            <td>
                                {!! Form::input('password', 're_password', '', ['class' => 'form-control', 'maxlength' => 50]) !!}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" align="center">
                                {!! HTML::link(route('admin.home'), trans('system.action.cancel'), ['class' => 'btn btn-default btn-flat']) !!}
                                {!! Form::submit(trans('system.action.save'), ['class' => 'btn btn-primary btn-flat']) !!}
                            </td>
                        </tr>
                    </table>
                {!! Form::close() !!}
            </div>
        </div>
    </section>
@stop