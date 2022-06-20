@extends('backend.master')
@section('title')
    {!! trans('system.action.create') !!} - {!! trans('banks.label') !!}
@stop
@section('head')
    {!! HTML::style(asset('assets/backend/plugins/jasny/css/jasny-bootstrap.min.css')) !!}
    {!! HTML::style('assets/backend/plugins/iCheck/all.css') !!}
@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('banks.label') !!}
            <small>{!! trans('system.action.create') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.banks.index') !!}">{!! trans('banks.label') !!}</a></li>
        </ol>
    </section>
    @if($errors->count())
        <div class="alert alert-warning alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-warning"></i> {!! trans('messages.error') !!}</h4>
            <ul>
                @foreach($errors->all() as $message)
                <li>{!! $message !!}</li>
                @endforeach
            </ul>
        </div>
    @endif
    {!! Form::open([ 'url' => route('admin.banks.store'), 'role' => 'form', 'files' => true ]) !!}
        <table class='table borderless'>
            <tr>
                <th class="text-right" style="width: 20%;">
                    {!! trans('banks.name') !!}
                </th>
                <td style="width: 30%;">
                    {!! Form::text('name', old('name'), array('class' => 'form-control', 'required', 'maxlength' => 100, 'id' => 'name')) !!}
                </td>
                <th class="text-right" style="width: 20%;">
                    {!! trans("banks.logo") !!}
                </th>
                <td style="width: 30%;" rowspan="3">
                    <div class="fileupload fileupload-new" data-provides="fileupload">
                        <div class="fileupload-preview thumbnail" style="min-height: 150px; max-height: 250px; max-width: 250px;">
                        </div>
                        <div>
                            <span class="btn btn-default btn-file btn-flat">
                                <span class="fileupload-new">
                                    {!! trans('system.action.select_image') !!}
                                </span>
                                {!! Form::file('logo') !!}
                            </span>
                            <a href="#" class="btn btn-danger fileupload-exists btn-flat" data-dismiss="fileupload">
                                {!! trans('system.action.remove') !!}
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th class="text-right">
                    {!! trans('banks.type') !!}
                </th>
                <td>
                    {!! Form::select('type', $types, old('type'), array('class' => 'form-control', 'required', 'maxlength' => 100)) !!}
                </td>
            </tr>
            <tr>
                <th class="text-right">
                    {!! trans('banks.gateway') !!}
                </th>
                <td>
                    {!! Form::select('gateway', [ '' => trans('system.dropdown_choice') ] + $gateways, old('gateway'), array('class' => 'form-control', 'required', 'maxlength' => 100)) !!}
                </td>
            </tr>
            <tr>
                <th class="text-right">
                    {!! trans('banks.code') !!}
                </th>
                <td>
                    {!! Form::text('code', old('code'), array('class' => 'form-control', 'required', 'maxlength' => 15)) !!}
                </td>
            </tr>
            <tr>
                <th class="text-right">
                    {!! trans('banks.fee_fixed') !!}
                </th>
                <td>
                    {!! Form::text('fee_fixed', old('fee_fixed'), array('class' => 'form-control amount', 'required')) !!}
                </td>
                <th class="text-right">
                    {!! trans('banks.fee_percent') !!}
                </th>
                <td>
                    {!! Form::text('fee_percent', old('fee_percent'), array('class' => 'form-control decimal', 'required')) !!}
                </td>
            </tr>
            <tr>
                <th class="text-right">
                    {!! trans('banks.raw_fee_fixed') !!}
                </th>
                <td>
                    {!! Form::text('raw_fee_fixed', old('raw_fee_fixed'), array('class' => 'form-control amount', 'required', 'disabled')) !!}
                </td>
                <th class="text-right">
                    {!! trans('banks.raw_fee_percent') !!}
                </th>
                <td>
                    {!! Form::text('fee_percent', old('fee_percent'), array('class' => 'form-control decimal', 'required', 'disabled')) !!}
                </td>
            </tr>
            <tr>
                <th class="text-center" colspan="4">
                    <label>
                        {!! Form::checkbox('status', 1, old('status', 1), [ 'class' => 'minimal' ]) !!}
                        {!! trans('banks.online') !!}
                    </label>
                   {{--  &nbsp;&nbsp;&nbsp;&nbsp;
                    <label>
                        {!! Form::checkbox('qr_code', 1, old('qr_code', 0), [ 'class' => 'minimal-red' ]) !!}
                        {!! trans('banks.qr_code') !!}
                    </label> --}}
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <label>
                        {!! Form::checkbox('is_partner', 1, old('is_partner', 0), [ 'class' => 'minimal' ]) !!}
                        {!! trans('banks.is_partner') !!}
                    </label>
                </th>
            </tr>
            <tr>
                <td colspan="4" align="center">
                    {!! HTML::link(route( 'admin.banks.index' ), trans('system.action.cancel'), array('class' => 'btn btn-danger btn-flat'))!!}
                    {!! Form::submit(trans('system.action.save'), array('class' => 'btn btn-primary btn-flat')) !!}
                    <span class="label label-danger message"></span>
                </td>
            </tr>
        </table>
    {!! Form::close() !!}
@stop
@section('footer')
<script src="{!! asset('assets/backend/plugins/jasny/js/bootstrap-fileupload.js') !!}"></script>
<script src="{!! asset('assets/backend/plugins/iCheck/icheck.min.js') !!}"></script>
<script src="{!! asset('assets/backend/plugins/input-mask/jquery.inputmask.js') !!}"></script>
<script>
    !function ($) {
        $(function() {
            $('input[type="checkbox"].minimal').iCheck({
                checkboxClass: 'icheckbox_minimal-blue'
            });
            $('input[type="checkbox"].minimal-red').iCheck({
                checkboxClass: 'icheckbox_minimal-red'
            });
            $(".amount").inputmask({'alias': 'decimal', 'digits': 0, 'min': '0', 'max': '99999999', 'groupSeparator': ',', 'autoGroup': true, 'allowMinus': false, 'removeMaskOnSubmit': true});
            $(".decimal").inputmask({'alias': 'decimal', 'groupSeparator': ',', 'autoGroup': true, 'min': 0, 'max': 99.99, 'digits': 2, 'allowMinus': false, 'removeMaskOnSubmit': true})
            if( $("select[name='gateway']").val() ) {
                $.getJSON("{!! route('admin.services.ajaxGetGateway') !!}?gateway_id=" + $("select[name='gateway']").val() + "&type=" + $("select[name='type']").val()).done(function (data) {
                    if(data.error) {
                        $("input[name='fee_fixed']").val('');
                        $("input[name='fee_percent']").val('');
                        $("input[name='raw_fee_fixed']").val('');
                        $("input[name='raw_fee_percent']").val('');
                        alert(data.message);
                    } else {
                        data = data.message;
                        $("input[name='fee_fixed']").val(data.fee_fixed);
                        $("input[name='fee_percent']").val(data.fee_percent);
                        $("input[name='raw_fee_fixed']").val(data.fee_fixed);
                        $("input[name='raw_fee_percent']").val(data.fee_percent);
                    }
                });
            }
            $("select[name='gateway']").change(function(event) {
                if( $(this).val() ) {
                    $.ajax({
                        url: "{!! route('admin.services.ajaxGetGateway') !!}",
                        data: { gateway_id: $(this).val(), type: $("select[name='type']").val() },
                        type: 'POST',
                        datatype: 'json',
                        headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                        success: function(res) {
                            if(res.error) {
                                $("input[name='fee_fixed']").val('');
                                $("input[name='fee_percent']").val('');
                                $("input[name='raw_fee_fixed']").val('');
                                $("input[name='raw_fee_percent']").val('');
                                alert(res.message);
                            } else {
                                res = res.message;
                                $("input[name='fee_fixed']").val(res.fee_fixed);
                                $("input[name='fee_percent']").val(res.fee_percent);
                                $("input[name='raw_fee_fixed']").val(res.fee_fixed);
                                $("input[name='raw_fee_percent']").val(res.fee_percent);
                            }
                        }
                    });
                } else {
                    alert('Bạn cần chọn 1 cổng thanh toán');
                    $("input[name='fee_fixed']").val('');
                    $("input[name='fee_percent']").val('');
                    $("input[name='raw_fee_fixed']").val('');
                    $("input[name='raw_fee_percent']").val('');
                }
            });
        });
    }(window.jQuery);
</script>
@stop