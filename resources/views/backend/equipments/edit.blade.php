@extends('backend.master')
@section('title')
    {!! trans('system.action.edit') !!} - {!! trans('equipments.label') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}" />
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}" />
@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('equipments.label') !!}
            <small>{!! trans('system.action.edit') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.equipments.index') !!}">{!! trans('equipments.label') !!}</a></li>
        </ol>
    </section>
    {!! Form::open(['url' => route('admin.equipments.update', $equipment->id), 'method' => 'PUT', 'role' => 'form']) !!}
    <table class='table borderless'>
        <tr>
            <th class="table_right_middle " style="width: 15%;">
                Loại
            </th>
            <td>
                <select name="type" class="select2" style="width: 70%" required>
                    <option value="">{!! trans('system.dropdown_all') !!}</option>
                    @foreach (\App\Defines\Equipment::OptionEquipment() as $key => $value)
                    <option value="{!! $key !!}" {!! $key == $equipment->type ? 'selected' : '' !!}>{!! $value !!}</option>
                    @endforeach
                </select>
            </td>
            <th class="table_right_middle " style="width: 15%;">
            </th>
            <td>
            </td>
        </tr>
        <tr>
            <th class="table_right_middle " style="width: 15%;">
                Tên thiết bị
            </th>
            <td>
                {!! Form::text('name', old('name', $equipment->name), ['class' => 'form-control', 'required']) !!}
            </td>
            <th class="table_right_middle " style="width: 15%;">
            </th>
            <td>
            </td>
        </tr>
        {{-- <tr>
            <th class="table_right_middle" style="width: 15%">
                Số lượng
            </th>
            <td style="width: 70%;"> 
                {!! Form::text('number', old('number', $equipment->number), ['class' => 'form-control currency',  'required']) !!}
            </td>
            <th class="table_right_middle " style="width: 15%;"></th>
        </tr> --}}
        <tr>
            <th class="table_right_middle" style="width: 15%">
                Giá thuê
            </th>
            <td style="width: 70%;"> 
                {!! Form::text('price', old('price', $equipment->price), ['class' => 'form-control currency',  'required']) !!}
            </td>
            <th class="table_right_middle " style="width: 15%;"></th>
        </tr>
        <tr>
            <td colspan="4" class="text-center">
                {!! HTML::link(route('admin.equipments.index'), trans('system.action.cancel'), ['class' => 'btn btn-danger btn-flat']) !!}
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
        ! function($) {
            $(function() {
                callInputMaskInteger();

                $(".select2").select2({
                    width: '100%'
                });
                $('input[type="checkbox"].minimal').iCheck({
                    checkboxClass: 'icheckbox_minimal-red'
                });
                $('.company_id').on('ifClicked', function() {
                    $('.company_id').not(this).iCheck('uncheck');

                });

            });
        }(window.jQuery);
    </script>
@stop
