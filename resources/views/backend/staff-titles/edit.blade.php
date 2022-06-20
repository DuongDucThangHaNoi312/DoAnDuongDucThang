@extends('backend.master')
@section('title')
    {!! trans('system.action.create') !!} - {!! trans('staff_titles.label') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') !!}"/>
@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('staff_titles.label') !!}
            <small>{!! trans('system.action.create') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.titles.index') !!}">{!! trans('staff_titles.label') !!}</a></li>
        </ol>
    </section>
        {!! Form::open(['url' => route('admin.titles.update',$titles->id),'method' => 'PUT', 'role' => 'form']) !!}
          <div style="margin-left: 20%;margin-top: 2%">
          <table class='table borderless'>

            <tr >
                <th class="table_right_middle " style="width: 15%;">
                    {!! trans('staff_titles.code') !!}
                </th>
                <td width="50%">
                    {!! Form::text('code', old('code',$titles->code), ['class' => 'form-control', 'required' ]) !!}
                </td>
                <th></th>
                <td></td>
            </tr>
            <tr>
                <th class="table_right_middle" style="width: 15%;">
                    {!! trans('staff_titles.name') !!}
                </th>
                <td>
                    {!! Form::text('name', old('name',$titles->name), ['class' => 'form-control',  'required']) !!}
                </td>
            </tr>
              <tr>
                  <th class="table_right_middle" style="width: 15%;">
                      {!! trans('staff_titles.name_es') !!}
                  </th>
                  <td>
                      {!! Form::text('name_es', old('name_es',$titles->name_es), ['class' => 'form-control',]) !!}
                  </td>
              </tr>
              <tr>
                <th class="table_right_middle" style="width: 15%;">
                    {!! trans('contracts.desc_qualification') !!}
                </th>
                <td>
                    {!! Form::textarea('description', old('description', $titles->description), ['class' => 'form-control', 'rows' => 2]) !!}
                </td>
            </tr>
            <tr>
                <td colspan="3" class="text-center">
                    {!! HTML::link(route( 'admin.titles.index' ), trans('system.action.cancel'), ['class' => 'btn btn-danger btn-flat']) !!}
                    {!! Form::submit(trans('system.action.save'), ['class' => 'btn btn-primary btn-flat']) !!}
                </td>
            </tr>
        </table>
         </div>
        {!! Form::close() !!}
@stop
@section('footer')
@stop
