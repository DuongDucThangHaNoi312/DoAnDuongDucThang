@extends('backend.master')
@section('title')
    {!! trans('system.action.edit') !!} - {!! trans('staffs.label') !!}
@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css') !!}"/>
    <style>
        input[type=number]::-webkit-inner-spin-button {
            -webkit-appearance: none;
        }
        .hidden {
            display: none;
        }

        .image_user {
        }
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        input[type="file"] {
            display: block;
        }
        .imageThumb {
            max-height: 100px;
            padding: 1px;
            cursor: pointer;
        }
        .pip {
            display: inline-block;
            margin: 10px 10px 0 0;
        }
        .remove {
            display: block;
            text-align: center;
            cursor: pointer;
        }
        p.text-danger {
            margin: 0 0 -16px 0;
        }
        #cancel, #save {
            margin-bottom: 3%;
        }
    </style>
@stop
@section('content')
    <?php
        $monthYear = [];
        for ($i = 25+date("Y"); $i >= 2010; $i--) {
            $j=12;
            if ($i == date("Y")) {
                $j = date("m");
            }
            while ($j) {
                $monthYear[str_pad($j, 2, '0', STR_PAD_LEFT) . '/' . $i] = str_pad($j, 2, '0', STR_PAD_LEFT) . '/' . $i;
                $j--;
            }
        }
    ?>
    @if($errors->count())
        <div class="alert alert-warning alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-warning"></i> {!! trans('messages.error') !!}</h4>
            <ul>
                @foreach($errors->all() as $message)
                    <li>{!! $message !!}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <section class="content-header">
        <h1>
            {!! trans('staffs.label') !!}
            <small>{!! trans('system.action.edit') !!}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.staffs.index') !!}">{!! trans('staffs.label') !!}</a></li>
        </ol>
    </section>
    <div class="box">
        <div class="box-body no-padding">
            {!! Form::open(['url' => route('admin.staffs.update', $user->id), 'method' => 'PUT', 'role' => 'form','enctype'=>'multipart/form-data']) !!}
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table class='table borderless'>
                            <tr>
                                <th class="table_right_middle">
                                    {!! trans('staffs.code') !!}
                                </th>
                                <td>
                                    {!! Form::text('code', old('code', $user->code), ['class' => 'form-control', 'maxlength' => 50, 'required', 'placeholder' => trans('staffs.code_placeholder')]) !!}
                                </td>
                                <th class="table_right_middle" style="width: 15%;">
                                    {!! trans('staffs.fullname') !!}
                                </th>
                                <td style="width: 35%;">
                                    {!! Form::text('fullname', old('fullname', $user->fullname), ['class' => 'form-control', 'maxlength' => 100, 'required']) !!}
                                </td>
                            </tr>
                            <tr>
                                <th class="table_right_middle">
                                    {!! trans('staffs.id_card_no') !!}
                                </th>
                                <td>
                                    {!! Form::text('id_card_no', old('id_card_no',$user->id_card_no), ['class' => 'form-control', 'required']) !!}
                                </td>
                                <th class="table_right_middle">
                                    {!! trans('staffs.email') !!}
                                </th>
                                <td colspan="4">
                                    <input type="email" name="email" value="{{old('email',$user->email)}}" class="form-control"
                                           placeholder="Email đăng nhập "/>
                                </td>
                            </tr>
                            <tr>
                                <th class="table_right_middle">
                                    {!! trans('staffs.issued_on') !!}
                                </th>
                                <td>
                                    {!! Form::text('issued_on', old('issued_on', $user->issued_on ? $user->issued_on->format('d/m/Y') : null ), ['class' => 'form-control datepicker ', 'maxlength' => 15, 'required']) !!}
                                </td>
                                <th class="table_right_middle">
                                    {!! trans('staffs.date_of_birth') !!}
                                </th>
                                <td>
                                    {!! Form::text('date_of_birth', old('date_of_birth',!$user->date_of_birth ? null : $user->date_of_birth->format('d/m/Y')), ['class' => 'form-control datepicker', 'maxlength' => 15, 'required']) !!}
                                </td>
                            </tr>
                            <tr>
                                <th class="table_right_middle">
                                    {!! trans('staffs.issued_at') !!}
                                </th>
                                <td>
                                    {!! Form::text('issued_at', old('issued_at', $user->issued_at), ['class' => 'form-control']) !!}
                                </td>
                                <th class="table_right_middle">
                                    {!! trans('staffs.genders.label') !!}
                                </th>
                                <td>
                                    {!! Form::select('gender', ['gender' => trans('system.gender')] + \App\Defines\Staff::getGendersForOption(), old('gender',$user->gender), ['class' => 'form-control select2', 'required']) !!}
                                </td>
                            </tr>
                            <tr>
                                <th class="table_right_middle">
                                    {!! trans('staffs.marital_status.label') !!}
                                </th>
                                <td>
                                    {!! Form::select('marital_status', ['' => trans('system.dropdown_choice')] + \App\Defines\Staff::getMaritalStatusForOption(), old('marital_status', $user->marital_status), ['class' => 'form-control select2']) !!}
                                </td>
                                <th class="table_right_middle">
                                    {!! trans('staffs.ethnicity') !!}
                                </th>
                                <td>
                                    {!! Form::text('ethnicity', old('ethnicity', $user->ethnicity), ['class' => 'form-control']) !!}
                                </td>
                            </tr>
                            <tr>
                                <th class="table_right_middle">
                                    {!! trans('staffs.addresses') !!}
                                </th>
                                <td>
                                    {!! Form::text('addresses', old('addresses', $user->addresses), ['class' => 'form-control']) !!}
                                </td>
                                <th class="table_right_middle">
                                    {!! trans('staffs.domicile') !!}
                                </th>
                                <td>
                                    {!! Form::text('domicile', old('domicile', $user->domicile), ['class' => 'form-control']) !!}
                                </td>
                            </tr>
                            <tr>
                                <th class="table_right_middle">
                                    {!! trans('staffs.nationality') !!}
                                </th>
                                <td>
                                    {!! Form::text('nationality', old('nationality',$user->nationality), ['class' => 'form-control']) !!}
                                </td>
                                <th class="table_right_middle">
                                    {!! trans('staffs.phone') !!}
                                </th>
                                <td>
                                    {!! Form::number('phone', old('phone', $user->phone), ['class' => 'form-control']) !!}
                                </td>
                            </tr>
                            <tr>
                                <th class="table_right_middle">
                                    {!! trans('staffs.staff_start') !!}
                                </th>
                                <td>
                                    {!! Form::text('staff_start', old('staff_start', $user->staff_start ? date("d/m/Y", strtotime($user->staff_start)) : null), ['class' => 'form-control datepicker1', 'required']) !!}
                                </td>
                                <th class="table_right_middle">
                                    {!! trans('staffs.code_timekeeping') !!}
                                </th>
                                <td>
                                    <div class="input-group">
                                        {!! Form::text('code_timekeeping', old('code_timekeeping', $user->code_timekeeping), ['class' => 'form-control']) !!}
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-primary btn-flat check-code-timek" data-toggle="modal" data-target="#mp">Mã phụ</button>
                                        </span>
                                    </div>
                                    <?php   
                                        $code_timekeeping_subs = array_filter(explode(',', $user->code_timekeeping_subs)); 
                                    ?>

                                    <div class="modal fade" id="mp" tabindex="-1" role="dialog" aria-labelledby="mpLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header text-center">
                                            <h4 class="modal-title" id="mpLabel">Thêm mới mã phụ</h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        {{-- <input type="text" class="form-control" name="code_timekeeping_subs[]"> --}}
                                                        <table class="table table-bordered">
                                                            <thead style="background: #3C8DBC;color: white;">
                                                                <tr>
                                                                    <th style="text-align: center; vertical-align: middle;">STT</th>
                                                                    <th style="text-align: center; vertical-align: middle;">Mã phụ</th>
                                                                    <th style="text-align: center; vertical-align: middle;">Thao tác</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="add_code_timekeeping_subs">
                                                                @if (!empty($code_timekeeping_subs))
                                                                @foreach ($code_timekeeping_subs as $i => $v)
                                                                    <tr>
                                                                        <td style="text-align: center; vertical-align: middle; width: 50px" class="text-center">{{ $i + 1 }}</td>
                                                                        <td style="text-align: center; vertical-align: middle;">
                                                                            <input type="text" name="code_timekeeping_subs[]" class="form-control time-subs" value="{{ $v }}">
                                                                        </td>
                                                                        <td style="text-align: center; vertical-align: middle; width: 70px">
                                                                            @if ($i == 0)
                                                                                <a href="javascript:void(0);" class="btn btn-default btn-xs btn-timekeeping-subs">
                                                                                    <i class="text-success fa fa-plus"></i>
                                                                                </a>
                                                                            @else
                                                                                <a href="javascript:void(0);" class="btn btn-xs btn-default remove-code-timek-sub">
                                                                                    <i class="text-danger fa fa-minus"></i>
                                                                                </a>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                                @else     
                                                                    <tr>
                                                                        <td style="text-align: center; vertical-align: middle; width: 50px" class="text-center">1</td>
                                                                        <td style="text-align: center; vertical-align: middle;">
                                                                            <input type="text" name="code_timekeeping_subs[]" class="form-control time-subs">
                                                                        </td>
                                                                        <td style="text-align: center; vertical-align: middle; width: 70px">
                                                                            <a href="javascript:void(0);" class="btn btn-default btn-xs btn-timekeeping-subs">
                                                                                <i class="text-success fa fa-plus"></i>
                                                                            </a>
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                                
                                                            </tbody>      
                                                        </table>
                                                    </div>
                                                    {{-- <div class="col-md-2">
                                                        <a href="javascript:void(0);" class="btn btn-xs btn-default add-code-timek-subs">
                                                            <i class="text-success fa fa-plus"></i>
                                                        </a>
                                                    </div> --}}
                                                </div>
                                            </div>
                                            <div class="modal-footer" style="text-align: center">
                                            {{-- <button type="button" class="btn btn-flat btn-danger btn-cancel-timk" data-dismiss="modal">Hủy</button> --}}
                                            <button type="button" class="btn btn-primary btn-flat" data-dismiss="modal">Lưu lại</button>
                                            </div>
                                        </div>
                                        </div>
                                    </div>

                                </td>
                            </tr>
                            <tr>
                                <th class="table_right_middle">
                                    {!! trans('staffs.tax_code') !!}
                                </th>
                                <td>
                                    {!! Form::number('tax_code', old('tax_code', $user->tax_code), ['class' => 'form-control']) !!}
                                </td>
                                <th class="table_right_middle">
                                    {!! trans('staffs.insurance_no') !!}
                                </th>
                                <td>
                                    {!! Form::text('insurance_no', old('insurance_no', $user->insurance_no), ['class' => 'form-control']) !!}
                                </td>
                            </tr>
                            <tr>
                                <th class="table_right_middle" style="width: 15% !important;">
                                    {!! trans('staffs.bank_name') !!}
                                </th>
                                <td style="width: 35%;">
                                    {!! Form::select('bank_name', ['' => trans('system.dropdown_choice')] + $banks, old('bank_name', $user->bank_name), ['class' => 'form-control select2']) !!}
                                </td>
                                <th class="table_right_middle" style="width: 15%;">
                                    {!! trans('staffs.bank_account') !!}
                                </th>
                                <td>
                                    {!! Form::text('bank_account', old('bank_account', $user->bank_account), ['class' => 'form-control']) !!}
                                </td>
                            </tr>
                            <tr>
                                <th class="table_right_middle">
                                    {!! trans('staffs.driver_license_no') !!}
                                </th>
                                <td>
                                    {!! Form::number('driver_license_no', old('driver_license_no', $user->driver_license_no), ['class' => 'form-control']) !!}
                                </td>
                                <th class="table_right_middle">
                                    {!! trans('staffs.driver_license_class') !!}
                                </th>
                                <td>
                                    {!! Form::select('driver_license_class', ['' => trans('system.dropdown_choice')] + \App\Defines\Staff::getDriverLicensesForOption(), old('driver_license_class', $user->driver_license_class), ['class' => 'form-control select2']) !!}
                                </td>
                            </tr>
                            <tr>
                                <th class="table_right_middle">
                                    {!! trans('staffs.driver_license_expire') !!}
                                </th>
                                <td>
                                    {!! Form::text('driver_license_expire', old('driver_license_expire', $user->driver_license_expire ? date("d/m/Y", strtotime($user->driver_license_expire)) : null), ['class' => 'form-control datepicker1']) !!}
                                </td>
                                <th class="table_right_middle">
                                    {!! trans('staffs.qualifications.label') !!}
                                </th>
                                <td>
                                    {!! Form::select('qualification', ['' => trans('system.dropdown_choice')] + \App\Defines\Staff::getQualificationsForOption(), old('qualification', $user->qualification), ['class' => 'form-control select2']) !!}
                                </td>
                            </tr>
                            <tr>
                                <th class="table_right_middle">
                                    {!! trans('staffs.emergency_contact') !!}
                                </th>
                                <td>
                                    {!! Form::text('emergency_contact', old('emergency_contact', $user->emergency_contact), ['class' => 'form-control']) !!}
                                </td>
                                <th class="table_right_middle">
                                    {!! trans('staffs.emergency_phone') !!}
                                </th>
                                <td>
                                    {!! Form::number('emergency_phone', old('emergency_phone', $user->emergency_phone), ['class' => 'form-control']) !!}
                                </td>
                            </tr>
                            <tr>
                                <th class="table_right_middle">
                                    {!! trans('staffs.image') !!}
                                </th>
                                <td colspan="4">
                                    <div class="field" align="left">
                                        <input type="file" id="files" name="image[]" value="" multiple>
                                        @foreach($user->UserImages as $userImage)
                                            <span class="pip">
                                                <input type="hidden" name="img_edit[]" value="{{ $userImage->id }}">
                                                <img class="imageThumb" src="{{ asset($userImage->image_path) }}"/>
                                                <br/><i class="text-danger remove glyphicon glyphicon-remove"></i>
                                            </span>
                                        @endforeach
                                        @if($errors->has('image'))
                                            <span class="text-danger">{{ $errors->first('image') }}</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <strong>{!! trans('staffs.family') !!}</strong>
                                    <table class="table table-bordered family">
                                        <thead style="background: #3C8DBC;color: white;">
                                            <tr>
                                                <th style="text-align: center; vertical-align: middle;">{!! trans('system.no.') !!}</th>
                                                <th style="text-align: center; vertical-align: middle; white-space: nowrap;">{!! trans('staffs.family_relationships.label') !!}</th>
                                                <th style="text-align: center; vertical-align: middle; white-space: nowrap;">{!! trans('staffs.fullname') !!}</th>
                                                <th style="text-align: center; vertical-align: middle; white-space: nowrap;">{!! trans('staffs.tax_code') !!}</th>
                                                <th style="text-align: center; vertical-align: middle; white-space: nowrap;">{!! trans('staffs.dob') !!}</th>
                                                <th style="text-align: center; vertical-align: middle; white-space: nowrap;">{!! trans('staffs.genders.label') !!}</th>
                                                <th style="text-align: center; vertical-align: middle; white-space: nowrap;">{!! trans('staffs.dependent') !!}</th>
                                                <th style="text-align: center; vertical-align: middle; white-space: nowrap;">{!! trans('staffs.dependent_from') !!}</th>
                                                <th style="text-align: center; vertical-align: middle; white-space: nowrap;">{!! trans('staffs.dependent_to') !!}</th>
                                                <th style="text-align: center; vertical-align: middle; white-space: nowrap;">{!! trans('system.action.label') !!}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $family_relationship_id = old('family_relationship_id', $families->pluck('id')->toArray());
                                                $family_relationship = old('family_relationship', $families->pluck('relationship')->toArray());
                                                $family_fullname = old('family_fullname', $families->pluck('fullname')->toArray());
                                                $family_tax_code = old('family_tax_code', $families->pluck('tax_code')->toArray());
                                                $family_dob = old('family_dob', $families->pluck('dob')->toArray());
                                                $family_gender = old('family_gender', $families->pluck('gender')->toArray());
                                                $family_dependent = old('family_dependent', $families->pluck('dependent')->toArray());
                                                $family_dependent_from = old('family_dependent_from', $families->pluck('dependent_from')->toArray());
                                                $family_dependent_to = old('family_dependent_to', $families->pluck('dependent_to')->toArray());
                                            ?>
                                            @for ($i = 0; $i < count($family_relationship); $i++)
                                                <tr>
                                                    <td style="text-align: center; vertical-align: middle;">{!! $i+1 !!}</td>
                                                    <td style="vertical-align: middle;">
                                                        {!! Form::select("family_relationship[$i]", ['' => trans('system.dropdown_choice')] + \App\Defines\Staff::getFamilyRelationshipsForOption(), old("family_relationship[$i]", $family_relationship[$i]), ['class' => 'form-control select2', 'required']) !!}
                                                        {!! Form::hidden("family_relationship_id[$i]", $family_relationship_id[$i]) !!}
                                                    </td>
                                                    <td style="vertical-align: middle;">
                                                        {!! Form::text('family_fullname[]', old('family_fullname[]', $family_fullname[$i]), ['class' => 'form-control', 'required']) !!}
                                                    </td>
                                                    <td style="vertical-align: middle;">
                                                        {!! Form::number('family_tax_code[]', old('family_tax_code[]', $family_tax_code[$i]), ['class' => 'form-control']) !!}
                                                    </td>
                                                    <td style="text-align: center; vertical-align: middle;">
                                                        {!! Form::text("family_dob[$i]", old("family_dob[$i]", $family_dob[$i] ? date("d/m/Y", strtotime($family_dob[$i])) : null), ['class' => 'form-control datepicker', 'required']) !!}
                                                    </td>
                                                    <td style="text-align: center; vertical-align: middle;">
                                                        {!! Form::select("family_gender[$i]", ['' => trans('system.dropdown_choice')] + \App\Defines\Staff::getGendersForOption(), old("family_gender[$i]", $family_gender[$i]), ['class' => 'form-control select2']) !!}
                                                    </td>
                                                    <td style="vertical-align: middle;">
                                                        {!! Form::select("family_dependent[$i]", [0 => trans('system.no'), 1 => trans('system.yes')], old("family_dependent[$i]", $family_dependent[$i]), ['class' => 'form-control select2']) !!}
                                                    </td>
                                                    <td style="text-align: center; vertical-align: middle;">
                                                        {!! Form::select("family_dependent_from[$i]", ['' => trans('system.dropdown_choice')] + $monthYear, old("family_dependent_from[$i]", date("m/Y", strtotime($family_dependent_from[$i]))), ['class' => 'form-control select2']) !!}
                                                    </td>
                                                    <td style="text-align: center; vertical-align: middle;">
                                                        {!! Form::select("family_dependent_to[$i]", ['' => trans('system.dropdown_choice')] + $monthYear, old("family_dependent_to[$i]", date("m/Y", strtotime($family_dependent_to[$i]))), ['class' => 'form-control select2']) !!}
                                                    </td>
                                                    <td style="text-align: center; vertical-align: middle;">
                                                        <a href="javascript:void(0);" class="btn btn-xs btn-default remove-family">
                                                            <i class="text-danger fa fa-minus"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endfor
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="" style="text-align: center; vertical-align: middle;">
                                                    <a href="javascript:void(0);" class="btn btn-xs btn-primary btn-flat add-family">
                                                        {!! trans('system.action.create') !!}
                                                    </a>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </th>
                            </tr>
                            <tr id="t" class="@if($user->active == 0) hidden @endif">
                                <td align="center" colspan="4">
                                    <label for="active" style="margin-left: 100px;cursor: pointer;">
                                        <input style="margin-left: 50px; cursor: pointer;" id="active"
                                               @if($user->active == 1) checked="checked" @endif name="active" value="1"
                                               type="checkbox" value="0" class="minimal"/>
                                        {!! trans('system.status.active') !!}
                                    </label>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="row @if($user->active == 0) hidden @endif users col-md-12 box box-info users">
                    <div class="col-md-12">
                        {!! Form::label('role', trans('staffs.roles') ) !!}
                    </div>
                    @foreach($roles as $role)
                        <div class="col-md-3">
                            {!! Form::checkbox('roles[]', $role->id,old('roles', isset($uRoles[$role->id]) ? $role->id : '' ),['class' => 'minimal roles'])!!} {!! $role->display_name !!}
                        </div>
                    @endforeach
                </div>
                <div class="col-md-12 text-center">
                    {!! HTML::link(route( 'admin.staffs.index' ), trans('system.action.cancel'), ['class' => 'btn btn-danger btn-flat','id'=>'cancel']) !!}
                    {!! Form::submit(trans('system.action.save'), ['class' => 'btn btn-primary btn-flat','id'=>'save']) !!}
                </div>
            {!! Form::close() !!}
        </div>
    </div>
