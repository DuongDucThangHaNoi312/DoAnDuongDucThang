@extends('backend.master')
@section('title'){!! trans('system.action.edit') !!} - {!! trans('staffs.label') !!}@stop
@section('head')
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/iCheck/all.css') !!}"/>
    <link rel="stylesheet" type="text/css" href="{!! asset('assets/backend/plugins/select2/select2.min.css') !!}"/>
@stop
@section('content')
    <section class="content-header">
        <h1>
            {!! trans('staffs.roles_user') !!}
        </h1>
        <ol class="breadcrumb">
            <li><a href="{!! route('admin.home') !!}">{!! trans('system.home') !!}</a></li>
            <li><a href="{!! route('admin.staffs.index') !!}">{!! trans('staffs.label') !!}</a></li>
        </ol>
    </section>
    <section class="content">
        <div class="container">
            <div class="row"></div>
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-info">
                        <div class="box-body">
                            {!! Form::open(['method' => 'POST', 'id' => 'title']) !!}
                            Nhân viên: <strong>{!! $user->fullname !!}</strong>
                            <br>
                            Quyền hiện tại: <strong>{!! $firstRole->display_name !!}</strong>
                            <br>
                            <strong>Thêm các quyền khác</strong>
                            <?php $count = 1; ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th rowspan="2" style="text-align: center; vertical-align: middle;">{!! trans('system.no.') !!}</th>
                                        <th rowspan="2" style="text-align: center; vertical-align: middle; white-space: nowrap;">{!! trans('roles.other') !!}</th>
                                        <th rowspan="2" style="text-align: center; vertical-align: middle; white-space: nowrap;">{!! trans('roles.module') !!}</th>
                                        <th colspan="4" style="text-align: center; white-space: nowrap;">{!! trans('roles.permission') !!}</th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: center; vertical-align: middle; white-space: nowrap;">{!! trans('system.action.read') !!}</th>
                                        <th style="text-align: center; vertical-align: middle; white-space: nowrap;">{!! trans('system.action.create') !!}</th>
                                        <th style="text-align: center; vertical-align: middle; white-space: nowrap;">{!! trans('system.action.update') !!}</th>
                                        <th style="text-align: center; vertical-align: middle; white-space: nowrap;">{!! trans('system.action.delete') !!}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($pGroups as $key => $value)
                                        <tr>
                                            <td style="text-align: center; vertical-align: middle;">{!! $count++ !!}</td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <a href="javascript:void(0);" class="add_unit" data-module-id="{!! $key !!}" data-module-name="{!! trans($key . '.label') !!}">
                                                    <i class="fas fa-angle-double-right"></i>
                                                </a>
                                            </td>
                                            <td style="vertical-align: middle; white-space: nowrap;">{!! trans($key . '.label') !!}</td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                @if(isset($value['read']))
                                                    <?php
                                                        $readByRole = (isset($permissionByRole[$value['read']]) && $permissionByRole[$value['read']]);
                                                        $readByAddRole = (isset($morePermissions[$value['read']]) && $morePermissions[$value['read']]);
                                                    ?>
                                                    {!! Form::checkbox('permissions[]', $value['read'], old('permissions', isset($permissions[$value['read']]) ? $value['read'] : ''), [$readByRole || $readByAddRole ? 'checked' : '', !$firstRole || $readByRole ? 'disabled' : '']) !!}
                                                @endif
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                @if(isset($value['create']))
                                                    <?php
                                                        $createByRole = (isset($permissionByRole[$value['create']]) && $permissionByRole[$value['create']]);
                                                        $createByAddRole = (isset($morePermissions[$value['create']]) && $morePermissions[$value['create']]);
                                                    ?>
                                                    {!! Form::checkbox('permissions[]', $value['create'], old('permissions', isset($permissions[$value['create']]) ? $value['create'] : ''), [$createByRole || $createByAddRole ? 'checked' : '', !$firstRole || $createByRole ? 'disabled' : '']) !!}
                                                @endif
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                @if(isset($value['update']))
                                                    <?php
                                                        $updateByRole = (isset($permissionByRole[$value['update']]) && $permissionByRole[$value['update']]);
                                                        $updateByAddRole = (isset($morePermissions[$value['update']]) && $morePermissions[$value['update']]);
                                                    ?>
                                                    {!! Form::checkbox('permissions[]', $value['update'], old('permissions', isset($permissions[$value['update']]) ? $value['update'] : ''), [$updateByRole || $updateByAddRole ? 'checked' : '', !$firstRole || $updateByRole ? 'disabled' : '']) !!}
                                                @endif
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                @if(isset($value['delete']))
                                                    <?php
                                                        $deleteByRole = (isset($permissionByRole[$value['delete']]) && $permissionByRole[$value['delete']]);
                                                        $deleteByAddRole = (isset($morePermissions[$value['delete']]) && $morePermissions[$value['delete']]);
                                                    ?>
                                                    {!! Form::checkbox('permissions[]', $value['delete'], old('permissions', isset($permissions[$value['delete']]) ? $value['delete'] : ''), [$deleteByRole || $deleteByAddRole ? 'checked' : '', !$firstRole || $deleteByRole ? 'disabled' : '']) !!}
                                                @endif
                                            </td>
                                            @foreach($value as $k => $v)
                                                @if (!in_array($k, ['read', 'update', 'create', 'delete']))
                                                    <?php
                                                        $otherByRole = (isset($permissionByRole[$v]) && $permissionByRole[$v]);
                                                        $otherByAddRole = (isset($morePermissions[$v]) && $morePermissions[$v]);
                                                    ?>
                                                    <td style="vertical-align: middle;">
                                                        {!! Form::checkbox('permissions[]', $v, old('permissions', isset($permissions[$v]) ? $v : ''), [$otherByRole || $otherByAddRole ? 'checked' : '', !$firstRole || $otherByRole ? 'disabled' : '']) !!} {!! trans('system.action.' . $k) !!}
                                                    </td>
                                                @endif
                                            @endforeach
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class='form-actions text-center'>
                                {!! HTML::link(route('admin.staffs.index'), trans('system.action.cancel'), ['class' => 'btn btn-default btn-flat']) !!}
                                @if($firstRole == null)
                                    <input type="button" class="btn btn-primary btn-flat" id="submitForm" value="{!! trans('system.action.save') !!}">
                                @else
                                    <input type="button" class="btn btn-primary btn-flat" id="update" value="{!! trans('system.action.update') !!}">
                                @endif
                            </div>
                            <input type="hidden" value="{!! $user->id !!}" name="id">
                            {!! Form::close() !!}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
