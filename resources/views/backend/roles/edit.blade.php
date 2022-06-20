@extends('backend.master')
@section('title'){!! trans('system.action.edit') !!} {!! trans('roles.label') !!}@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('roles.label') !!}
            <small>{!! trans('system.action.edit') !!}</small>
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
        {!! Form::open(['url' => route('admin.roles.update', $role->id), 'method' => 'PUT', 'role' => 'form']) !!}
            <table class='table borderless'>
                <tr>
                    <th class="text-right" style="width: 15%;">
                        {!! trans('roles.name') !!}
                    </th>
                    <td style="width: 35%;">
                        {!! Form::text("name", old("name", $role->name), ['class' => 'form-control', 'required']) !!}
                    </td>
                    <th class="text-right" style="width: 15%;">
                        {!! trans('roles.display_name') !!}
                    </th>
                    <td>
                        {!! Form::text("display_name", old("display_name", $role->display_name), ['class' => 'form-control', 'required']) !!}
                    </td>
                </tr>
                <tr>
                    <th class="text-right">
                        {!! trans('roles.description') !!}
                    </th>
                    <td colspan="3">
                        {!! Form::textarea('description', old('description', $role->description), ['class' => 'form-control tinymce', 'rows' => 1]) !!}
                    </td>
                </tr>
            </table>
            <div class="row">
                <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-body">
                        {!! Form::label('permission', trans('roles.permissions') ) !!}
                        <?php $count = 1; ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th rowspan="2" style="text-align: center; vertical-align: middle; white-space: nowrap;">{!! trans('system.no.') !!}</th>
                                        <th rowspan="2"  style="text-align: center; vertical-align: middle; white-space: nowrap;">{!! trans('roles.module') !!}</th>
                                        <th colspan="4" style="text-align: center; white-space: nowrap;" >{!! trans('roles.permission') !!}</th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: center; vertical-align: middle; white-space: nowrap;">{!! trans('system.action.read') !!}</th>
                                        <th style="text-align: center; vertical-align: middle; white-space: nowrap;">{!! trans('system.action.create') !!}</th>
                                        <th style="text-align: center; vertical-align: middle; white-space: nowrap;">{!! trans('system.action.update') !!}</th>
                                        <th style="text-align: center; vertical-align: middle; white-space: nowrap;">{!! trans('system.action.delete') !!}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pGroups as $key => $value)
                                        <tr>
                                            <td style="text-align: center; vertical-align: middle;">{!! $count++ !!}</td>
                                            <td style="text-align: center; vertical-align: middle; white-space: nowrap;" >{!! trans($key . '.label') !!}</td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                @if(isset($value['read']))
                                                    {!! Form::checkbox('permissions[]', $value['read'], old('permissions', isset($permissions[$value['read']]) ? $value['read'] : ''), []) !!}
                                                @endif
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                @if(isset($value['create']))
                                                    {!! Form::checkbox('permissions[]', $value['create'], old('permissions', isset($permissions[$value['create']]) ? $value['create'] : ''), []) !!}
                                                @endif
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                @if(isset($value['update']))
                                                    {!! Form::checkbox('permissions[]', $value['update'], old('permissions', isset($permissions[$value['update']]) ? $value['update'] : ''), []) !!}
                                                @endif
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                @if(isset($value['delete']))
                                                    {!! Form::checkbox('permissions[]', $value['delete'], old('permissions', isset($permissions[$value['delete']]) ? $value['delete'] : ''), []) !!}
                                                @endif
                                            </td>
                                            @foreach($value as $k => $v)
                                                @if (!in_array($k, ['read', 'update', 'create', 'delete']))
                                                     <td style="text-align: center; vertical-align: middle;">
                                                        {!! Form::checkbox('permissions[]', $v, old('permissions', isset($permissions[$v]) ? $v : ''), []) !!} {!! trans('system.action.' . $k) !!}
                                                    </td>
                                                @endif
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class='form-actions text-center'>
                            {!! HTML::link(route('admin.roles.index'), trans('system.action.cancel'), ['class' => 'btn btn-default btn-flat']) !!}
                            {!! Form::submit(trans('system.action.save'), ['class' => 'btn btn-primary btn-flat', 'onclick' => 'return save();']) !!}
                        </div>
                    </div>
                </div>
                </div>
            </div>
        {!! Form::close() !!}
    </section>
@stop