@stop
@section('footer')
    <script src="{!! asset('assets/backend/plugins/iCheck/icheck.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/select2/select2.full.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/moment/min/moment-with-locales.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/input-mask/jquery.inputmask.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.vi.min.js') !!}"></script>
    <script>
        $(document).ready(function() {
            $('.datepicker').datepicker({
                format: 'dd/mm/yyyy',
                language: "vi",
                autoclose: true,
            });
            $('.datepicker1').datepicker({
                format: 'dd/mm/yyyy',
                autoclose: true,
                language: 'vi',
            });
            $(".select2").select2({width: '100%'});
            $('input[type="checkbox"].minimal').iCheck({
                checkboxClass: 'icheckbox_minimal-red'
            });
            $('#active').on('ifChanged', function () {
                if ($(this).prop("checked")) {
                    $('.users').removeClass('hidden');
                    $("#password").prop('required', true);
                    $("#re_password").prop('required', true);
                    // $("#email").prop('required', true);
                } else {
                    $('.users').addClass('hidden');
                    $("#password").prop('required', false);
                    $("#re_password").prop('required', false);
                    // $("#email").prop('required', false);
                    /*$role_group = $("input:checkbox[name='roles[]']");
                    $role_group.is(":checked") ? $role_group.prop('required', false) : $role_group.prop('required', true);
                    $('input[name="roles[]"]').prop('required', true);*/
                }
            });
            $(".remove").click(function () {
                $(this).parent(".pip").remove();
            });
            if (window.File && window.FileList && window.FileReader) {
                $("#files").on("change", function (e) {
                    var files = e.target.files,
                        filesLength = files.length;
                    for (var i = 0; i < filesLength; i++) {
                        var f = files[i]
                        var fileReader = new FileReader();
                        fileReader.onload = (function (e) {
                            var file = e.target;
                            $("<span class=\"pip\">" +
                                "<img class=\"imageThumb\" src=\"" + e.target.result + "\" title=\"" + file.name + "\"/>" +
                                "<br/><i class=\"text-danger remove glyphicon glyphicon-remove\"></i>" +
                                "</span>").insertAfter("#files");
                            $(".remove").click(function () {
                                $(this).parent(".pip").remove();
                            });

                        });
                        fileReader.readAsDataURL(f);
                    }
                });
            } else {
                alert("Your browser doesn't support to File API")
            }

            var counterFamily = {!! count($family_relationship_id) + 1 !!};
            $(".add-family").on("click", function () {
                var newRow = $("<tr>");
                var cols = "";
                cols += '<td style="text-align: center; vertical-align: middle;">' + counterFamily + '</td>';
                cols += '<td style="vertical-align: middle; white-space: nowrap;">';
                cols += '{!! Form::select('family_relationship[]', ['' => trans('system.dropdown_choice')] + \App\Defines\Staff::getFamilyRelationshipsForOption(), null, ['class' => 'form-control select2', 'required']) !!}';
                cols += '</td>';
                cols += '<td style="vertical-align: middle;">';
                cols += '{!! Form::text('family_fullname[]',  null, ['class' => 'form-control', 'required']) !!}';
                cols += '</td>';
                cols += '<td style="vertical-align: middle;">';
                cols += '{!! Form::number('family_tax_code[]', null, ['class' => 'form-control']) !!}';
                cols += '</td>';
                cols += '<td style="text-align: center; vertical-align: middle;">';
                cols += '{!! Form::text('family_dob[]',  null, ['class' => 'form-control datepicker']) !!}';
                cols += '</td>';
                cols += '<td style="text-align: center; vertical-align: middle; white-space: nowrap;">';
                cols += '{!! Form::select('family_gender[]', ['' => trans('system.dropdown_choice')] + \App\Defines\Staff::getGendersForOption(), null, ['class' => 'form-control select2', 'required']) !!}';
                cols += '</td>';
                cols += '<td style="vertical-align: middle;">';
                cols += '{!! Form::select('family_dependent[]', [0 => trans('system.no'), 1 => trans('system.yes')], null, ['class' => 'form-control select2']) !!}';
                cols += '</td>';
                cols += '<td style="vertical-align: middle;">';
                cols += '{!! Form::select("family_dependent_from[]", ['' => trans('system.dropdown_choice')] + $monthYear, null, ['class' => 'form-control select2']) !!}';
                cols += '</td>';
                cols += '<td style="vertical-align: middle;">';
                cols += '{!! Form::select("family_dependent_to[]", ['' => trans('system.dropdown_choice')] + $monthYear, null, ['class' => 'form-control select2']) !!}';
                cols += '</td>';
                cols += '<td style="text-align: center; vertical-align: middle;">';
                cols += '<a href="javascript:void(0);" class="btn btn-xs btn-default remove-family">';
                cols += '<i class="text-danger fa fa-minus"></i>';
                cols += '</a>';
                cols += '</td>';
                newRow.append(cols);
                $("table.family").append(newRow);
                counterFamily++;
                $('.datepicker').datepicker({
                    format: 'dd/mm/yyyy',
                    autoclose: true,
                    endDate: '-1d',
                    language: 'vi',
                });
                $(".select2").select2({width: '100%'});
            });
            $(document).on("click", ".remove-family", function (event) {
                $(this).closest("tr").remove();
                counterFamily -= 1;
                var tmp = 1;
                $("table.family tbody td:first-child").each(function () {
                    $(this).html(tmp++);
                });

                
            });
            $('.roles').on('ifClicked', function() {
                $('.roles').not(this).iCheck('uncheck');
            });

            var count = {{ count($code_timekeeping_subs) > 0 ? (count($code_timekeeping_subs) + 1) : 2}};
                $('.btn-timekeeping-subs').on('click', function() {
                    let html = `
                        <tr>
                            <td style="text-align: center; vertical-align: middle; width: 50px" class="text-center">${count++}</td>
                            <td style="text-align: center; vertical-align: middle;">
                                <input type="text" name="code_timekeeping_subs[]" class="form-control time-subs">
                            </td>
                            <td style="text-align: center; vertical-align: middle; width: 70px">
                                <a href="javascript:void(0);" class="btn btn-xs btn-default remove-code-timek-sub">
                                    <i class="text-danger fa fa-minus"></i>
                                </a>
                            </td>
                        </tr>
                    `;
                    $('#add_code_timekeeping_subs').append(html);

                    $('.btn-cancel-timk').on('click', function() {
                        $('.time-subs').val('');
                    })
                });
            $(document).on("click", ".remove-code-timek-sub", function (event) {
                $(this).closest("tr").remove();
                count -= 1;
                let tmp = 1;
                $("#add_code_timekeeping_subs td:first-child").each(function() {
                    $(this).html(tmp++);
                });
            });

            $('.btn-cancel-timk').on('click', function() {
                $('.time-subs').val('');
            });

            $('.check-code-timek').on('click', function() {
                let val_code = $('input[name=code_timekeeping]').val();
                if (val_code == '') {
                    toastr.error('Chưa nhập mã công chính');
                    return false;
                };
            })
        });
    </script>
@stop