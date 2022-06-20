<div class="col-md-2">
    {!! Form::label('company', trans('companies.label') ) !!}
    {!! Form::select('company', [-1 => trans('system.dropdown_all')] + $companies, Request::get('company'), ['class' => 'form-control select2']) !!}
</div>
{{--<div class="col-md-2">--}}
{{--    {!! Form::label('combined', trans('combined.label') ) !!}--}}
{{--    {!! Form::select('combined', [-1 => trans('system.dropdown_all')] + $groupDepts, Request::get('combined'), ['class' => 'form-control select2']) !!}--}}
{{--</div>--}}
<div class="col-md-2">
    {!! Form::label('department', trans('departments.label') ) !!}
    {!! Form::select('department', [-1 => trans('system.dropdown_all')] + $departments, Request::get('department'), ['class' => 'form-control select2']) !!}
</div>
<div class="col-md-2">
    {!! Form::label('dependent', trans('staffs.dependent') ) !!}
    {!! Form::select("dependent", [-1 => trans('system.dropdown_all'), 1 => trans('system.yes'), 0 => trans('system.no')], Request::get("dependent"), ['class' => 'form-control select2']) !!}
</div>
<div class="col-md-2">
    {!! Form::label('relationship', trans('staffs.family_relationships.label') ) !!}
    {!! Form::select("relationship", [-1 => trans('system.dropdown_all')] + \App\Defines\Staff::getFamilyRelationshipsForOption(), Request::get("relationship"), ['class' => 'form-control select2']) !!}
</div>
<div class="col-md-3">
    <div class="form-group">
        {!! Form::label('age', "Tuổi") !!}
        <div class="row">
            <div class="col-md-6">
                {!! Form::select('age_operator', [-1 => trans('system.dropdown_all')] + \App\Define\Report::getOperators(), Request::get('age_operator'), ['class' => 'form-control select2']) !!}
            </div>
            <div class="col-md-6">
                {!! Form::text('age' , Request::get('age'), ['class' => 'form-control']) !!}
            </div>
        </div>
    </div>
</div>