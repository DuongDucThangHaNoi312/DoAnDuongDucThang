@extends('backend.master')
@section('title')
    {!! trans('system.action.list') !!} {!! trans('users.label') !!}
@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('users.label') !!}
            <small>{!! trans('system.action.list') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.users.index') !!}">{!! trans('users.label') !!}</a></li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-2">
                @permission('users.create')
                    <a href="{!! route('admin.users.create') !!}" class='btn btn-primary btn-flat'>
                        <span class="glyphicon glyphicon-plus"></span>&nbsp;{!! trans('system.action.create') !!}
                    </a>
                @endpermission
            </div>
            <div class="col-md-10 text-right">
                {!! $users->appends( Request::except('page') )->render() !!}
            </div>
        </div>
        @if (count($users) > 0)
            <?php $i = (($users->currentPage() - 1) * $users->perPage()) + 1; ?>
            <div class="box">
                <div class="box-header">
                    <div class="form-inline">
                        <div class="form-group">
                            {!! trans('system.show_from') !!} {!! $i . ' ' . trans('system.to') . ' ' . ($i - 1 + $users->count()) . ' ( ' . trans('system.total') . ' ' . $users->total() . ' )' !!}
                            | <i>Chú giải: </i>
                            <span class="text-info"><i class="fa fa-key"></i> {!! trans('users.changepwd') !!} </span>&nbsp;&nbsp;
                            <span class="text-warning"><i class="fa fa-edit"></i> {!! trans('system.action.update') !!} </span>&nbsp;&nbsp;
                            <span class="text-danger"><i class="glyphicon glyphicon-remove"></i> {!! trans('system.action.delete') !!} </span>
                        </div>
                    </div>
                </div>
                <div class="box-body no-padding">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th style="text-align: center; vertical-align: middle;">{!! trans('system.no.') !!}</th>
                                    <th style="vertical-align: middle;"> {!! trans('users.fullname') !!} </th>
                                    <th style="vertical-align: middle;"> {!! trans('users.email') !!} </th>
                                    <th style="text-align: center; vertical-align: middle;">{!! trans('users.role') !!}</th>
                                    <th style="text-align: center; vertical-align: middle;">{!! trans('users.last_login') !!}</th>
                                    <th style="text-align: center; vertical-align: middle;">{!! trans('system.status.label') !!}</th>
                                    @permission('users.update','users.delete')
                                    <th style="text-align: center; vertical-align: middle;"> {!! trans('system.action.label') !!} </th>
                                    @endpermission
                                </tr>
                            </thead>
                            <tbody>
                                <?php $labels = ['success', 'info', 'danger', 'warning', 'default']; ?>
                                @foreach ($users as $item)
                                <tr>
                                    <td style="text-align: center; vertical-align: middle;">{!! $i++ !!}</td>
                                    <td style="vertical-align: middle;">
                                        <a href="{!! route('admin.users.show', $item->id) !!}" title="{!! trans('system.action.detail') !!}">
                                            {!! $item->fullname !!}
                                        </a>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        {!! $item->email !!}
                                    </td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        @foreach($item->roles()->get() as $role)
                                            <span class="label label-{!! $labels[ $role->id % 5 ] !!}">{!! $role->display_name !!}</span>
                                        @endforeach
                                    </td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        @if ($item->last_login)
                                        {!! date("d/m/Y H:i", strtotime($item->last_login)) !!}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        @if($item->activated == 0)
                                        <span class="label label-danger"><span class='glyphicon glyphicon-remove'></span></span>
                                        @elseif($item->activated == 1)
                                        <span class="label label-success"><span class='glyphicon glyphicon-ok'></span></span>
                                        @endif
                                    </td>
                                    @permission('users.update','users.delete')
                                        @if($item->id <> \Auth::guard('admin')->user()->id)
                                            <td style="text-align: center; vertical-align: middle; white-space:nowrap;">
                                                @permission('users.update')
                                                    <a href="{!! route('admin.users.update_password', $item->id) !!}" class="btn btn-default btn-xs"><i class="text-info fa fa-key"></i> </a>
                                                    <a href="{!! route('admin.users.edit', $item->id) !!}" class="btn btn-default btn-xs"><i class="text-warning fa fa-edit"></i> </a>
                                                @endpermission
                                                @permission('users.delete')
                                                    <a href="javascript:void (0)" link="{!! route('admin.users.destroy', $item->id) !!}" class="btn-confirm-del btn btn-default btn-xs"><i class="text-danger glyphicon glyphicon-remove"></i></a>
                                                @endpermission
                                            </td>
                                        @endif
                                    @endpermission
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-info" style="margin-top: 20px;"> {!! trans('system.no_record_found') !!}</div>
        @endif
    </section>
@stop
