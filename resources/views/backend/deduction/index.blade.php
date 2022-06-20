@extends('backend.master')
@section('title')
    {!! trans('system.action.list') !!} Các khoản khấu trừ
@stop
@section('head')

    <style>
        .error {
            width: 100%;
            height: 100px;
            line-height: 100px;
        }

        .text-size {
            font-size: 16px;
        }

        tr td {
            text-align: center;
        }

        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type=number] {
            -moz-appearance: textfield;
        }

        b, strong {
            font-weight: 500;
        }
    </style>
@stop
@section('content')
    <section class="content-header">
        <h1>
            Các khoảng khấu trừ
            <small>{!! trans('system.action.list') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.timekeeping.index') !!}">Các khoảng khấu trừ</a></li>
        </ol>
    </section>
    <section class="content overlay">
        <div class="box">
            <div class="box-body no-padding">
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th style="text-align: center; vertical-align: middle; width: 70px;">{!! trans('system.no.') !!}</th>
                        <th style="text-align: center; vertical-align: middle; width: 200px">{!! trans('timekeeping.company') !!}</th>
                        <th style="text-align: center; vertical-align: middle; width: 200px">Số nhân viên</th>
                        <th style="text-align: center; vertical-align: middle; width: 100px;">{!! trans('system.action.label') !!}</th>
                    </tr>
                    </thead>
                    <tbody>
                        @if (count($companies) > 0)
                            @foreach ($companies as $key => $company)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $company->shortened_name }}</td>
                                    <td>{{ count($company->users) }}</td>
                                    <td>
                                        <a href="{{ route('admin.deductions.create', $company->id) }}" class="btn btn-info btn-xs">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>           
                </table>
                @if (count($companies) == 0)
                <div class="text-center error">
                    <span class="text-size"><i class="fas fa-search"></i> {!! trans('timekeeping.no_data') !!}</span>
                </div>
                @endif
            </div>
        </div>
    </section>
@stop
@section('footer')
    
@stop