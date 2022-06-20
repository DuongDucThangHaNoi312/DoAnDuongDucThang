<div id="modal-excel" class="modal fade" tabindex="-1" role="dialog" aria-hidden="false" data-keyboard="false"
     data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #ccc">
                <h1 class="modal-title">Xuất dữ liệu hợp đồng</h1>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col col-md-12">
                        {!! Form::open(['url' => route('admin.contracts.export-excel'), 'role' => 'form','id'=>'excel-form', 'method'=>'post']) !!}
                        <div class="row" style="margin-bottom: 15px;">
                            <span class="col col-md-3 text-right text-black">Hợp đồng
                                <input type="radio" name="type_export" class="minimal" value="1" checked>
                            </span>
                             <span class="col col-md-3 text-right">Phụ lục
                                <input type="radio" name="type_export" value="2">
                            </span>
                             <span class="col col-md-3 text-right">Kiêm nhiệm
                                <input type="radio" name="type_export" value="3">
                            </span>
                        </div>
                        <div class="row" >
                            <span class="col col-md-3 text-right" style="font-size: 16px; font-weight: 500; margin-top: 5px">Tên báo cáo</span>
                            <div class="col col-md-9">
                                {!! Form::text('name_excel', old('name_excel', 'HopDong-'. now()->format('Y-m-d_H-m-s')), ['class' => 'form-control', 'required']) !!}
                            </div>
                        </div>
                        {!! Form::close() !!}
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
    !function ($) {
        $(function () {
            $('input[name="type_export"]').on('ifClicked', function (event) {
                let v = this.value
                let nameExcel = 'HopDong-'
                if (v == 2) nameExcel = 'PhuLuc-'
                else if (v == 3) nameExcel = 'KiemNhiem-'
                else nameExcel = 'HopDong-'
                nameExcel = nameExcel + moment().format('YYYY-MM-DD_HH-mm-ss')
                $('input[name="name_excel"]').val(nameExcel)
            });
        });
    }(window.jQuery);
</script>
