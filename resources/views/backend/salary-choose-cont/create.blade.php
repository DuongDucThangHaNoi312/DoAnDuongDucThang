 <!-- Modal -->
 <div class="modal fade" id="exampleModal" tabindex="" role="dialog" aria-labelledby="exampleModalLabel" 
    aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-create" role="document">
        {!! Form::open(['id' => 'btn_create', 'url' => route('admin.salary-choose-containers.store'), 'method' => 'POST', ]) !!}
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title" id="exampleModalLabel">Thêm mới lương khoán </h1>
            </div>
            <div class="modal-body">
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
                            <span class="text-danger">
                                <strong id="company-error"></strong>
                            </span>
                        </div>
                        <div class="col-md-6">
                            <label>{!! trans('timekeeping.department') !!} <span class="text-danger">(*)</span></label>
                            <select name="department_id" class="departmentSelect form-control select2 department_id" disabled="true">
                                <option value="" {!! old('department_id') !!}>{!! trans('system.dropdown_choice') !!}</option>
                            </select>
                            <span class="text-danger">
                                <strong id="department-error"></strong>
                            </span>
                        </div>
                    </div>
                </div>                      
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            <label>{!! trans('timekeeping.month') !!} <span class="text-danger">(*)</span></label>
                            <select name="month" id="" class="form-control select2">
                                @foreach (\App\Define\Timekeeping::getMonth() as $key => $item)
                                <option value="{{ $key }}" {{ $key == date('m') ? "selected" : '' }}>{{ $item }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger">
                                <strong id="month-error"></strong>
                            </span>
                        </div>
                        <div class="col-md-6">
                            <label>{!! trans('timekeeping.year') !!} <span class="text-danger">(*)</span></label>
                            <select name="year" id="" class="form-control select2">
                                <option value="">{{ trans('system.dropdown_choice') }}</option>
                                @foreach (\App\Define\Timekeeping::getYear() as $key => $item)
                                <option value="{{ $key }}" {{ $key == date('Y') ? "selected" : '' }}>{{ $item }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger">
                                <strong id="year-error"></strong>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">{!! trans('system.action.cancel') !!}</button>
                <button type="button" id="submitForm" value="add" class="btn btn-primary btn-flat btn-create">{!! trans('system.action.save') !!}</button>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>