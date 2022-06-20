@extends('backend.master')

@section('title')
    {!! trans('system.action.detail') !!} - {!! trans('banks.label') !!}
@stop

@section('content')
    <section class="content-header">
        <h1>
            {!! trans('banks.label') !!}
            <small>{!! trans('system.action.detail') !!}</small>
            @if($bank->status)
                <label class="label label-success">{!! trans('system.status.active') !!}</label>
            @else
                <label class="label label-danger">{!! trans('system.status.deactive') !!}</label>
            @endif
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.banks.index') !!}">{!! trans('banks.label') !!}</a></li>
        </ol>
    </section>
    <table class='table borderless'>
        <tr>
            <th class="text-right" style="width: 20%;">
                {!! trans('banks.name') !!}
            </th>
            <td style="width: 30%;">
                {!! $bank->name !!}
            </td>
            <th class="text-right" style="width: 20%;">
                {!! trans("banks.logo") !!}
            </th>
            <td style="width: 30%;" rowspan="3">
                <div class="fileupload fileupload-new" data-provides="fileupload">
                    <div class="fileupload-preview thumbnail" style="min-height: 150px; max-height: 250px; max-width: 250px;">
                        @if( $bank->logo )
                            <img src="{!! asset($bank->logo) !!}">
                        @endif
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <th class="text-right">
                {!! trans('banks.type') !!}
            </th>
            <td>
                {!! Form::select('type', $types, old('type', $bank->type), array('class' => 'form-control', 'disabled')) !!}
            </td>
        </tr>
        <tr>
            <th class="text-right">
                {!! trans('banks.gateway') !!}
            </th>
            <td>
                {!! Form::select('gateway', $gateways, old('gateway', $bank->gateway_id), array('class' => 'form-control', 'disabled')) !!}
            </td>
        </tr>
        <tr>
            <th class="text-right">
                {!! trans('banks.code') !!}
            </th>
            <td>
                {!! $bank->code !!}
            </td>
        </tr>
        <tr>
            <th class="text-right">
                {!! trans('banks.fee_fixed') !!}
            </th>
            <td>
                {!! App\Helper\HString::currencyFormat( $bank->fee_fixed ) !!} VND
            </td>
            <th class="text-right">
                {!! trans('banks.fee_percent') !!}
            </th>
            <td>
                {!! App\Helper\HString::decimalFormat( $bank->fee_percent ) !!}%
            </td>
        </tr>
        <tr>
            <th class="text-right">
                {!! trans('banks.raw_fee_fixed') !!}
            </th>
            <td>
                {!! App\Helper\HString::currencyFormat( $bank->raw_fee_fixed ) !!} VND
            </td>
            <th class="text-right">
                {!! trans('banks.raw_fee_percent') !!}
            </th>
            <td>
                {!! App\Helper\HString::decimalFormat( $bank->raw_fee_percent ) !!}%
            </td>
        </tr>
    </table>
@stop