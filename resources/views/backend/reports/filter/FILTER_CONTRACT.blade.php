{{-- <div class="col-md-2">
    {!! Form::label('code', trans('contracts.code') ) !!}
    {!! Form::text('code' , Request::get('code'), ['class' => 'form-control']) !!}
</div> --}}
<div class="col-md-3">
    {!! Form::label('company', trans('companies.label') ) !!}
    {!! Form::select('company', [-1 => trans('system.dropdown_all')] + $companies, Request::get('company'), ['class' => 'form-control select2']) !!}
</div>
<div class="col-md-3">
    {!! Form::label('department', trans('departments.label') ) !!}
    {!! Form::select('department', [-1 => trans('system.dropdown_all')] + $departments, Request::get('department_group'), ['class' => 'form-control select2']) !!}
</div>
{{-- <div class="col-md-3">
    {!! Form::label('department_group', trans('contracts.department_group') ) !!}
    {!! Form::select('department_group', [-1 => trans('system.dropdown_all')] + $departmentGroups, Request::get('department_group'), ['class' => 'form-control select2']) !!}
</div> --}}
<div class="col-md-3">
    {!! Form::label('position_id', trans('contracts.position_id') ) !!}
    {!! Form::select('position_id', [-1 => trans('system.dropdown_all')] + $positions, Request::get('position_id'), ['class' => 'form-control select2']) !!}
</div>
<div class="col-md-3">
    {!! Form::label('type_status', trans('system.status.label') ) !!}
    {!! Form::select('type_status', [-1 => trans('system.dropdown_all')] + $typeStatus, Request::get('type_status'), ['class' => 'form-control select2']) !!}
</div>
<div class="col-md-3">
    <div class="form-group">
        {!! Form::label('set_notvalid_on', trans('contracts.set_notvalid_on')) !!}
        {!! Form::text('set_notvalid_on', Request::input('set_notvalid_on'), ['class' => 'form-control date_range1','placeholder' => 'Select date...']) !!}
    </div>
</div>
<div class="col-md-3">
    <div class="form-group">
        {!! Form::label('valid', trans('contracts.valid')) !!}
        {!! Form::text('valid', Request::input('valid'), ['class' => 'form-control date_range2','placeholder' => 'Select date...']) !!}
    </div>
</div>