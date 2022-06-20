<!-- Modal -->
<div class="modal fade" id="exampleModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        {!! Form::open(['id' => 'workschedule']) !!}
            <div class="modal-content">
                <div class="modal-header" style="background-color: #3c8dbc; color: white; text-align: center">
                    <h3 class="modal-title" id="exampleModalLabel">{!! trans('workschedule.add_title') !!}</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <div class="form-group">
                            <label for="">{!! trans('workschedule.company') !!} <span class="text-danger">(*)</span></label>
                            <select name="company_id" id="company" class="companySelect form-control select2">
                                <option value="" selected="selected">{{ trans('overtimes.choose_company') }}</option>
                                @if($companysOption)
                                    @foreach ($companysOption as $key => $item)
                                        <option value="{{ $key }}">{{ $item }}</option>
                                    @endforeach
                                @else
                                @foreach (\App\Helpers\GetOption::getCompaniesForOption() as $key => $item)
                                <option value="{{ $key }}">{{ $item }}</option>
                                @endforeach
                                    @endif
                            </select>
                            <span class="text-danger">
                                <strong id="company-error"></strong>
                            </span>
                        </div>
                        <div class="form-group choice-department">
                            <label for="">{!! trans('workschedule.department') !!} <span class="text-danger">(*)</span></label>
                            <select name="department_id" id="departmentSelect" class="form-control select2 department_id" disabled="true" required>
                                {{-- <option value="" {!! old('department_id') !!}>{!! trans('overtimes.choose_department') !!}</option> --}}
                            </select>
                            <span class="text-danger">
                                <strong id="department-error"></strong>
                            </span>
                        </div>
                        <div class="office">
                            <hr>
                            <h4>{!! trans('workschedule.title') !!}</h4>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="">{!! trans('workschedule.morning') !!} <span class="text-danger">(*)</span></label>
                                    </div>
                                    <div class="col-md-4">
                                        {!! Form::text('from_morning', old('from_morning'), ['class' => 'form-control timepicker-am', 'placeholder' => 'Giờ vào', 'id' => 'from_morning']) !!}
                                    </div>
                                    <div class="col-md-4">
                                        {!! Form::text('to_morning', old('to_morning'), ['class' => 'form-control timepicker-am', 'placeholder' => 'Giờ ra', 'id' => 'to_morning']) !!}
                                    </div>
                                </div>
                                <span class="text-danger">
                                    <strong id="time1-error"></strong>
                                </span>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="">{!! trans('workschedule.afternoon') !!} <span class="text-danger">(*)</span></label>
                                    </div>
                                    <div class="col-md-4">
                                        {!! Form::text('from_afternoon', old('from_afternoon'), ['class' => 'form-control timepicker-pm', 'placeholder' => 'Giờ vào', 'id' => 'from_afternoon']) !!}
                                    </div>
                                    <div class="col-md-4">
                                        {!! Form::text('to_afternoon', old('to_afternoon'), ['class' => 'form-control timepicker-pm', 'placeholder' => 'Giờ ra', 'id' => 'to_afternoon']) !!}
                                    </div>
                                </div>
                                <span class="text-danger">
                                    <strong id="time2-error"></strong>
                                </span>
                            </div>
                            <hr>
                            <h4>{!! trans('workschedule.saturday') !!} &nbsp; &nbsp;<input type="checkbox" class="minimal" value="1" name="type" id="type1"><span style="font-size: 14px"> &nbsp;Làm tại nhà</span></h4>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="">{!! trans('workschedule.morning') !!}</label>
                                    </div>
                                    <div class="col-md-4">
                                        {!! Form::text('from_sa_morning', old('from_sa_morning'), ['class' => 'form-control timepicker-am', 'placeholder' => 'Giờ vào', 'id' => 'from_sa_morning']) !!}
                                    </div>
                                    <div class="col-md-4">
                                        {!! Form::text('to_sa_morning', old('to_sa_morning'), ['class' => 'form-control timepicker-am', 'placeholder' => 'Giờ ra', 'id' => 'to_sa_morning']) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="">{!! trans('workschedule.afternoon') !!}</label>
                                    </div>
                                    <div class="col-md-4">
                                        {!! Form::text('from_sa_afternoon', old('from_sa_afternoon'), ['class' => 'form-control timepicker-pm', 'placeholder' => 'Giờ vào', 'id' => 'from_sa_afternoon']) !!}
                                    </div>
                                    <div class="col-md-4">
                                        {!! Form::text('to_sa_afternoon', old('to_sa_afternoon'), ['class' => 'form-control timepicker-pm', 'placeholder' => 'Giờ ra', 'id' => 'to_sa_afternoon']) !!}
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="">{!! trans('workschedule.ot') !!} <span class="text-danger">(*)</span></label>
                                    </div>
                                    <div class="col-md-4">
                                        {!! Form::text('ot', old('ot'), ['class' => 'form-control timepicker', 'placeholder' => 'Bắt đầu', 'id' => 'ot']) !!}
                                    </div>
                                </div>
                                <span class="text-danger">
                                    <strong id="ot-error"></strong>
                                </span>
                            </div>
                        </div>
                        <div class="shift">
                            
                            <h4>Ca làm <span class="text-danger">(*)</span></h4>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <select name="category_shift_id" id="category_shift_id" class="form-control" required>
                                            <option value="">Chọn 1 ca</option>
                                            @foreach (\App\Define\Shift::getAllShift() as $key => $cate)
                                                <option value="{{ $key }}">{{ $cate }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <span class="text-danger" id="shift">
                            </span>
                            <hr>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label for="">Giờ vào <span class="text-danger">(*)</span></label>
                                    </div>
                                    <div class="col-md-4">
                                        {!! Form::text('time_in', old('time_in'), ['class' => 'form-control timepicker', 'placeholder' => 'Giờ vào', 'id' => 'time_in', 'required']) !!}
                                    </div>
                                    <div class="col-md-2">
                                        <label for="">Giờ ra <span class="text-danger">(*)</span></label>
                                    </div>
                                    <div class="col-md-4">
                                        {!! Form::text('time_out', old('time_out'), ['class' => 'form-control timepicker', 'placeholder' => 'Giờ ra', 'id' => 'time_out', 'required']) !!}
                                    </div>
                                </div>
                                <span class="text-danger" id="time-in-out">
                                </span>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label for="">Nghỉ giữa ca <span class="text-danger">(*)</span></label>
                                    </div>
                                    <div class="col-md-4">
                                        {!! Form::text('off_mid_shift', old('off_mid_shift'), ['class' => 'form-control timepicker', 'placeholder' => 'Nghỉ giữa ca', 'id' => 'off_mid_shift', 'required']) !!}
                                    </div>
                                    <div class="col-md-2">
                                        <label for="">Bắt đầu giữa ca <span class="text-danger">(*)</span></label>
                                    </div>
                                    <div class="col-md-4">
                                        {!! Form::text('start_mid_shift', old('start_mid_shift'), ['class' => 'form-control timepicker', 'placeholder' => 'Bắt đầu giữa ca', 'id' => 'start_mid_shift', 'required']) !!}
                                    </div>
                                </div>
                               
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label for="">Giờ sớm nhất <span class="text-danger">(*)</span></label>
                                    </div>
                                    <div class="col-md-4">
                                        {!! Form::text('limit_time_in', old('limit_time_in'), ['class' => 'form-control timepicker', 'placeholder' => 'Chấm vào sớm nhất', 'id' => 'limit_time_in', 'required']) !!}
                                    </div>
                                    <div class="col-md-2">
                                        <label for="">Giờ sau cùng <span class="text-danger">(*)</span></label>
                                    </div>
                                    <div class="col-md-4">
                                        {!! Form::text('limit_time_out', old('limit_time_out'), ['class' => 'form-control timepicker', 'placeholder' => 'Chấm ra muộn nhất', 'id' => 'limit_time_out', 'required']) !!}
                                    </div>
                                </div>
                                
                            </div>
                            
                            {{-- <hr>
                            <h4>{!! trans('workschedule.shift1') !!} <span class="text-danger">(*)</span></h4>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        {!! Form::text('shift1_in', old('shift1_in'), ['class' => 'form-control timepicker', 'placeholder' => 'Giờ vào', 'id' => 'shift1_in']) !!}
                                    </div>
                                    <div class="col-md-6">
                                        {!! Form::text('shift1_out', old('shift1_out'), ['class' => 'form-control timepicker', 'placeholder' => 'Giờ ra', 'id' => 'shift1_out']) !!}
                                    </div>
                                </div>
                                <span class="text-danger">
                                    <strong id="shift1-error"></strong>
                                </span>
                            </div>
                            <hr>
                            <h4>{!! trans('workschedule.shift2') !!} <span class="text-danger">(*)</span></h4>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        {!! Form::text('shift2_in', old('shift2_in'), ['class' => 'form-control timepicker', 'placeholder' => 'Giờ vào', 'id' => 'shift2_in']) !!}
                                    </div>
                                    <div class="col-md-6">
                                        {!! Form::text('shift2_out', old('shift2_out'), ['class' => 'form-control timepicker', 'placeholder' => 'Giờ ra', 'id' => 'shift2_out']) !!}
                                    </div>
                                </div>
                                <span class="text-danger">
                                    <strong id="shift2-error"></strong>
                                </span>
                            </div>
                            <hr>
                            <h4>{!! trans('workschedule.shift3') !!} <span class="text-danger">(*)</span></h4>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        {!! Form::text('shift3_in', old('shift3_in'), ['class' => 'form-control timepicker', 'placeholder' => 'Giờ vào', 'id' => 'shift3_in']) !!}
                                    </div>
                                    <div class="col-md-6">
                                        {!! Form::text('shift3_out', old('shift3_out'), ['class' => 'form-control timepicker', 'placeholder' => 'Giờ ra', 'id' => 'shift3_out']) !!}
                                    </div>
                                </div>
                                <span class="text-danger">
                                    <strong id="shift3-error"></strong>
                                </span>
                            </div>
                            <hr>
                            <h4>{!! trans('workschedule.shift4') !!}</h4>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        {!! Form::text('shift4_in', old('shift4_in'), ['class' => 'form-control timepicker', 'placeholder' => 'Giờ vào', 'id' => 'shift4_in']) !!}
                                    </div>
                                    <div class="col-md-6">
                                        {!! Form::text('shift4_out', old('shift4_out'), ['class' => 'form-control timepicker', 'placeholder' => 'Giờ ra', 'id' => 'shift4_out']) !!}
                                    </div>
                                </div>
                                <span class="text-danger">
                                    <strong id="shift3-error"></strong>
                                </span>
                            </div> --}}
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="text-align: center">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">{!! trans('system.action.cancel') !!}</button>
                    <button type="button" id="submitForm" value="add" class="btn btn-primary">{!! trans('system.action.save') !!}</button>
                </div>
            </div>
        {!! Form::close() !!}
    </div>
</div>