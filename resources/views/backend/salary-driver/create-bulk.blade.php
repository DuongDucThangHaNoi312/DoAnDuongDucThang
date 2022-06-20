@extends('backend.master')
@section('title')
{!! trans('system.action.import') !!} - Nhập lương lái xe
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}" />
    <link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
    <style>
        /* Absolute Center Spinner */
        /* Absolute Center Spinner */
        .loading {
            position: fixed;
            z-index: 999;
            height: 2em;
            width: 2em;
            overflow: visible;
            margin: auto;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
        }
        
        /* Transparent Overlay */
        .loading:before {
            content: '';
            display: block;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.3);
        }
        
        /* :not(:required) hides these rules from IE9 and below */
        .loading:not(:required) {
            /* hide "loading..." text */
            font: 0/0 a;
            color: transparent;
            text-shadow: none;
            background-color: transparent;
            border: 0;
        }
        
        .loading:not(:required):after {
            content: '';
            display: block;
            font-size: 10px;
            width: 1em;
            height: 1em;
            margin-top: -0.5em;
            -webkit-animation: spinner 1500ms infinite linear;
            -moz-animation: spinner 1500ms infinite linear;
            -ms-animation: spinner 1500ms infinite linear;
            -o-animation: spinner 1500ms infinite linear;
            animation: spinner 1500ms infinite linear;
            border-radius: 0.5em;
            -webkit-box-shadow: rgba(0, 0, 0, 0.75) 1.5em 0 0 0, rgba(0, 0, 0, 0.75) 1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) 0 1.5em 0 0, rgba(0, 0, 0, 0.75) -1.1em 1.1em 0 0, rgba(0, 0, 0, 0.5) -1.5em 0 0 0, rgba(0, 0, 0, 0.5) -1.1em -1.1em 0 0, rgba(0, 0, 0, 0.75) 0 -1.5em 0 0, rgba(0, 0, 0, 0.75) 1.1em -1.1em 0 0;
            box-shadow: rgba(0, 0, 0, 0.75) 1.5em 0 0 0, rgba(0, 0, 0, 0.75) 1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) 0 1.5em 0 0, rgba(0, 0, 0, 0.75) -1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) -1.5em 0 0 0, rgba(0, 0, 0, 0.75) -1.1em -1.1em 0 0, rgba(0, 0, 0, 0.75) 0 -1.5em 0 0, rgba(0, 0, 0, 0.75) 1.1em -1.1em 0 0;
        }
        
        /* Animation */
        
        @-webkit-keyframes spinner {
            0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
            }
            100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
            }
        }
        @-moz-keyframes spinner {
            0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
            }
            100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
            }
        }
        @-o-keyframes spinner {
            0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
            }
            100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
            }
        }
        @keyframes spinner {
            0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
            }
            100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
            }
        }
        
        /* Animation */
    </style>
