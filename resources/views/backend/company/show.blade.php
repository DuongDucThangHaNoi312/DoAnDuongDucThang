@extends('backend.master')
@section('title')
    {!! trans('system.action.edit') !!} - {!! trans('companies.label') !!}
@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('companies.label') !!}
            <small>{!! trans('system.action.detail') !!}</small>
            @if($company->status)
                <label class="label label-success">
                    {!! trans('system.status.active') !!}
                </label>
            @else
                <label class="label label-default">
                    {!! trans('system.status.deactive') !!}
                </label>
            @endif
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.companies.index') !!}">{!! trans('companies.label') !!}</a></li>
        </ol>
    </section>
    {!! Form::open(['url' => route('admin.companies.update', $company->id), 'method' => 'PUT', 'role' => 'form']) !!}
    <table class='table borderless'>
        <tr>
            <th class="table_right_middle " style="width: 15%;">
                {!! trans('companies.name') !!}
            </th>
            <td>
                {!! Form::text('name', old('name',$company->name), ['class' => 'form-control ', 'disabled',  'required']) !!}
            </td>
            <th class="table_right_middle " style="width: 15%;">
            </th>
            <td>
            </td>
        </tr>
        <tr>
            <th class="table_right_middle " style="">
                {!! trans('companies.name_es') !!}
            </th>
            <td>
                {!! Form::text('name_es', old('name_es',$company->name_es), ['class' => 'form-control', 'disabled' ]) !!}
            </td>
            <th></th>
            <td></td>
        </tr>
        <tr>
            <th class="table_right_middle " style="width: 15%;">
                {!! trans('companies.shortened_name') !!}
            </th>
            <td>
                {!! Form::text('shortened_name', old('shortened_name',$company->shortened_name), ['class' => 'form-control', 'required', 'disabled' ]) !!}
            </td>
            <th></th>
            <td></td>
        </tr>

        <tr>
            <th class="table_right_middle" style="width: 15%;">
                {!! trans('companies.telephone') !!}
            </th>
            <td>
                {!! Form::text('telephone', old('telephone',$company->telephone), ['class' => 'form-control', 'disabled', 'maxlength' => 13, 'required']) !!}
            </td>
        </tr>
        <tr>
            <th class="table_right_middle">
                {!! trans('companies.tax_code') !!}
            </th>
            <td>
                {!! Form::text('tax_code',old('tax_code',$company->tax_code), ['class' => 'form-control ', 'disabled']) !!}
            </td>
        </tr>
        <tr>
            <th class="table_right_middle">
                {!! trans('companies.address') !!}
            </th>
            <td>
                {!! Form::text('address', old('address',$company->address), ['class' => 'form-control ', 'disabled']) !!}
            </td>
        </tr>
        <tr>
            <th class="table_right_middle">
                {!! trans('companies.address_es') !!}
            </th>
            <td>
                {!! Form::text('address_es', old('address_es',$company->address_es), ['class' => 'form-control', 'disabled']) !!}
            </td>
        </tr>
        <tr>
            <th class="table_right_middle">
                {!! trans('companies.user') !!}
            </th>
            <td>
                {!! Form::select('user_id', ['' => trans('system.dropdown_choice')]+\App\Define\Company::getUser() ,old('user_id',$company->user_id), ['class' => 'form-control select2 ', 'disabled']) !!}
            </td>
        </tr>
        <tr>
            <th class="table_right_middle">
                {!! trans('companies.qualification') !!}
            </th>
            <td>
                {!! Form::select('qualification_id', ['' => trans('system.dropdown_choice')]+\App\Define\Company::getQualification() ,old('qualification_id',$company->qualification_id), ['class' => 'form-control select2 ', 'disabled']) !!}
            </td>
        </tr>

    </table>

    {!! Form::close() !!}
@stop
