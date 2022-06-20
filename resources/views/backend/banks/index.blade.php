@extends('backend.master')
@section('title')
    {!! trans('system.action.list') !!} {!! trans('banks.label') !!}
@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('banks.label') !!}
            <small>{!! trans('system.action.list') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.banks.index') !!}">{!! trans('banks.label') !!}</a></li>
        </ol>
    </section>
    <section class="content overlay">
        <div class="box box-default">
            <div class="box-header with-border">
            <h3 class="box-title">{!! trans('system.action.filter') !!}</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                </div>
            </div>
            <div class="box-body">
                {!! Form::open(['url' =>route('admin.banks.index') , 'role'=>'search', 'method' => 'GET']) !!}
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('name', trans('banks.name')) !!}
                                {!! Form::text('name', Request::input('name'), ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                {!! Form::label('status', trans('system.status.label')) !!}
                                {!! Form::select('status', [ -1 => trans('system.dropdown_all'), 0 => trans('system.status.deactive'), 1 => trans('system.status.active') ], Request::input('status'), ['class' => 'form-control select2'])!!}
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                {!! Form::label('is_partner', trans('banks.is_partner')) !!}
                                {!! Form::select('is_partner', [ -1 => trans('system.dropdown_all'), 0 => trans('system.no'), 1 => trans('system.yes') ], Request::input('is_partner'), ['class' => 'form-control select2'])!!}
                            </div>
                        </div>
                        {{-- <div class="col-md-2">
                            <div class="form-group">
                                {!! Form::label('qr_code', trans('banks.qr_code')) !!}
                                {!! Form::select('qr_code', [ -1 => trans('system.dropdown_all'), 0 => trans('system.no'), 1 => trans('system.yes') ], Request::input('qr_code'), ['class' => 'form-control select2'])!!}
                            </div>
                        </div> --}}
                        <div class="col-md-1">
                            <div class="form-group">
                                {!! Form::label('filter', trans('system.action.label')) !!}
                                <button type="submit" class="btn btn-default btn-flat" style="display: block;">
                                    <span class="glyphicon glyphicon-search"></span>&nbsp; {!! trans('system.action.search') !!}
                                </button>
                            </div>
                        </div>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
        <div class="row">
            <div class="col-md-2">
                <a href="{!! route('admin.banks.create') !!}" class='btn btn-primary btn-flat'>
                    <span class="glyphicon glyphicon-plus"></span>&nbsp;{!! trans('system.action.create') !!}
                </a>
            </div>
            <div class="col-md-10 text-right">
                {!! $banks->appends(Request::except('page'))->render() !!}
            </div>
        </div>
        @if (count($banks) > 0)
            <div class="box">
                <div class="box-header">
                    <?php $i = (($banks->currentPage() - 1) * $banks->perPage()) + 1; ?>
                    <div class="form-inline">
                        <div class="form-group">
                            {!! trans('system.show_from') !!} {!! $i . ' ' . trans('system.to') . ' ' . ($i - 1 + $banks->count()) . ' ( ' . trans('system.total') . ' ' . $banks->total() . ' )' !!}
                            | <i>Chú giải: </i>&nbsp;&nbsp;
                            <span class="text-info"><i class="fa fa-eye"></i> {!! trans('system.action.detail') !!} </span>&nbsp;&nbsp;
                            <span class="text-warning"><i class="glyphicon glyphicon-edit"></i> {!! trans('system.action.update') !!} </span>&nbsp;&nbsp;
                            <span class="text-danger"><i class="glyphicon glyphicon-remove"></i> {!! trans('system.action.delete') !!}</span>
                        </div>
                    </div>
                </div>
                <div class="box-body no-padding">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th style="text-align: center; vertical-align: middle;">#</th>
                                <th style="text-align: center; vertical-align: middle;"> {!! trans('banks.gateway') !!} </th>
                                <th style="text-align: center; vertical-align: middle;"> {!! trans('banks.name') !!} </th>
                                <th style="text-align: center; vertical-align: middle;"> {!! trans('banks.logo') !!} </th>
                                <th style="text-align: center; vertical-align: middle;"> {!! trans('banks.type') !!} </th>
                                <th style="text-align: center; vertical-align: middle;"> {!! trans('banks.fee_fixed') !!} </th>
                                <th style="text-align: center; vertical-align: middle;"> {!! trans('banks.online') !!} </th>
                                <th style="text-align: center; vertical-align: middle;"> {!! trans('banks.is_partner') !!} </th>
                                {{-- <th style="text-align: center; vertical-align: middle;"> {!! trans('banks.qr_code') !!} </th> --}}
                                <th style="text-align: center; vertical-align: middle;"> {!! trans('system.updated_at') !!} </th>
                                <th style="text-align: center; vertical-align: middle;"> {!! trans('system.action.label') !!} </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($banks as $item)
                            <tr>
                                <td style="text-align: center; vertical-align: middle;">{!! $i++ !!}</td>
                                <td style="text-align: center; vertical-align: middle;">
                                    <?php $gateway = \App\Gateway::find($item->gateway_id); ?>
                                    {!! is_null($gateway) ? '-' : $gateway->name !!}
                                </td>
                                <td style="vertical-align: middle;">
                                    {!! HTML::link( route('admin.banks.show', $item->id), \App\Helper\HString::modSubstr($item->name, 50), array('class' => '', 'name' => trans('system.view_detail'))) !!}
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    @if( $item->logo )
                                        <img src="{!! asset($item->logo) !!}" height="30px">
                                    @else
                                        <img src="{!! asset('backend/img/404.png') !!}" height="30px">
                                    @endif
                                </td>
                                <td style="text-align: center; vertical-align: middle;">{!! trans('banks.types.' . $item->type) !!}</td>
                                <td style="text-align: right; vertical-align: middle;">
                                    {!! App\Helper\HString::currencyFormat( $item->fee_fixed ) !!} (VND) / {!! App\Helper\HString::decimalFormat( $item->fee_percent ) !!}%
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    @if($item->status == 0)
                                        <span class="label label-danger"><span class='glyphicon glyphicon-remove'></span></span>
                                    @elseif($item->status == 1)
                                        <span class="label label-success"><span class='glyphicon glyphicon-ok'></span></span>
                                    @endif
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    @if($item->is_partner == 0)
                                        <span class="label label-danger"><span class='glyphicon glyphicon-remove'></span></span>
                                    @elseif($item->is_partner == 1)
                                        <span class="label label-success"><span class='glyphicon glyphicon-ok'></span></span>
                                    @endif
                                </td>
                              {{--   <td style="text-align: center; vertical-align: middle;">
                                    @if($item->qr_code == 0)
                                        <span class="label label-danger"><span class='glyphicon glyphicon-remove'></span></span>
                                    @elseif($item->qr_code == 1)
                                        <span class="label label-success"><span class='glyphicon glyphicon-ok'></span></span>
                                    @endif
                                </td> --}}
                                <td style="text-align: center; vertical-align: middle;">{!! date("d/m/Y", strtotime($item->updated_at)) !!}</td>
                                <td style="text-align: center; vertical-align: middle;">
                                    <a href="{!! route('admin.banks.edit', $item->id) !!}" class="btn btn-xs btn-default">
                                        <i class="text-warning glyphicon glyphicon-edit"></i>
                                    </a>
                                    <a href="javascript:void(0)" link="{!! route('admin.banks.destroy', $item->id) !!}" class="btn-confirm-del btn btn-default btn-xs">
                                        <i class="text-danger glyphicon glyphicon-remove"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="alert alert-info" style="margin-top: 20px;"> {!! trans('system.no_record_found') !!}</div>
        @endif
    </section>
@stop
@section('footer')
@stop