@section('footer')
    <div class="modal fade" id="add_unit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h5 class="title text-center">Thêm quyền module <strong id="module-name"></strong> cho nhân viên <strong>{!! $user->fullname !!}</strong></h5>
                </div>
                <div class="modal-body">
                    {!! Form::hidden('module_id', "") !!}
                    <div class="box-body no-padding">
                        @foreach ($companies as $company)
                            <div class="row">
                                <div class="col-md-12">
                                    {!! Form::checkbox('companies', $company['id'], null, ["class" => "companies", "id" => "com_" . $company['id']]) !!} <label class="text-danger" for="com_{!! $company['id'] !!}">{!! $company['shortened_name'] !!}</label>
                                </div>
                                @foreach ($company['departments'] as $deptId => $deptName)
                                    <div class="col-md-3">
                                        {!! Form::checkbox('department_' . $deptId, $deptId, null, ["class" => "departments", "id" => "dept_" . $deptId]) !!} <label for="dept_{!! $deptId !!}">{!! $deptName !!}</label>
                                    </div>
                                @endforeach
                            </div>
                            <hr/>
                        @endforeach
                        <div class="form-group">
                            <label>Team</label>
                            {!! Form::select('teams[]', $teams, null, ["class" => "form-control select2", "multiple" => "multiple", "disabled"]) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::checkbox('manager_other', 1, old('manager_other', 0), ["class" => "minimal", "id" => "manager_other"]) !!}
                            <label for="manager_other">Quản lý phòng/công ty khác</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer text-center">
                    <a href="javascript:void(0)" class="btn btn-danger btn-flat text-center" data-dismiss="modal">{!! trans('system.action.cancel') !!}</a>
                    <a href="javascript:void(0)" class="btn btn-primary btn-flat text-center" id="save-roles">{!! trans('system.action.save') !!}</a>
                </div>
            </div>
        </div>
    </div>
    <script src="{!! asset('assets/backend/plugins/iCheck/icheck.min.js') !!}"></script>
    <script src="{!! asset('assets/backend/plugins/select2/select2.full.min.js') !!}"></script>
    <script>
        $('#submitForm').on('click',function () {
            $.ajax({
                url: "{{ route('admin.staffs.storeRoles') }}",
                type: 'POST',
                data: $('#title').serialize(),
                headers: {
                    'X-CSRF-Token': "{!! csrf_token() !!}"
                },
                success: function (data) {
                    toastr.success('{!!trans('staff_titles.add') !!}');
                    setTimeout(function () {
                        location.reload();
                    }, 1500);                }
            })
        });
        $('#update').on('click',function () {
            // var disabled=$('input[type=checkbox]').attr('disabled')
            // if(typeof disabled !== 'undefined' && disabled !== false){
            //     $('input[type=checkbox]').prop('disabled',false)
            // } else {
                $.ajax({
                    url: "{{ route('admin.staffs.storeRoles') }}",
                    type: 'POST',
                    data: $('#title').serialize(),
                    headers: {
                        'X-CSRF-Token': "{!! csrf_token() !!}"
                    },
                    success: function (data) {
                        toastr.success('{!!trans('staff_titles.add') !!}');
                        setTimeout(function () {
                            location.reload();
                        }, 1500);
                    }
                })
            // }
        });
        (function($) {
            "use strict"
            $(".select2").select2({'width': '100%', closeOnSelect: false}).on('select2:selecting', e => $(e.currentTarget).data('scrolltop', $('.select2-results__options').scrollTop())).on('select2:select', e => $('.select2-results__options').scrollTop($(e.currentTarget).data('scrolltop')));
            $(".add_unit").click(function(event) {
                // get info
                var moduleName = $(this).attr('data-module-name'), moduleId = $(this).attr('data-module-id');
                $.getJSON("{!! route('admin.staffs.get-more-roles') !!}?module_id=" + moduleId + "&user_id=" + {!! $user->id !!}).done(function (data) {
                    $("#module-name").html(moduleName);
                    $("input[name='module_id']").val(moduleId);
                    data = data.data;
                    if (data.manager_other) {
                        $("input[name='manager_other']").iCheck("check");
                        $(".select2").prop('disabled', false);
                        $.each(data.companies, function(index, val) {
                            $('#com_' + val).iCheck("check");
                            $('#com_' + val).find('.departments').iCheck("check").iCheck("disable");
                        });
                        $.each(data.departments, function(index, val) {
                            $('#dept_' + val).iCheck("check");
                        });
                        $("select[name='teams[]']").val(data.teams).trigger('change');
                    } else {
                        $("input[name='manager_other']").iCheck("uncheck");
                        $ ('.departments').iCheck("uncheck").iCheck("disable");
                        $ ('.companies').iCheck("uncheck").iCheck("disable");
                        $(".select2").val("").trigger("change");
                        $(".select2").prop('disabled', true);
                    }
                    $("#add_unit").modal('show');
                }).fail(function(jqxhr, textStatus, error) {
                    var error = $.parseJSON(jqxhr.responseText);
                    toastr.error(error.message, '{!! trans('system.info') !!}');
                }).always(function() {
                });
            });

            $(".departments").iCheck({
                checkboxClass: 'icheckbox_minimal-blue'
            });
            $(".companies").iCheck({
                checkboxClass: 'icheckbox_minimal-red'
            }).on('ifChecked', function (event) {
                $(this).closest('.row').find('.departments').iCheck("check").iCheck("disable");
            }).on('ifUnchecked', function (event) {
                $(this).closest('.row').find('.departments').iCheck("unCheck").iCheck("enable");
            });
            $("input[name='manager_other']").iCheck({
                checkboxClass: 'icheckbox_minimal-blue'
            }).on('ifChecked', function (event) {
                $(".select2").prop('disabled', false);
                $('.departments').iCheck("enable");
                $('.companies').iCheck("enable");
            }).on('ifUnchecked', function (event) {
                $('.departments').iCheck("uncheck").iCheck("disable");
                $('.companies').iCheck("uncheck").iCheck("disable");
                $(".select2").val("").trigger("change");
                $(".select2").prop('disabled', true);
            });
            $("#save-roles").click(function(event) {
                var manager_other = $("input[name='manager_other']").is(":checked") ? 1 : 0;
                var teams = $("select[name='teams[]']").val(), companies = [], departments = [];
                if (manager_other == 1 && companies == null && departments == null && teams == null) {
                    toastr.error("Cần chọn ít nhất một Công ty, Phòng ban hoặc Team", '{!! trans('system.have_an_error') !!}');
                    return false;
                }
                $('.companies').each(function(index, val) {
                    if ($(this).is(":checked")) {
                        companies.push($(this).val());
                    }
                });
                $('.departments').each(function(index, val) {
                    if ($(this).is(":checked")) {
                        if (typeof $(this).attr("disabled") === "undefined") departments.push($(this).val());
                    }
                });
                // NProgress.start();
                $.ajax({
                    url: "{!! route('admin.staffs.save-more-roles') !!}",
                    data: { manager_other: manager_other, companies: companies, departments: departments, teams: teams, user_id: {!! $user->id !!}, module_id: $("input[name='module_id']").val() },
                    type: 'POST',
                    datatype: 'json',
                    headers: {'X-CSRF-Token': "{!! csrf_token() !!}"},
                    success: function(res) {
                        toastr.success('{!!trans('staff_titles.add') !!}');
                        setTimeout(function () {
                            location.reload();
                        }, 1000);
                    },
                    error: function(obj, status, err) {
                        var error = $.parseJSON(obj.responseText);
                        toastr.error(error.message, '{!! trans('system.have_an_error') !!}');
                    }
                }).always(function() {
                    // NProgress.done();
                });
            });
        })(jQuery);
    </script>
@stop
