<div id="modal-excel" class="modal fade" tabindex="-1" role="dialog" aria-hidden="false" data-keyboard="false"
     data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #ccc">
                <h1 class="modal-title">Xuất dữ liệu nhân viên</h1>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col col-md-12">
                        {!! Form::open(['url' => route('admin.staffs.export'), 'role' => 'form','id'=>'excel-form', 'method'=>'post']) !!}
                        <div class="row" >
                            <span class="col col-md-3 text-right" style="font-size: 16px; font-weight: 500; margin-top: 5px">Tên báo cáo</span>
                            <div class="col col-md-9">
                                {!! Form::text('name_excel', old('name_excel', 'Nhanvien-'. now()->format('Y-m-d_H-m')), ['class' => 'form-control', 'required']) !!}
                            </div>
                        </div>
{{--                        <div class="row" style="margin-top: 20px">--}}
{{--                            <div class="box box-default collapsed-box">--}}
{{--                                <div class="box-header with-border">--}}
{{--                                    <h3 class="box-title">Lọc thêm</h3>--}}
{{--                                    <div class="box-tools pull-right">--}}
{{--                                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                                <div class="box-body">--}}
{{--                                    The body of the box--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
                        {!! Form::close() !!}
{{--                        <div class="row" style="border-bottom: 1px solid #e5e5e5; margin-top: 20px;">--}}
{{--                           <span style="font-size: 20px; font-weight: 600;">Cấu hình hiển thị</span>--}}
{{--                        </div>--}}
                        <div class="row text-center" style="margin-top: 5px; font-size: 14px;">
                            <div id="show-mess" class="text-danger col col-md-12"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="text-align: center">
                <button type="button"
                        class="btn btn-danger btn-flat"
                        id="cancel-event" class="close"
                        data-dismiss="modal"
                        aria-label="Close">
                    {!! trans('system.action.cancel') !!}
                </button>
                <button type="submit" class="btn btn-primary btn-flat"
                        id="btn-export">Xuất excel
                </button>
            </div>
        </div>
    </div>
</div>

<script>

    $('#file-input').on('change', function () {
        console.log($(this)[0].files[0])
        $('#show-file').html($(this)[0].files[0]['name'])
    })
    function upload() {
        if (document.getElementById("file-input").files.length === 0) {
            $('#show-mess').html('Vui lòng chọn file trước khi lưu.')
            return false
        }
        let myFile = new FormData()
        myFile.append('file', $('#file-input')[0].files[0])
        $.ajax({
            url: "{!! route('admin.user.import') !!}",
            data: myFile,
            type: 'POST',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            processData: false,
            contentType: false,
            success: function (data) {
                console.log(data)
                $('#show-mess').html('Thanh cong')
            },
            error: function (err) {
                let errors = $.parseJSON(err.responseText);
                let error = errors.message
                $('#show-mess').html('Hàng thứ ' + error[0].row + ': ' + error[0].errors[0])
                console.log(error)
            }
        })
    }
</script>
