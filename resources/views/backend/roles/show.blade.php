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
        <table class='box table table-bordered'>
            <tr>
                <th class="text-right" style="width: 15%;">
                    {!! trans('roles.name') !!}
                </th>
                <td style="width: 35%;">
                    {!! $role->name !!}
                </td>
                <th class="text-right" style="width: 15%;">
                    {!! trans('roles.display_name') !!}
                </th>
                <td>
                    {!! $role->display_name !!}
                </td>
            </tr>
            <tr>
                <th class="text-right">
                    {!! trans('roles.description') !!}
                </th>
                <td colspan="3">
                    {!! $role->description !!}
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
                                    <th colspan="4" style="text-align: center;" >{!! trans('roles.permission') !!}
                                    </th>
                                </tr>
                                <tr>
                                    <th style="text-align: center; vertical-align: middle;">{!! trans('system.action.read') !!}</th>
                                    <th style="text-align: center; vertical-align: middle;">{!! trans('system.action.create') !!}</th>
                                    <th style="text-align: center; vertical-align: middle;">{!! trans('system.action.update') !!}</th>
                                    <th style="text-align: center; vertical-align: middle;">{!! trans('system.action.delete') !!}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pGroups as $key => $value)
                                    <tr>
                                        <td style="text-align: center; vertical-align: middle;">{!! $count++ !!}</td>
                                        <td style="min-width: 200px;" >{!! trans($key . '.label') !!}</td>
                                        <td style="text-align: center; vertical-align: middle;">
                                            @if(isset($value['read']) && isset($permissions[$value['read']]))
                                                <input type="checkbox" name="permissions[]" disabled checked />
                                            @endif
                                        </td>
                                        <td style="text-align: center; vertical-align: middle;">
                                            @if(isset($value['create']) && isset($permissions[$value['create']]))
                                                <input type="checkbox" name="permissions[]" disabled checked />
                                            @endif
                                        </td>
                                        <td style="text-align: center; vertical-align: middle;">
                                            @if(isset($value['update']) && isset($permissions[$value['update']]))
                                                <input type="checkbox" name="permissions[]" disabled checked />
                                            @endif
                                        </td>
                                        <td style="text-align: center; vertical-align: middle;">
                                            @if(isset($value['delete']) && isset($permissions[$value['delete']]))
                                                <input type="checkbox" name="permissions[]" disabled checked />
                                            @endif
                                        </td>
                                        @foreach($value as $k => $v)
                                            @if (!in_array($k, ['read', 'update', 'create', 'delete']))
                                                 <td style="text-align: center; vertical-align: middle;">
                                                    <input type="checkbox" name="permissions[]" disabled @if(isset($v) && isset($permissions[$v])) checked @endif /> {!! trans('system.action.' . $k) !!}
                                                </td>
                                            @endif
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </section>
@stop