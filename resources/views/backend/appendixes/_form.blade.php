<div class="modal fade" id="modal-appendix"  role="dialog" aria-labelledby="exampleModalLabel" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <form method="POST" id="form-appendix" class="modal-content">
            {{csrf_field()}}
            <input type="hidden" value="" id="appendix-id" name="id">
            <div class="modal-header" style="background-color: #3c8dbc; color: #FFFFFF;">
                <span class="modal-title" id="modal-appendix-label" style="font-weight: 600; font-size: 20px;">
                    <i class="glyphicon glyphicon-plus"></i>
                    <span></span>
                </span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-bottom: 15px;">
                    <i class="text-black glyphicon glyphicon-remove"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label  class="col-form-label">{!! trans('appendixes.name') !!}<span class="text-danger">*</span></label>
                    {!! Form::text('name', old('name'), ['class' => 'form-control', 'maxlength' => 100]) !!}
                    <span class="text-danger">
                        <strong id="name-error"></strong>
                    </span>
                </div>
                <div class="form-group">
                    <label class="col-form-label">{!! trans('appendixes.expense') !!}<span class="text-danger">*</span></label>
                    <div class="input-group">
                        {!! Form::text('expense',old('expense'), ["class" => "form-control currency"]) !!}
                        <div class="input-group-addon price-addon">VNĐ</div>
                    </div>
                    <span class="text-danger">
                        <strong id="expense-error"></strong>
                    </span>
                </div>
                <div class="form-group">
                    <label  class="col-form-label">{!! trans('system.desc') !!}<span class="text-danger"></span></label>
                    {!! Form::textarea('description', old('description'), ['class' => 'form-control description', 'rows' => 4, 'placeholder' => 'Mô tả chi tiết phụ lục']) !!}
                </div>
                <div class="row">
                    <div colspan="4" class="text-center">
                        {!! Form::checkbox('status', 1, old('status', 1), [ 'class' => 'minimal-red' ]) !!}
                        {!! trans('system.status.active') !!}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-flat" data-dismiss="modal">{!! trans('system.action.cancel') !!}</button>
                <button type="button" id="submitForm" class="btn btn-primary btn-save">{!! trans('system.action.save') !!}</button>
            </div>
        </form>
    </div>
</div>