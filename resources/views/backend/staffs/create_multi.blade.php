@extends('backend.master')
@section('title')
{!! trans('system.action.import') !!} - {!! trans('staffs.label') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}" />
    <link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('staffs.label') !!}
            <small>{!! trans('system.action.import') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.staffs.index') !!}">{!! trans('staffs.label') !!}</a></li>
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
                            <p class="help-block">Tải file theo định dạng sau <a href="{!! route('admin.staffs.download') . '?time=' . time() !!}">tại đây</a></p>
                        </div>
                    </div>
                    <div class="col-md-3">
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
                    <div class="col-md-3">
                        <div class="form-group">
                            <button type="submit" class="btn btn-info btn-flat" onclick="save()">{!! trans('system.action.save') !!}</button>
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
        $.ajax({
            url: "{!! route('admin.staffs.read-bulk') . '?time=' . time() !!}",
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
        // NProgress.start();
        $.ajax({
            url: "{!! route('admin.staffs.save-bulk') !!}",
            data: { data: data },
            type: 'POST',
            datatype: 'json',
            headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
            success: function(res) {
                window.location.href = "{!! route('admin.staffs.index') !!}";
            },
            error: function(obj, status, err) {
                var error = $.parseJSON(obj.responseText);
                toastr.error(error.message, '{!! trans('system.have_an_error') !!}');
            }
        }).always(function() {
            // NProgress.done();
        });
    }
</script>
@stop
