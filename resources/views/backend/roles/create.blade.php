@extends('backend.master')
@section('title')
    {!! trans('system.action.create') !!} {!! trans('roles.label') !!}
@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('roles.label') !!}
            <small>{!! trans('system.action.create') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.roles.index') !!}">{!! trans('roles.label') !!}</a></li>
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

        {!! Form::open(array('url' => route('admin.roles.store'), 'role' => 'form' )) !!}
            <table class='table borderless'>
                <tr>
                    <th class="text-right" style="width: 15%;">
                        {!! trans('roles.name') !!}
                    </th>
                    <td style="width: 35%;">
                        {!! Form::text("name", old("name"), ['class' => 'form-control', 'required']) !!}
                    </td>
                    <th class="text-right" style="width: 15%;">
                        {!! trans('roles.display_name') !!}
                    </th>
                    <td>
                        {!! Form::text("display_name", old("display_name"), ['class' => 'form-control', 'required']) !!}
                    </td>
                </tr>
                <tr>
                    <th class="text-right">
                        {!! trans('roles.description') !!}
                    </th>
                    <td colspan="3">
                        {!! Form::textarea('description', old('description'), array('class' => 'form-control tinymce', 'rows' => 2)) !!}
                    </td>
                </tr>
            </table>
            <div class="row">
                <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-body">
                        {!! Form::label('permission', trans('roles.permissions') ) !!}
                        <?php $count = 1; $i = 1; ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th rowspan="2" style="text-align: center; vertical-align: middle;">{!! trans('system.no.') !!}</th>
                                        <th rowspan="2"  style="text-align: center; vertical-align: middle;">Module</th>
                                        <th colspan="4" style="text-align: center;" >{!! trans('roles.permission') !!}</th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: center; vertical-align: middle;">{!! trans('system.action.read') !!}</th>
                                        <th style="text-align: center; vertical-align: middle;">{!! trans('system.action.create') !!}</th>
                                        <th style="text-align: center; vertical-align: middle;">{!! trans('system.action.update') !!}</th>
                                        <th style="text-align: center; vertical-align: middle;">{!! trans('system.action.delete') !!}</th>
                                    </tr>
                                </thead>
                                <tbody>
{{--                                {{dd($pGroups)}}--}}
                                @foreach($pGroups as $key => $value)
                                        <tr>
                                            <td style="text-align: center; vertical-align: middle;">{!! $count++ !!}</td>
                                            <td style="min-width: 200px;" >{!! trans($key . '.label') !!}</td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                @if(isset($value['read']))
                                                    {!! Form::checkbox('permissions[]', $value['read'], old('permissions'), []) !!}
                                                @endif
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                @if(isset($value['create']))
                                                    {!! Form::checkbox('permissions[]', $value['create'], old('permissions'), []) !!}
                                                @endif
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                @if(isset($value['update']))
                                                    {!! Form::checkbox('permissions[]', $value['update'], old('permissions'), []) !!}
                                                @endif
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                @if(isset($value['delete']))
                                                    {!! Form::checkbox('permissions[]', $value['delete'], old('permissions'), []) !!}
                                                @endif
                                            </td>
                                            @foreach($value as $k => $v)
                                                @if (!in_array($k, ['read', 'update', 'create', 'delete']))
                                                     <td style="text-align: center; vertical-align: middle;">
                                                        {!! Form::checkbox('permissions[]', $v, old('permissions'), []) !!} {!! trans('system.action.' . $k) !!}
                                                    </td>
                                                @endif
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class='form-actions text-center'>
                            {!! HTML::link(route('admin.roles.index'), trans('system.action.cancel'), array('class' => 'btn btn-default btn-flat'))!!}
                            {!! Form::submit(trans('system.action.save'), array('class' => 'btn btn-primary btn-flat', 'onclick' => 'return save();')) !!}
                        </div>
                    </div>
                </div>
                </div>
            </div>
        {!! Form::close() !!}
    </section>
@stop