@extends('backend.master')
@section('title')
    {!! trans('system.action.filter') !!} {!! trans('emails.label') !!}
@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('emails.label') !!}
            <small>{!! trans('system.action.filter') !!} {!! trans('emails.label') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
        </ol>
    </section>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="title">Nhập liệu</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Copy và paste các địa chỉ</h4>
                            {!! Form::textarea('email_lists', old('email_lists'), ['class' => 'form-control', 'rows' => 5, 'placeholder' => 'Mỗi dòng là 1 địa chỉ']) !!}
                            <br/>
                            <span class="label label-default">Chỉ kiểm tra định dạng</span> {!! Form::checkbox('only_format', 1, old('only_format', 1), []) !!} &nbsp;&nbsp;&nbsp;&nbsp;
                            <span class="label label-default">Chỉ gồm Gmail</span> {!! Form::checkbox('only_gmail', 1, old('only_gmail', 1), []) !!} &nbsp;&nbsp;&nbsp;&nbsp;
                            <a href="#" class="btn btn-success btn-sm validate_list">
                                Kiểm tra
                            </a>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    "Chỉ kiểm tra định dạng" => Nếu BỎ check này có nghĩa là kiểm tra cả sự tồn tại THỰC SỰ của địa chỉ (chính xác 80-90%). Mỗi lần thực hiện thao tác kiểm tra tồn tại này chỉ xử lý khoảng 20-30 địa chỉ/lần! Còn chỉ kiểm tra định dạng thì nhập bao nhiêu tuỳ ý!
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="title">Kết quả</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12" style="overflow-y: scroll; max-height: 500px;">
                            <p id="message"></p>
                            <table class="table table-condensed table-bordered" id="example" style="overflow-x: scroll;">
                                <thead>
                                    <tr>
                                        <th style="vertical-align: center; white-space: nowrap;" class="vert-align">Email</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('footer')
    <script>
        !function ($) {
            $(function() {
                $("a.validate_list").click(function(event) {
                    var email_lists = $.trim($("textarea[name='email_lists']").val());
                    if (email_lists == "") {
                        toastr.warning("Danh sách email không được bỏ trống");
                        return false;
                    }

                    box1 = new ajaxLoader('body', {classOveride: 'blue-loader', bgColor: '#000', opacity: '0.3'});
                    $.ajax({
                        url: "{!! route('admin.email-validate') !!}",
                        data: { email_lists: email_lists, only_gmail: $("input[name='only_gmail']").is(':checked'), only_format: $("input[name='only_format']").is(':checked') },
                        type: 'POST',
                        datatype: 'json',
                        headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                        success: function(res) {
                            $("#message").html("").append(res.message);
                            $("#example tbody").html("");
                            $.each(res.results, function(index, val) {
                                $("#example tbody").append("<tr><td>"+val+"</td></tr>");
                            });
                        },
                        error: function(obj, status, err) {
                        var error = $.parseJSON(obj.responseText);
                        toastr.error(error.message, '{!! trans('system.info') !!}');
                        }
                    }).always(function() {
                        if(box1) box1.remove();
                    });

                });
            });
        }(window.jQuery);
    </script>
@stop