@stop
@section('content')
    <div class="loading"></div>
    <section class="content-header">
        <h1>
            Nhập lương lái xe
            <small>{!! trans('system.action.import') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.drivers.index') !!}">Lương lái xe</a></li>
        </ol>
    </section>
    <section class="content overlay">
        <div class="box box-default">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="excel_file">File excel dữ liệu</label>
                            {!! Form::file('excel_file', [ 'id' => 'excel_file', "accept" => ".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" ]) !!}
                            <p class="help-block">Tải file theo định dạng sau <a href="{!! route('admin.drivers.download') . '?time=' . time() !!}">tại đây</a></p>
                            {{-- <p class="help-block"  style="color: red">Lưu ý: Nhân viên cùng thuộc 1 công ty </p> --}}

                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <label>{!! trans('timekeeping.company') !!} <span class="text-danger">(*)</span></label>
                                    <select name="company_id" id="company" class="companySelect form-control select2">
                                        <option value="" selected="selected">{{ trans('system.dropdown_choice') }}</option>
                                        @foreach (\App\Helpers\GetOption::getCompaniesForOption() as $key => $item)
                                        <option value="{{ $key }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label>Tiêu đề <span class="text-danger">(*)</span></label>
                                    <input type="text" name="title" id="" class="form-control title">
                                </div>
                                <div class="col-md-6">
                                    <label>Loại <span class="text-danger">(*)</span></label>
                                    <select name="type" id="" class="form-control select2 type">
                                        <option value="LUONG_LAI_XE">Lương lái xe</option>
                                        <option value="LUONG_KO_CHAM_VAN_TAY">Lương không chấm vân tay</option>
                                    </select>
                                    <span class="text-danger">
                                        <strong id="year-error"></strong>
                                    </span>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>{!! trans('timekeeping.month') !!} <span class="text-danger">(*)</span></label>
                                            <select name="month" id="" class="form-control select2 month">
                                                @foreach (\App\Define\Timekeeping::getMonth() as $key => $item)
                                                <option value="{{ $key }}" {{ $key == date('m') ? "selected" : '' }}>{{ $item }}</option>
                                                @endforeach
                                            </select>
                                            
                                        </div>
                                        <div class="col-md-6">
                                            <label>{!! trans('timekeeping.year') !!} <span class="text-danger">(*)</span></label>
                                            <select name="year" id="" class="form-control select2 year">
                                                <option value="">{{ trans('system.dropdown_choice') }}</option>
                                                @foreach (\App\Define\Timekeeping::getYear() as $key => $item)
                                                <option value="{{ $key }}" {{ $key == date('Y') ? "selected" : '' }}>{{ $item }}</option>
                                                @endforeach
                                            </select>
                                            
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            {!! Form::label('filter', trans('system.action.label'), ['style' => 'width: 100%;']) !!}
                            <button class="btn btn-success upload btn-flat" onclick="return upload()">
                                <span class="fa fa-upload"></span> {!! trans('system.action.upload') !!}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="box box-info">
            <div class="box-body">
                <div class="table-responsive" style="overflow-y: scroll; max-height: 400px;">
                    <table class="table table-condensed table-bordered" id="data" style="overflow-x: scroll;">
                        <tbody>
                            <tr>
                                <td>Chưa có dữ liệu, kéo trên dưới và sang hai bên (nếu cần) để xem thêm các cột khác...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="box-footer">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <button type="submit" class="btn btn-info btn-flat" onclick="save()">{!! trans('system.action.save') !!}</button>
                            <a href="{{ route('admin.vans.index') }}" class="btn btn-danger btn-flat" >Hủy bỏ</a>
                        </div>
                        
                    </div>
                    
                </div>
            </div>
        </div>
    </section>
@stop
@section('footer')
<script src="{!! asset('assets/backend/plugins/select2/select2.full.min.js') !!}"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
<script>
    !function ($) {
        $('.loading').hide();
        $(function() {
            $(".select2").select2();
        });
    }(window.jQuery);

    function upload() {
        if ( document.getElementById("excel_file").files.length == 0 ) {
            toastr.error("Bạn chưa chọn file", '{!! trans('system.info') !!}');
            return false;
        }
        var myfile = new FormData();
        myfile.append('file', $('#excel_file')[0].files[0]);
        // NProgress.start();
        $('.loading').show();

        $.ajax({
            url: "{!! route('admin.drivers.read-bulk') . '?time=' . time() !!}",
            data: myfile,
            type: 'POST',
            contentType: false,
            processData: false,
            headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
            success: function(res) {
                $("#data tbody").html('').append(res.message);
            },
            error: function(obj, status, err) {
                // NProgress.done();
                var error = $.parseJSON(obj.responseText);
                toastr.error(error.message, '{!! trans('system.have_an_error') !!}');
            }
        }).always(function() {
            // NProgress.done();
            $('.loading').hide();

        });
    }

    function save() {
        var table = $("#data tbody");
        var data = [], have_error = false, i = 0;
        table.find('tr').each(function (row) {
            if (i++) {
                var $tds = $(this).find('td');
                var dRow = [];
                dRow.push(i-1);
                for(var $i = 1; $i < $tds.length; $i++) {
                    if ($tds.eq($i).find("span").text() == '-') {
                        dRow.push('');
                    } else {
                        dRow.push($.trim($tds.eq($i).find("span").text()))
                    }
                }
                data.push(dRow);
            }
        });
        if (have_error) return false;
        if(data.length === 0) {
            toastr.error("Chưa có dữ liệu để thêm mới");
            return false;
        }

        let month = $('.month').val();
        let year = $('.year').val();
        let company_id = $('#company').val();
        let title = $('.title').val();
        let type = $('.type').val();

        if (!company_id) {
            toastr.error("Công ty không được để trống");
            return false;
        }

        if (!title) {
            toastr.error("Tiêu đề không được để trống");
            return false;
        }
        // NProgress.start();
        $('.loading').show();

        $.ajax({
            url: "{!! route('admin.drivers.save-bulk') !!}",
            data: { data: data, 'month': month, 'year': year, 'company_id': company_id, 'title': title, 'type': type },
            type: 'POST',
            datatype: 'json',
            headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
            success: function(res) {
                window.location.href = "{!! route('admin.drivers.index') !!}";
            },
            error: function(obj, status, err) {
                var error = $.parseJSON(obj.responseText);
                toastr.error(error.message, '{!! trans('system.have_an_error') !!}');
            }
        }).always(function() {
            // NProgress.done();
            $('.loading').hide();

        });
    }
</script>
@stop
