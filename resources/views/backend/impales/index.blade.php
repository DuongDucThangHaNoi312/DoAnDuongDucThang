@extends('backend.master')
@section('title')
    {!! trans('system.action.list') !!} Thưởng phạt
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/daterangepicker/daterangepicker.css') !!}" />
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
@stop
@section('content')
    <section class="content-header">
        <h1>
            Danh mục thưởng phạt
            <small>{!! trans('system.action.list') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.payoffs.index') !!}">Danh mục thưởng phạt</a></li>
        </ol>
    </section>
    <section class="content overlay">
        <div class="row">
            <div class="col-md-2">
               <a href="{!! route('admin.payoffs.create') !!}" class='btn btn-primary btn-flat'>
                   <span class="glyphicon glyphicon-plus"></span>&nbsp;{!! trans('system.action.create') !!}
               </a>
            </div>
            <div class="col-md-10 text-right">
            </div>
        </div>
        @if (count($allowanceCategories) > 0)
            <?php $labels = ['default', 'success', 'info', 'danger', 'warning']; ?>
            <div class="box">
                
                <div class="box-body no-padding">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('system.no.') !!}</th>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('allowance_categories.name') !!}</th>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('allowance_categories.has_kpi') !!}</th>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('allowance_categories.is_social_security') !!}</th>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('allowance_categories.is_exemp') !!}</th>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('allowance_categories.ot') !!}</th>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('system.status.label') !!}</th>
                                <th style="text-align: center; vertical-align: middle; white-space: nowrap;">{!! trans('allowance_categories.created_at') !!}</th>
                                <th style="text-align: center; vertical-align: middle;">{!! trans('system.action.label') !!}</th>
                            </tr>
                            </thead>
                            <tbody>
                            
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-info">{!! trans('system.no_record_found') !!}</div>
        @endif
    </section>
@stop
@section('footer')
    
@stop