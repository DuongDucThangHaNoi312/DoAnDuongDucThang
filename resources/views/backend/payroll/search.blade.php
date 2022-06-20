{!! Form::open([ 'url' => route('admin.payrolls.index'), 'method' => 'GET', 'role' => 'search' ]) !!}
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('company_id', trans('timekeeping.company')) !!}
                <select name="company_id" id="company" class="companySelect1 form-control select2">
                    <option value="" selected="selected">{{ trans('system.dropdown_choice') }}</option>
                    @foreach (\App\Helpers\GetOption::getCompaniesForOption() as $key => $item)
                    <option value="{{ $key }}">{{ $item }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                {!! Form::label('company_id', trans('timekeeping.department')) !!}
                <select name="department_id" id="departmentSelect1" class="form-control select2 department_id" disabled="true">
                    <option value="" {!! old('department_id') !!}>{!! trans('system.dropdown_choice') !!}</option>
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                {!! Form::label('month', trans('timekeeping.month')) !!}
                {!! Form::number('month', Request::input('month'), ['class' => 'form-control', 'min' => '1', 'max' => '12']) !!}
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                {!! Form::label('year', trans('timekeeping.year')) !!}
                {!! Form::number('year', date('Y'), ['class' => 'form-control', 'min' => '2018', 'max' => '2030', 'id' => 'year']) !!}
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                {!! Form::label('filter', trans('system.action.label'), ['style' => 'display: block;']) !!}
                <button type="submit" class="btn btn-primary btn-flat" style="display: block;">
                    <span class="glyphicon glyphicon-search"></span>&nbsp; {!! trans('system.action.search') !!}
                </button>
            </div>
        </div>
    </div>
{!! Form::close() !!}