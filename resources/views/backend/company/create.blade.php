@extends('backend.master')
@section('title')
    {!! trans('system.action.create') !!} - {!! trans('companies.label') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('companies.label') !!}
            <small>{!! trans('system.action.create') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.companies.index') !!}">{!! trans('companies.label') !!}</a></li>
        </ol>
    </section>

    {!! Form::open(['url' => route('admin.companies.store'), 'method'=>'POST']) !!}
    <table class='table borderless'>
        <tr>
            <th class="table_right_middle " style="width: 15%">
                {!! trans('companies.name') !!}
            </th>
            <td>
                {!! Form::text('name', old('name'), ['class' => 'form-control', 'required' ]) !!}
            </td>
            <th></th>
            <td></td>
        </tr>
        <tr>
            <th class="table_right_middle " style="">
                {!! trans('companies.name_es') !!}
            </th>
            <td>
                {!! Form::text('name_es', old('name_es'), ['class' => 'form-control' ]) !!}
            </td>
            <th></th>
            <td></td>
        </tr>
        <tr>
            <th class="table_right_middle " style="">
                {!! trans('companies.shortened_name') !!}
            </th>
            <td>
                {!! Form::text('shortened_name', old('shortened_name'), ['class' => 'form-control', 'required' ]) !!}
            </td>
            <th></th>
            <td></td>
        </tr>

        <tr>
            <th class="table_right_middle" style="">
                {!! trans('companies.telephone') !!}
            </th>
            <td>
                {!! Form::text('telephone', old('telephone'), ['class' => 'form-control', 'maxlength' => 13,  'required']) !!}
            </td>
        </tr>
        <tr>
            <th class="table_right_middle" style="">
                {!! trans('companies.fax') !!}
            </th>
            <td>
                {!! Form::text('fax', old('fax'), ['class' => 'form-control', 'maxlength' => 100,]) !!}
            </td>
        </tr>

        <tr>
            <th class="table_right_middle">
                {!! trans('companies.tax_code') !!}
            </th>
            <td>
                {!! Form::text('tax_code',old('tax_code'), ['class' => 'form-control', 'required']) !!}
            </td>
        </tr>
        <tr>
            <th class="table_right_middle">
                {!! trans('companies.address') !!}
            </th>
            <td>
                {!! Form::text('address', old('address'), ['class' => 'form-control', 'required']) !!}
            </td>
        </tr>
        <tr>
            <th class="table_right_middle">
                {!! trans('companies.address_es') !!}
            </th>
            <td>
                {!! Form::text('address_es', old('address_es'), ['class' => 'form-control']) !!}
            </td>
        </tr>
        <tr>
            <th class="table_right_middle">
                {!! trans('companies.user') !!}
            </th>
            <td>
                {!! Form::select('user_id', ['' => trans('system.dropdown_choice')]+\App\Define\Company::getUser() ,old('user_id'), ['class' => 'form-control select2 ']) !!}
            </td>
        </tr>
        <tr>
            <th class="table_right_middle">
                {!! trans('companies.qualification') !!}
            </th>
            <td>
                {!! Form::select('qualification_id', ['' => trans('system.dropdown_choice')]+\App\Define\Company::getQualification() ,old('qualification_id'), ['class' => 'form-control select2 ']) !!}
            </td>
        </tr>
        <tr>
            <td class="text-center table_right_middle1" colspan="4">
                <label>
                    {!! Form::checkbox('status', 1, old('status', 1), [ 'class' => 'minimal' ]) !!}
                    {!! trans('system.status.active') !!}
                </label>
            </td>
        </tr>
        <tr>
            <td colspan="4" class="text-center">
                {!! HTML::link(route( 'admin.companies.index' ), trans('system.action.cancel'), ['class' => 'btn btn-danger btn-flat']) !!}
                {!! Form::submit(trans('system.action.save'), ['class' => 'btn btn-primary btn-flat']) !!}
            </td>
        </tr>
    </table>
    {!! Form::close() !!}
@stop
@section('footer')
    <script src="{!! asset('assets/backend/plugins/iCheck/icheck.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/select2/select2.full.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/moment/min/moment-with-locales.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/input-mask/jquery.inputmask.min.js') !!}"></script>
    <script>
        !function ($) {
            $(function () {
                $(".select2").select2({
                    width: '100%',
                    placeholder: '  {!! trans('system.dropdown_choice') !!} '
                });
                $('input[type="checkbox"].minimal').iCheck({
                    checkboxClass: 'icheckbox_minimal-red'
                });
            });
        }(window.jQuery);
    </script>
@stop