@extends('backend.master')
@section('title')
    {!! trans('system.action.list') !!} Các khoản điều chỉnh
@stop

@section('head')
<link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/>
<link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
<link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') !!}"/>
<style>
    .form-group .control-label:after { 
        color: #d00;
        content: "*";
        position: absolute;
        margin-left: 8px;
    }
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type="file"] {
        display: block;
    }

    .imageThumb {
        max-height: 100px;
        padding: 1px;
        cursor: pointer;
    }

    .pip {
        display: inline-block;
        margin: 10px 10px 0 0;
    }

    .remove {
        display: block;
        text-align: center;
        cursor: pointer;
    }

    p.text-danger {
        margin: 0 0 -16px 0;
    }

    #cancel,#save{
        margin-bottom: 3%;
    }
</style>

@stop

@section('content')
    <section class="content-header">
        <h1>
            {!! trans('adjustments.label') !!}
            
            <small>{!! trans('system.action.edit') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.adjustments.index') !!}">{!! trans('adjustments.label') !!}</a></li>
        </ol>
    </section>
    <section class="content overlay">
        <div class="box box-primary">
            {!! Form::open(['url' => route('admin.adjustments.update',$adjustment->id), 'role' => 'form', 'method'=>'POST','enctype'=>'multipart/form-data']) !!}
                {{csrf_field()}}
                {{ method_field('PUT') }}
                <div class="row" style="margin-top: 20px">
                    <div class="col-md-8">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6" style="text-align: right">
                                    <label for=""class='control-label'>{!! trans('adjustments.code') !!}</label>
                                </div>
                                <div class="col-md-6">
                                    {!! Form::text('code', old('code',$adjustment->code), ['class' => 'form-control',  'required']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6" style="text-align: right">
                                    <label for=""class='control-label'>{!! trans('adjustments.adjustment_name') !!}</label>
                                </div>
                                <div class="col-md-6">
                                    {!! Form::text('title', old('title',$adjustment->title), ['class' => 'form-control',  'required']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6" style="text-align: right">
                                    <label for=""class='control-label'>{!! trans('adjustments.adjustment_type') !!}</label>
                                </div>
                                <div class="col-md-6">
                                    <td>
                                    {!! Form::select('type', ['' => trans('system.adjustment_type')] + \App\Defines\Adjustment::getAdjustmentTypesForOption(), old('adjustment_type',$adjustment->type), ['class' => 'form-control select2', 'required']) !!}
                                    </td>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6" style="text-align: right">
                                    <label for="" class='control-label'>{!! trans('adjustments.tax_status.label') !!}</label>
                                </div>
                                <div class="col-md-6">
                                    {!! Form::select('status', ['' => trans('system.tax_status')] + \App\Defines\Adjustment::getTaxStatusForOption(), old('status',$adjustment->status), ['class' => 'form-control select2 currency', 'required']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6" style="text-align: right">
                                    <label for="">Số tiền </label>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        {!! Form::text('amount',old('amount',$adjustment->amount), ["class" => "form-control", 'autocomplete' => 'off']) !!}
                                        <div class="input-group-addon price-addon">VNĐ</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group" style="text-align: center; margin-top: 10px;">
                    <label for="">{!! trans('adjustments.active') !!}</label>
                        <input type="checkbox"  name="action" id="action" class="minimal" value="1" {{ $adjustment->action == 1 ? 'checked' : '' }}>
                </div>
                <div class="form-group" style="text-align: center">
                    <a href="{{ route('admin.adjustments.index') }}" class="btn btn-danger btn-sm">Trở lại</a>
                    <button type="submit" class="btn btn-primary btn-sm">Lưu lại</button>
                </div>
            {!! Form::close() !!}
        </div>
    </section>
@stop

@section('footer')

<script src="{!! asset('assets/backend/plugins/iCheck/icheck.min.js') !!}"></script>
<script src="{!! asset('assets/backend/plugins/iCheck/icheck.min.js') !!}"></script>
<script src="{!! asset('assets/backend/plugins/select2/select2.full.min.js') !!}"></script>
<script src="{!! asset('assets/backend/plugins/moment/min/moment-with-locales.min.js') !!}"></script>
<script src="{!! asset('assets/backend/plugins/input-mask/jquery.inputmask.min.js') !!}"></script>
<script src="{!! asset('assets/backend/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') !!}"></script>
<script src="{!! asset('assets/backend/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.vi.min.js') !!}"></script>
<script src="{!! asset('assets/backend/js/contract.js') !!}"></script>
<script type="text/javascript" charset="utf8"
    src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>
<script>
    $(document).ready(function() {
        $(".select2").select2({width: '100%'});
        inputAmount();
    });

    function inputAmount() {    
        $("input[name='amount']").inputmask({
        'alias': 'decimal',
        'groupSeparator': ',',
        'autoGroup': true,
        'min': 0,
        'digits': 0,
        'removeMaskOnSubmit': true
        });
    }

        !function ($) {
            $(function () {
                $('input[type="checkbox"].minimal').iCheck({
                    checkboxClass: 'icheckbox_minimal-red'
                });
            });
        }(window.jQuery);
</script>
@stop