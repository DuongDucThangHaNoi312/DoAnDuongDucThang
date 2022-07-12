@extends('backend.master')
@section('title')
    {!! trans('system.action.create') !!} - {!! trans('staffs.label') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') !!}"/>
    <style>
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
    <?php
        $monthYear = [];
        for ($i = 25+date("Y"); $i >= 2010; $i--) {
            $j=12;
            if ($i == date("Y")) {
                $j = date("m");
            }
            while ($j) {
                $monthYear[str_pad($j, 2, '0', STR_PAD_LEFT) . '/' . $i] = str_pad($j, 2, '0', STR_PAD_LEFT) . '/' . $i;
                $j--;
            }
        }
    ?>
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
    <section class="content-header">
        <h1>
            {!! trans('staffs.label') !!}
            <small>{!! trans('system.action.create') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.staffs.index') !!}">{!! trans('staffs.label') !!}</a></li>
        </ol>
    </section>
    <div class="box">
        <div class="box-body no-padding">
            {!! Form::open(['url' => route('admin.staffs.store'), 'role' => 'form','id'=>'imageForm','enctype'=>'multipart/form-data']) !!}
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <div class="table-responsive">
                            <table class='table borderless'>
                                <tr>
                                    <th class="table_right_middle">
                                        {!! trans('staffs.code') !!}
                                    </th>
                                    <td>
                                        {!! Form::text('code', old('code', $codeNew), ['class' => 'form-control', 'maxlength' => 50, 'required', 'placeholder' => trans('staffs.code_placeholder')]) !!}
                                    </td>
                                    <th class="table_right_middle">
                                        {!! trans('staffs.fullname') !!}
                                    </th>
                                    <td>
                                        {!! Form::text('fullname', old('fullname'), ['class' => 'form-control', 'maxlength' => 50, 'required']) !!}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="table_right_middle">
                                        {!! trans('staffs.id_card_no') !!}
                                    </th>
                                    <td>
                                        {!! Form::text('id_card_no', old('id_card_no'), ['class' => 'form-control', 'placeholder' => trans('staffs.card_no'), 'required']) !!}
                                    </td>
                                    <th class="table_right_middle">
                                        {!! trans('staffs.email') !!}
                                    </th>
                                    <td colspan="4">
                                        <input type="email" name="email" value="{{old('email')}}" id="email" class="form-control" placeholder="Email đăng nhập" />
                                    </td>
                                </tr>
                                <tr>
                                    <th class="table_right_middle">
                                        {!! trans('staffs.issued_on') !!}
                                    </th>
                                    <td>
                                        {!! Form::text('issued_on', old('issued_on'), ['class' => 'form-control datepicker', 'maxlength' => 15, 'required','autocomplete' => 'off']) !!}
                                    </td>
                                    <th class="table_right_middle">
                                        {!! trans('staffs.date_of_birth') !!}
                                    </th>
                                    <td>
                                        {!! Form::text('date_of_birth', old('date_of_birth'), ['class' => 'form-control datepicker', 'maxlength' => 15, 'required','autocomplete' => 'off']) !!}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="table_right_middle">
                                        {!! trans('staffs.issued_at') !!}
                                    </th>
                                    <td>
                                        {!! Form::text('issued_at', old('issued_at'), ['class' => 'form-control', 'required']) !!}
                                    </td>
                                    <th class="table_right_middle">
                                        {!! trans('staffs.genders.label') !!}
                                    </th>
                                    <td>
                                        {!! Form::select('gender', ['' => trans('system.gender')] + \App\Defines\Staff::getGendersForOption(), old('gender'), ['class' => 'form-control select2', 'required']) !!}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="table_right_middle">
                                        {!! trans('staffs.marital_status.label') !!}
                                    </th>
                                    <td>
                                        {!! Form::select('marital_status', ['' => trans('system.dropdown_choice')] + \App\Defines\Staff::getMaritalStatusForOption(), old('marital_status'), ['class' => 'form-control select2']) !!}
                                    </td>
                                    <th class="table_right_middle">
                                        {!! trans('staffs.ethnicity') !!}
                                    </th>
                                    <td>
                                        {!! Form::text('ethnicity', old('ethnicity'), ['class' => 'form-control']) !!}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="table_right_middle">
                                        {!! trans('staffs.addresses') !!}
                                    </th>
                                    <td>
                                        {!! Form::text('addresses', old('addresses'), ['class' => 'form-control', 'required']) !!}
                                    </td>
                                    <th class="table_right_middle">
                                        {!! trans('staffs.domicile') !!}
                                    </th>
                                    <td>
                                        {!! Form::text('domicile', old('domicile'), ['class' => 'form-control', 'required']) !!}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="table_right_middle">
                                        {!! trans('staffs.nationality') !!}
                                    </th>
                                    <td>
                                        {!! Form::text('nationality', old('nationality'), ['class' => 'form-control', 'required']) !!}
                                    </td>
                                    <th class="table_right_middle">
                                        {!! trans('staffs.phone') !!}
                                    </th>
                                    <td>
                                        {!! Form::number('phone', old('phone'), ['class' => 'form-control', 'required']) !!}
                                    </td>
                                </tr>
                                <tr>
                                    {{-- <th class="table_right_middle">
                                        {!! trans('staffs.image') !!}
                                    </th>
                                    <td >
                                        <div class="field" align="left">
                                            <input type="file" id="files" name="image[]" multiple />
                                        </div>
                                        @if($errors->has('image'))
                                            <span class="text-danger">{{ $errors->first('image') }}</span>
                                        @endif
                                    </td> --}}
                                    <th class="table_right_middle">
                                        Văn phòng
                                    </th>
                                    <td>
                                        <select name="deparment" class="select2">
                                            <option >{!! trans('system.dropdown_all') !!}</option>
                                            @foreach ($departments as $key => $value)
                                            <option value="{!! $key !!}"  {!! old('deparment') == $key ? 'selected' : ''  !!}>{!! $value !!}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-center">
                                        <label>
                                            {!! Form::checkbox('admin', 1, old('admin', 0), [ 'class' => 'minimal' ]) !!}
                                            {!! 'Admin' !!}
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" colspan="4">
                                        {!! HTML::link(route( 'admin.staffs.index' ), trans('system.action.cancel'), ['class' => 'btn btn-danger btn-flat','id'=>'cancel']) !!}
                                        {!! Form::submit(trans('system.action.save'), ['class' => 'btn btn-primary btn-flat','id'=>'save']) !!}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
@stop
@section('footer')
    <script src="{!! asset('assets/backend/plugins/iCheck/icheck.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/select2/select2.full.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/moment/min/moment-with-locales.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/input-mask/jquery.inputmask.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.vi.min.js') !!}"></script>
    <script>
        $(document).ready(function() {
            if (window.File && window.FileList && window.FileReader) {
                $("#files").on("change", function(e) {
                    var files = e.target.files,
                        filesLength = files.length;
                    for (var i = 0; i < filesLength; i++) {
                        var f = files[i]
                        var fileReader = new FileReader();
                        fileReader.onload = (function(e) {
                            var file = e.target;
                            $("<span class=\"pip\">" +
                                "<img class=\"imageThumb\" src=\"" + e.target.result + "\" title=\"" + file.name + "\"/>" +
                                "<br/><i class=\"text-danger remove glyphicon glyphicon-remove\"></i>" +
                                "</span>").insertAfter("#files");
                            $(".remove").click(function(){
                                $(this).parent(".pip").remove();
                            });

                        });
                        fileReader.readAsDataURL(f);
                    }
                });
            } else {
                alert("Your browser doesn't support to File API")
            }
            $(".select2").select2({width: '100%'});
            $('.datepicker').datepicker({
                format: 'dd/mm/yyyy',
                autoclose: true,
                endDate:'-1d',
                language: 'vi',
            });
            $('.datepicker1').datepicker({
                format: 'dd/mm/yyyy',
                todayHighlight: true,
                autoclose: true,
                language: 'vi',
            });
            $(".select2").select2({width: '100%'});

        });
    </script>
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
