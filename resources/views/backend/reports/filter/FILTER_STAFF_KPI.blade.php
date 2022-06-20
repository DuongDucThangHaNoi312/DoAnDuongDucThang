<div class="col-md-2">
    {!! Form::label('company', trans('companies.label') ) !!}
    {!! Form::select('company', [-1 => trans('system.dropdown_all')] + $companies, Request::get('company'), ['class' => 'form-control select2']) !!}
</div>
<div class="col-md-2">
    {!! Form::label('department', trans('departments.label') ) !!}
    {!! Form::select('department', [-1 => trans('system.dropdown_all')] + $departments, Request::get('department'), ['class' => 'form-control select2']) !!}
</div>
<div class="col-md-2">
    {!! Form::label('from_month', 'Từ') !!}<br>
    {!! Form::select('from_month', $monthOption, Request::get('from_month', 1), ['class' => 'form-control select2']) !!}
</div>
<div class="col-md-2">
    {!! Form::label('to_month', 'Đến') !!} <br>
    {!! Form::select('to_month', $monthOption, Request::get('to_month', now()->month), ['class' => 'form-control select2']) !!}
</div>
<div class="col-md-2">
    {!! Form::label('year', 'Năm') !!} <br>
    {!! Form::select('year', $yearOption, Request::get('year', now()->year), ['class' => 'form-control select2']) !!}
</div>
