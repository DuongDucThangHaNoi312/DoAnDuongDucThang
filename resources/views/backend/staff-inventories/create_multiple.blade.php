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
            <li><a href="{!! route('admin.prices.index') !!}">{!! trans('inventories.label') !!}</a></li>
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
                            <p class="help-block">Tải file theo định dạng sau <a href="{!! route('admin.inventories.download') !!}">tại đây</a></p>
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
                        <thead>
                        <tr>
                            <th style="text-align: center; vertical-align: middle; white-space: nowrap; width: 5%;"> {!! trans('system.no.') !!} </th>
                            <th style="text-align: center; vertical-align: middle; white-space: nowrap; width: 20%;">{!! trans('staffs.fullname') !!}</th>
                            <th style="text-align: center; vertical-align: middle; white-space: nowrap; width: 15%;">{!! trans('staffs.code') !!}</th>
                            <th style="text-align: center; vertical-align: middle; white-space: nowrap; width: 15%;">{!! trans('staffs.addresses') !!}</th>
                            <th style="text-align: center; vertical-align: middle; white-space: nowrap; width: 15%;">{!! trans('staffs.genders.label') !!}</th>
                            <th style="text-align: center; vertical-align: middle; white-space: nowrap;">{!! trans('staffs.date_of_birth') !!}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td align="center" colspan="6">Chưa có dữ liệu, kéo trên dưới và sang hai bên (nếu cần) để xem thêm các cột khác...</td>
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
            box1 = new ajaxLoader('body', {classOveride: 'blue-loader', bgColor: '#000', opacity: '0.3', type_image: 'ajax_loader_circle'});
            $.ajax({
                url: "{!! route('admin.prices.read-bulk') !!}",
                data: myfile,
                type: 'POST',
                contentType: false,
                processData: false,
                headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                success: function(res) {
                    $("#data tbody").html('').append(res.message);
                },
                error: function(obj, status, err) {
                    if(box1) box1.remove();
                    var error = $.parseJSON(obj.responseText);
                    toastr.error(error.message, '{!! trans('system.have_an_error') !!}');
                }
            }).always(function() {
                if(box1) box1.remove();
            });
        }

        function save() {
            var table = $("#data tbody");
            var data = [], have_error = false, categories = {};
            table.find('tr').each(function (row) {
                var $tds = $(this).find('td');
                var dRow = [];
                var stt = $.trim($tds.eq(0).html()),
                    sku = $.trim($tds.eq(1).find("span").text()),
                    ipo = $tds.eq(2).find("span").text(),
                    dropship = $tds.eq(3).find("span").text(),
                    ctv = $tds.eq(4).find("span").text(),
                    cus = $tds.eq(5).find("span").text(),
                    note = $.trim($tds.eq(6).find("span").text());
                if (sku == "" || sku.length > 100) {
                    toastr.error("Vui lòng kiểm tra lại `SKU` dòng số: " + stt);
                    have_error = true;
                    return false;
                }
                // if (isNaN(ipo) || ipo < 1 || ipo > 999999999) {
                //     toastr.error("Vui lòng kiểm tra lại `Giá niêm yết` dòng số: " + stt);
                //     have_error = true;
                //     return false;
                // }
                if (ipo%100 != 0) {
                    toastr.error("`Giá niêm yết` dòng số: " + stt + " phải là tròn trăm");
                    have_error = true;
                    return false;
                }
                if (dropship != "-" || ctv != "-" || cus != "-") {
                    if (dropship == "-" || ctv == "-" || cus == "-") {
                        toastr.error("Giá Dropship, CTV, Khách yêu cầu đủ hoặc trống tất cả tại dòng số: " + stt);
                        have_error = true;
                        return false;
                    }

                    if (isNaN(dropship) || dropship < 1 || dropship > 999999999) {
                        toastr.error("Vui lòng kiểm tra lại `Giá Dropship` dòng số: " + stt);
                        have_error = true;
                        return false;
                    }
                    if (dropship%100 != 0) {
                        toastr.error("`Giá Dropship` dòng số: " + stt + " phải là tròn trăm");
                        have_error = true;
                        return false;
                    }

                    if (isNaN(ctv) || ctv < 1 || ctv > 999999999) {
                        toastr.error("Vui lòng kiểm tra lại `Giá cho CTV` dòng số: " + stt);
                        have_error = true;
                        return false;
                    }
                    if (ctv%100 != 0) {
                        toastr.error("`Giá cho CTV` dòng số: " + stt + " phải là tròn trăm");
                        have_error = true;
                        return false;
                    }

                    if (isNaN(cus) || cus < 1 || cus > 999999999) {
                        toastr.error("Vui lòng kiểm tra lại `Giá cho Khách` dòng số: " + stt);
                        have_error = true;
                        return false;
                    }
                    if (cus%100 != 0) {
                        toastr.error("`Giá cho Khách` dòng số: " + stt + " phải là tròn trăm");
                        have_error = true;
                        return false;
                    }
                } else {
                    dropship = ctv = cus = 0;
                }

                if (note.length > 255) {
                    toastr.error("Vui lòng kiểm tra lại `Ghi chú` dòng số: " + stt);
                    have_error = true;
                    return false;
                }
                dRow.push(stt);
                dRow.push(sku);
                dRow.push(ipo);
                dRow.push(dropship);
                dRow.push(ctv);
                dRow.push(cus);
                dRow.push(note);
                data.push(dRow);
            });
            if (have_error) return false;
            if(data.length === 0) {
                toastr.error("Chưa có dữ liệu để thêm mới");
                return false;
            }
            box1 = new ajaxLoader('body', {classOveride: 'blue-loader', bgColor: '#000', opacity: '0.3', type_image: 'ajax_loader_circle'});
            $.ajax({
                url: "{!! route('admin.prices.save-bulk') !!}",
                data: { data: data },
                type: 'POST',
                datatype: 'json',
                headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                success: function(res) {
                    window.location.href = "{!! route('admin.prices.index') !!}";
                },
                error: function(obj, status, err) {
                    var error = $.parseJSON(obj.responseText);
                    toastr.error(error.message, '{!! trans('system.have_an_error') !!}');
                }
            }).always(function() {
                if(box1) box1.remove();
            });
        }
    </script>
@stop
