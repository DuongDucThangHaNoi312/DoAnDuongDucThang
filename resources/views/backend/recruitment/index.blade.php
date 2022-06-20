@extends('backend.master')
@section('title')
    {!! trans('system.action.list') !!} {!! trans('departments.label') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('recruitment.label') !!}
            <small>{!! trans('system.action.list') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.recruitment.index') !!}">{!! trans('recruitment.label') !!}</a></li>
        </ol>
    </section>
    <section class="content overlay">
        <div class="box box-default">
            <div class="box-header with-bconsumer">
                <h3 class="box-title">{!! trans('system.action.filter') !!}</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                </div>
            </div>
            <div class="box-body">
                {!! Form::open([ 'url' => route('admin.recruitment.index'), 'method' => 'GET', 'role' => 'search' ]) !!}
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('name', trans('recruitment.name')) !!}
                            {!! Form::text('name', Request::input('name'), ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            {!! Form::label('company_id', trans('recruitment.company_id')) !!}
                            {!! Form::select('company_id',['' => trans('system.dropdown_choice')]+ \App\Define\Recruitment::getCompanyNamesForOption() ,'', ['class' => 'form-control select2']) !!}
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            {!! Form::label('title_id', trans('recruitment.title_id')) !!}
                            {!! Form::select('title_id',['' => trans('system.dropdown_choice')]+ \App\Define\Recruitment::getTitleNamesForOption() ,'', ['class' => 'form-control select2']) !!}
                        </div>
                    </div>
                    <div class="col-md-2 ">
                        <div class="form-group">
                            {!! Form::label('filter', trans('system.action.label'), ['style' => 'display: block;']) !!}
                            <button type="submit" class="btn btn-primary btn-flat" style="display: block;">
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
                <a href="{!! route('admin.recruitment.create') !!}" class='btn btn-primary btn-flat'>
                    <span class="glyphicon glyphicon-plus"></span>&nbsp;{!! trans('system.action.create') !!}
                </a>
            </div>
            <div class="col-md-10 text-right">
            </div>
        </div>

        @if ((count($recruitment)) > 0)
            <div class="box">
                <div class="box-header">
                    <?php $i = (($recruitment->currentPage() - 1) * $recruitment->perPage()) + 1; ?>
                </div>
                <div class="box-body no-padding">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th style="text-align: center; vertical-align: middle;">{!! trans('system.no.') !!}</th>
                            <th style="text-align: center; vertical-align: middle;">{!! trans('recruitment.name') !!}
                            <th style="text-align: center; vertical-align: middle;">{!! trans('recruitment.telephone') !!}
                            <th style="text-align: center; vertical-align: middle;">{!! trans('recruitment.company_id') !!}</th>
                            <th style="text-align: center; vertical-align: middle;">{!! trans('recruitment.title_id') !!}</th>
                            <th style="text-align: center; vertical-align: middle;">{!! trans('system.action.label') !!}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($recruitment as $item)
                            <tr>
                                <td style="text-align: center; vertical-align: middle;">{!! $i++ !!}</td>
                                <td style="text-align: center; vertical-align: middle;">
                                    <a href="{!! route('admin.recruitment.show', $item->id) !!}">{!! $item->name !!}</a><br/>
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    {!! $item->telephone !!}
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    {!! \App\Models\Company::find($item->company_id)->shortened_name !!}
                                </td>
                                <td style="text-align: center; vertical-align: middle;">
                                    {!! App\Position::find($item->title_id)->name !!}
                                </td>
                                <td style="text-align: center; vertical-align: middle; white-space: nowrap;">
                                    <a href="{!! route('admin.recruitment.show', $item->id) !!}"
                                       class="btn-detail btn btn-default btn-xs"
                                       data-toggle="tooltip" data-placement="top" title="{!! trans('system.action.detail') !!}">
                                        <i class="text-info glyphicon glyphicon-eye-open"></i>
                                    </a>
                                    <a href="{!! route('admin.recruitment.edit', $item->id) !!}"
                                       class="btn btn-xs btn-default"><i
                                                data-toggle="tooltip" data-placement="top"  title="{!! trans('system.action.update') !!}"          class="text-warning glyphicon glyphicon-edit"></i></a>
                                    <a href="javascript:void(0)"
                                       data-toggle="tooltip" data-placement="top" title="{!! trans('system.action.delete') !!} "   link="{!! route('admin.recruitment.destroy', $item->id) !!}"
                                       class="btn-confirm-del btn btn-default btn-xs">
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
            <div class="alert alert-info">{!! trans('system.no_record_found') !!}</div>
        @endif
    </section>
@stop
@section('footer')
    <script src="{!! asset('assets/backend/plugins/iCheck/icheck.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/select2/select2.full.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/moment/min/moment-with-locales.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/input-mask/jquery.inputmask.min.js') !!}"></script>
    <script>
        !function ($) {
            $(function () {
                $(".select2").select2({width: '100%'});
            });
        }(window.jQuery);
    </script>
@stop