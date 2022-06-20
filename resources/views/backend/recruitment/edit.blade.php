@extends('backend.master')
@section('title')
    {!! trans('system.action.create') !!} - {!! trans('recruitment.label') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') !!}"/>
@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('recruitment.label') !!}
            <small>{!! trans('system.action.edit') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.recruitment.index') !!}">{!! trans('recruitment.label') !!}</a></li>
        </ol>
    </section>

    {!! Form::open(['url' => route('admin.recruitment.update',$recruitment->id), 'role' => 'form', 'enctype'=>"multipart/form-data",'method'=>'PUT']) !!}
    <div class="">
        <div class="box-header with-border">
            <h3 class="box-title">{!! trans('recruitment.person_info') !!}</h3>
        </div>
        <div class="box-body">
            <table class='table borderless'>
                <tr>
                    <th class="table_right_middle">
                        {!! trans('recruitment.name') !!}
                    </th>
                    <td>
                        {!! Form::text('name', old('name',$recruitment->name), ['class' => 'form-control']) !!}
                    </td>

                    <th class="table_right_middle">
                        {!! trans('recruitment.telephone') !!}
                    </th>
                    <td>
                        {!! Form::text('telephone', old('telephone',$recruitment->telephone), ['class' => 'form-control', 'maxlength' => 10]) !!}
                    </td>
                </tr>
                <tr>
                    <th class="table_right_middle">
                        {!! trans('recruitment.email') !!}
                    </th>
                    <td>
                        {!! Form::text('email', old('email',$recruitment->email), ['class' => 'form-control']) !!}
                    </td>
                    <th class="table_right_middle">
                        {!! trans('recruitment.dob') !!}
                    </th>
                    <td>
                        {!! Form::text('dob', old('dob',$recruitment->dob->format('d/m/Y')), ['class' => 'form-control datepicker','autocomplete'=>'off' ]) !!}
                    </td>
                </tr>
                <tr>
                    <th class="table_right_middle">
                        {!! trans('recruitment.id_card_no') !!}
                    </th>
                    <td>
                        {!! Form::text('id_card_no', old('id_card_no',$recruitment->id_card_no), ['class' => 'form-control  ',  ]) !!}
                    </td>
                    <th class="table_right_middle">
                        {!! trans('recruitment.gender.label') !!}
                    </th>
                    <td>
                        {!! Form::select('gender', ['' => trans('system.gender')] + App\Define\Recruitment::getGendersForOption(), old('gender',$recruitment->gender), ['class' => 'form-control select2']) !!}
                    </td>
                </tr>
                <tr>
                    <th class="table_right_middle">
                        {!! trans('recruitment.permanent_residence') !!}
                    </th>
                    <td>
                        {!! Form::text('permanent_residence', old('permanent_residence',$recruitment->permanent_residence), ['class' => 'form-control']) !!}
                    </td>
                    <th class="table_right_middle">
                        {!! trans('recruitment.education_level.label') !!}
                    </th>
                    <td>
                        {!! Form::select('education_level', ['' => trans('system.dropdown_choice')] + App\Define\Recruitment::getLevelForOption(), old('education_level',$recruitment->education_level), ['class' => 'form-control select2']) !!}
                    </td>

                </tr>
                <tr>
                    <th class="table_right_middle">
                        {!! trans('recruitment.file') !!}
                    </th>
                    <td >
                        <input type="hidden" name="file" value="{{ $recruitment->file_cv }}">
                        <a href="{{url('storage/'.$recruitment->file_cv) }}">  {{ $recruitment->file_cv }}</a>
                        <input type="file" class="" name="file_cv" accept=".docx, .doc, .zip, .pdf, .rar">
                    </td>
                </tr>
                <tr>
                    <th class="table_right_middle">
                        {!! trans('recruitment.description') !!}
                    </th>
                    <td colspan="4">
                        <textarea class="form-control" rows="5" name="description">{!! $recruitment->description  !!}</textarea>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="">
        <div class="box-header with-border">
            <h3 class="box-title">{!! trans('recruitment.company_info') !!}</h3>
        </div>
        <div class="box-body">
            <table class='table borderless'>
                <tr>
                    <th class=" col-md-4 table_right_middle">
                        {!! trans('recruitment.company_id') !!}
                    </th>
                    <td>
                        {!! Form::select('company_id', ['' => trans('system.dropdown_choice')] + App\Define\Recruitment::getCompanyNamesForOption(), old('company_id',$recruitment->company_id), ['class' => 'form-control select2 companySelect', 'required']) !!}
                    </td>
                    <th></th>
                    <td></td>
                </tr>
                <tr>
                    <th class="table_right_middle">
                        {!! trans('recruitment.department_id') !!}
                    </th>
                    <td id="department-tooltip" data-toggle="tooltip" data-placement="bottom">
                        {!! Form::select('department_id',$departmentOption, old('department_id',$recruitment->department_id), ['class' => 'form-control select2', 'required', 'id' => 'departmentSelect']) !!}
                    </td>
                    <th></th>
                    <td></td>
                </tr>
                <tr>
                    <th class="table_right_middle">
                        {!! trans('recruitment.title_id') !!}
                    </th>
                    <td>
                        {!! Form::select('title_id', ['' => trans('system.dropdown_choice')] + App\Define\Recruitment::getTitleNamesForOption(), old('title_id',$recruitment->title_id), ['class' => 'form-control select2', 'required']) !!}
                    </td>
                    <th></th>
                    <td></td>
                </tr>
                <tr>
                    <th class="table_right_middle">
                        {!! trans('recruitment.recruitment_address') !!}
                    </th>
                    <td colspan="4">
                        <textarea class="form-control" rows="5" name="recruitment_address">{!!  $recruitment->recruitment_address !!}</textarea>
                    </td>
                    <th></th>
                    <td></td>
                </tr>
            </table>
        </div>
    </div>
    <div class="box box-default">
        <div class="box-body">
            <div class="container col-xs-12">
                <div class="row">
                    <div class="col-md-6 col-md-offset-3 text-center">
                        {!! HTML::link(route( 'admin.recruitment.index' ), trans('system.action.cancel'), ['class' => 'btn btn-danger btn-flat']) !!}
                        {!! Form::submit(trans('system.action.save'), ['class' => 'btn btn-primary btn-flat']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}

@stop
@section('footer')
    <script src="{!! asset('assets/backend/plugins/iCheck/icheck.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/select2/select2.full.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/moment/min/moment-with-locales.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/input-mask/jquery.inputmask.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.vi.min.js') !!}"></script>
    <script>
        $(".select2").select2({width: '100%'});
        $('.datepicker').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true,
            language: "vi",
            endDate:'-1d'
        });
        $(document).on('change', '.companySelect', function (e) {
            let companyId = $(this). val();
            if (companyId) {
                $('#departmentSelect').attr('disabled', false)
                console.log(companyId)
                $.ajax({
                    url: "{!! route('admin.contracts.setDepartmentOption') !!}",
                    data: {companyId: companyId},
                    type: 'POST',
                    headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                    success: function (res) {
                        console.log(res)
                        $('#departmentSelect option').remove()
                        $('#departmentSelect').append('<option selected>'+ '{!! trans('system.dropdown_choice') !!}'  + '</option>')
                        $.each(res, function (index, value) {
                            console.log(index)
                            $('#departmentSelect').append('<option value="' + index + '">' + value + '</option>')
                        })
                    },
                    error: function (data) {
                        console.log(data)
                    }
                })
            } else {
                $('#departmentSelect').attr('disabled', true)
            }
        })
    </script>

@stop