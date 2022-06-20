@extends('backend.master')

@section('title')
    {!! trans('system.action.detail') !!} - {!! trans('static_pages.label') !!}
@stop

@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}" />
@stop

@section('content')
    <section class="content-header">
        <h1>
            {!! trans('static_pages.label') !!}
            <small>{!! trans('system.action.detail') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.static-pages.index') !!}">{!! trans('static_pages.label') !!}</a></li>
        </ol>
    </section>
    <table class='box table table-bordered'>
        <tr>
            <th class="text-right">
                {!! trans('static_pages.title') !!}
            </th>
            <td>
                {!! $news->title !!}
            </td>
        </tr>
        <tr>
            <th class="text-right">
                {!! trans('static_pages.description') !!}
            </th>
            <td>
                {!! $news->description !!}
            </td>
        </tr>
        <tr>
            <th class="text-right">
                {!! trans('system.status.active') !!}
            </th>
            <td>
                @if($news->status == 0)
                    <span class="label label-danger"><span class='glyphicon glyphicon-remove'></span></span>
                @elseif($news->status == 1)
                    <span class="label label-success"><span class='glyphicon glyphicon-ok'></span></span>
                @endif
            </td>
        </tr>
    </table>
@stop